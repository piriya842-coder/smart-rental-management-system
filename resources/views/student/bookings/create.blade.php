@extends('layouts.student')

@section('title', 'Booking Checkout • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $choco   = '#4a2c2a';
  $cream   = '#fffafa';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';

  $deposit = 100;
  $price = is_numeric($room->price_monthly ?? null) ? (float)$room->price_monthly : 0;
  $total = $deposit + $price;

  $roomTitle = $room->title ?? 'Room near MSU';
@endphp

<div class="max-w-5xl mx-auto">

  <div class="mb-4">
    <a href="{{ route('student.rooms.show', $room->id) }}"
       class="font-semibold hover:opacity-80"
       style="color: {{ $choco }};">
      ← Back to Room Details
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- LEFT --}}
    <div class="lg:col-span-7 space-y-6">

      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6">
        <div class="text-xl font-extrabold" style="color: {{ $choco }};">
          Booking Checkout
        </div>
        <div class="mt-1 text-sm text-gray-600">
          Choose your start date. Contract end date will be auto set to 1 year.
        </div>

        @if ($errors->any())
          <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <div class="font-bold mb-1">Please fix:</div>
            <ul class="list-disc ml-5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('student.bookings.store', $room->id) }}" class="mt-6 space-y-5">
          @csrf

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div>
              <label class="text-sm font-bold text-gray-700">Contract Start Date *</label>
              <input type="date"
                     id="start_date"
                     name="start_date"
                     value="{{ old('start_date') }}"
                     class="mt-2 w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]"
                     required>
              <div class="mt-2 text-xs text-gray-600">
                Start date must be today or later.
              </div>
            </div>

            <div>
              <label class="text-sm font-bold text-gray-700">Contract End Date (Auto 1 year)</label>
              <input type="text"
                     id="end_date_preview"
                     value="—"
                     readonly
                     class="mt-2 w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-gray-100 px-4 py-3 text-gray-700 cursor-not-allowed">
              <div class="mt-2 text-xs text-gray-600">
                End date is automatically set by system. Student cannot change.
              </div>
            </div>

          </div>

          <div>
            <label class="text-sm font-bold text-gray-700">Note to Landlord (optional)</label>
            <textarea name="note_to_landlord"
                      rows="4"
                      maxlength="500"
                      placeholder="Example: I prefer move-in after 4pm. Can I view the room this weekend?"
                      class="mt-2 w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]">{{ old('note_to_landlord') }}</textarea>
            <div class="mt-2 text-xs text-gray-600">Max 500 characters.</div>
          </div>

          <button type="submit"
                  class="w-full rounded-2xl px-6 py-3 text-sm font-extrabold text-white shadow-sm transition"
                  style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);"
                  onmouseover="this.style.filter='brightness(0.92)'"
                  onmouseout="this.style.filter='brightness(1)'">
            Confirm Booking (Next: Payment)
          </button>

        </form>
      </div>

      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6">
        <div class="text-base font-extrabold" style="color: {{ $choco }};">Contract Rule</div>
        <div class="mt-2 text-sm text-gray-700 leading-relaxed">
          This system uses a standard <span class="font-bold">1-year contract</span>.
          After 1 year, student can request renewal or end the contract (future module).
        </div>
      </div>

    </div>

    {{-- RIGHT --}}
    <div class="lg:col-span-5">
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6 lg:sticky lg:top-6 space-y-5">

        <div>
          <div class="text-sm font-bold text-gray-600">Room</div>
          <div class="mt-1 text-lg font-extrabold" style="color: {{ $choco }};">
            {{ $roomTitle }}
          </div>
          <div class="text-sm text-gray-600">
            Landlord: {{ $room->landlord?->name ?? 'Landlord' }}
          </div>
        </div>

        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5 bg-white">
          <div class="text-base font-extrabold" style="color: {{ $choco }};">Payment Summary</div>

          <div class="mt-3 space-y-2 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-gray-700 font-semibold">Deposit</span>
              <span class="font-extrabold" style="color: {{ $choco }};">RM {{ number_format($deposit, 0) }}</span>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-gray-700 font-semibold">1 month rent</span>
              <span class="font-extrabold" style="color: {{ $choco }};">RM {{ number_format($price, 0) }}</span>
            </div>

            <div class="pt-3 mt-3 border-t border-[rgba(201,42,42,.10)] flex items-center justify-between">
              <span class="font-extrabold text-gray-800">Total due now</span>
              <span class="font-extrabold" style="color: {{ $gold }};">RM {{ number_format($total, 0) }}</span>
            </div>

            <div class="text-xs text-gray-600 mt-2">
              Payment module is next (QR / FPX option).
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<script>
  function pad2(n){ return n.toString().padStart(2,'0'); }

  document.getElementById('start_date').addEventListener('change', function () {
    const v = this.value;
    const out = document.getElementById('end_date_preview');

    if (!v) { out.value = '—'; return; }

    const d = new Date(v + 'T00:00:00');
    if (isNaN(d.getTime())) { out.value = '—'; return; }

    const end = new Date(d);
    end.setFullYear(end.getFullYear() + 1);
    end.setDate(end.getDate() - 1);

    out.value = `${end.getFullYear()}-${pad2(end.getMonth()+1)}-${pad2(end.getDate())}`;
  });
</script>
@endsection