@extends('layouts.admin')

@section('title', 'Landlords • All Statuses')

@php
    // Red premium admin theme
    $primary = '#2D1414';
    $primarySoft = '#7B5B5B';
    $accent = '#C92A2A';
    $accentSoft = '#E45B5B';

    $tabBtn = function($key) use ($tab) {
        $active = $tab === $key;

        return $active
            ? "rounded-2xl px-4 py-2 font-extrabold text-sm border shadow-sm text-[#2D1414] bg-gradient-to-r from-[#F4FAFF] to-[#FFF7F7] border-[rgba(201,42,42,.16)]"
            : "rounded-2xl px-4 py-2 font-extrabold text-sm border border-[rgba(201,42,42,.10)] bg-gradient-to-r from-[#FFF8F8] to-[#F7FBFF] text-[#7B5B5B] hover:brightness-[0.99]";
    };

    $badge = function($type, $text) {
        return match($type) {
            'pending'  => '<span class="inline-flex items-center rounded-full px-3 py-1 border font-extrabold text-xs"
                           style="background: rgba(201,42,42,.08); border-color: rgba(201,42,42,.18); color:#C92A2A;">'.$text.'</span>',
            'approved' => '<span class="inline-flex items-center rounded-full px-3 py-1 border font-extrabold text-xs"
                           style="background: rgba(34,197,94,.08); border-color: rgba(34,197,94,.22); color:#15803d;">'.$text.'</span>',
            'rejected' => '<span class="inline-flex items-center rounded-full px-3 py-1 border font-extrabold text-xs"
                           style="background: rgba(239,68,68,.08); border-color: rgba(239,68,68,.22); color:#b91c1c;">'.$text.'</span>',
            default    => '<span class="inline-flex items-center rounded-full px-3 py-1 border font-extrabold text-xs"
                           style="background: rgba(0,0,0,.03); border-color: rgba(0,0,0,.10); color:#111;">'.$text.'</span>',
        };
    };

    $safe = fn($v) => e($v ?? '');
@endphp

@section('content')

