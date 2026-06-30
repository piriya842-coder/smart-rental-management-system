@extends('layouts.landlord')

@section('title', 'Bookings • Landlord')
@section('page_title', 'Bookings')
@section('page_subtitle', 'Track student bookings, payment progress, and active tenants.')

@section('content')
@php
    function bookingStatusConfig($status) {
        return match(strtolower((string)$status)) {
            'pending' => [
                'label' => 'Waiting for Payment',
                'badge' => 'background:#FFF8F1; color:#9A5B13; border:1px solid #EFD6B4;',
            ],
            'payment_submitted' => [
                'label' => 'Payment Submitted',
                'badge' => 'background:#F5F7FB; color:#5D6F91; border:1px solid #D8DFEC;',
            ],
            'paid' => [
                'label' => 'Active / Paid',
                'badge' => 'background:#F4F8F5; color:#5B7C69; border:1px solid #D5E4D8;',
            ],
            'cancel_requested' => [
                'label' => 'Cancel Requested',
                'badge' => 'background:#FFF7F2; color:#9A6040; border:1px solid #EBCDBB;',
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'badge' => 'background:#FBF4F4; color:#9C5F66; border:1px solid #E9D1D5;',
            ],
            default => [
                'label' => ucfirst((string)$status),
                'badge' => 'background:#F7F7F8; color:#5F6672; border:1px solid #E5E7EB;',
            ],
        };
    }
@endphp

