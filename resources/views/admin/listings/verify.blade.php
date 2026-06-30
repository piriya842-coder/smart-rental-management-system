@extends('layouts.admin')

@section('title', 'Verify Listings • Smart Rental')

@section('content')
@php
    // 🔥 RED ADMIN THEME (same as dashboard)
    $primary = '#7F1D1D';
    $primarySoft = '#9A5E5E';
    $accent = '#DC2626';
    $accentSoft = '#B91C1C';
@endphp

<div class="rounded-3xl border border-[#F3CACA] shadow-lg p-6 md:p-8"
     style="background: linear-gradient(135deg, #FFF7F7, #FDEEEE);">

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <div class="text-3xl font-black" style="color: {{ $primary }};">Verify Listings</div>
            <div class="mt-1 font-semibold" style="color: {{ $primarySoft }};">
                Approve or reject rooms before they appear to students.
            </div>
        </div>

        <form method="GET" action="{{ route('admin.listings.verify') }}" class="flex flex-wrap gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <input name="q" value="{{ $q }}"
                   placeholder="Search title / address / city / state..."
                   class="rounded-2xl px-4 py-3 border border-[#F0CFCF] bg-white w-72 font-semibold text-[#3B0A0A] focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]">

            <button class="rounded-2xl px-5 py-3 font-extrabold text-sm text-white shadow-md"
                    style="background: linear-gradient(135deg, {{ $accent }}, {{ $accentSoft }});">
                Search
            </button>
        </form>
    </div>

    {{-- Tabs --}}
    <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.listings.verify', ['tab'=>'pending','q'=>$q]) }}"
           class="px-4 py-2 rounded-2xl border text-sm font-extrabold transition
           {{ $tab==='pending' ? 'text-white shadow-md border-transparent' : 'bg-white/80 border-[#F3CACA] hover:bg-white text-[#9A5E5E]' }}"
           @if($tab==='pending') style="background: linear-gradient(135deg, #DC2626, #B91C1C);" @endif>
            Pending ({{ $pendingCount }})
        </a>

        <a href="{{ route('admin.listings.verify', ['tab'=>'approved','q'=>$q]) }}"
           class="px-4 py-2 rounded-2xl border text-sm font-extrabold transition
           {{ $tab==='approved' ? 'text-white shadow-md border-transparent' : 'bg-white/80 border-[#F3CACA] hover:bg-white text-[#9A5E5E]' }}"
           @if($tab==='approved') style="background: linear-gradient(135deg, #22C55E, #16A34A);" @endif>
            Approved ({{ $approvedCount }})
        </a>

        <a href="{{ route('admin.listings.verify', ['tab'=>'rejected','q'=>$q]) }}"
           class="px-4 py-2 rounded-2xl border text-sm font-extrabold transition
           {{ $tab==='rejected' ? 'text-white shadow-md border-transparent' : 'bg-white/80 border-[#F3CACA] hover:bg-white text-[#9A5E5E]' }}"
           @if($tab==='rejected') style="background: linear-gradient(135deg, #EF4444, #DC2626);" @endif>
            Rejected ({{ $rejectedCount }})
        </a>

        <a href="{{ route('admin.listings.verify', ['tab'=>'all','q'=>$q]) }}"
           class="px-4 py-2 rounded-2xl border text-sm font-extrabold transition
           {{ $tab==='all' ? 'text-white shadow-md border-transparent' : 'bg-white/80 border-[#F3CACA] hover:bg-white text-[#9A5E5E]' }}"
           @if($tab==='all') style="background: linear-gradient(135deg, #7F1D1D, #991B1B);" @endif>
            All
        </a>
    </div>

    {{-- List --}}
    <div class="mt-6 space-y-4">
        @forelse($rooms as $room)
            @php
                $cover = null;
                if (!empty($room->cover_image)) {
                    $cover = asset('storage/'.$room->cover_image);
                } elseif (isset($room->images) && $room->images->count()) {
                    $img = $room->images->firstWhere('is_cover', true) ?? $room->images->first();
                    $cover = $img ? asset('storage/'.$img->path) : null;
                }
            @endphp

            <div class="rounded-3xl border border-[#F3D2D2] p-5 shadow-sm"
                 style="background: rgba(255,255,255,0.78); backdrop-filter: blur(6px);">

                <div class="flex flex-col md:flex-row gap-4">

                    <div class="w-full md:w-56">
                        <div class="rounded-2xl overflow-hidden border border-[#F3D2D2] bg-white aspect-[4/3] shadow-sm">
                            @if($cover)
                                <img src="{{ $cover }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-[#B98B8B] text-sm font-semibold">
                                    No Image
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1">

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <div class="text-xl font-black text-[#3B0A0A]">
                                    {{ $room->title }}
                                </div>

                                <div class="text-sm mt-1 font-semibold text-[#9A5E5E]">
                                    {{ $room->address ?? '-' }},
                                    {{ $room->city ?? '-' }},
                                    {{ $room->state ?? '-' }}
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-sm font-semibold text-[#B98B8B]">Price</div>
                                <div class="text-xl font-black text-[#2D1414]">
                                    RM {{ number_format($room->price_monthly ?? 0) }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2 items-center">

                            <span class="px-3 py-1 rounded-full text-xs font-extrabold border
                                @if($room->verification_status==='pending') bg-yellow-50 text-yellow-800 border-yellow-200
                                @elseif($room->verification_status==='approved') bg-green-50 text-green-800 border-green-200
                                @else bg-red-50 text-red-800 border-red-200 @endif">
                                {{ strtoupper($room->verification_status) }}
                            </span>

                            <span class="px-3 py-1 rounded-full text-xs font-extrabold border bg-white border-[#F3D2D2] text-[#7B4A4A]">
                                {{ strtoupper($room->room_type ?? '-') }}
                            </span>

                            <span class="px-3 py-1 rounded-full text-xs font-extrabold border bg-white border-[#F3D2D2] text-[#7B4A4A]">
                                Gender: {{ ucfirst($room->gender_preference ?? 'any') }}
                            </span>

                            <span class="px-3 py-1 rounded-full text-xs font-extrabold border bg-white border-[#F3D2D2] text-[#7B4A4A]">
                                Landlord: {{ optional($room->landlord)->name ?? '—' }}
                            </span>
                        </div>

                        @if($room->verification_status === 'rejected' && $room->verification_reason)
                            <div class="mt-3 rounded-2xl p-3 text-sm border border-red-200 bg-red-50 text-red-800">
                                <span class="font-extrabold">Rejected reason:</span> {{ $room->verification_reason }}
                            </div>
                        @endif

                        @if($room->verification_status === 'pending')
                            <div class="mt-4 flex flex-wrap gap-2">

                                <form method="POST" action="{{ route('admin.listings.approve', $room) }}">
                                    @csrf
                                    <button class="rounded-2xl px-5 py-2.5 font-extrabold text-sm text-white shadow-md hover:scale-105 transition"
                                            style="background: linear-gradient(135deg,#22C55E,#16A34A);">
                                        Approve
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.listings.reject', $room) }}" class="flex flex-wrap gap-2">
                                    @csrf
                                    <input name="reason" required
                                           class="rounded-2xl px-4 py-2.5 border border-[#F0CFCF] bg-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]"
                                           placeholder="Reject reason (required)...">

                                    <button class="rounded-2xl px-5 py-2.5 font-extrabold text-sm text-white shadow-md hover:scale-105 transition"
                                            style="background: linear-gradient(135deg,#EF4444,#DC2626);">
                                        Reject
                                    </button>
                                </form>

                            </div>
                        @else
                            <div class="mt-4 text-sm font-semibold text-[#B98B8B]">
                                No action needed for this status.
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        @empty
            <div class="rounded-3xl p-10 text-center border border-dashed border-[#F0CFCF] bg-white/60 text-[#B98B8B] font-semibold">
                No rooms found.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $rooms->links() }}
    </div>
</div>
@endsection