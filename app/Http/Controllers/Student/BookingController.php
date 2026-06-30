<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Notifications\BookingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BookingController extends Controller
{
    // statuses that "hold" the booking (active)
    private array $activeStatuses = ['pending', 'payment_submitted', 'paid', 'cancel_requested'];

    public function index()
    {
        $studentId = Auth::id();

        $bookings = Booking::with(['room', 'landlord'])
            ->where('student_id', $studentId)
            ->latest()
            ->paginate(10);

        return view('student.bookings.index', compact('bookings'));
    }

    /**
     * ✅ helper: get current active booking for this student (case-insensitive)
     */
    private function getActiveBookingForStudent(int $studentId): ?Booking
    {
        return Booking::with(['room', 'landlord'])
            ->where('student_id', $studentId)
            ->whereIn(DB::raw('LOWER(status)'), $this->activeStatuses)
            ->latest()
            ->first();
    }

    public function create(Room $room)
    {
        $studentId = Auth::id();

        // ✅ If already has active booking, redirect to that booking (professional UX)
        $active = $this->getActiveBookingForStudent($studentId);
        if ($active) {
            return redirect()
                ->route('student.bookings.show', $active->id)
                ->with('error', 'You already have an active booking. Please complete it here.');
        }

        // ✅ prevent opening booking page if room is not bookable (inventory)
        $type = strtolower((string)($room->room_type ?? 'single'));

        // ✅ treat as shared if contains word "shared"
        $isShared = str_contains($type, 'shared');

        if (!$isShared) {
            // single-like rooms: if landlord turned off availability, block
            if (isset($room->is_available) && (bool)$room->is_available === false) {
                return redirect()
                    ->route('student.rooms.show', $room->id)
                    ->with('error', 'This room is no longer available.');
            }
        } else {
            // ✅ SHARED: if available_slots exists, TRUST it (because store() already decreases it)
            $cap = is_numeric($room->capacity ?? null) ? (int)$room->capacity : 4;

            if (is_numeric($room->available_slots ?? null)) {
                $slots = (int)$room->available_slots;
            } else {
                // ✅ if DB slots is NULL, compute from bookings
                $booked = 0;

                try {
                    if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'room_id')) {
                        $q = \App\Models\Booking::query()->where('room_id', $room->id);

                        if (Schema::hasColumn('bookings', 'status')) {
                            $q->whereIn(DB::raw('LOWER(status)'), ['pending','payment_submitted','paid','cancel_requested']);
                        }

                        $booked = (int)$q->count();
                    }
                } catch (\Throwable $e) {
                    // ignore
                }

                $slots = max(0, $cap - $booked);
            }

            if ($slots <= 0) {
                return redirect()
                    ->route('student.rooms.show', $room->id)
                    ->with('error', 'No slots available for this shared room.');
            }
        }

        return view('student.bookings.create', compact('room'));
    }

    public function store(Request $request, Room $room)
    {
        $student = Auth::user();

        // ✅ prevent double booking (student only one active)
        $hasActive = Booking::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'payment_submitted', 'paid', 'cancel_requested'])
            ->exists();

        if ($hasActive) {
            return redirect()
                ->route('student.bookings.index')
                ->with('error', 'You already have an active booking. Please complete or cancel it first.');
        }

        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'note_to_landlord' => ['nullable', 'string', 'max:500'],
        ]);

        return DB::transaction(function () use ($validated, $student, $room) {

            // ✅ lock room row (avoid 2 students book same time)
            $lockedRoom = Room::where('id', $room->id)->lockForUpdate()->firstOrFail();

            // ✅ check availability NOW
            if (!$lockedRoom->isBookable()) {
                return redirect()
                    ->route('student.rooms.show', $lockedRoom->id)
                    ->with('error', 'Sorry, this room is not available now.');
            }

            $start = Carbon::parse($validated['start_date'])->startOfDay();

            // ✅ ALWAYS 1 YEAR (fixed)
            $end = (clone $start)->addYear()->subDay();

            $deposit = 100;
            $rent = is_numeric($lockedRoom->price_monthly ?? null) ? (float)$lockedRoom->price_monthly : 0;
            $total = $deposit + $rent;

            // ✅ create booking
            $booking = Booking::create([
                'student_id' => $student->id,
                'landlord_id' => $lockedRoom->landlord_id,
                'room_id' => $lockedRoom->id,

                'contract_start_date' => $start->toDateString(),
                'contract_end_date' => $end->toDateString(),

                'deposit_amount' => $deposit,
                'monthly_rent' => $rent,
                'total_due' => $total,

                'status' => 'pending',
                'student_note' => $validated['note_to_landlord'] ?? null,
            ]);

            // ✅ NEW: notify landlord about new booking
            $landlord = User::find($lockedRoom->landlord_id);
            if ($landlord) {
                $landlord->notify(new BookingNotification(
                    'New booking received',
                    $student->name . ' booked "' . $lockedRoom->title . '" and is expected to proceed with payment.'
                ));
            }

            // ✅✅✅ ADD ONLY THIS PART (CREATE CONTRACT)
            try {
                if (class_exists(\App\Models\Contract::class) && Schema::hasTable('contracts')) {
                    $contractNo = 'SR-CTR-' . now()->format('Y') . '-' . str_pad((string)($booking->id), 5, '0', STR_PAD_LEFT);

                    \App\Models\Contract::create([
                        'contract_no'     => $contractNo,
                        'booking_id'      => $booking->id,
                        'room_id'         => $lockedRoom->id,
                        'student_id'      => $student->id,
                        'landlord_id'     => $lockedRoom->landlord_id,

                        // snapshot
                        'room_title'      => $lockedRoom->title,
                        'room_type'       => $lockedRoom->room_type,

                        'start_date'      => $start->toDateString(),
                        'end_date'        => $end->toDateString(),

                        'monthly_rent'    => $rent,
                        'deposit_amount'  => $deposit,
                        'total_amount'    => $total,

                        'status'          => 'draft',
                        'payment_status'  => 'unpaid',
                    ]);
                }
            } catch (\Throwable $e) {
                // do nothing (booking still must succeed)
            }
            // ✅✅✅ END ADD

            // ✅ reserve inventory immediately
            $type = strtolower((string)$lockedRoom->room_type);
            $isShared = str_contains($type, 'shared');

            if ($isShared) {
                $cap = is_numeric($lockedRoom->capacity ?? null) ? (int)$lockedRoom->capacity : 4;

                // ✅ if available_slots is NULL, init from capacity
                $currentSlots = is_numeric($lockedRoom->available_slots ?? null)
                    ? (int)$lockedRoom->available_slots
                    : $cap;

                $currentSlots = max(0, $currentSlots - 1);
                $lockedRoom->available_slots = $currentSlots;

                // if no slot left, mark not available
                if ($currentSlots <= 0) {
                    $lockedRoom->is_available = false;
                } else {
                    // ✅ ensure available true when slots still > 0
                    $lockedRoom->is_available = true;
                }

                $lockedRoom->save();
            } else {
                // single/studio etc => once booked, not available
                $lockedRoom->is_available = false;
                $lockedRoom->save();
            }

            return redirect()
                ->route('student.bookings.show', $booking)
                ->with('success', 'Booking created! Next step: Payment.');
        });
    }

    public function show(Booking $booking)
    {
        $this->ensureOwner($booking);
        $booking->load(['room', 'landlord', 'payments', 'review']);
        return view('student.bookings.show', compact('booking'));
    }

    // Cancel ONLY if pending (before payment). restore inventory.
    public function cancel(Request $request, Booking $booking)
    {
        $this->ensureOwner($booking);

        $status = strtolower((string)$booking->status);

        if ($status !== 'pending') {
            return back()->with('error', 'You can only cancel a booking before payment is submitted.');
        }

        return DB::transaction(function () use ($booking) {

            // lock room row
            $room = Room::where('id', $booking->room_id)->lockForUpdate()->first();

            // update booking
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_reason' => 'Cancelled by student before payment.',
            ]);

            // ✅ NEW: notify landlord about booking cancelled before payment
            $landlord = User::find($booking->landlord_id);
            if ($landlord) {
                $landlord->notify(new BookingNotification(
                    'Booking cancelled before payment',
                    'A student cancelled booking #' . $booking->id . ' before submitting payment.'
                ));
            }

            if ($room) {
                $type = strtolower((string)$room->room_type);
                $isShared = str_contains($type, 'shared');

                if ($isShared) {
                    $cap = max(1, (int)($room->capacity ?? 1));
                    $slots = (int)($room->available_slots ?? $cap);

                    // restore 1 slot (but not exceed capacity)
                    $slots = min($cap, $slots + 1);
                    $room->available_slots = $slots;

                    // if got slot, available again
                    if ($slots > 0) {
                        $room->is_available = true;
                    }
                    $room->save();
                } else {
                    // single => make available again
                    $room->is_available = true;
                    $room->save();
                }
            }

            return redirect()
                ->route('student.bookings.index')
                ->with('success', 'Booking cancelled successfully.');
        });
    }

    // Request cancel/refund after payment submitted/paid
    public function requestCancel(Request $request, Booking $booking)
    {
        $this->ensureOwner($booking);

        $status = strtolower((string)$booking->status);
        if (!in_array($status, ['payment_submitted', 'paid'], true)) {
            return back()->with('error', 'Cancel/Refund request is only allowed after payment is submitted/paid.');
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $booking->update([
            'status' => 'cancel_requested',
            'cancel_requested_at' => now(),
            'cancel_request_reason' => $data['reason'],
        ]);

        // ✅ NEW: notify landlord about cancel/refund request
        $landlord = User::find($booking->landlord_id);
        if ($landlord) {
            $landlord->notify(new BookingNotification(
                'Cancel/refund request submitted',
                'Booking #' . $booking->id . ' has a new cancel/refund request from the student.'
            ));
        }

        return redirect()
            ->route('student.bookings.show', $booking->id)
            ->with('success', 'Cancel/Refund request submitted. Waiting landlord verification.');
    }

    private function ensureOwner(Booking $booking): void
    {
        if ((int)$booking->student_id !== (int)Auth::id()) {
            abort(403);
        }
    }
}