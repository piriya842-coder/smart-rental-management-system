@extends('layouts.student')

@section('title', 'My Contracts • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $cream   = '#fffafa';
  $choco   = '#4a2c2a';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';
@endphp

<div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm overflow-hidden">

  <div class="p-6 md:p-8 border-b border-[rgba(201,42,42,.08)] bg-gradient-to-r from-white via-white to-[#fffafa]">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
      <div>
        <div class="text-xs font-black tracking-wider text-black/50">DOCUMENTS</div>
        <h1 class="text-3xl md:text-4xl font-extrabold mt-1" style="color: {{ $choco }};">
          My Contracts
        </h1>
        <p class="mt-2 text-black/60">
          Review, sign, and download your rental agreements.
        </p>
      </div>
    </div>
  </div>

  <div class="p-6 md:p-8">

    @if(session('success'))
      <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 font-semibold">
        ✅ {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-semibold">
        ❌ {{ session('error') }}
      </div>
    @endif

    @if($contracts->count() === 0)
      <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] p-8 text-center">
        <div class="text-5xl">📄</div>

        <div class="mt-3 text-xl font-extrabold" style="color: {{ $choco }};">
          No contracts available
        </div>

        <div class="mt-2 text-black/60">
          Your rental contracts will appear here after booking/payment is completed.
        </div>
      </div>
    @else

      <div class="space-y-4">
        @foreach($contracts as $contract)
          @php
            $contractNo = $contract->contract_no ?? ('CTR-' . str_pad($contract->id, 6, '0', STR_PAD_LEFT));
          @endphp

          <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white p-6">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

              <div>
                <div class="text-sm font-black tracking-wider text-black/50">CONTRACT NO</div>

                <div class="text-xl font-extrabold" style="color: {{ $choco }};">
                  {{ $contractNo }}
                </div>

                <div class="mt-2 text-black/70">
                  <span class="font-semibold">Room:</span>
                  {{ $contract->room_title ?? '-' }}
                </div>

                <div class="mt-1 text-black/70">
                  <span class="font-semibold">Rental Period:</span>
                  {{ optional($contract->start_date)->format('d M Y') ?? '-' }}
                  -
                  {{ optional($contract->end_date)->format('d M Y') ?? '-' }}
                </div>

                <div class="mt-1 text-black/70">
                  <span class="font-semibold">Monthly Rent:</span>
                  RM {{ number_format((float)$contract->monthly_rent, 2) }}
                </div>
              </div>

              <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">

                @if($contract->is_signed)
                  <span class="inline-flex rounded-full px-4 py-2 text-xs font-extrabold bg-green-100 text-green-700">
                    Signed
                  </span>
                @else
                  <span class="inline-flex rounded-full px-4 py-2 text-xs font-extrabold bg-yellow-100 text-yellow-700">
                    Pending Signature
                  </span>
                @endif

                <a href="{{ route('student.contracts.show', $contract) }}"
                   class="rounded-2xl px-5 py-3 font-extrabold text-white shadow-sm transition"
                   style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);"
                   onmouseover="this.style.filter='brightness(0.92)'"
                   onmouseout="this.style.filter='brightness(1)'">
                  Open Contract
                </a>

                @if($contract->is_signed)
                  <a href="{{ route('student.contracts.pdf', $contract) }}"
                     class="rounded-2xl px-5 py-3 font-extrabold border border-[rgba(201,42,42,.10)] hover:bg-[#fffafa] transition">
                    Download PDF
                  </a>
                @endif

              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-6">
        {{ $contracts->links() }}
      </div>

    @endif
  </div>
</div>
@endsection