<style>
    .sr-panel{
        border:1px solid #F0D9DC;
        box-shadow:0 18px 45px rgba(92,32,41,.06);
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
        right:-18px;
        width:96px;
        height:96px;
        border-radius:999px;
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

    .sr-summary-submitted{
        background:linear-gradient(135deg,#FAFBFD 0%,#F5F7FB 100%);
        border-color:#D8DFEC;
    }
    .sr-summary-submitted::after{ background:#AAB8D0; }

    .sr-summary-paid{
        background:linear-gradient(135deg,#FAFCFA 0%,#F4F8F5 100%);
        border-color:#D5E4D8;
    }
    .sr-summary-paid::after{ background:#A7C1AE; }

    .sr-summary-cancel{
        background:linear-gradient(135deg,#FFFBFA 0%,#FFF6F3 100%);
        border-color:#EBCDBB;
    }
    .sr-summary-cancel::after{ background:#D9AE97; }

    .sr-section{
        overflow:hidden;
        border:1px solid #F0D9DC;
        box-shadow:0 18px 40px rgba(92,32,41,.06);
        background:#FFFFFF;
    }

    .sr-section-head{
        background:
            radial-gradient(circle at top right, rgba(185,28,28,.05), transparent 28%),
            linear-gradient(135deg,#FFFFFF 0%,#FFF7F8 100%);
        border-bottom:1px solid #F0D9DC;
    }

    .sr-card{
        border:1px solid #EFE6E8;
        background:
            radial-gradient(circle at top right, rgba(185,28,28,.03), transparent 25%),
            linear-gradient(135deg,#FFFFFF 0%,#FCFAFA 100%);
        box-shadow:0 12px 24px rgba(92,32,41,.04);
        transition:all .25s ease;
    }
    .sr-card:hover{
        transform:translateY(-2px);
        box-shadow:0 18px 36px rgba(92,32,41,.07);
    }

    .sr-info-box{
        background:#FFFFFF;
        border:1px solid #EFE6E8;
        box-shadow:inset 0 1px 0 rgba(255,255,255,.65);
    }

    .sr-mini-label{
        font-size:11px;
        letter-spacing:.08em;
        text-transform:uppercase;
        font-weight:800;
        color:#AA9AA0;
    }

    .sr-action-primary{
        background:linear-gradient(135deg,#8C9AB5 0%,#AAB6CC 100%);
        color:#FFFFFF;
        border:1px solid #94A2BC;
        box-shadow:0 10px 22px rgba(121,138,170,.18);
        transition:all .25s ease;
    }
    .sr-action-primary:hover{
        transform:translateY(-2px);
        box-shadow:0 14px 28px rgba(121,138,170,.24);
    }

    .sr-action-danger{
        background:linear-gradient(135deg,#B56D75 0%,#9F5C64 100%);
        color:#FFFFFF;
        border:1px solid #9F5C64;
        box-shadow:0 10px 22px rgba(159,92,100,.18);
        transition:all .25s ease;
    }
    .sr-action-danger:hover{
        transform:translateY(-2px);
        box-shadow:0 14px 28px rgba(159,92,100,.24);
    }

    .sr-empty{
        background:linear-gradient(135deg,#FFFFFF 0%,#FBF8F8 100%);
        border:1px dashed #E2D4D7;
    }

    .sr-student-chip{
        background:#FAF7F7;
        border:1px solid #E8DADD;
        color:#7F5B61;
    }
</style>

<div class="space-y-8">

    {{-- TOP SUMMARY --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="sr-summary-card sr-summary-total rounded-[28px] p-5">
            <div class="text-sm font-semibold" style="color:#7E6E73;">Total Bookings</div>
            <div class="mt-3 text-4xl font-extrabold relative z-10" style="color:#2A0709;">{{ $totalBookings }}</div>
            <div class="mt-2 text-xs relative z-10" style="color:#AE9EA3;">All booking records under your rooms</div>
        </div>

        <div class="sr-summary-card sr-summary-pending rounded-[28px] p-5">
            <div class="text-sm font-semibold" style="color:#9A5B13;">Waiting for Payment</div>
            <div class="mt-3 text-4xl font-extrabold relative z-10" style="color:#B67A2E;">{{ $pendingCount }}</div>
            <div class="mt-2 text-xs relative z-10" style="color:#A2753D;">Student booked but has not submitted payment yet</div>
        </div>

        <div class="sr-summary-card sr-summary-submitted rounded-[28px] p-5">
            <div class="text-sm font-semibold" style="color:#6C7C98;">Payment Submitted</div>
            <div class="mt-3 text-4xl font-extrabold relative z-10" style="color:#7E8FAC;">{{ $paymentSubmittedCount }}</div>
            <div class="mt-2 text-xs relative z-10" style="color:#74829D;">Ready for payment verification</div>
        </div>

        <div class="sr-summary-card sr-summary-paid rounded-[28px] p-5">
            <div class="text-sm font-semibold" style="color:#5F7E68;">Active / Paid</div>
            <div class="mt-3 text-4xl font-extrabold relative z-10" style="color:#73907A;">{{ $paidCount }}</div>
            <div class="mt-2 text-xs relative z-10" style="color:#64806B;">Confirmed tenants with approved first payment</div>
        </div>

        <div class="sr-summary-card sr-summary-cancel rounded-[28px] p-5">
            <div class="text-sm font-semibold" style="color:#9A6040;">Cancel Requests</div>
            <div class="mt-3 text-4xl font-extrabold relative z-10" style="color:#B17757;">{{ $cancelRequestedCount }}</div>
            <div class="mt-2 text-xs relative z-10" style="color:#9E6A4D;">Requests waiting for your review</div>
        </div>
    </div>

    {{-- WAITING FOR PAYMENT --}}
    <section class="sr-section rounded-[30px]">
        <div class="sr-section-head px-6 py-5 md:px-8">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-black tracking-wider"
                         style="background:#FFF8F1; color:#9A5B13; border:1px solid #EFD6B4;">
                        PAYMENT STAGE
                    </div>
                    <h2 class="text-2xl font-extrabold mt-3" style="color:#2A0709;">Waiting for Payment</h2>
                    <p class="mt-1 text-sm" style="color:#7E6E73;">
                        Student has created the booking and should continue to first payment.
                    </p>
                </div>

                <div class="inline-flex w-fit items-center rounded-full px-4 py-2 text-sm font-bold"
                     style="background:#FFF8F1; color:#A16A2E; border:1px solid #EFD6B4;">
                    {{ $pendingBookings->count() }} booking(s)
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-5">
            @forelse($pendingBookings as $booking)
                @php $status = bookingStatusConfig($booking->status); @endphp

                <div class="sr-card rounded-[28px] p-5 md:p-6">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="h-14 w-14 rounded-2xl grid place-items-center text-2xl sr-student-chip">👤</div>
                            <div>
                                <div class="text-xl font-bold" style="color:#2A0709;">{{ $booking->student->name ?? '-' }}</div>
                                <div class="mt-1 text-sm" style="color:#7E6E73;">{{ $booking->student->email ?? '-' }}</div>
                            </div>
                        </div>

                        <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold"
                              style="{{ $status['badge'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Room</div>
                            <div class="mt-2 font-bold" style="color:#2A0709;">{{ $booking->room->title ?? 'Room' }}</div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">{{ $booking->room->room_type ?? '-' }}</div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Contract Period</div>
                            <div class="mt-2 font-semibold" style="color:#2A0709;">
                                {{ optional($booking->contract_start_date)->format('d M Y') ?? '-' }}
                            </div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">
                                to {{ optional($booking->contract_end_date)->format('d M Y') ?? '-' }}
                            </div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Amount Details</div>
                            <div class="mt-2 text-sm" style="color:#5E5558;">Deposit: RM {{ number_format((float)$booking->deposit_amount, 2) }}</div>
                            <div class="mt-1 text-sm" style="color:#5E5558;">Monthly Rent: RM {{ number_format((float)$booking->monthly_rent, 2) }}</div>
                            <div class="mt-1 text-sm font-bold" style="color:#2A0709;">Total: RM {{ number_format((float)$booking->total_due, 2) }}</div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Booking Date</div>
                            <div class="mt-2 font-semibold" style="color:#2A0709;">
                                {{ optional($booking->created_at)->format('d M Y') ?? '-' }}
                            </div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">
                                {{ optional($booking->created_at)->format('h:i A') ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 sr-info-box rounded-2xl p-4">
                        <div class="sr-mini-label">Student Note</div>
                        <div class="mt-2 text-sm" style="color:#5E5558;">
                            {{ $booking->student_note ?: 'No note from student.' }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="sr-empty rounded-[24px] px-6 py-12 text-center" style="color:#7E6E73;">
                    <div class="text-4xl mb-3">🕒</div>
                    <div class="font-extrabold text-lg" style="color:#5E5558;">No bookings are waiting for payment.</div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- PAYMENT SUBMITTED --}}
    <section class="sr-section rounded-[30px]">
        <div class="sr-section-head px-6 py-5 md:px-8">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-black tracking-wider"
                         style="background:#F5F7FB; color:#6C7C98; border:1px solid #D8DFEC;">
                        ACTION REQUIRED
                    </div>
                    <h2 class="text-2xl font-extrabold mt-3" style="color:#2A0709;">Payment Submitted</h2>
                    <p class="mt-1 text-sm" style="color:#7E6E73;">
                        Student uploaded first payment. Please verify it from the payments page.
                    </p>
                </div>

                <a href="{{ route('landlord.payments.index') }}"
                   class="inline-flex w-fit items-center rounded-full px-5 py-3 text-sm font-bold sr-action-primary">
                    Go to Payments Page
                </a>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-5">
            @forelse($paymentSubmittedBookings as $booking)
                @php
                    $status = bookingStatusConfig($booking->status);
                    $latestPayment = $booking->payments->sortByDesc('created_at')->first();
                @endphp

                <div class="sr-card rounded-[28px] p-5 md:p-6">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="h-14 w-14 rounded-2xl grid place-items-center text-2xl sr-student-chip">👤</div>
                            <div>
                                <div class="text-xl font-bold" style="color:#2A0709;">{{ $booking->student->name ?? '-' }}</div>
                                <div class="mt-1 text-sm" style="color:#7E6E73;">{{ $booking->student->email ?? '-' }}</div>
                            </div>
                        </div>

                        <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold"
                              style="{{ $status['badge'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Room</div>
                            <div class="mt-2 font-bold" style="color:#2A0709;">{{ $booking->room->title ?? 'Room' }}</div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">{{ $booking->room->room_type ?? '-' }}</div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Payment Method</div>
                            <div class="mt-2 font-semibold" style="color:#2A0709;">{{ $latestPayment->method ?? '-' }}</div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">Provider: {{ $latestPayment->provider ?? '-' }}</div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">Ref: {{ $latestPayment->provider_ref ?? '-' }}</div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Amount Submitted</div>
                            <div class="mt-2 font-bold" style="color:#2A0709;">
                                RM {{ number_format((float)($latestPayment->amount ?? $booking->total_due), 2) }}
                            </div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">
                                Submitted: {{ optional($latestPayment?->created_at)->format('d M Y, h:i A') ?? '-' }}
                            </div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Contract Period</div>
                            <div class="mt-2 font-semibold" style="color:#2A0709;">
                                {{ optional($booking->contract_start_date)->format('d M Y') ?? '-' }}
                            </div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">
                                to {{ optional($booking->contract_end_date)->format('d M Y') ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('landlord.disputes.create', $booking->id) }}"
                           class="inline-flex items-center justify-center rounded-2xl px-5 py-3 text-sm font-extrabold sr-action-danger">
                            Open Dispute
                        </a>
                    </div>
                </div>
            @empty
                <div class="sr-empty rounded-[24px] px-6 py-12 text-center" style="color:#7E6E73;">
                    <div class="text-4xl mb-3">💳</div>
                    <div class="font-extrabold text-lg" style="color:#5E5558;">No payment submissions yet.</div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ACTIVE / PAID --}}
    <section class="sr-section rounded-[30px]">
        <div class="sr-section-head px-6 py-5 md:px-8">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-black tracking-wider"
                         style="background:#F4F8F5; color:#5F7E68; border:1px solid #D5E4D8;">
                        CONFIRMED TENANTS
                    </div>
                    <h2 class="text-2xl font-extrabold mt-3" style="color:#2A0709;">Active / Paid Bookings</h2>
                    <p class="mt-1 text-sm" style="color:#7E6E73;">
                        These bookings are confirmed after first payment approval.
                    </p>
                </div>

                <div class="inline-flex w-fit items-center rounded-full px-4 py-2 text-sm font-bold"
                     style="background:#F4F8F5; color:#5F7E68; border:1px solid #D5E4D8;">
                    {{ $paidBookings->count() }} active
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-5">
            @forelse($paidBookings as $booking)
                @php
                    $status = bookingStatusConfig($booking->status);
                    $latestPayment = $booking->payments->sortByDesc('created_at')->first();
                @endphp

                <div class="sr-card rounded-[28px] p-5 md:p-6">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="h-14 w-14 rounded-2xl grid place-items-center text-2xl sr-student-chip">👤</div>
                            <div>
                                <div class="text-xl font-bold" style="color:#2A0709;">{{ $booking->student->name ?? '-' }}</div>
                                <div class="mt-1 text-sm" style="color:#7E6E73;">{{ $booking->student->email ?? '-' }}</div>
                            </div>
                        </div>

                        <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold"
                              style="{{ $status['badge'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Room</div>
                            <div class="mt-2 font-bold" style="color:#2A0709;">{{ $booking->room->title ?? 'Room' }}</div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">{{ $booking->room->room_type ?? '-' }}</div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Contract Period</div>
                            <div class="mt-2 font-semibold" style="color:#2A0709;">
                                {{ optional($booking->contract_start_date)->format('d M Y') ?? '-' }}
                            </div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">
                                to {{ optional($booking->contract_end_date)->format('d M Y') ?? '-' }}
                            </div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Amount Details</div>
                            <div class="mt-2 text-sm" style="color:#5E5558;">Deposit: RM {{ number_format((float)$booking->deposit_amount, 2) }}</div>
                            <div class="mt-1 text-sm" style="color:#5E5558;">Monthly Rent: RM {{ number_format((float)$booking->monthly_rent, 2) }}</div>
                            <div class="mt-1 text-sm font-bold" style="color:#2A0709;">Total: RM {{ number_format((float)$booking->total_due, 2) }}</div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Latest Payment</div>
                            <div class="mt-2 font-semibold" style="color:#2A0709;">{{ $latestPayment?->method ?? '-' }}</div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">
                                {{ optional($latestPayment?->paid_at)->format('d M Y, h:i A') ?? 'No paid time' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('landlord.disputes.create', $booking->id) }}"
                           class="inline-flex items-center justify-center rounded-2xl px-5 py-3 text-sm font-extrabold sr-action-danger">
                            Open Dispute
                        </a>
                    </div>
                </div>
            @empty
                <div class="sr-empty rounded-[24px] px-6 py-12 text-center" style="color:#7E6E73;">
                    <div class="text-4xl mb-3">✅</div>
                    <div class="font-extrabold text-lg" style="color:#5E5558;">No active bookings yet.</div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- CANCEL / REFUND REQUESTS --}}
    <section class="sr-section rounded-[30px]">
        <div class="sr-section-head px-6 py-5 md:px-8">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-black tracking-wider"
                         style="background:#FFF7F2; color:#9A6040; border:1px solid #EBCDBB;">
                        REVIEW CAREFULLY
                    </div>
                    <h2 class="text-2xl font-extrabold mt-3" style="color:#2A0709;">Cancel / Refund Requests</h2>
                    <p class="mt-1 text-sm" style="color:#7E6E73;">
                        Students who requested cancellation or refund after payment stage.
                    </p>
                </div>

                <div class="inline-flex w-fit items-center rounded-full px-4 py-2 text-sm font-bold"
                     style="background:#FFF7F2; color:#9A6040; border:1px solid #EBCDBB;">
                    {{ $cancelRequestedBookings->count() }} request(s)
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-5">
            @forelse($cancelRequestedBookings as $booking)
                @php $status = bookingStatusConfig($booking->status); @endphp

                <div class="sr-card rounded-[28px] p-5 md:p-6">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="h-14 w-14 rounded-2xl grid place-items-center text-2xl sr-student-chip">👤</div>
                            <div>
                                <div class="text-xl font-bold" style="color:#2A0709;">{{ $booking->student->name ?? '-' }}</div>
                                <div class="mt-1 text-sm" style="color:#7E6E73;">{{ $booking->student->email ?? '-' }}</div>
                            </div>
                        </div>

                        <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold"
                              style="{{ $status['badge'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Room</div>
                            <div class="mt-2 font-bold" style="color:#2A0709;">{{ $booking->room->title ?? 'Room' }}</div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">{{ $booking->room->room_type ?? '-' }}</div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Requested At</div>
                            <div class="mt-2 font-semibold" style="color:#2A0709;">
                                {{ optional($booking->cancel_requested_at)->format('d M Y') ?? '-' }}
                            </div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">
                                {{ optional($booking->cancel_requested_at)->format('h:i A') ?? '-' }}
                            </div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Reason</div>
                            <div class="mt-2 text-sm" style="color:#5E5558;">
                                {{ $booking->cancel_request_reason ?: 'No reason provided.' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('landlord.disputes.create', $booking->id) }}"
                           class="inline-flex items-center justify-center rounded-2xl px-5 py-3 text-sm font-extrabold sr-action-danger">
                            Open Dispute
                        </a>
                    </div>
                </div>
            @empty
                <div class="sr-empty rounded-[24px] px-6 py-12 text-center" style="color:#7E6E73;">
                    <div class="text-4xl mb-3">📩</div>
                    <div class="font-extrabold text-lg" style="color:#5E5558;">No cancel or refund requests.</div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- CANCELLED --}}
    <section class="sr-section rounded-[30px]">
        <div class="sr-section-head px-6 py-5 md:px-8">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-black tracking-wider"
                         style="background:#FBF4F4; color:#9C5F66; border:1px solid #E9D1D5;">
                        HISTORY
                    </div>
                    <h2 class="text-2xl font-extrabold mt-3" style="color:#2A0709;">Cancelled Bookings</h2>
                    <p class="mt-1 text-sm" style="color:#7E6E73;">
                        Booking history that has already been cancelled.
                    </p>
                </div>

                <div class="inline-flex w-fit items-center rounded-full px-4 py-2 text-sm font-bold"
                     style="background:#FBF4F4; color:#9C5F66; border:1px solid #E9D1D5;">
                    {{ $cancelledBookings->count() }} cancelled
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-5">
            @forelse($cancelledBookings as $booking)
                @php $status = bookingStatusConfig($booking->status); @endphp

                <div class="sr-card rounded-[28px] p-5 md:p-6">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="h-14 w-14 rounded-2xl grid place-items-center text-2xl sr-student-chip">👤</div>
                            <div>
                                <div class="text-xl font-bold" style="color:#2A0709;">{{ $booking->student->name ?? '-' }}</div>
                                <div class="mt-1 text-sm" style="color:#7E6E73;">{{ $booking->student->email ?? '-' }}</div>
                            </div>
                        </div>

                        <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-bold"
                              style="{{ $status['badge'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Room</div>
                            <div class="mt-2 font-bold" style="color:#2A0709;">{{ $booking->room->title ?? 'Room' }}</div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">{{ $booking->room->room_type ?? '-' }}</div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Cancelled At</div>
                            <div class="mt-2 font-semibold" style="color:#2A0709;">
                                {{ optional($booking->cancelled_at)->format('d M Y') ?? '-' }}
                            </div>
                            <div class="mt-1 text-sm" style="color:#7E6E73;">
                                {{ optional($booking->cancelled_at)->format('h:i A') ?? '-' }}
                            </div>
                        </div>

                        <div class="sr-info-box rounded-2xl p-4">
                            <div class="sr-mini-label">Cancelled Reason</div>
                            <div class="mt-2 text-sm" style="color:#5E5558;">
                                {{ $booking->cancelled_reason ?: 'No reason recorded.' }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="sr-empty rounded-[24px] px-6 py-12 text-center" style="color:#7E6E73;">
                    <div class="text-4xl mb-3">🗂️</div>
                    <div class="font-extrabold text-lg" style="color:#5E5558;">No cancelled bookings yet.</div>
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection