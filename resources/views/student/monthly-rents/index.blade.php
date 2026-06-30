@extends('layouts.student')

@section('title', 'Monthly Rent • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $choco   = '#4a2c2a';
  $cream   = '#fffafa';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';
@endphp

<div class="max-w-7xl mx-auto">

  @if(session('success'))
    <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 font-semibold shadow-sm">
      ✅ {{ session('success') }}
    </div>
  @endif

  @php
    $allPaid = $monthlyRents->count() > 0 && $monthlyRents->where('status', 'paid')->count() === $monthlyRents->count();
  @endphp

  @if($allPaid)
    <div class="mb-6 overflow-hidden rounded-[30px] border border-[rgba(201,42,42,.10)] bg-gradient-to-br from-[#c92a2a] to-[#a61e1e] px-8 py-10 text-white shadow-[0_14px_40px_rgba(0,0,0,0.12)]">
      <div class="text-center">
        <div class="text-5xl mb-3">🎉</div>
        <div class="text-xl md:text-2xl font-bold opacity-95">Yay, you've cleared all bills!</div>
        <div class="mt-2 text-white/90">All monthly rental payments have been completed successfully.</div>
      </div>
    </div>
  @endif

  <div class="overflow-hidden rounded-[30px] border border-[rgba(201,42,42,.08)] bg-white/95 shadow-[0_14px_40px_rgba(0,0,0,0.08)]">
    <div class="border-b border-[rgba(201,42,42,.08)] bg-gradient-to-br from-white via-white to-[#fffafa] px-7 py-8 md:px-10">
      <div class="text-[11px] uppercase tracking-[0.2em] font-black text-black/45">Smart Billing</div>
      <h1 class="mt-2 text-4xl font-extrabold" style="color: {{ $choco }};">Monthly Rent</h1>
      <p class="mt-3 text-[16px] leading-8 text-black/60">
        Track your monthly rent schedule, due dates, and payment submissions.
      </p>
    </div>

    <div class="border-b border-[rgba(201,42,42,.08)] bg-[#fffafa] px-7 py-7 md:px-10">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-white p-5 shadow-sm">
          <div class="text-xs uppercase tracking-[0.14em] font-black text-black/40">Paid</div>
          <div class="mt-3 text-3xl font-extrabold text-green-600">{{ $paidCount }}</div>
        </div>

        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-white p-5 shadow-sm">
          <div class="text-xs uppercase tracking-[0.14em] font-black text-black/40">Submitted</div>
          <div class="mt-3 text-3xl font-extrabold text-blue-600">{{ $submittedCount }}</div>
        </div>

        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-white p-5 shadow-sm">
          <div class="text-xs uppercase tracking-[0.14em] font-black text-black/40">Overdue</div>
          <div class="mt-3 text-3xl font-extrabold text-red-600">{{ $overdueCount }}</div>
        </div>

        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] bg-white p-5 shadow-sm">
          <div class="text-xs uppercase tracking-[0.14em] font-black text-black/40">Remaining</div>
          <div class="mt-3 text-3xl font-extrabold" style="color: {{ $choco }};">{{ $remainingCount }}</div>
        </div>
      </div>
    </div>

    <div class="px-7 py-7 md:px-10">
      @if($monthlyRents->count() === 0)
        <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] p-8 text-center">
          <div class="text-5xl">📅</div>
          <div class="mt-3 text-xl font-extrabold" style="color: {{ $choco }};">No monthly rent schedule yet</div>
          <div class="mt-2 text-black/60">Monthly bills will appear after your contract is signed.</div>
        </div>
      @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
          @foreach($monthlyRents as $rent)
            @php
              $status = strtolower($rent->status);

              $bg = 'bg-white';
              $badge = 'bg-gray-100 text-gray-700';
              $border = 'border-[rgba(201,42,42,.10)]';

              if ($status === 'paid') {
                $bg = 'bg-green-50';
                $badge = 'bg-green-100 text-green-700';
                $border = 'border-green-200';
              } elseif ($status === 'submitted') {
                $bg = 'bg-blue-50';
                $badge = 'bg-blue-100 text-blue-700';
                $border = 'border-blue-200';
              } elseif ($status === 'overdue') {
                $bg = 'bg-red-50';
                $badge = 'bg-red-100 text-red-700';
                $border = 'border-red-200';
              } elseif ($status === 'due_soon') {
                $bg = 'bg-yellow-50';
                $badge = 'bg-yellow-100 text-yellow-700';
                $border = 'border-yellow-200';
              }
            @endphp

            <div class="rounded-[26px] border {{ $border }} {{ $bg }} p-6 shadow-sm">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <div class="text-xs uppercase tracking-[0.14em] font-black text-black/40">Rent Month</div>
                  <div class="mt-2 text-2xl font-extrabold" style="color: {{ $choco }};">
                    {{ $rent->month_label }}
                  </div>
                </div>

                <span class="inline-flex rounded-full px-4 py-2 text-xs font-extrabold {{ $badge }}">
                  {{ str_replace('_', ' ', ucfirst($status)) }}
                </span>
              </div>

              <div class="mt-5 space-y-2 text-black/70">
                <div><span class="font-bold">Amount:</span> RM {{ number_format((float)$rent->amount, 2) }}</div>
                <div><span class="font-bold">Due Date:</span> {{ optional($rent->due_date)->format('d M Y') }}</div>
              </div>

              @if($status === 'paid')
                <div class="mt-4 rounded-2xl bg-green-100 px-4 py-3 text-sm font-bold text-green-700">
                  ✅ Paid successfully
                </div>
              @elseif($status === 'submitted')
                <div class="mt-4 rounded-2xl bg-blue-100 px-4 py-3 text-sm font-bold text-blue-700">
                  ⏳ Receipt submitted, waiting verification
                </div>
              @endif

              <div class="mt-5">
                <a href="{{ route('student.monthly-rents.show', $rent) }}"
                   class="inline-flex items-center justify-center rounded-2xl px-5 py-3 font-extrabold text-white shadow-sm hover:brightness-95 transition"
                   style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
                  View Payment
                </a>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>
@endsection