{{-- HERO HEADER --}}
<div class="rounded-3xl overflow-hidden border shadow-lg"
     style="border-color:rgba(201,42,42,.10); background: linear-gradient(135deg, #FFF8F8, #FFF4F4);">
    <div class="relative p-7 sm:p-9">
        <div class="absolute inset-0 opacity-[0.16]"
             style="background:
                radial-gradient(circle at 18% 20%, rgba(201,42,42,.18), transparent 45%),
                radial-gradient(circle at 90% 10%, rgba(228,91,91,.14), transparent 45%),
                linear-gradient(120deg, rgba(255,255,255,.80), rgba(255,248,248,.96));
             ">
        </div>

        <div class="absolute -top-24 -right-24 h-64 w-64 rounded-full blur-3xl opacity-25"
             style="background: rgba(201,42,42,.22);"></div>
        <div class="absolute -bottom-28 -left-28 h-64 w-64 rounded-full blur-3xl opacity-20"
             style="background: rgba(228,91,91,.18);"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                     style="border-color: rgba(201,42,42,.18); background: rgba(201,42,42,.08); color: {{ $accent }};">
                    👥 Landlord Management
                </div>

                <h1 class="mt-3 text-4xl font-black" style="color: {{ $primary }};">
                    All Landlords (All Statuses)
                </h1>

                <p class="mt-2 font-semibold max-w-2xl" style="color: {{ $primarySoft }};">
                    Track pending approvals, manage approved accounts, and review rejected reasons — like a real admin system.
                </p>

                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                          style="border-color: rgba(0,0,0,.08); background: rgba(255,255,255,.82); color:#4B5563;">
                        🕒 {{ now()->format('d M Y, h:i A') }}
                    </span>

                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                          style="border-color: rgba(201,42,42,.18); background: rgba(201,42,42,.08); color: {{ $accent }};">
                        ⏳ Pending: {{ $pendingCount }}
                    </span>
                </div>
            </div>

            {{-- Search + Filter --}}
            <form method="GET" class="w-full lg:w-[520px]">
                <div class="rounded-3xl border bg-white/80 backdrop-blur p-4 shadow-sm"
                     style="border-color: rgba(201,42,42,.10);">
                    <div class="text-sm font-black" style="color: {{ $primary }};">Search & Filter</div>
                    <div class="text-xs font-semibold mt-1" style="color: {{ $primarySoft }};">Find landlord by name/email + status</div>

                    <div class="mt-4 flex flex-col sm:flex-row gap-2">
                        <input name="q"
                               value="{{ $q }}"
                               placeholder="Search name or email..."
                               class="w-full rounded-2xl px-4 py-3 border bg-white font-semibold focus:outline-none focus:ring-2"
                               style="border-color: rgba(201,42,42,.10); --tw-ring-color: rgba(201,42,42,.14);" />

                        <select name="tab"
                                class="w-full sm:w-44 rounded-2xl px-4 py-3 border bg-white font-semibold text-[#4B5563]"
                                style="border-color: rgba(201,42,42,.10);">
                            <option value="all" {{ $tab==='all'?'selected':'' }}>All</option>
                            <option value="pending" {{ $tab==='pending'?'selected':'' }}>Pending</option>
                            <option value="approved" {{ $tab==='approved'?'selected':'' }}>Approved</option>
                            <option value="rejected" {{ $tab==='rejected'?'selected':'' }}>Rejected</option>
                        </select>

                        <button class="rounded-2xl px-5 py-3 font-extrabold text-sm text-white shadow-md"
                                style="background: linear-gradient(135deg, {{ $accent }}, {{ $accentSoft }});">
                            Search
                        </button>
                    </div>

                    <div class="mt-3 text-[11px] font-semibold" style="color: {{ $primarySoft }};">
                        Tip: Use tabs below for quick switching.
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- TOP STATS --}}
<div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="rounded-3xl border shadow-sm p-5"
         style="border-color:rgba(201,42,42,.10); background:linear-gradient(135deg,#F6FBFF 0%,#FFF8F8 100%);">
        <div class="text-[11px] font-black tracking-wider uppercase text-[#AA8A8A]">Pending</div>
        <div class="mt-1 text-3xl font-black" style="color: {{ $accent }};">{{ $pendingCount }}</div>
        <div class="mt-2 text-xs font-semibold text-[#7B5B5B]">Needs admin decision.</div>
    </div>

    <div class="rounded-3xl border shadow-sm p-5"
         style="border-color:rgba(201,42,42,.10); background:linear-gradient(135deg,#F6FBFF 0%,#FFF8F8 100%);">
        <div class="text-[11px] font-black tracking-wider uppercase text-[#AA8A8A]">Approved</div>
        <div class="mt-1 text-3xl font-black text-green-600">{{ $approvedCount }}</div>
        <div class="mt-2 text-xs font-semibold text-[#7B5B5B]">Active landlord accounts.</div>
    </div>

    <div class="rounded-3xl border shadow-sm p-5"
         style="border-color:rgba(201,42,42,.10); background:linear-gradient(135deg,#F6FBFF 0%,#FFF8F8 100%);">
        <div class="text-[11px] font-black tracking-wider uppercase text-[#AA8A8A]">Rejected</div>
        <div class="mt-1 text-3xl font-black text-red-600">{{ $rejectedCount }}</div>
        <div class="mt-2 text-xs font-semibold text-[#7B5B5B]">Includes rejection reasons.</div>
    </div>

    <div class="rounded-3xl border shadow-sm p-5"
         style="border-color:rgba(201,42,42,.10); background:linear-gradient(135deg,#F6FBFF 0%,#FFF8F8 100%);">
        <div class="text-[11px] font-black tracking-wider uppercase text-[#AA8A8A]">Total</div>
        <div class="mt-1 text-3xl font-black" style="color: {{ $primary }};">{{ $pendingCount + $approvedCount + $rejectedCount }}</div>
        <div class="mt-2 text-xs font-semibold text-[#7B5B5B]">All landlord registrations.</div>
    </div>
</div>

{{-- TABS --}}
<div class="mt-5 flex flex-wrap gap-2">
    <a class="{{ $tabBtn('all') }}" href="{{ route('admin.landlords.all', ['tab'=>'all','q'=>$q]) }}">
        All
        <span class="ml-2 inline-flex items-center rounded-full px-2.5 py-1 border text-xs font-extrabold"
              style="background: rgba(0,0,0,.03); border-color: rgba(0,0,0,.10); color:#374151;">
            {{ $pendingCount + $approvedCount + $rejectedCount }}
        </span>
    </a>

    <a class="{{ $tabBtn('pending') }}" href="{{ route('admin.landlords.all', ['tab'=>'pending','q'=>$q]) }}">
        Pending {!! $badge('pending', (string)$pendingCount) !!}
    </a>

    <a class="{{ $tabBtn('approved') }}" href="{{ route('admin.landlords.all', ['tab'=>'approved','q'=>$q]) }}">
        Approved {!! $badge('approved', (string)$approvedCount) !!}
    </a>

    <a class="{{ $tabBtn('rejected') }}" href="{{ route('admin.landlords.all', ['tab'=>'rejected','q'=>$q]) }}">
        Rejected {!! $badge('rejected', (string)$rejectedCount) !!}
    </a>
</div>

