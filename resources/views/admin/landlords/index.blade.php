@extends('layouts.admin')

@section('title', 'Landlord Approvals • Smart Rental')

@section('content')
@php
  // 🔥 RED ADMIN THEME (matched with dashboard)
  $primary = '#7F1D1D';
  $accent  = '#DC2626';
@endphp

<div class="rounded-3xl border border-[#F3CACA] shadow-lg p-8"
     style="background: linear-gradient(135deg, #FFF7F7, #FDEEEE);">

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold" style="color: {{ $primary }};">
                Landlord Approvals
            </h1>
            <p class="mt-2 text-[#9A5E5E]">
                Review landlord registrations. Approve to allow access to landlord dashboard.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="rounded-2xl px-4 py-2 font-extrabold text-sm border border-[#E9C9C9]
                  bg-white/80 hover:bg-white shadow-sm text-[#7F1D1D]">
            ← Back
        </a>
    </div>

    <div class="mt-6 space-y-4">
        @forelse($pendingLandlords as $l)
            @php
                $docPath = $l->verification_document_path ?? null;
                $docUrl = $docPath ? asset('storage/' . $docPath) : null;
            @endphp

            <div class="rounded-3xl border border-[#F3D2D2] p-6 shadow-sm"
                 style="background: rgba(255,255,255,0.78); backdrop-filter: blur(6px);">

                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                    <div>
                        <div class="text-xl font-extrabold text-[#3B0A0A]">
                            {{ $l->name }}
                        </div>

                        <div class="text-sm text-[#A16A6A]">
                            {{ $l->email }}
                        </div>

                        <div class="mt-3 text-sm text-[#6F4A4A] space-y-1">
                            <div><span class="font-extrabold">Company:</span> {{ $l->company_name ?? '-' }}</div>
                            <div><span class="font-extrabold">Phone:</span> {{ $l->phone ?? '-' }}</div>
                            <div><span class="font-extrabold">Address:</span> {{ $l->address ?? '-' }}</div>
                            <div><span class="font-extrabold">Registered:</span> {{ optional($l->created_at)->format('d M Y, h:i A') }}</div>
                        </div>

                        <div class="mt-4">
                            <div class="text-sm font-extrabold text-[#7F1D1D]">
                                Verification Document
                            </div>

                            @if($docUrl)
                                <div class="mt-2">
                                    <a href="{{ $docUrl }}" target="_blank"
                                       class="inline-flex items-center rounded-2xl px-4 py-2 text-sm font-extrabold text-white shadow-md"
                                       style="background: linear-gradient(135deg,#DC2626,#B91C1C);">
                                        View Document
                                    </a>
                                </div>
                            @else
                                <div class="mt-2 text-sm text-red-500 font-semibold">
                                    No verification document uploaded.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 min-w-[280px]">

                        <!-- APPROVE -->
                        <form method="POST" action="{{ route('admin.landlords.approve', $l->id) }}">
                            @csrf
                            <button class="w-full rounded-2xl px-4 py-3 text-sm font-extrabold text-white shadow-md hover:scale-105 transition"
                                    style="background: linear-gradient(135deg,#22C55E,#16A34A);">
                                Approve
                            </button>
                        </form>

                        <!-- REJECT -->
                        <form method="POST" action="{{ route('admin.landlords.reject', $l->id) }}" class="space-y-2">
                            @csrf
                            <input name="reason" required
                                   placeholder="Reject reason..."
                                   class="w-full rounded-2xl border border-[#F0CFCF] px-4 py-3 text-sm bg-white text-[#3B0A0A] placeholder-[#B98B8B]">

                            <button class="w-full rounded-2xl px-4 py-3 text-sm font-extrabold text-white shadow-md hover:scale-105 transition"
                                    style="background: linear-gradient(135deg,#EF4444,#DC2626);">
                                Reject
                            </button>
                        </form>

                    </div>
                </div>
            </div>

        @empty
            <div class="rounded-3xl border border-dashed border-[#EBCACA] p-8 text-center"
                 style="background: rgba(255,255,255,0.68);">

                <div class="text-lg font-extrabold text-[#7F1D1D]">
                    No pending landlords ✅
                </div>

                <div class="text-sm text-[#9A5E5E] mt-1">
                    All landlord applications have been reviewed.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection