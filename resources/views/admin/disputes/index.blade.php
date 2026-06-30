@extends('layouts.admin')

@section('title', 'Resolve Disputes • Smart Rental')

@section('content')
@php
  // Red admin theme
  $primary = '#7F1D1D';
  $primarySoft = '#9A5E5E';
  $accent = '#DC2626';
  $accentSoft = '#B91C1C';
@endphp

<div class="space-y-6">

  <!-- HERO -->
  <div class="rounded-[32px] border border-[#F3CACA] bg-white/90 shadow-lg overflow-hidden">
    <div class="px-6 py-7 md:px-8 md:py-8 border-b border-[#F3CACA]"
         style="background: linear-gradient(135deg, #FFF7F7, #FDEEEE);">
      <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">

        <div class="min-w-0">
          <div class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-black tracking-[0.18em] uppercase border"
               style="background: rgba(220,38,38,.08); border-color: rgba(220,38,38,.20); color: {{ $accent }};">
            Admin Control
          </div>

          <h1 class="mt-3 text-3xl md:text-4xl font-extrabold tracking-tight" style="color: {{ $primary }};">
            Resolve Disputes
          </h1>

          <p class="mt-2 max-w-2xl text-sm md:text-base font-semibold" style="color: {{ $primarySoft }};">
            Review formal complaints from students and landlords, monitor ticket status, and record final resolutions in one place.
          </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 w-full xl:w-auto">
          <div class="rounded-3xl border bg-white px-5 py-4 shadow-sm border-[#F3CACA]">
            <div class="text-[11px] font-black tracking-wide text-[#B98B8B] uppercase">Open</div>
            <div class="mt-2 text-3xl font-extrabold text-[#2D1414]">
              {{ $openCount ?? 0 }}
            </div>
          </div>

          <div class="rounded-3xl border bg-white px-5 py-4 shadow-sm border-[#F3CACA]">
            <div class="text-[11px] font-black tracking-wide uppercase text-[#B98B8B]">In Review</div>
            <div class="mt-2 text-3xl font-extrabold text-[#DC2626]">
              {{ $reviewCount ?? 0 }}
            </div>
          </div>

          <div class="rounded-3xl border bg-green-50 px-5 py-4 shadow-sm border-green-200">
            <div class="text-[11px] font-black tracking-wide uppercase text-green-700">Resolved</div>
            <div class="mt-2 text-3xl font-extrabold text-green-700">
              {{ $resolvedCount ?? 0 }}
            </div>
          </div>

          <div class="rounded-3xl border bg-red-50 px-5 py-4 shadow-sm border-red-200">
            <div class="text-[11px] font-black tracking-wide uppercase text-red-700">High Priority</div>
            <div class="mt-2 text-3xl font-extrabold text-red-700">
              {{ $highCount ?? 0 }}
            </div>
          </div>
        </div>

      </div>

      <!-- FILTER BAR -->
      <form method="GET" class="mt-7">
        <div class="rounded-[28px] border border-[#F3CACA] bg-white p-4 md:p-5 shadow-sm">
          <div class="flex flex-col 2xl:flex-row 2xl:items-center gap-3">

            <div class="flex-1 min-w-[260px]">
              <label class="block text-[11px] font-black uppercase tracking-wide text-[#B98B8B] mb-2">Search</label>
              <div class="relative">
                <input name="q" value="{{ $q ?? '' }}"
                       placeholder="Search ticket / student / landlord / booking"
                       class="w-full h-12 rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] pl-4 pr-11 text-sm font-semibold text-[#4B5563] focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]" />
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[#D4A5A5] pointer-events-none text-lg">🔎</span>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 2xl:w-[560px]">
              <div>
                <label class="block text-[11px] font-black uppercase tracking-wide text-[#B98B8B] mb-2">Status</label>
                <select name="status"
                        class="h-12 w-full rounded-2xl border border-[#F3CACA] bg-white px-4 text-sm font-semibold text-[#4B5563] focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]">
                  <option value="">All Status</option>
                  <option value="open" {{ ($status ?? '') === 'open' ? 'selected' : '' }}>Open</option>
                  <option value="in_review" {{ ($status ?? '') === 'in_review' ? 'selected' : '' }}>In Review</option>
                  <option value="resolved" {{ ($status ?? '') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                  <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
              </div>

              <div>
                <label class="block text-[11px] font-black uppercase tracking-wide text-[#B98B8B] mb-2">Priority</label>
                <select name="priority"
                        class="h-12 w-full rounded-2xl border border-[#F3CACA] bg-white px-4 text-sm font-semibold text-[#4B5563] focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]">
                  <option value="">All Priority</option>
                  <option value="low" {{ ($priority ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                  <option value="medium" {{ ($priority ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                  <option value="high" {{ ($priority ?? '') === 'high' ? 'selected' : '' }}>High</option>
                </select>
              </div>

              <div>
                <label class="block text-[11px] font-black uppercase tracking-wide text-[#B98B8B] mb-2">Category</label>
                <select name="category"
                        class="h-12 w-full rounded-2xl border border-[#F3CACA] bg-white px-4 text-sm font-semibold text-[#4B5563] focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]">
                  <option value="">All Category</option>
                  <option value="payment" {{ ($category ?? '') === 'payment' ? 'selected' : '' }}>Payment</option>
                  <option value="booking" {{ ($category ?? '') === 'booking' ? 'selected' : '' }}>Booking</option>
                  <option value="listing" {{ ($category ?? '') === 'listing' ? 'selected' : '' }}>Listing</option>
                  <option value="behavior" {{ ($category ?? '') === 'behavior' ? 'selected' : '' }}>Behaviour</option>
                  <option value="other" {{ ($category ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>
            </div>

            <div class="flex gap-3 2xl:self-end">
              <button
                class="h-12 rounded-2xl px-7 text-sm font-extrabold text-white shadow-md transition hover:scale-105"
                style="background: linear-gradient(135deg, {{ $accent }}, {{ $accentSoft }});">
                Filter
              </button>

              <a href="{{ route('admin.disputes.index') }}"
                 class="h-12 inline-flex items-center justify-center rounded-2xl px-6 text-sm font-extrabold border border-[#F3CACA] bg-white hover:bg-[#FFF3F3] text-[#7F1D1D] transition">
                Reset
              </a>
            </div>

          </div>
        </div>
      </form>
    </div>

    <!-- TABLE -->
    <div class="p-4 md:p-6">
      <div class="rounded-[30px] border border-[#F3CACA] bg-white overflow-hidden shadow-sm">
        <div class="px-5 py-4 md:px-6 border-b border-[#F3CACA] flex items-center justify-between gap-3"
             style="background: linear-gradient(180deg, #fff 0%, #FFF7F7 100%);">
          <div class="font-extrabold text-lg text-[#7F1D1D]">Dispute Tickets</div>
          <div class="inline-flex items-center rounded-full px-3 py-1 text-xs font-black border"
               style="background: rgba(220,38,38,.08); border-color: rgba(220,38,38,.20); color: {{ $accent }};">
            {{ method_exists($disputes, 'total') ? $disputes->total() : (is_countable($disputes) ? count($disputes) : 0) }} total
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full min-w-[980px] text-sm">
            <thead style="background: rgba(220,38,38,.05);" class="text-[#B98B8B]">
              <tr>
                <th class="text-left px-5 py-4 font-black uppercase tracking-wide text-[11px]">Ticket</th>
                <th class="text-left px-5 py-4 font-black uppercase tracking-wide text-[11px]">Category</th>
                <th class="text-left px-5 py-4 font-black uppercase tracking-wide text-[11px]">Student</th>
                <th class="text-left px-5 py-4 font-black uppercase tracking-wide text-[11px]">Landlord</th>
                <th class="text-left px-5 py-4 font-black uppercase tracking-wide text-[11px]">Booking</th>
                <th class="text-left px-5 py-4 font-black uppercase tracking-wide text-[11px]">Priority</th>
                <th class="text-left px-5 py-4 font-black uppercase tracking-wide text-[11px]">Status</th>
                <th class="text-left px-5 py-4 font-black uppercase tracking-wide text-[11px]">Updated</th>
                <th class="text-right px-5 py-4 font-black uppercase tracking-wide text-[11px]">Action</th>
              </tr>
            </thead>

            <tbody>
              @forelse($disputes as $d)
                @php
                  $ticket = $d->code ?? ('DSP-' . str_pad((string)$d->id, 5, '0', STR_PAD_LEFT));
                  $cat = ucfirst(str_replace('_', ' ', (string)($d->category ?? 'other')));

                  $studentName  = $d->student->name ?? '—';
                  $landlordName = $d->landlord->name ?? '—';

                  $bookingId = $d->booking_id ?? null;

                  $prio = strtolower((string)($d->priority ?? 'medium'));
                  $st   = strtolower((string)($d->status ?? 'open'));

                  $prioLabel = ucfirst($prio);
                  $stLabel   = ucwords(str_replace('_', ' ', $st));

                  $updated = optional($d->updated_at)->format('d M Y, h:i A') ?? '—';

                  $prioClass = $prio === 'high'
                    ? 'bg-red-100 text-red-800 border border-red-200'
                    : ($prio === 'low'
                        ? 'bg-sky-100 text-sky-800 border border-sky-200'
                        : 'bg-amber-100 text-amber-800 border border-amber-200');

                  $stClass = $st === 'resolved'
                    ? 'bg-green-100 text-green-800 border border-green-200'
                    : ($st === 'in_review'
                        ? 'bg-yellow-100 text-yellow-800 border border-yellow-200'
                        : ($st === 'rejected'
                            ? 'bg-gray-200 text-gray-800 border border-gray-300'
                            : 'bg-blue-100 text-blue-800 border border-blue-200'));
                @endphp

                <tr class="border-t border-[#F3E3E3] hover:bg-[#FFF8F8] transition">
                  <td class="px-5 py-5 align-top">
                    <div class="font-extrabold text-[#2D1414]">{{ $ticket }}</div>
                    @if(!empty($d->title))
                      <div class="mt-1 text-xs font-semibold text-[#B98B8B] max-w-[250px] leading-relaxed">
                        {{ $d->title }}
                      </div>
                    @endif
                  </td>

                  <td class="px-5 py-5 align-top">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold border"
                          style="background: rgba(220,38,38,.08); border-color: rgba(220,38,38,.20); color: {{ $accent }};">
                      {{ $cat }}
                    </span>
                  </td>

                  <td class="px-5 py-5 align-top">
                    <div class="font-extrabold text-[#2D1414]">{{ $studentName }}</div>
                    <div class="text-xs text-[#B98B8B] font-semibold mt-1">Student</div>
                  </td>

                  <td class="px-5 py-5 align-top">
                    <div class="font-extrabold text-[#2D1414]">{{ $landlordName }}</div>
                    <div class="text-xs text-[#B98B8B] font-semibold mt-1">Landlord</div>
                  </td>

                  <td class="px-5 py-5 align-top font-semibold text-[#7B4A4A]">
                    {{ $bookingId ? ('#' . $bookingId) : '—' }}
                  </td>

                  <td class="px-5 py-5 align-top">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold {{ $prioClass }}">
                      {{ $prioLabel }}
                    </span>
                  </td>

                  <td class="px-5 py-5 align-top">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold {{ $stClass }}">
                      {{ $stLabel }}
                    </span>
                  </td>

                  <td class="px-5 py-5 align-top text-[#7B4A4A] font-semibold whitespace-nowrap">
                    {{ $updated }}
                  </td>

                  <td class="px-5 py-5 align-top text-right">
                    <a href="{{ route('admin.disputes.show', $d->id) }}"
                       class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 font-extrabold text-white shadow-md transition hover:scale-105"
                       style="background: linear-gradient(135deg, #DC2626, #B91C1C);">
                      View Details
                      <span class="text-white/70">›</span>
                    </a>
                  </td>
                </tr>

              @empty
                <tr>
                  <td colspan="9" class="px-6 py-16 text-center">
                    <div class="inline-flex flex-col items-center gap-3">
                      <div class="h-14 w-14 rounded-3xl grid place-items-center border border-[#F3CACA] bg-[#FFF3F3]">
                        🛟
                      </div>
                      <div class="font-extrabold text-lg text-[#9A5E5E]">No disputes found</div>
                      <div class="text-sm text-[#B98B8B] font-semibold">
                        When students or landlords submit dispute tickets, they will appear here for admin review.
                      </div>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if(method_exists($disputes, 'links'))
          <div class="p-4 md:p-5 border-t border-[#F3CACA] bg-white">
            {{ $disputes->appends(request()->query())->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>

</div>
@endsection