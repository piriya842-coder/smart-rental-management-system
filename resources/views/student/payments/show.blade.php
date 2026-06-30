@extends('layouts.student')

@section('title', 'Payment • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $choco   = '#4a2c2a';
  $cream   = '#fffafa';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';

  $status = strtolower($booking->status ?? 'pending');
  $alreadySubmitted = in_array($status, ['payment_submitted', 'paid']);

  $qrImage = asset('images/payments/qr-demo.png');
@endphp

<div class="max-w-6xl mx-auto">

  <div class="mb-4">
    <a href="{{ route('student.bookings.show', $booking->id) }}"
       class="font-semibold hover:opacity-80"
       style="color: {{ $choco }};">
      ← Back to Booking Details
    </a>
  </div>

  @if ($errors->any())
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
      <div class="font-bold mb-1">Please fix:</div>
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- LEFT --}}
    <div class="lg:col-span-7">
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6">

        <div class="text-sm font-bold text-gray-600">Booking</div>

        <div class="mt-1 text-2xl font-extrabold" style="color: {{ $choco }};">
          {{ $booking->room?->title ?? 'Room' }}
        </div>

        <div class="mt-2 text-gray-700">
          Contract:
          {{ optional($booking->contract_start_date)->format('d M Y') }}
          → {{ optional($booking->contract_end_date)->format('d M Y') }}
        </div>

        <div class="mt-5 rounded-2xl border border-[rgba(201,42,42,.10)] p-5" style="background: {{ $softRed }};">
          <div class="text-sm font-bold text-gray-700">Total Due Now</div>

          <div class="mt-2 text-4xl font-extrabold" style="color: {{ $gold }};">
            RM {{ number_format($booking->total_due ?? 0, 0) }}
          </div>

          <div class="mt-2 text-sm text-gray-700">
            Deposit RM {{ number_format($booking->deposit_amount ?? 100, 0) }}
            + 1 month rent RM {{ number_format($booking->monthly_rent ?? 0, 0) }}
          </div>
        </div>

      </div>
    </div>

    {{-- RIGHT --}}
    <div class="lg:col-span-5">
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6 space-y-5">

        <div class="text-sm text-gray-700">
          Scan QR using your banking app and upload the receipt to complete payment.
        </div>

        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5 bg-white">
          <div class="text-sm font-bold text-gray-600">Pay to</div>

          <div class="mt-1 text-xl font-extrabold" style="color: {{ $choco }};">
            Smart Rental - Landlord
          </div>

          <div class="text-sm text-gray-600">Maybank / DuitNow QR</div>

          <div class="mt-4 rounded-2xl border border-[rgba(201,42,42,.10)] p-4 bg-white flex items-center justify-center">
            <img src="{{ $qrImage }}"
                 alt="DuitNow QR"
                 class="max-w-[220px] w-full h-auto"
                 onerror="this.onerror=null; this.style.display='none';">
          </div>

          <div class="mt-3 text-center text-sm text-gray-700">
            Amount:
            <span class="font-extrabold">
              RM {{ number_format($booking->total_due ?? 0, 0) }}
            </span>
          </div>
        </div>

        @if($alreadySubmitted)
          <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            Receipt already submitted ✅ Waiting verification.
          </div>

          <a href="{{ route('student.bookings.index') }}"
             class="rounded-2xl px-5 py-3 text-sm font-extrabold text-white text-center transition"
             style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);"
             onmouseover="this.style.filter='brightness(0.92)'"
             onmouseout="this.style.filter='brightness(1)'">
            Back to My Bookings
          </a>
        @else
          <form method="POST"
                action="{{ route('student.payments.upload', $booking->id) }}"
                enctype="multipart/form-data"
                class="space-y-3">
            @csrf

            <input type="hidden" name="method" value="qr">

            <div class="text-lg font-extrabold" style="color: {{ $choco }};">
              Upload Receipt
            </div>

            <input type="file"
                   name="receipt"
                   required
                   class="w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-white px-4 py-3">

            <button type="submit"
                    class="w-full rounded-2xl px-6 py-3 text-sm font-extrabold text-white shadow-sm transition"
                    style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);"
                    onmouseover="this.style.filter='brightness(0.92)'"
                    onmouseout="this.style.filter='brightness(1)'">
              Upload Receipt (Submit Payment)
            </button>

            <div class="text-xs text-gray-600">
              After upload, status becomes Payment Submitted and waits landlord verification.
            </div>
          </form>
        @endif

      </div>
    </div>

  </div>
</div>
@endsection