@extends('layouts.student')

@section('title', 'Monthly Rent Payment • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $choco   = '#4a2c2a';
  $cream   = '#fffafa';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';

  $qrImage = asset('images/payments/qr-demo.png');
  $status = strtolower($monthlyRent->status);
  $alreadySubmitted = in_array($status, ['submitted', 'paid']);
@endphp

<div class="max-w-6xl mx-auto">

  <div class="mb-4">
    <a href="{{ route('student.monthly-rents.index') }}"
       class="font-semibold hover:opacity-80"
       style="color: {{ $choco }};">
      ← Back to Monthly Rent
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

        <div class="text-sm font-bold text-gray-600">Monthly Rent</div>

        <div class="mt-1 text-3xl font-extrabold" style="color: {{ $choco }};">
          {{ $monthlyRent->month_label }}
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

          <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5" style="background: {{ $softRed }};">
            <div class="text-sm font-bold text-gray-700">Amount Due</div>

            <div class="mt-2 text-4xl font-extrabold" style="color: {{ $gold }};">
              RM {{ number_format((float)$monthlyRent->amount, 2) }}
            </div>
          </div>

          <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5" style="background: {{ $softRed }};">
            <div class="text-sm font-bold text-gray-700">Due Date</div>

            <div class="mt-2 text-2xl font-extrabold" style="color: {{ $choco }};">
              {{ optional($monthlyRent->due_date)->format('d M Y') }}
            </div>
          </div>

        </div>

        <div class="mt-5 rounded-2xl border border-[rgba(201,42,42,.10)] p-5 bg-white">
          <div class="text-sm font-bold text-gray-600">Contract</div>

          <div class="mt-1 text-lg font-extrabold" style="color: {{ $choco }};">
            {{ $monthlyRent->contract->room_title ?? 'Room' }}
          </div>

          <div class="mt-2 text-gray-700">
            Rental Period:
            {{ optional($monthlyRent->contract->start_date)->format('d M Y') }}
            →
            {{ optional($monthlyRent->contract->end_date)->format('d M Y') }}
          </div>
        </div>

      </div>
    </div>

    {{-- RIGHT --}}
    <div class="lg:col-span-5">
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6 space-y-5">

        <div class="text-sm text-gray-700">
          Scan QR using your banking app and upload your receipt for {{ $monthlyRent->month_label }} rental payment.
        </div>

        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5 bg-white">
          <div class="text-sm font-bold text-gray-600">Pay to</div>

          <div class="mt-1 text-xl font-extrabold" style="color: {{ $choco }};">
            Smart Rental - Monthly Rent
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
              RM {{ number_format((float)$monthlyRent->amount, 2) }}
            </span>
          </div>
        </div>

        {{-- STATUS --}}
        @if($status === 'paid')
          <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 font-bold">
            ✅ This month has been fully paid.
          </div>

        @elseif($alreadySubmitted)
          <div class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 font-bold">
            ⏳ Receipt already submitted. Waiting verification.
          </div>

        @else
          <form method="POST"
                action="{{ route('student.monthly-rents.upload', $monthlyRent) }}"
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
              Submit {{ $monthlyRent->month_label }} Payment
            </button>

            <div class="text-xs text-gray-600">
              After upload, this month will become Payment Submitted and wait for verification.
            </div>
          </form>
        @endif

      </div>
    </div>

  </div>
</div>
@endsection