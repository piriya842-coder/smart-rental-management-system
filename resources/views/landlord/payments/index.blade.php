@extends('layouts.landlord')

@section('title', 'Payments • Landlord')
@section('page_title', 'Payments')
@section('page_subtitle', 'Verify student payment receipts for your bookings.')

@section('content')
@php
    $gold  = '#C6A15B';
    $green = '#7FA089';
    $red   = '#B56D75';
    $blue  = '#8C9AB5';
@endphp

<style>
    .sr-panel{
        border:1px solid #F0D9DC;
        box-shadow:0 18px 42px rgba(92,32,41,.06);
        background:#FFFFFF;
    }

    .sr-summary-card{
        position:relative;
        overflow:hidden;
        border:1px solid #EDE5E7;
        box-shadow:0 14px 30px rgba(92,32,41,.05);
        transition:all .25s ease;
    }
    .sr-summary-card:hover{
        transform:translateY(-3px);
        box-shadow:0 18px 36px rgba(92,32,41,.08);
    }
    .sr-summary-card::after{
        content:"";
        position:absolute;
        top:-24px;
        right:-16px;
        width:92px;
        height:92px;
        border-radius:9999px;
        opacity:.12;
    }

    .sr-summary-total{
        background:linear-gradient(135deg,#FFFFFF 0%,#FBF8F8 100%);
        border-color:#EFE4E6;
    }
    .sr-summary-total::after{ background:#C8B8BC; }

    .sr-summary-pending{
        background:linear-gradient(135deg,#FFFDF9 0%,#FFF8F1 100%);
        border-color:#EFD6B4;
    }
    .sr-summary-pending::after{ background:#D9B37A; }

    .sr-summary-approved{
        background:linear-gradient(135deg,#FAFCFA 0%,#F4F8F5 100%);
        border-color:#D5E4D8;
    }
    .sr-summary-approved::after{ background:#A7C1AE; }

    .sr-summary-amount{
        background:linear-gradient(135deg,#FAFBFD 0%,#F5F7FB 100%);
        border-color:#D8DFEC;
    }
    .sr-summary-amount::after{ background:#AAB8D0; }

    .sr-section-head{
        background:
            radial-gradient(circle at top right, rgba(185,28,28,.05), transparent 28%),
            linear-gradient(135deg,#FFFFFF 0%,#FFF7F8 100%);
        border-bottom:1px solid #F0D9DC;
    }

    .sr-pill{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:.55rem 1rem;
        border-radius:9999px;
        font-size:12px;
        font-weight:800;
        letter-spacing:.02em;
    }

    .sr-table-wrap{
        overflow:hidden;
        border-radius:26px;
        border:1px solid #EFE6E8;
        background:#FFFFFF;
    }

    .sr-table{
        min-width:1800px;
        width:100%;
        color:#5E5558;
    }

    .sr-table thead tr{
        background:linear-gradient(135deg,#FFFFFF 0%,#FBF8F8 100%);
        border-bottom:1px solid #EFE6E8;
    }

    .sr-table th{
        text-align:left;
        padding:1rem 1.25rem;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.12em;
        font-weight:900;
        color:#8E7D82;
        white-space:nowrap;
    }

    .sr-table td{
        padding:1.25rem 1.25rem;
        vertical-align:top;
        border-bottom:1px solid #EFE6E8;
        background:#FFFFFF;
    }

    .sr-table tbody tr{
        transition:all .2s ease;
    }
    .sr-table tbody tr:hover td{
        background:#FCFAFA;
    }

    .sr-info-badge{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:9999px;
        padding:.35rem .8rem;
        font-size:11px;
        font-weight:800;
        white-space:nowrap;
    }

    .sr-soft-box{
        border:1px solid #EFE6E8;
        background:#FFFFFF;
        border-radius:18px;
        padding:.9rem 1rem;
    }

    .sr-receipt-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:14px;
        padding:.6rem 1rem;
        font-size:12px;
        font-weight:800;
        transition:all .2s ease;
        background:#FFFFFF;
        color:#5E5558;
        border:1px solid #E2D4D7;
    }
    .sr-receipt-btn:hover{
        transform:translateY(-2px);
        box-shadow:0 10px 18px rgba(92,32,41,.08);
        background:#FFF9FA;
    }

    .sr-action-approve{
        width:100%;
        border-radius:14px;
        padding:.75rem 1rem;
        font-size:14px;
        font-weight:900;
        color:#FFFFFF;
        background:linear-gradient(135deg,#7B9583 0%,#95AD9A 100%);
        box-shadow:0 10px 20px rgba(123,149,131,.18);
        transition:all .2s ease;
    }
    .sr-action-approve:hover{
        transform:translateY(-2px);
        box-shadow:0 14px 24px rgba(123,149,131,.24);
    }

    .sr-action-reject{
        width:100%;
        border-radius:14px;
        padding:.75rem 1rem;
        font-size:14px;
        font-weight:900;
        color:#FFFFFF;
        background:linear-gradient(135deg,#B56D75 0%,#9F5C64 100%);
        box-shadow:0 10px 20px rgba(159,92,100,.18);
        transition:all .2s ease;
    }
    .sr-action-reject:hover{
        transform:translateY(-2px);
        box-shadow:0 14px 24px rgba(159,92,100,.24);
    }

    .sr-status-approved{
        background:#F4F8F5;
        color:#5F7E68;
        border:1px solid #D5E4D8;
    }

    .sr-status-rejected{
        background:#FBF4F4;
        color:#9C5F66;
        border:1px solid #E9D1D5;
    }

    .sr-status-submitted{
        background:#FFF8F1;
        color:#9A5B13;
        border:1px solid #EFD6B4;
    }

    .sr-empty{
        background:linear-gradient(135deg,#FFFFFF 0%,#FBF8F8 100%);
        border:1px dashed #E2D4D7;
    }
</style>

@if(session('success'))
    <div class="mb-6 rounded-2xl px-5 py-4 font-semibold shadow-sm"
         style="background:#F4F8F5; color:#5F7E68; border:1px solid #D5E4D8;">
        ✅ {{ session('success') }}
    </div>
@endif

<div class="space-y-6">

    <div class="rounded-[30px] overflow-hidden sr-panel">
        <div class="p-6 md:p-8">
            <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="text-xs uppercase tracking-[0.22em] font-black" style="color:#AA9AA0;">
                        PAYMENT MANAGEMENT
                    </div>
                    <div class="mt-2 text-3xl md:text-4xl font-extrabold" style="color:#2A0709;">
                        Landlord Payment Review
                    </div>
                    <p class="mt-3 max-w-2xl leading-7" style="color:#7E6E73;">
                        Review student payment submissions, verify uploaded receipts, and approve or reject both first payment and monthly rent in one place.
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 min-w-full xl:min-w-[620px]">
                    <div class="sr-summary-card sr-summary-total rounded-2xl p-4">
                        <div class="text-[11px] uppercase tracking-[0.16em] font-black relative z-10" style="color:#AA9AA0;">
                            Total Records
                        </div>
                        <div class="mt-2 text-3xl font-extrabold relative z-10" style="color:#2A0709;">
                            {{ $payments->total() }}
                        </div>
                    </div>

                    <div class="sr-summary-card sr-summary-pending rounded-2xl p-4">
                        <div class="text-[11px] uppercase tracking-[0.16em] font-black relative z-10" style="color:#9A5B13;">
                            Pending
                        </div>
                        <div class="mt-2 text-3xl font-extrabold relative z-10" style="color:#B67A2E;">
                            {{ $submittedCount }}
                        </div>
                    </div>

                    <div class="sr-summary-card sr-summary-approved rounded-2xl p-4">
                        <div class="text-[11px] uppercase tracking-[0.16em] font-black relative z-10" style="color:#5F7E68;">
                            Approved
                        </div>
                        <div class="mt-2 text-3xl font-extrabold relative z-10" style="color:#73907A;">
                            {{ $paidCount }}
                        </div>
                    </div>

                    <div class="sr-summary-card sr-summary-amount rounded-2xl p-4">
                        <div class="text-[11px] uppercase tracking-[0.16em] font-black relative z-10" style="color:#6C7C98;">
                            Total Amount
                        </div>
                        <div class="mt-2 text-3xl font-extrabold relative z-10" style="color: {{ $gold }};">
                            RM {{ number_format((float)$totalAmount, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-[30px] overflow-hidden sr-panel">
        <div class="sr-section-head p-6 md:p-7">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="text-2xl font-extrabold" style="color:#2A0709;">Payment Records</div>
                    <p class="mt-2" style="color:#7E6E73;">
                        First payment and monthly rent payment records are shown together for easier verification.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="sr-pill" style="background:#FFF8F1; color:#9A5B13; border:1px solid #EFD6B4;">
                        Submitted {{ $submittedCount }}
                    </span>
                    <span class="sr-pill" style="background:#F4F8F5; color:#5F7E68; border:1px solid #D5E4D8;">
                        Paid {{ $paidCount }}
                    </span>
                    <span class="sr-pill" style="background:#FBF4F4; color:#9C5F66; border:1px solid #E9D1D5;">
                        Rejected {{ $rejectedCount }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 md:p-7">
            @if($payments->count() === 0)
                <div class="sr-empty rounded-[28px] p-10 text-center">
                    <div class="text-6xl mb-4">💳</div>
                    <div class="text-2xl font-extrabold" style="color:#2A0709;">No payments found</div>
                    <div class="mt-3" style="color:#7E6E73;">
                        Student payment submissions will appear here once receipts are uploaded.
                    </div>
                </div>
            @else
                <div class="sr-table-wrap">
                    <div class="overflow-x-auto">
                        <table class="sr-table">
                            <thead>
                                <tr>
                                    <th>Payment</th>
                                    <th>Payment Type</th>
                                    <th>Rent Month</th>
                                    <th>Student</th>
                                    <th>Room</th>
                                    <th>Contract</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th>Receipt</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($payments as $payment)
                                    @php
                                        $status = strtolower($payment->status ?? 'submitted');

                                        $badgeStyle = 'background:#FFF8F1; color:#9A5B13; border:1px solid #EFD6B4;';
                                        $statusText = 'Submitted';

                                        if ($status === 'paid') {
                                            $badgeStyle = 'background:#F4F8F5; color:#5F7E68; border:1px solid #D5E4D8;';
                                            $statusText = 'Paid';
                                        } elseif ($status === 'rejected') {
                                            $badgeStyle = 'background:#FBF4F4; color:#9C5F66; border:1px solid #E9D1D5;';
                                            $statusText = 'Rejected';
                                        }

                                        $receiptUrl = $payment->receipt_path ? asset('storage/' . $payment->receipt_path) : null;
                                        $ext = $payment->receipt_path ? strtolower(pathinfo($payment->receipt_path, PATHINFO_EXTENSION)) : null;

                                        $typeBadgeStyle = $payment->source_type === 'monthly_rent'
                                            ? 'background:#F7F4FA; color:#86739E; border:1px solid #E1D8EA;'
                                            : 'background:#F5F7FB; color:#6C7C98; border:1px solid #D8DFEC;';
                                    @endphp

                                    <tr>
                                        <td>
                                            <div class="font-extrabold" style="color:#2A0709;">#{{ $payment->payment_id }}</div>
                                            <div class="mt-2 text-xs" style="color:#AA9AA0;">
                                                Booking #{{ $payment->booking_id ?? '-' }}
                                            </div>
                                        </td>

                                        <td>
                                            <span class="sr-info-badge" style="{{ $typeBadgeStyle }}">
                                                {{ $payment->type_label }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="font-extrabold" style="color:#2A0709;">{{ $payment->rent_month }}</div>
                                            <div class="mt-2 text-xs" style="color:#AA9AA0;">
                                                @if($payment->source_type === 'monthly_rent')
                                                    Monthly rental cycle
                                                @else
                                                    Initial contract payment
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div class="font-extrabold" style="color:#2A0709;">{{ $payment->student_name }}</div>
                                            <div class="mt-2 text-xs break-all" style="color:#AA9AA0;">{{ $payment->student_email }}</div>
                                        </td>

                                        <td>
                                            <div class="font-extrabold leading-6" style="color:#2A0709;">{{ $payment->room_title }}</div>
                                            <div class="mt-2 text-xs" style="color:#AA9AA0;">{{ $payment->room_type }}</div>
                                        </td>

                                        <td>
                                            <div class="sr-soft-box">
                                                <div class="font-bold" style="color:#5E5558;">
                                                    {{ optional($payment->contract_start)->format('d M Y') ?? '-' }}
                                                </div>
                                                <div class="mt-1 text-xs" style="color:#AA9AA0;">to</div>
                                                <div class="mt-1 font-bold" style="color:#5E5558;">
                                                    {{ optional($payment->contract_end)->format('d M Y') ?? '-' }}
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="font-extrabold" style="color: {{ $gold }};">
                                                RM {{ number_format((float)$payment->amount, 2) }}
                                            </div>
                                            <div class="mt-2 text-xs" style="color:#7E6E73; line-height:1.6;">
                                                Deposit: RM {{ number_format((float)$payment->deposit_amount, 2) }}<br>
                                                Rent: RM {{ number_format((float)$payment->monthly_rent, 2) }}
                                            </div>
                                        </td>

                                        <td>
                                            <div class="font-extrabold" style="color:#2A0709;">{{ strtoupper($payment->method ?? '-') }}</div>
                                            <div class="mt-2 text-xs" style="color:#AA9AA0;">{{ $payment->provider ?? 'Manual Upload' }}</div>
                                        </td>

                                        <td>
                                            <div class="font-bold" style="color:#5E5558;">
                                                {{ optional($payment->submitted_at)->format('d M Y') ?? '-' }}
                                            </div>
                                            <div class="mt-2 text-xs" style="color:#AA9AA0;">
                                                {{ optional($payment->submitted_at)->format('h:i A') ?? '-' }}
                                            </div>
                                        </td>

                                        <td>
                                            <span class="sr-info-badge" style="{{ $badgeStyle }}">
                                                {{ strtoupper($statusText) }}
                                            </span>

                                            @if($status === 'paid' && $payment->paid_at)
                                                <div class="mt-2 text-xs" style="color:#73907A; line-height:1.5;">
                                                    Verified {{ $payment->paid_at->format('d M Y, h:i A') }}
                                                </div>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="flex flex-col gap-2 min-w-[140px]">
                                                @if($payment->receipt_path)
                                                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                                        <a href="{{ $receiptUrl }}" target="_blank" class="sr-receipt-btn">
                                                            View Image
                                                        </a>
                                                    @elseif($ext === 'pdf')
                                                        <a href="{{ $receiptUrl }}" target="_blank" class="sr-receipt-btn">
                                                            Open PDF
                                                        </a>
                                                    @else
                                                        <span class="text-xs" style="color:#AA9AA0;">Unsupported</span>
                                                    @endif
                                                @else
                                                    <span class="text-xs" style="color:#AA9AA0;">No receipt</span>
                                                @endif

                                                <a href="{{ route('landlord.payments.summary-pdf', [$payment->source_type, $payment->source_id]) }}"
                                                   class="sr-receipt-btn"
                                                   target="_blank">
                                                    Summary PDF
                                                </a>
                                            </div>
                                        </td>

                                        <td>
                                            @if($status === 'submitted')
                                                <div class="flex flex-col gap-2 min-w-[150px]">
                                                    @if($payment->source_type === 'monthly_rent')
                                                        <form method="POST" action="{{ route('landlord.monthly-rents.approve', $payment->source_id) }}">
                                                            @csrf
                                                            <button type="submit" class="sr-action-approve">
                                                                Approve
                                                            </button>
                                                        </form>

                                                        <form method="POST"
                                                              action="{{ route('landlord.monthly-rents.reject', $payment->source_id) }}"
                                                              onsubmit="return confirm('Reject this monthly rent payment? Student will need to upload again.')">
                                                            @csrf
                                                            <button type="submit" class="sr-action-reject">
                                                                Reject
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('landlord.payments.approve', $payment->source_id) }}">
                                                            @csrf
                                                            <button type="submit" class="sr-action-approve">
                                                                Approve
                                                            </button>
                                                        </form>

                                                        <form method="POST"
                                                              action="{{ route('landlord.payments.reject', $payment->source_id) }}"
                                                              onsubmit="return confirm('Reject this first payment? Student will need to upload again.')">
                                                            @csrf
                                                            <button type="submit" class="sr-action-reject">
                                                                Reject
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @elseif($status === 'paid')
                                                <div class="rounded-xl px-3 py-2 text-xs font-extrabold text-center min-w-[120px] sr-status-approved">
                                                    Approved
                                                </div>
                                            @elseif($status === 'rejected')
                                                <div class="rounded-xl px-3 py-2 text-xs font-extrabold text-center min-w-[120px] sr-status-rejected">
                                                    Rejected
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection