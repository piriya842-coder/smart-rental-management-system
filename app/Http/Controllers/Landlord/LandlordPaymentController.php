<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\MonthlyRent;
use App\Models\User;
use App\Notifications\PaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class LandlordPaymentController extends Controller
{
    public function index(Request $request)
    {
        $landlordId = Auth::id();

        // FIRST PAYMENT / DEPOSIT RECORDS
        $bookingPayments = Payment::with(['booking.student', 'booking.room'])
            ->whereHas('booking', function ($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            })
            ->get()
            ->map(function ($payment) {
                $booking = $payment->booking;
                $student = $booking?->student;
                $room = $booking?->room;

                return (object) [
                    'source_type'     => 'booking_payment',
                    'source_id'       => $payment->id,

                    'payment_id'      => $payment->id,
                    'booking_id'      => $booking?->id,
                    'type_label'      => 'First Payment',
                    'rent_month'      => 'First Month / Deposit',

                    'student_name'    => $student?->name ?? '-',
                    'student_email'   => $student?->email ?? '-',

                    'room_title'      => $room?->title ?? 'Room',
                    'room_type'       => $room?->room_type ?? 'Room',

                    'contract_start'  => $booking?->contract_start_date,
                    'contract_end'    => $booking?->contract_end_date,

                    'amount'          => $payment->amount,
                    'deposit_amount'  => $booking?->deposit_amount ?? 0,
                    'monthly_rent'    => $booking?->monthly_rent ?? 0,

                    'method'          => $payment->method,
                    'provider'        => $payment->provider,
                    'provider_ref'    => $payment->provider_ref,
                    'status'          => $payment->status,
                    'receipt_path'    => $payment->receipt_path,
                    'submitted_at'    => $payment->created_at,
                    'paid_at'         => $payment->paid_at,
                ];
            });

        // MONTHLY RENT RECORDS
        $monthlyRentPayments = MonthlyRent::with(['contract.student', 'contract.room'])
            ->whereHas('contract', function ($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            })
            ->where(function ($q) {
                $q->whereNotNull('receipt_path')
                  ->orWhereIn('status', ['submitted', 'paid', 'rejected']);
            })
            ->get()
            ->map(function ($rent) {
                $contract = $rent->contract;
                $student = $contract?->student;
                $room = $contract?->room;

                return (object) [
                    'source_type'     => 'monthly_rent',
                    'source_id'       => $rent->id,

                    'payment_id'      => $rent->id,
                    'booking_id'      => $rent->booking_id,
                    'type_label'      => 'Monthly Rent',
                    'rent_month'      => $rent->month_label ?? '-',

                    'student_name'    => $student?->name ?? '-',
                    'student_email'   => $student?->email ?? '-',

                    'room_title'      => $contract?->room_title ?? ($room?->title ?? 'Room'),
                    'room_type'       => $contract?->room_type ?? ($room?->room_type ?? 'Room'),

                    'contract_start'  => $contract?->start_date,
                    'contract_end'    => $contract?->end_date,

                    'amount'          => $rent->amount,
                    'deposit_amount'  => $contract?->deposit_amount ?? 0,
                    'monthly_rent'    => $contract?->monthly_rent ?? 0,

                    'method'          => $rent->method,
                    'provider'        => 'Monthly Rent Receipt',
                    'provider_ref'    => null,
                    'status'          => $rent->status,
                    'receipt_path'    => $rent->receipt_path,
                    'submitted_at'    => $rent->submitted_at ?? $rent->updated_at,
                    'paid_at'         => $rent->paid_at,
                ];
            });

        // MERGE BOTH
        $allPayments = $bookingPayments
            ->concat($monthlyRentPayments)
            ->sort(function ($a, $b) {
                $priority = [
                    'submitted' => 0,
                    'paid' => 1,
                    'rejected' => 2,
                    'overdue' => 3,
                    'due_soon' => 4,
                    'upcoming' => 5,
                ];

                $aPriority = $priority[strtolower($a->status ?? '')] ?? 99;
                $bPriority = $priority[strtolower($b->status ?? '')] ?? 99;

                if ($aPriority !== $bPriority) {
                    return $aPriority <=> $bPriority;
                }

                $aTime = optional($a->submitted_at)->timestamp ?? 0;
                $bTime = optional($b->submitted_at)->timestamp ?? 0;

                return $bTime <=> $aTime;
            })
            ->values();

        $submittedCount = $allPayments->where('status', 'submitted')->count();
        $paidCount      = $allPayments->where('status', 'paid')->count();
        $rejectedCount  = $allPayments->where('status', 'rejected')->count();
        $totalAmount    = $allPayments->sum('amount');

        $payments = $this->paginateCollection($allPayments, 10, $request);

        return view('landlord.payments.index', compact(
            'payments',
            'submittedCount',
            'paidCount',
            'rejectedCount',
            'totalAmount'
        ));
    }

    protected function paginateCollection(Collection $collection, int $perPage, Request $request): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    public function summaryPdf(string $sourceType, int $sourceId)
    {
        $landlordId = Auth::id();

        if ($sourceType === 'booking_payment') {
            $payment = Payment::with(['booking.student', 'booking.room', 'booking.landlord'])
                ->whereHas('booking', function ($q) use ($landlordId) {
                    $q->where('landlord_id', $landlordId);
                })
                ->findOrFail($sourceId);

            $booking = $payment->booking;
            $student = $booking?->student;
            $room = $booking?->room;
            $landlord = $booking?->landlord ?? Auth::user();

            $summary = (object) [
                'source_type'    => 'booking_payment',
                'payment_id'     => $payment->id,
                'booking_id'     => $booking?->id,
                'type_label'     => 'First Payment',
                'rent_month'     => 'First Month / Deposit',
                'student_name'   => $student?->name ?? '-',
                'student_email'  => $student?->email ?? '-',
                'room_title'     => $room?->title ?? 'Room',
                'room_type'      => $room?->room_type ?? 'Room',
                'contract_start' => $booking?->contract_start_date,
                'contract_end'   => $booking?->contract_end_date,
                'amount'         => $payment->amount,
                'deposit_amount' => $booking?->deposit_amount ?? 0,
                'monthly_rent'   => $booking?->monthly_rent ?? 0,
                'method'         => $payment->method,
                'provider'       => $payment->provider,
                'provider_ref'   => $payment->provider_ref,
                'status'         => $payment->status,
                'submitted_at'   => $payment->created_at,
                'paid_at'        => $payment->paid_at,
                'landlord_name'  => $landlord?->name ?? '-',
                'company_name'   => $landlord?->company_name ?? '-',
                'landlord_email' => $landlord?->email ?? '-',
                'landlord_phone' => $landlord?->phone ?? '-',
            ];
        } elseif ($sourceType === 'monthly_rent') {
            $rent = MonthlyRent::with(['contract.student', 'contract.room', 'contract.landlord'])
                ->whereHas('contract', function ($q) use ($landlordId) {
                    $q->where('landlord_id', $landlordId);
                })
                ->findOrFail($sourceId);

            $contract = $rent->contract;
            $student = $contract?->student;
            $room = $contract?->room;
            $landlord = $contract?->landlord ?? Auth::user();

            $summary = (object) [
                'source_type'    => 'monthly_rent',
                'payment_id'     => $rent->id,
                'booking_id'     => $rent->booking_id,
                'type_label'     => 'Monthly Rent',
                'rent_month'     => $rent->month_label ?? '-',
                'student_name'   => $student?->name ?? '-',
                'student_email'  => $student?->email ?? '-',
                'room_title'     => $room?->title ?? 'Room',
                'room_type'      => $room?->room_type ?? 'Room',
                'contract_start' => $contract?->start_date,
                'contract_end'   => $contract?->end_date,
                'amount'         => $rent->amount,
                'deposit_amount' => $contract?->deposit_amount ?? 0,
                'monthly_rent'   => $contract?->monthly_rent ?? 0,
                'method'         => $rent->method,
                'provider'       => 'Monthly Rent Receipt',
                'provider_ref'   => null,
                'status'         => $rent->status,
                'submitted_at'   => $rent->submitted_at ?? $rent->updated_at,
                'paid_at'        => $rent->paid_at,
                'landlord_name'  => $landlord?->name ?? '-',
                'company_name'   => $landlord?->company_name ?? '-',
                'landlord_email' => $landlord?->email ?? '-',
                'landlord_phone' => $landlord?->phone ?? '-',
            ];
        } else {
            abort(404);
        }

        $pdf = Pdf::loadView('landlord.payments.summary-pdf', [
            'summary' => $summary,
        ])->setPaper('a4', 'portrait');

        $fileName = 'payment-summary-' . $sourceType . '-' . $sourceId . '.pdf';

        return $pdf->download($fileName);
    }

    // FIRST PAYMENT APPROVE
    public function approve(Payment $payment)
    {
        $booking = $payment->booking;

        abort_unless($booking && $booking->landlord_id === Auth::id(), 403);

        if ($payment->status === 'paid') {
            return back()->with('success', 'This payment is already approved.');
        }

        DB::transaction(function () use ($payment, $booking) {
            $payment->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            $booking->update([
                'status' => 'paid',
            ]);

            $student = User::find($booking->student_id);
            if ($student) {
                $student->notify(new PaymentNotification(
                    'First payment approved',
                    'Your first payment for booking #' . $booking->id . ' has been approved by the landlord.'
                ));
            }
        });

        return back()->with('success', 'First payment approved successfully.');
    }

    // FIRST PAYMENT REJECT
    public function reject(Payment $payment)
    {
        $booking = $payment->booking;

        abort_unless($booking && $booking->landlord_id === Auth::id(), 403);

        DB::transaction(function () use ($payment, $booking) {
            $payment->update([
                'status'  => 'rejected',
                'paid_at' => null,
            ]);

            $booking->update([
                'status' => 'pending',
            ]);

            $student = User::find($booking->student_id);
            if ($student) {
                $student->notify(new PaymentNotification(
                    'First payment rejected',
                    'Your first payment for booking #' . $booking->id . ' was rejected. Please upload a new receipt.'
                ));
            }
        });

        return back()->with('success', 'First payment rejected. Student can upload again.');
    }

    // MONTHLY RENT APPROVE
    public function approveMonthly(MonthlyRent $monthlyRent)
    {
        abort_unless($monthlyRent->contract && $monthlyRent->contract->landlord_id === Auth::id(), 403);

        if ($monthlyRent->status === 'paid') {
            return back()->with('success', 'This monthly rent is already approved.');
        }

        $monthlyRent->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        $student = User::find($monthlyRent->contract->student_id);
        if ($student) {
            $student->notify(new PaymentNotification(
                'Monthly rent approved',
                'Your ' . $monthlyRent->month_label . ' monthly rent payment has been approved.'
            ));
        }

        return back()->with('success', $monthlyRent->month_label . ' monthly rent approved successfully.');
    }

    // MONTHLY RENT REJECT
    public function rejectMonthly(MonthlyRent $monthlyRent)
    {
        abort_unless($monthlyRent->contract && $monthlyRent->contract->landlord_id === Auth::id(), 403);

        $monthlyRent->update([
            'status'  => 'rejected',
            'paid_at' => null,
        ]);

        $student = User::find($monthlyRent->contract->student_id);
        if ($student) {
            $student->notify(new PaymentNotification(
                'Monthly rent rejected',
                'Your ' . $monthlyRent->month_label . ' monthly rent payment was rejected. Please upload a new receipt.'
            ));
        }

        return back()->with('success', $monthlyRent->month_label . ' monthly rent rejected.');
    }
}