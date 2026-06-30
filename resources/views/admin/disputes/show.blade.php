@extends('layouts.admin')

@section('title', 'Dispute Ticket • Smart Rental')

@section('content')
@php
  // Red admin theme
  $primary = '#7F1D1D';
  $primarySoft = '#9A5E5E';
  $accent = '#DC2626';
  $accentSoft = '#B91C1C';

  $pretty = fn($v) => ucwords(str_replace('_',' ', (string)$v));

  $status = strtolower((string)$dispute->status);
  $priority = strtolower((string)$dispute->priority);

  $statusBadge = match ($status) {
    'open' => 'bg-red-100 text-red-800 border border-red-200',
    'in_review' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
    'resolved' => 'bg-green-100 text-green-800 border border-green-200',
    'rejected' => 'bg-gray-100 text-gray-800 border border-gray-200',
    default => 'bg-gray-100 text-gray-800 border border-gray-200',
  };

  $priorityBadge = match ($priority) {
    'high' => 'bg-red-100 text-red-800 border border-red-200',
    'medium' => 'bg-amber-100 text-amber-800 border border-amber-200',
    'low' => 'bg-sky-100 text-sky-800 border border-sky-200',
    default => 'bg-gray-100 text-gray-800 border border-gray-200',
  };

  $student = $dispute->student;
  $landlord = $dispute->landlord;
  $booking = $dispute->booking;
  $room = $dispute->room;

  $title = $dispute->title ?: 'Dispute Ticket';
@endphp

<div class="space-y-6">

  <!-- HERO -->
  <div class="rounded-[32px] border border-[#F3CACA] bg-white/90 shadow-lg overflow-hidden">
    <div class="px-6 py-7 md:px-8 md:py-8 border-b border-[#F3CACA]"
         style="background: linear-gradient(135deg, #FFF7F7, #FDEEEE);">
      <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-6">

        <div class="min-w-0">
          <div class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-black tracking-[0.18em] uppercase border"
               style="background: rgba(220,38,38,.08); border-color: rgba(220,38,38,.20); color: {{ $accent }};">
            Admin Review
          </div>

          <div class="mt-3 flex flex-wrap items-center gap-3">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[#7F1D1D]">
              {{ $dispute->code }}
            </h1>

            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold {{ $statusBadge }}">
              {{ $pretty($status) }}
            </span>

            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold {{ $priorityBadge }}">
              {{ ucfirst($priority) }} Priority
            </span>
          </div>

          <div class="mt-3 font-semibold text-[#9A5E5E]">
            {{ $title }} • Category:
            <span class="font-extrabold">{{ $pretty($dispute->category) }}</span>
          </div>

          <div class="mt-2 text-xs text-[#B98B8B] font-semibold">
            Created: {{ optional($dispute->created_at)->format('d M Y, h:i A') }}
            • Updated: {{ optional($dispute->updated_at)->format('d M Y, h:i A') }}
          </div>
        </div>

        <div class="flex flex-wrap gap-2">
          <a href="{{ route('admin.disputes.index') }}"
             class="rounded-2xl px-5 py-3 font-extrabold border border-[#F3CACA] bg-white hover:bg-[#FFF3F3] text-[#7F1D1D] transition">
            ← Back
          </a>

          <form method="POST" action="{{ route('admin.disputes.status', $dispute->id) }}">
            @csrf
            <input type="hidden" name="status" value="in_review">
            <button class="rounded-2xl px-5 py-3 font-extrabold text-white shadow-md transition hover:scale-105"
                    style="background: linear-gradient(135deg, {{ $accent }}, {{ $accentSoft }});">
              Mark In Review
            </button>
          </form>

          <form method="POST" action="{{ route('admin.disputes.status', $dispute->id) }}">
            @csrf
            <input type="hidden" name="status" value="open">
            <button class="rounded-2xl px-5 py-3 font-extrabold border border-[#F3CACA] bg-white hover:bg-[#FFF3F3] text-[#7F1D1D] transition">
              Re-open
            </button>
          </form>
        </div>

      </div>
    </div>

    <!-- BODY -->
    <div class="p-4 md:p-6 lg:p-8">
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        <!-- LEFT -->
        <div class="xl:col-span-7 space-y-6">

          <!-- Summary -->
          <div class="rounded-[30px] border border-[#F3CACA] bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
              <div class="font-extrabold text-xl text-[#7F1D1D]">Dispute Summary</div>
              <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold border"
                    style="background: rgba(220,38,38,.08); border-color: rgba(220,38,38,.20); color: {{ $accent }};">
                {{ $pretty($dispute->category) }}
              </span>
            </div>

            <div class="mt-5 rounded-[24px] border border-[#F3CACA] bg-[#FFF3F3] p-5">
              <div class="text-sm leading-7 text-[#7B4A4A] whitespace-pre-line font-semibold">
                {{ $dispute->description ?: '—' }}
              </div>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="rounded-[24px] border border-[#F3CACA] bg-[#FFF3F3] p-5">
                <div class="text-[11px] font-black tracking-wide uppercase text-[#B98B8B]">Booking</div>
                <div class="mt-2 text-3xl font-extrabold text-[#2D1414]">
                  {{ $dispute->booking_id ? ('#'.$dispute->booking_id) : '—' }}
                </div>
              </div>

              <div class="rounded-[24px] border border-[#F3CACA] bg-[#FFF3F3] p-5">
                <div class="text-[11px] font-black tracking-wide uppercase text-[#B98B8B]">Room</div>
                <div class="mt-2 text-xl font-extrabold leading-snug text-[#2D1414]">
                  {{ optional($room)->title ?? optional($room)->name ?? ($dispute->room_id ? ('#'.$dispute->room_id) : '—') }}
                </div>
              </div>
            </div>
          </div>

          <!-- Evidence -->
          <div class="rounded-[30px] border border-[#F3CACA] bg-white p-6 shadow-sm">
            <div class="font-extrabold text-xl text-[#7F1D1D]">Evidence</div>
            <div class="mt-1 text-sm text-[#9A5E5E] font-semibold">
              Review submitted proof or upload additional supporting evidence.
            </div>

            <form method="POST"
                  action="{{ route('admin.disputes.evidence', $dispute->id) }}"
                  enctype="multipart/form-data"
                  class="mt-5">
              @csrf

              <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-3 items-end">
                <div>
                  <label class="block text-[11px] font-black uppercase tracking-wide text-[#B98B8B] mb-2">
                    Upload Evidence
                  </label>

                  <div class="rounded-[22px] border border-[#F3CACA] bg-[#FFF3F3] p-3">
                    <input type="file" name="evidence"
                           class="block w-full text-sm font-semibold text-[#4B5563]
                                  file:mr-4 file:rounded-xl file:border-0
                                  file:bg-white file:px-4 file:py-2.5
                                  file:font-extrabold file:text-[#7F1D1D]
                                  hover:file:bg-[#FFF1F1]"
                           required>
                  </div>
                </div>

                <button class="h-[52px] rounded-2xl px-6 font-extrabold text-white transition hover:scale-[1.02] shadow-md w-full lg:w-auto"
                        style="background: linear-gradient(135deg, {{ $accent }}, {{ $accentSoft }});">
                  Upload
                </button>
              </div>
            </form>

            <div class="mt-5">
              @if($dispute->evidenceUrl())
                @if(str_ends_with(strtolower($dispute->evidence_path), '.pdf'))
                  <a href="{{ $dispute->evidenceUrl() }}" target="_blank"
                     class="inline-flex items-center gap-2 rounded-2xl border border-[#F3CACA] bg-[#FFF3F3] px-5 py-3 font-extrabold text-[#7F1D1D] hover:bg-white transition">
                    📄 View PDF Evidence
                  </a>
                @else
                  <a href="{{ $dispute->evidenceUrl() }}" target="_blank" class="block">
                    <div class="rounded-[26px] border border-[#F3CACA] bg-[#FFF3F3] p-4">
                      <img src="{{ $dispute->evidenceUrl() }}"
                           class="rounded-[20px] border border-[#F3CACA] max-h-[420px] object-contain bg-white w-full">
                    </div>
                  </a>
                @endif
              @else
                <div class="rounded-[24px] border border-dashed border-[#F0CFCF] bg-[#FFF8F8] px-5 py-10 text-center">
                  <div class="text-3xl">📎</div>
                  <div class="mt-3 font-extrabold text-[#9A5E5E]">No evidence uploaded</div>
                  <div class="mt-1 text-sm font-semibold text-[#B98B8B]">
                    Admin can upload supporting evidence if needed.
                  </div>
                </div>
              @endif
            </div>
          </div>

          <!-- People -->
          <div class="rounded-[30px] border border-[#F3CACA] bg-white p-6 shadow-sm">
            <div class="font-extrabold text-xl text-[#7F1D1D]">People Involved</div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="rounded-[24px] border border-[#F3CACA] bg-[#FFF3F3] p-5">
                <div class="text-[11px] font-black tracking-wide uppercase text-[#B98B8B]">Student</div>
                <div class="mt-3 text-2xl font-extrabold text-[#2D1414]">{{ optional($student)->name ?? '—' }}</div>
                <div class="mt-2 text-sm text-[#7B4A4A] break-words font-semibold">{{ optional($student)->email ?? '—' }}</div>
                <div class="mt-1 text-sm text-[#7B4A4A] font-semibold">{{ optional($student)->phone ?? '—' }}</div>
              </div>

              <div class="rounded-[24px] border border-[#F3CACA] bg-[#FFF3F3] p-5">
                <div class="text-[11px] font-black tracking-wide uppercase text-[#B98B8B]">Landlord</div>
                <div class="mt-3 text-2xl font-extrabold text-[#2D1414]">{{ optional($landlord)->name ?? '—' }}</div>
                <div class="mt-2 text-sm text-[#7B4A4A] break-words font-semibold">{{ optional($landlord)->email ?? '—' }}</div>
                <div class="mt-1 text-sm text-[#7B4A4A] font-semibold">{{ optional($landlord)->phone ?? '—' }}</div>
              </div>
            </div>
          </div>

        </div>

        <!-- RIGHT -->
        <div class="xl:col-span-5 space-y-6">

          <!-- Notes -->
          <div class="rounded-[30px] border border-[#F3CACA] bg-white p-6 shadow-sm">
            <div class="font-extrabold text-xl text-[#7F1D1D]">Admin Notes</div>
            <div class="mt-1 text-sm text-[#9A5E5E] font-semibold">
              Internal notes for review and case handling.
            </div>

            <form method="POST" action="{{ route('admin.disputes.note', $dispute->id) }}" class="mt-4">
              @csrf
              <textarea name="admin_note" rows="7"
                        class="w-full rounded-[24px] border border-[#F3CACA] bg-[#FFF3F3] px-4 py-4 font-semibold text-[#4B5563] focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]"
                        placeholder="Write admin notes...">{{ old('admin_note', $dispute->admin_note) }}</textarea>

              <button class="mt-4 w-full rounded-2xl px-5 py-3.5 font-extrabold text-white hover:scale-[1.01] transition shadow-md"
                      style="background: linear-gradient(135deg, {{ $accent }}, {{ $accentSoft }});">
                Save Note
              </button>
            </form>
          </div>

          <!-- Outcome -->
          <div class="rounded-[30px] border border-[#F3CACA] bg-white p-6 shadow-sm">
            <div class="font-extrabold text-xl text-[#7F1D1D]">Final Outcome</div>
            <div class="mt-1 text-sm text-[#9A5E5E] font-semibold">
              Record the final decision for this dispute ticket.
            </div>

            <form method="POST" action="{{ route('admin.disputes.resolve', $dispute->id) }}" class="mt-4 space-y-4">
              @csrf

              <div>
                <label class="block text-[11px] font-black uppercase tracking-wide text-[#B98B8B] mb-2">Resolution</label>
                <select name="resolution"
                        class="w-full rounded-2xl border border-[#F3CACA] bg-white px-4 py-3.5 font-semibold text-[#4B5563] focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]"
                        required>
                  <option value="">Select Resolution</option>
                  <option value="refund_approved">Refund Approved</option>
                  <option value="refund_rejected">Refund Rejected</option>
                  <option value="warning_landlord">Warning Landlord</option>
                  <option value="cancel_booking">Booking Cancelled</option>
                  <option value="flag_listing">Listing Flagged / Unverified</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div>
                <label class="block text-[11px] font-black uppercase tracking-wide text-[#B98B8B] mb-2">Outcome Details</label>
                <textarea name="outcome_details" rows="5"
                          class="w-full rounded-[24px] border border-[#F3CACA] bg-[#FFF3F3] px-4 py-4 font-semibold text-[#4B5563] focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]"
                          placeholder="Write the final decision details...">{{ old('outcome_details', $dispute->outcome_details) }}</textarea>
              </div>

              <button class="w-full rounded-2xl px-5 py-3.5 font-extrabold text-white hover:scale-[1.01] transition shadow-md"
                      style="background: linear-gradient(135deg, #22C55E, #16A34A);">
                Resolve Ticket
              </button>
            </form>

            <form method="POST" action="{{ route('admin.disputes.reject', $dispute->id) }}" class="mt-3">
              @csrf
              <input type="hidden" name="outcome_details" value="Ticket rejected by admin.">
              <button class="w-full rounded-2xl px-5 py-3.5 font-extrabold border border-[#F3CACA] bg-white hover:bg-[#FFF3F3] text-[#7F1D1D] transition">
                Reject Ticket
              </button>
            </form>

            <div class="mt-5 rounded-[22px] border border-[#F3CACA] bg-[#FFF8F8] px-4 py-4 text-xs font-semibold text-[#9A5E5E]">
              @if($dispute->resolved_at)
                <span class="font-extrabold text-[#7F1D1D]">Resolved at:</span>
                {{ optional($dispute->resolved_at)->format('d M Y, h:i A') }}
                @if($dispute->resolver)
                  <span class="mx-1">•</span>
                  <span class="font-extrabold text-[#7F1D1D]">By:</span> {{ $dispute->resolver->name }}
                @endif
              @else
                This ticket has not been resolved yet.
              @endif
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>

</div>
@endsection