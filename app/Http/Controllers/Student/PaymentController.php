<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $studentId = Auth::id();

        // show student's payments history
        $payments = Payment::with(['booking.room'])
            ->whereHas('booking', function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            })
            ->orderByDesc('id')
            ->paginate(8);

        return view('student.payments.index', compact('payments'));
    }

    public function show(Booking $booking)
    {
        // ✅ Security: only owner can pay
        abort_unless($booking->student_id === Auth::id(), 403);

        // ✅ If already submitted/paid, still show page but disable upload accordingly in blade
        $payment = Payment::where('booking_id', $booking->id)->latest()->first();

        return view('student.payments.show', compact('booking', 'payment'));
    }

    public function upload(Request $request, Booking $booking)
    {
        abort_unless($booking->student_id === Auth::id(), 403);

        // ✅ Prevent double upload if already submitted/paid
        if (in_array($booking->status, ['payment_submitted', 'paid'])) {
            return redirect()->route('student.bookings.index')
                ->with('success', 'Payment already submitted. Waiting verification.');
        }

        $request->validate([
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'method'  => ['nullable', 'in:qr,fpx'],
        ]);

        $method = $request->input('method', 'qr');
        $path = $request->file('receipt')->store('receipts', 'public');

        DB::transaction(function () use ($booking, $method, $path) {

            // lock booking row
            $booking->refresh();
            $booking->load('room');

            $room = $booking->room;

            // ✅ INVENTORY CHECK (very important)
            if ($room) {
                $type = strtolower((string)($room->room_type ?? 'single'));

                if ($type === 'shared') {
                    $slots = (int)($room->available_slots ?? 0);

                    if ($slots <= 0) {
                        abort(422, 'This shared room is fully booked.');
                    }

                    $room->available_slots = $slots - 1;
                    $room->is_available = $room->available_slots > 0;
                    $room->save();
                } else {
                    // single / studio => once paid submitted, mark not available
                    $room->available_slots = 0;
                    $room->is_available = false;
                    $room->save();
                }
            }

            $payment = Payment::create([
                'booking_id'   => $booking->id,
                'amount'       => $booking->total_due,
                'method'       => $method,
                'status'       => 'submitted',
                'receipt_path' => $path,

                'provider'     => $method === 'fpx' ? 'FPX (Demo)' : 'DuitNow QR (Demo)',
                'provider_ref' => 'SR-' . Str::upper(Str::random(10)),

                // ✅ paid_at stays NULL until landlord verifies
                'paid_at'      => null,
            ]);

            // ✅ After receipt upload: booking becomes payment_submitted
            $booking->status = 'payment_submitted';
            $booking->save();

            // ✅ NEW: notify landlord about first payment submission
            $landlord = User::find($booking->landlord_id);
            if ($landlord) {
                $landlord->notify(new PaymentNotification(
                    'First payment submitted',
                    Auth::user()->name . ' uploaded the first payment receipt for booking #' . $booking->id . '.'
                ));
            }
        });

        return redirect()->route('student.bookings.index')
            ->with('success', 'Receipt uploaded ✅ Your booking is now pending verification.');
    }
}