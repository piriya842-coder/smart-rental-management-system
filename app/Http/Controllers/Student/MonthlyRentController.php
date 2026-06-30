<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\MonthlyRent;
use App\Models\User;
use App\Notifications\PaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MonthlyRentController extends Controller
{
    public function index()
    {
        $studentId = Auth::id();

        // ✅ BACKFILL old signed contracts that do not yet have monthly rent rows
        $contracts = Contract::where('student_id', $studentId)
            ->whereNotNull('signed_at')
            ->get();

        foreach ($contracts as $contract) {
            if (!$contract->monthlyRents()->exists()) {
                $this->generateMonthlyRents($contract);
            }
        }

        $monthlyRents = MonthlyRent::with(['contract.room'])
            ->whereHas('contract', function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            })
            ->orderBy('month_date')
            ->get();

        // ✅ auto refresh statuses
        foreach ($monthlyRents as $rent) {
    if (!in_array($rent->status, ['paid', 'submitted'])) {

        $originalStatus = $rent->status;

        if (now()->gt($rent->due_date)) {
            $rent->status = 'overdue';

            // 🚨 OVERDUE notification
            if ($originalStatus !== 'overdue') {
                Auth::user()->notify(new PaymentNotification(
                    'Payment Overdue',
                    'Your ' . $rent->month_label . ' rent is overdue. Please pay immediately.'
                ));
            }

        } elseif (now()->diffInDays($rent->due_date, false) <= 3) {
            $rent->status = 'due_soon';

            // 🔔 REMINDER notification
            if ($originalStatus !== 'due_soon') {
                Auth::user()->notify(new PaymentNotification(
                    'Rent Due Soon',
                    'Your ' . $rent->month_label . ' rent is due on ' . $rent->due_date->format('d M Y') . '.'
                ));
            }

        } else {
            $rent->status = 'upcoming';
        }

        $rent->save();
    }
}

        $monthlyRents = MonthlyRent::with(['contract.room'])
            ->whereHas('contract', function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            })
            ->orderBy('month_date')
            ->get();

        $paidCount = $monthlyRents->where('status', 'paid')->count();
        $submittedCount = $monthlyRents->where('status', 'submitted')->count();
        $overdueCount = $monthlyRents->where('status', 'overdue')->count();
        $remainingCount = $monthlyRents->whereNotIn('status', ['paid'])->count();

        return view('student.monthly-rents.index', compact(
            'monthlyRents',
            'paidCount',
            'submittedCount',
            'overdueCount',
            'remainingCount'
        ));
    }

    public function show(MonthlyRent $monthlyRent)
    {
        abort_unless($monthlyRent->contract->student_id === Auth::id(), 403);

        return view('student.monthly-rents.show', compact('monthlyRent'));
    }

    public function upload(Request $request, MonthlyRent $monthlyRent)
    {
        abort_unless($monthlyRent->contract->student_id === Auth::id(), 403);

        if (in_array($monthlyRent->status, ['submitted', 'paid'])) {
            return back()->with('success', 'This monthly rent has already been submitted.');
        }

        $request->validate([
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'method'  => ['nullable', 'in:qr,fpx'],
        ]);

        $path = $request->file('receipt')->store('monthly-rent-receipts', 'public');

        $monthlyRent->update([
            'receipt_path' => $path,
            'method'       => $request->input('method', 'qr'),
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        // ✅ NEW: notify landlord about monthly rent submission
        $landlord = User::find($monthlyRent->contract->landlord_id);
        if ($landlord) {
            $landlord->notify(new PaymentNotification(
                'Monthly rent submitted',
                Auth::user()->name . ' submitted ' . $monthlyRent->month_label . ' monthly rent receipt.'
            ));
        }

        return redirect()
            ->route('student.monthly-rents.index')
            ->with('success', $monthlyRent->month_label . ' payment submitted successfully.');
    }

    protected function generateMonthlyRents(Contract $contract): void
    {
        if (!$contract->start_date || !$contract->end_date || !$contract->monthly_rent) {
            return;
        }

        // start from NEXT month after contract start
        $start = Carbon::parse($contract->start_date)->copy()->addMonthNoOverflow()->startOfMonth();
        $end   = Carbon::parse($contract->end_date)->copy()->startOfMonth();

        while ($start->lte($end)) {
            MonthlyRent::firstOrCreate(
                [
                    'contract_id' => $contract->id,
                    'month_date'  => $start->copy()->format('Y-m-d'),
                ],
                [
                    'booking_id'  => $contract->booking_id,
                    'month_label' => $start->format('F Y'),
                    'due_date'    => $start->copy()->day(min(5, $start->daysInMonth)),
                    'amount'      => $contract->monthly_rent,
                    'status'      => 'upcoming',
                ]
            );

            $start->addMonthNoOverflow();
        }
    }
}