{{-- TABLE --}}
<div class="mt-6 rounded-3xl border shadow-sm overflow-hidden"
     style="border-color:rgba(201,42,42,.10); background:linear-gradient(135deg,#F8FBFF 0%,#FFF8F8 100%);">
    <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-lg font-black" style="color: {{ $primary }};">Landlords List</div>
            <div class="text-sm font-semibold" style="color: {{ $primarySoft }};">
                Approve/reject from here (pending only). Rejected reasons shown.
            </div>
        </div>

        <div class="text-xs font-extrabold text-[#AA8A8A]">
            Showing {{ $landlords->count() }} of {{ $landlords->total() }}
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead style="background: linear-gradient(135deg, rgba(234,245,255,.75), rgba(255,245,245,.95));">
                <tr class="text-left font-black text-[#AA8A8A]">
                    <th class="py-4 px-6">Landlord</th>
                    <th class="py-4 px-6">Email</th>
                    <th class="py-4 px-6">Status</th>
                    <th class="py-4 px-6">Registered</th>
                    <th class="py-4 px-6">Rejected Reason</th>
                    <th class="py-4 px-6 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y" style="--tw-divide-opacity:1; border-color:rgba(201,42,42,.06);">
            @forelse($landlords as $u)
                @php
                    $status = $u->landlord_status ?? 'unknown';
                    $reason = $u->landlord_rejected_reason ?? null;
                    $modalId = 'rejectModal_'.$u->id;
                @endphp

                <tr class="hover:bg-[#FFF9F9]">
                    <td class="py-4 px-6">
                        <div class="font-extrabold text-[#2D1414] truncate max-w-[240px]">
                            {{ $u->name }}
                        </div>
                        <div class="text-xs text-[#A09A9A] font-semibold">
                            ID: {{ $u->id }}
                        </div>
                    </td>

                    <td class="py-4 px-6 font-semibold text-[#5F5A5A]">
                        <a class="underline" href="mailto:{{ $u->email }}">{{ $u->email }}</a>
                    </td>

                    <td class="py-4 px-6">
                        {!! $badge($status, ucfirst($status)) !!}
                    </td>

                    <td class="py-4 px-6 font-semibold text-[#5F5A5A]">
                        {{ optional($u->created_at)->format('d M Y') }}
                        <div class="text-xs text-[#A09A9A] font-semibold">
                            {{ optional($u->created_at)->diffForHumans() }}
                        </div>
                    </td>

                    <td class="py-4 px-6 font-semibold text-[#5F5A5A]">
                        @if($status === 'rejected')
                            <div class="max-w-[320px]">
                                <div class="rounded-2xl border border-red-200 bg-red-50 px-3 py-2 text-red-800 font-semibold">
                                    {{ $reason ?: '— (No reason recorded)' }}
                                </div>
                            </div>
                        @else
                            <span class="text-black/30">—</span>
                        @endif
                    </td>

                    <td class="py-4 px-6">
                        <div class="flex items-center justify-end gap-2">
                            @if($status === 'pending')
                                {{-- Approve --}}
                                <form method="POST" action="{{ route('admin.landlords.approve', $u->id) }}">
                                    @csrf
                                    <button class="rounded-2xl px-3 py-2 text-xs font-extrabold text-white shadow-md"
                                            style="background: linear-gradient(135deg,#22C55E,#16A34A);">
                                        Approve
                                    </button>
                                </form>

                                {{-- Reject --}}
                                <button type="button"
                                        class="rounded-2xl px-3 py-2 text-xs font-extrabold text-white shadow-md"
                                        style="background: linear-gradient(135deg,#C92A2A,#A61E1E);"
                                        onclick="document.getElementById('{{ $modalId }}').classList.remove('hidden')">
                                    Reject
                                </button>
                            @else
                                <a href="{{ route('admin.landlords.index') }}"
                                   class="rounded-2xl px-3 py-2 text-xs font-extrabold border bg-gradient-to-r from-[#F4FAFF] to-[#FFF8F8] hover:brightness-[0.99] text-[#2D1414]"
                                   style="border-color:rgba(201,42,42,.10);">
                                    Manage
                                </a>
                            @endif
                        </div>

                        {{-- Reject Modal --}}
                        <div id="{{ $modalId }}" class="hidden fixed inset-0 z-50">
                            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                                 onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')"></div>

                            <div class="relative max-w-lg mx-auto mt-24 bg-white rounded-3xl shadow-xl border overflow-hidden"
                                 style="border-color:rgba(201,42,42,.10);">
                                <div class="p-5 border-b" style="border-color:rgba(201,42,42,.08);">
                                    <div class="text-lg font-black" style="color: {{ $primary }};">Reject Landlord</div>
                                    <div class="text-sm font-semibold mt-1" style="color: {{ $primarySoft }};">
                                        Write a reason.
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.landlords.reject', $u->id) }}">
                                    @csrf
                                    <div class="p-5 space-y-3">
                                        <div class="rounded-2xl border p-4"
                                             style="border-color:rgba(201,42,42,.10); background:linear-gradient(135deg,#F8FBFF 0%,#FFF8F8 100%);">
                                            <div class="font-extrabold text-[#2D1414]">{{ $u->name }}</div>
                                            <div class="text-sm font-semibold text-[#7B5B5B]">{{ $u->email }}</div>
                                        </div>

                                        <textarea name="reason" required rows="4"
                                                  class="w-full rounded-2xl px-4 py-3 border bg-white font-semibold focus:outline-none focus:ring-2"
                                                  style="border-color:rgba(201,42,42,.10); --tw-ring-color: rgba(201,42,42,.14);"
                                                  placeholder="Example: Invalid documents / unclear identity / incomplete registration..."></textarea>

                                        @error('reason')
                                            <div class="text-sm font-semibold text-red-600">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="p-5 flex items-center justify-end gap-2 border-t bg-[#FFF9F9]"
                                         style="border-color:rgba(201,42,42,.08);">
                                        <button type="button"
                                                class="rounded-2xl px-4 py-2 font-extrabold text-sm border bg-white hover:bg-[#FFF8F8] text-[#2D1414]"
                                                style="border-color:rgba(201,42,42,.10);"
                                                onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')">
                                            Cancel
                                        </button>

                                        <button class="rounded-2xl px-4 py-2 font-extrabold text-sm text-white"
                                                style="background: linear-gradient(135deg,#C92A2A,#A61E1E);">
                                            Confirm Reject
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        {{-- End modal --}}
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="6" class="py-14 text-center font-semibold text-[#AA8A8A]">
                        No landlords found for this filter.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-5">
        {{ $landlords->links() }}
    </div>
</div>

@endsection