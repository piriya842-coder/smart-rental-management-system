@extends('layouts.student')

@section('title', 'My Bookings • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $choco   = '#4a2c2a';
  $cream   = '#fffafa';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';

  function s($x){ return strtolower((string)$x); }
@endphp

<div class="max-w-6xl mx-auto">

  <div class="mb-4 flex items-center justify-between">
    <a href="{{ route('student.rooms.index') }}" class="font-semibold hover:opacity-80" style="color: {{ $choco }};">
      ← Back to Rooms
    </a>
    <div class="text-lg font-extrabold" style="color: {{ $choco }};">My Bookings</div>
  </div>

  @if(session('success'))
    <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-semibold">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 font-semibold">
      {{ session('error') }}
    </div>
  @endif

  <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/85 shadow-sm p-5 mb-6">
    <div class="font-extrabold" style="color: {{ $choco }};">Booking rule:</div>
    <div class="text-sm text-gray-700 mt-1">
      One student can have <span class="font-bold">only one active booking</span> at a time
      (Pending / Payment Submitted / Paid / Cancel Requested).
    </div>
  </div>

  @if($bookings->count() === 0)
    <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-8 text-center">
      <div class="text-xl font-extrabold" style="color: {{ $choco }};">No bookings yet</div>
      <div class="text-gray-600 mt-2">Go to Rooms and book a room to see it here.</div>
      <a href="{{ route('student.rooms.index') }}"
         class="inline-flex mt-5 rounded-2xl px-6 py-3 text-sm font-extrabold text-white shadow-sm"
         style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
        Browse Rooms
      </a>
    </div>
  @else

    <div class="space-y-6">
      @foreach($bookings as $b)
        @php
          $status = s($b->status);
          $roomTitle = $b->room?->title ?? 'Room';
          $landlordName = $b->landlord?->name ?? 'Landlord';

          $badgeText = strtoupper($status);
          $badgeBg = match($status) {
            'pending' => '#444',
            'payment_submitted' => $gold,
            'paid' => '#1B9A59',
            'cancel_requested' => '#9B59B6',
            'cancelled' => '#777',
            default => '#555'
          };
        @endphp

        <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-2xl font-extrabold" style="color: {{ $choco }};">
                {{ $roomTitle }}
              </div>

              <div class="mt-2 text-sm text-gray-700">
                <div><span class="font-extrabold">Landlord:</span> {{ $landlordName }}</div>
                <div class="mt-1">
                  <span class="font-extrabold">Contract:</span>
                  {{ optional($b->contract_start_date)->format('d M Y') }}
                  → {{ optional($b->contract_end_date)->format('d M Y') }}
                </div>
                <div class="mt-1">
                  <span class="font-extrabold">Total Due:</span>
                  <span class="font-extrabold" style="color: {{ $gold }};">RM {{ number_format((float)$b->total_due, 0) }}</span>
                </div>
              </div>
            </div>

            <div class="shrink-0">
              <span class="inline-flex items-center rounded-full px-4 py-2 text-xs font-extrabold text-white"
                    style="background: {{ $badgeBg }};">
                {{ $badgeText }}
              </span>
            </div>
          </div>

          {{-- Buttons --}}
          <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
            {{-- View --}}
            <a href="{{ route('student.bookings.show', $b->id) }}"
               class="rounded-xl px-4 py-3 text-sm font-extrabold text-center border border-[rgba(201,42,42,.12)] bg-white hover:bg-[#fff5f5] transition"
               style="color: {{ $choco }};">
              View
            </a>

            {{-- Payment (only when pending) --}}
            @if($status === 'pending')
              <a href="{{ route('student.payments.show', $b->id) }}"
                 class="rounded-xl px-4 py-3 text-sm font-extrabold text-center text-white shadow-sm"
                 style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
                Payment
              </a>

              <form method="POST" action="{{ route('student.bookings.cancel', $b->id) }}"
                    onsubmit="return confirm('Cancel this booking?');">
                @csrf
                <button type="submit"
                        class="w-full rounded-xl px-4 py-3 text-sm font-extrabold text-white shadow-sm"
                        style="background: {{ $choco }};">
                  Cancel
                </button>
              </form>
            @endif

            {{-- Request Cancel/Refund (when payment_submitted or paid) --}}
            @if(in_array($status, ['payment_submitted','paid'], true))
              <form method="POST" action="{{ route('student.bookings.request_cancel', $b->id) }}"
                    onsubmit="return confirm('Submit cancel/refund request? Landlord will review.');">
                @csrf
                <input type="hidden" name="reason" value="Student requested cancel/refund after payment. (Can edit later in Booking Details)">
                <button type="submit"
                        class="w-full rounded-xl px-4 py-3 text-sm font-extrabold text-white shadow-sm"
                        style="background: #9B59B6;">
                  Request Cancel/Refund
                </button>
              </form>
            @endif

            {{-- Cancel requested state --}}
            @if($status === 'cancel_requested')
              <div class="sm:col-span-2 rounded-2xl border border-purple-200 bg-purple-50 px-4 py-3 text-sm text-purple-800 font-semibold">
                Cancel/Refund request sent. Waiting landlord verification.
              </div>
            @endif

            {{-- Cancelled state --}}
            @if($status === 'cancelled')
              <div class="sm:col-span-2 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 font-semibold">
                This booking is cancelled.
              </div>
            @endif
          </div>

          {{-- Note --}}
          <div class="mt-5 rounded-2xl border border-[rgba(201,42,42,.10)] p-4" style="background: {{ $softRed }};">
            <div class="font-extrabold text-sm" style="color: {{ $choco }};">Your note:</div>
            <div class="text-sm text-gray-700 mt-1">{{ $b->student_note ?: 'Nothing to tell' }}</div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $bookings->links() }}
    </div>

  @endif

</div>
@endsection