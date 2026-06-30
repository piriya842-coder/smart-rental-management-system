@extends('layouts.student')

@section('title', 'Transaction History • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $choco   = '#4a2c2a';
  $cream   = '#fffafa';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';
@endphp

<div class="max-w-6xl mx-auto">
  <h1 class="text-2xl sm:text-3xl font-extrabold" style="color: {{ $choco }};">
    Transaction History
  </h1>

  <p class="mt-1 text-gray-600">All payment attempts and receipts.</p>

  <div class="mt-6 space-y-4">
    @forelse($payments as $p)
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6">

        <div class="flex items-start justify-between gap-3">
          <div>
            <div class="text-lg font-extrabold" style="color: {{ $choco }};">
              RM {{ number_format((float)$p->amount, 0) }}
            </div>

            <div class="text-sm text-gray-600">
              Booking #{{ $p->booking_id }} • {{ strtoupper($p->method) }} • {{ strtoupper($p->status) }}
            </div>

            <div class="text-xs text-gray-500 mt-1">
              {{ $p->created_at->format('d M Y, h:i A') }}
            </div>
          </div>

          <a href="{{ route('student.payments.show', $p->booking_id) }}"
             class="rounded-xl px-4 py-2 text-sm font-extrabold text-white shadow-sm transition"
             style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);"
             onmouseover="this.style.filter='brightness(0.92)'"
             onmouseout="this.style.filter='brightness(1)'">
            View
          </a>
        </div>

      </div>
    @empty
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-8">
        <div class="text-lg font-extrabold" style="color: {{ $choco }};">
          No transactions yet
        </div>

        <p class="mt-2 text-gray-700">
          Once you upload a receipt, it will appear here.
        </p>
      </div>
    @endforelse
  </div>

  <div class="mt-8">
    {{ $payments->links() }}
  </div>
</div>
@endsection