@extends('layouts.admin')

@section('title', 'Reports • Smart Rental')

@section('content')
@php
  // Red admin theme
  $primary = '#7F1D1D';
  $primarySoft = '#9A5E5E';
  $accent = '#DC2626';
  $accentSoft = '#B91C1C';

  $fmtMoney = fn($v) => 'RM ' . number_format((float)$v, 2);

  $isFiltered = (bool)($rangeActive ?? false);
  $fromVal = $from ?? '';
  $toVal   = $to ?? '';
@endphp

<div class="rounded-3xl border border-[#F3CACA] bg-white/90 shadow-lg overflow-hidden">

  <!-- HEADER -->
  <div class="p-6 md:p-8 border-b border-[#F3CACA]"
       style="background: linear-gradient(135deg, #FFF7F7, #FDEEEE);">
    <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-6">

      <div class="max-w-2xl">
        <div class="text-xs font-black tracking-wider text-[#B98B8B]">ANALYTICS</div>

        <div class="flex items-center gap-3 mt-1">
          <h1 class="text-3xl md:text-4xl font-extrabold text-[#7F1D1D]">Reports</h1>

          @if($isFiltered)
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold bg-blue-100 text-blue-800">
              Filtered range
            </span>
          @else
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold border border-[#F3CACA] bg-white text-[#9A5E5E]">
              Live overview
            </span>
          @endif
        </div>

        <p class="mt-2 font-semibold text-[#9A5E5E]">
          Real system monitoring — users, rooms, bookings, payments & disputes with optional date filter.
        </p>

        @if($isFiltered)
          <div class="text-sm text-[#B98B8B] mt-3">
            Showing results <span class="font-extrabold">from</span> {{ $fromVal }}
            <span class="font-extrabold">to</span> {{ $toVal }}.
          </div>
        @endif
      </div>

      <!-- FILTER CARD -->
      <div class="w-full xl:w-[640px]">
        <div class="rounded-3xl border border-[#F3CACA] bg-white p-4 md:p-6 overflow-hidden shadow-sm">
          <form method="GET" class="grid grid-cols-12 gap-3">

            <!-- FROM -->
            <div class="col-span-12 md:col-span-6">
              <div class="text-xs font-black text-[#B98B8B] mb-2">FROM</div>
              <input type="date" name="from" value="{{ $fromVal }}"
                     class="w-full rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] px-4 py-3 font-semibold text-[#4B5563] focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]"/>
            </div>

            <!-- TO -->
            <div class="col-span-12 md:col-span-6">
              <div class="text-xs font-black text-[#B98B8B] mb-2">TO</div>
              <input type="date" name="to" value="{{ $toVal }}"
                     class="w-full rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] px-4 py-3 font-semibold text-[#4B5563] focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]"/>
            </div>

            <!-- BUTTONS -->
            <div class="col-span-12">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1">
                <button type="submit"
                        class="w-full rounded-2xl px-6 py-3 font-extrabold text-white shadow-md hover:scale-[1.01] transition"
                        style="background: linear-gradient(135deg, {{ $accent }}, {{ $accentSoft }});">
                  Apply
                </button>

                <a href="{{ route('admin.reports.index') }}"
                   class="w-full text-center rounded-2xl px-6 py-3 font-extrabold border border-[#F3CACA] bg-white hover:bg-[#FFF3F3] transition text-[#7F1D1D]">
                  Reset
                </a>
              </div>
            </div>

          </form>

          <div class="mt-4 text-xs text-[#B98B8B]">
            Revenue is calculated from <span class="font-extrabold">payments with status “paid”</span>.
            When date filter is used, it filters by <span class="font-extrabold">payment date</span>.
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- BODY -->
  <div class="p-6 md:p-8">

    <!-- KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

      <!-- Users -->
      <div class="rounded-3xl border border-[#F3CACA] bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="text-xs font-black text-[#B98B8B]">TOTAL USERS</div>
            <div class="text-4xl font-extrabold mt-2 text-[#2D1414]">{{ $totalUsers ?? 0 }}</div>
          </div>
          <div class="rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] px-4 py-3">👥</div>
        </div>

        <div class="mt-4 text-sm text-[#9A5E5E] font-semibold">
          🎓 {{ $studentsCount ?? 0 }} students • 🏠 {{ $landlordsCount ?? 0 }} landlords
        </div>
      </div>

      <!-- Rooms -->
      <div class="rounded-3xl border border-[#F3CACA] bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="text-xs font-black text-[#B98B8B]">TOTAL ROOMS</div>
            <div class="text-4xl font-extrabold mt-2 text-[#2D1414]">{{ $totalRooms ?? 0 }}</div>
          </div>
          <div class="rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] px-4 py-3">🏠</div>
        </div>

        <div class="mt-4 text-sm text-[#9A5E5E] font-semibold">
          @if(!is_null($roomVerifiedCount))
            ✅ {{ $roomVerifiedCount }} verified • ⏳ {{ $roomPendingCount ?? 0 }} pending
          @else
            Room listings in system
          @endif
        </div>
      </div>

      <!-- Bookings -->
      <div class="rounded-3xl border border-[#F3CACA] bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="text-xs font-black text-[#B98B8B]">BOOKINGS</div>
            <div class="text-4xl font-extrabold mt-2 text-[#2D1414]">{{ $totalBookings ?? 0 }}</div>
          </div>
          <div class="rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] px-4 py-3">📌</div>
        </div>

        <div class="mt-4 text-sm text-[#9A5E5E] font-semibold">
          @if(!is_null($bookingPending))
            ⏳ {{ $bookingPending }} pending • ✅ {{ $bookingActive ?? 0 }} active
          @else
            Student bookings created
          @endif
        </div>
      </div>

      <!-- Revenue -->
      <div class="rounded-3xl border border-[#F3CACA] bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <div class="text-xs font-black text-[#B98B8B]">REVENUE (PAID)</div>
            <div class="text-4xl font-extrabold mt-2 leading-tight text-[#DC2626]">
              {{ $fmtMoney($revenuePaidRange ?? 0) }}
            </div>
          </div>

          <div class="rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] px-4 py-3">💳</div>
        </div>

        <div class="mt-4 text-sm text-[#9A5E5E] font-semibold">
          ✅ {{ $paidCountRange ?? 0 }} paid • ⏳ {{ $pendingCountRange ?? 0 }} pending
        </div>

        @if($isFiltered)
          <div class="mt-2 text-xs text-[#B98B8B]">
            All-time paid revenue: <span class="font-extrabold">{{ $fmtMoney($revenuePaidAll ?? 0) }}</span>
            ({{ $paidCountAll ?? 0 }} paid)
          </div>
        @endif
      </div>

    </div>

    <!-- LOWER GRID -->
    <div class="mt-6 grid grid-cols-1 xl:grid-cols-12 gap-6">

      <!-- System Summary -->
      <div class="xl:col-span-7">
        <div class="rounded-3xl border border-[#F3CACA] bg-white p-6 shadow-sm">
          <div class="flex items-center justify-between gap-4">
            <div>
              <div class="text-xs font-black text-[#B98B8B]">BREAKDOWN</div>
              <div class="text-2xl font-extrabold mt-1 text-[#7F1D1D]">System Status Summary</div>
            </div>
            <div class="rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] px-4 py-3">📊</div>
          </div>

          <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Landlords status -->
            <div class="rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] p-5">
              <div class="text-xs font-black text-[#B98B8B]">LANDLORDS</div>
              <div class="mt-3 text-sm font-semibold text-[#9A5E5E]">
                Approved / Pending / Rejected tracked in user table
              </div>

              @php
                $landApproved = \App\Models\User::where('role','landlord')->where('landlord_status','approved')->count();
                $landPending  = \App\Models\User::where('role','landlord')->where(function($q){
                    $q->whereNull('landlord_status')->orWhere('landlord_status','pending');
                })->count();
                $landRejected = \App\Models\User::where('role','landlord')->where('landlord_status','rejected')->count();
                $landTotal = max(1, ($landApproved + $landPending + $landRejected));
                $approvedPct = (int) round(($landApproved / $landTotal) * 100);
              @endphp

              <div class="mt-4 text-sm font-extrabold text-[#7F1D1D]">
                ✅ Approved: {{ $landApproved }} • ⏳ Pending: {{ $landPending }} • ❌ Rejected: {{ $landRejected }}
              </div>

              <div class="mt-4 w-full h-2 rounded-full bg-[#F3CACA] overflow-hidden">
                <div class="h-2 rounded-full" style="width: {{ $approvedPct }}%; background: {{ $accent }};"></div>
              </div>
              <div class="mt-2 text-xs text-[#B98B8B]">Approved ratio: {{ $approvedPct }}%</div>
            </div>

            <!-- Disputes -->
            <div class="rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] p-5">
              <div class="text-xs font-black text-[#B98B8B]">SUPPORT</div>
              <div class="mt-1 text-xl font-extrabold text-[#7F1D1D]">Disputes Summary</div>

              <div class="mt-4 grid grid-cols-2 gap-3 text-sm font-semibold text-[#9A5E5E]">
                <div class="rounded-xl border border-[#F3CACA] bg-white px-4 py-3">
                  Total: <span class="font-extrabold text-[#2D1414]">{{ $totalDisputes ?? 0 }}</span>
                </div>
                <div class="rounded-xl border border-[#F3CACA] bg-white px-4 py-3">
                  Open: <span class="font-extrabold text-[#DC2626]">{{ $openDisputes ?? 0 }}</span>
                </div>
                <div class="rounded-xl border border-green-200 bg-white px-4 py-3">
                  Resolved: <span class="font-extrabold text-green-700">{{ $closedDisputes ?? 0 }}</span>
                </div>
                <div class="rounded-xl border border-red-200 bg-white px-4 py-3">
                  High: <span class="font-extrabold text-red-700">{{ $highPriorityDisputes ?? 0 }}</span>
                </div>
              </div>

              <div class="mt-4 text-xs text-[#B98B8B]">
                Used for admin monitoring & resolution tracking.
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Leaderboard -->
      <div class="xl:col-span-5">
        <div class="rounded-3xl border border-[#F3CACA] bg-white p-6 shadow-sm">
          <div class="flex items-center justify-between gap-4">
            <div>
              <div class="text-xs font-black text-[#B98B8B]">LEADERBOARD</div>
              <div class="text-2xl font-extrabold mt-1 text-[#7F1D1D]">Top Landlords (by rooms)</div>
            </div>
            <div class="rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] px-4 py-3">🏆</div>
          </div>

          <div class="mt-5 space-y-3">
            @forelse($topLandlords as $l)
              @php
                $initials = strtoupper(substr($l->name ?? 'SR', 0, 2));
                $rooms = (int)($l->rooms_count ?? 0);
                $ls = strtolower((string)($l->landlord_status ?? 'pending'));
                if ($ls === '') $ls = 'pending';
              @endphp

              <div class="rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] p-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-extrabold text-white"
                       style="background: linear-gradient(135deg, {{ $accent }}, {{ $accentSoft }});">
                    {{ $initials }}
                  </div>
                  <div class="min-w-0">
                    <div class="font-extrabold truncate text-[#2D1414]">{{ $l->name ?? '—' }}</div>
                    <div class="text-xs text-[#B98B8B] truncate">{{ $l->email ?? '—' }}</div>

                    <div class="mt-1 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-extrabold
                      {{ $ls === 'approved' ? 'bg-green-100 text-green-800' : ($ls === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                      {{ ucfirst($ls) }}
                    </div>
                  </div>
                </div>

                <div class="text-right">
                  <div class="text-xs font-black text-[#B98B8B]">ROOMS</div>
                  <div class="text-xl font-extrabold text-[#DC2626]">{{ $rooms }}</div>
                </div>
              </div>
            @empty
              <div class="text-sm text-[#B98B8B] font-semibold">
                No landlords found.
              </div>
            @endforelse
          </div>

          <div class="mt-4 text-xs text-[#B98B8B]">
            Tip: This report is for admin monitoring & FYP “real system” feel.
          </div>
        </div>
      </div>

    </div>

  </div>
</div>
@endsection