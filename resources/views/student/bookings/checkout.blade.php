@extends('layouts.student')

@section('title', 'Booking Checkout • Smart Rental')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;

  $gold    = '#c92a2a';
  $choco   = '#4a2c2a';
  $cream   = '#fffafa';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';

  $heroFallback = asset('images/slider/slide1.jpg');

  // image
  $img = $heroFallback;
  if (!empty($room->cover_image)) {
    $img = str_starts_with($room->cover_image, 'http') ? $room->cover_image : Storage::url($room->cover_image);
  } elseif (isset($room->images) && $room->images && count($room->images)) {
    $first = $room->images[0];
    if (!empty($first->path)) $img = Storage::url($first->path);
  }

  $title = $room->title ?? 'Room';
  $loc = trim(implode(', ', array_filter([$room->city ?? null, $room->state ?? null]))) ?: 'Near MSU';
@endphp

<div class="max-w-6xl mx-auto">

  <div class="mb-4 flex items-center justify-between gap-3">
    <a href="{{ route('student.rooms.show', $room->id) }}"
       class="font-semibold hover:opacity-80"
       style="color: {{ $choco }};">
      ← Back to Details
    </a>
    <div class="text-xs font-bold text-gray-500">Checkout • Booking & Payment</div>
  </div>

  @if(session('error'))
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
      {{ session('error') }}
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- LEFT --}}
    <div class="lg:col-span-7 space-y-6">
      <div class="rounded-3xl overflow-hidden border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm">

        <div class="h-[240px] bg-cover bg-center" style="background-image:url('{{ $img }}')"></div>

        <div class="p-6">
          <div class="text-xs font-extrabold text-gray-500">Booking for</div>

          <div class="mt-1 text-2xl font-extrabold" style="color: {{ $choco }};">
            {{ $title }}
          </div>

          <div class="mt-3 text-sm text-gray-700 flex flex-wrap items-center gap-2">
            <span>📍 {{ $loc }}</span>
            <span class="opacity-50">•</span>
            <span>🏠 {{ ucwords(str_replace('_',' ', $room->room_type ?? 'Room')) }}</span>
            <span class="opacity-50">•</span>
            <span>👥 {{ ucwords($room->gender_preference ?? 'Any') }}</span>
          </div>

          <div class="mt-4 rounded-2xl p-4 border border-[rgba(201,42,42,.10)]" style="background: {{ $softRed }};">
            <div class="text-xs font-bold text-gray-600">Payment breakdown (due now)</div>

            <div class="mt-3 space-y-2 text-sm">
              <div class="flex items-center justify-between">
                <span class="font-semibold text-gray-700">Deposit</span>
                <span class="font-extrabold" style="color: {{ $choco }};">RM {{ number_format($deposit,0) }}</span>
              </div>

              <div class="flex items-center justify-between">
                <span class="font-semibold text-gray-700">1 month rent</span>
                <span class="font-extrabold" style="color: {{ $choco }};">RM {{ number_format($rent,0) }}</span>
              </div>

              <div class="pt-2 mt-2 border-t border-[rgba(201,42,42,.10)] flex items-center justify-between">
                <span class="font-extrabold text-gray-800">Total</span>
                <span class="font-extrabold text-lg" style="color: {{ $gold }};">
                  RM {{ number_format($total,0) }}
                </span>
              </div>
            </div>

            <div class="mt-2 text-xs text-gray-600">
              Deposit is refundable at contract end (if no damage/unpaid dues).
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- RIGHT --}}
    <div class="lg:col-span-5">
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6 lg:sticky lg:top-6">

        <div class="text-xl font-extrabold" style="color: {{ $choco }};">
          Contract Dates (Required)
        </div>

        <p class="mt-2 text-sm text-gray-600">
          Choose your rental contract period. These dates will appear in your Contract & Booking History.
        </p>

        <form method="POST" action="{{ route('student.bookings.store', $room->id) }}" class="mt-5 space-y-4">
          @csrf

          <div>
            <label class="text-sm font-semibold text-gray-700">Start Date</label>
            <input type="date" name="contract_start_date" value="{{ old('contract_start_date') }}"
                   class="mt-2 w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]">
            @error('contract_start_date')
              <div class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</div>
            @enderror
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700">End Date</label>
            <input type="date" name="contract_end_date" value="{{ old('contract_end_date') }}"
                   class="mt-2 w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]">
            @error('contract_end_date')
              <div class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</div>
            @enderror
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700">Note to Landlord (optional)</label>
            <textarea name="student_note" rows="3"
                      class="mt-2 w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]"
                      placeholder="Example: I prefer to move in after 5pm.">{{ old('student_note') }}</textarea>
            @error('student_note')
              <div class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit"
                  class="w-full rounded-xl px-5 py-3 text-sm font-extrabold text-white shadow-sm transition"
                  style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);"
                  onmouseover="this.style.filter='brightness(0.92)'"
                  onmouseout="this.style.filter='brightness(1)'">
            Create Booking & Continue to Payment
          </button>

          <div class="text-xs text-gray-500">
            Status after this: <span class="font-bold">Pending</span> → you’ll upload payment proof next.
          </div>
        </form>

      </div>
    </div>

  </div>
</div>
@endsection