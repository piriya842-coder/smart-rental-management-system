<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\MonthlyRent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::where('student_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('student.contracts.index', compact('contracts'));
    }

    public function show(Contract $contract)
    {
        abort_unless($contract->student_id === Auth::id(), 403);

        return view('student.contracts.show', compact('contract'));
    }

    public function sign(Request $request, Contract $contract)
    {
        abort_unless($contract->student_id === Auth::id(), 403);

        if ($contract->is_signed) {
            return back()->with('success', 'This contract has already been signed.');
        }

        $request->validate([
            'agreed_to_terms' => ['accepted'],
            'signed_name'     => ['required', 'string', 'max:255'],
            'signature_data'  => ['required', 'string'],
        ], [
            'agreed_to_terms.accepted' => 'You must agree to the terms and conditions before signing.',
            'signed_name.required'     => 'Please enter your full name as your digital signature.',
            'signature_data.required'  => 'Please draw your signature before submitting.',
        ]);

        $signatureData = $request->signature_data;

        if (!str_starts_with($signatureData, 'data:image/png;base64,')) {
            return back()->withErrors([
                'signature_data' => 'Invalid signature format.'
            ])->withInput();
        }

        $contract->update([
            'agreed_to_terms' => true,
            'signed_name'     => trim($request->signed_name),
            'signed_at'       => now(),
            'signature_ip'    => $request->ip(),
            'signature_data'  => $signatureData,
            'status'          => 'signed',
        ]);

        $this->generateMonthlyRents($contract);

        return redirect()
            ->route('student.contracts.show', $contract)
            ->with('success', 'Contract signed successfully.');
    }

    protected function generateMonthlyRents(Contract $contract): void
    {
        if ($contract->monthlyRents()->exists()) {
            return;
        }

        if (!$contract->start_date || !$contract->end_date) {
            return;
        }

        $start = Carbon::parse($contract->start_date)->copy()->addMonth()->startOfMonth();
        $end   = Carbon::parse($contract->end_date)->copy()->startOfMonth();

        while ($start->lte($end)) {
            $dueDate = $start->copy()->day(min(5, $start->daysInMonth));

            MonthlyRent::create([
                'contract_id' => $contract->id,
                'booking_id'  => $contract->booking_id,
                'month_label' => $start->format('F Y'),
                'month_date'  => $start->copy(),
                'due_date'    => $dueDate,
                'amount'      => $contract->monthly_rent,
                'status'      => 'upcoming',
            ]);

            $start->addMonth();
        }
    }

    public function downloadPdf(Contract $contract)
    {
        abort_unless($contract->student_id === Auth::id(), 403);

        if (!$contract->is_signed) {
            return back()->with('error', 'Please sign the contract before downloading the PDF.');
        }

        $pdf = Pdf::loadView('student.contracts.pdf', compact('contract'))
            ->setPaper('a4', 'portrait');

        $filename = 'contract-' . ($contract->contract_no ?: $contract->id) . '.pdf';

        return $pdf->download($filename);
    }
}