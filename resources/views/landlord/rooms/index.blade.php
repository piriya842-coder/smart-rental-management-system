@extends('layouts.landlord')

@section('title', 'My Rooms • Smart Rental')

@section('page_title', 'My Rooms')
@section('page_subtitle', 'Draft stays private. Active is visible to students. Inactive is hidden.')

@section('top_actions')
    <a href="{{ route('landlord.rooms.create') }}"
       class="rounded-2xl px-5 py-3 text-sm font-extrabold inline-flex items-center gap-2 transition hover:-translate-y-[1px]"
       style="background:linear-gradient(135deg,#B91C1C 0%, #DC2626 100%); color:#FFFFFF; border:1px solid #C81E2A; box-shadow:0 12px 24px rgba(185,28,28,.22);">
        <span class="text-lg -mt-[1px]">+</span> Add Room
    </a>
@endsection

@section('content')
    <style>
        .sr-rooms-shell{
            background:
                radial-gradient(circle at top right, rgba(220,38,38,.10), transparent 26%),
                radial-gradient(circle at bottom left, rgba(127,16,27,.07), transparent 22%),
                linear-gradient(135deg, #FFFFFF 0%, #FFF6F7 100%);
            border: 1px solid #F2D7DB;
            box-shadow: 0 18px 44px rgba(127,16,27,.08);
        }

        .sr-head-soft{
            background:
                radial-gradient(circle at top right, rgba(185,28,28,.10), transparent 24%),
                linear-gradient(135deg, #FFFFFF 0%, #FFF2F4 100%);
        }

        .sr-title{
            color:#2A0709;
        }

        .sr-sub{
            color:#8A5B63;
        }

        .sr-row{
            transition: all .22s ease;
        }

        .sr-row:hover{
            background: linear-gradient(135deg, #FFF9FA 0%, #FFF3F5 100%);
        }

        .sr-cover{
            border:1px solid #F2D7DB;
            background:#FFF8F8;
            box-shadow: 0 10px 22px rgba(127,16,27,.08);
        }

        .sr-room-name{
            color:#2A0709;
        }

        .sr-pill{
            background: #FFFFFF;
            color: #8F1721;
            border: 1px solid #F1D4D8;
            box-shadow: 0 6px 14px rgba(127,16,27,.05);
        }

        .sr-pill:hover{
            background:#FFF7F8;
            border-color:#E9B8BF;
        }

        .sr-chip{
            border: 1px solid transparent;
            box-shadow: 0 8px 18px rgba(127,16,27,.06);
        }

        .sr-chip-active{
            background: linear-gradient(135deg, #FFF1F2 0%, #FFE4E6 100%);
            color:#B91C1C;
            border-color:#F3C6CB;
        }

        .sr-chip-draft{
            background: linear-gradient(135deg, #FFF5F5 0%, #FFF0F1 100%);
            color:#991B1B;
            border-color:#F2B8C0;
        }

        .sr-chip-inactive{
            background: linear-gradient(135deg, #FFF8F8 0%, #FFF4F5 100%);
            color:#7C4A52;
            border-color:#F1D4D8;
        }

        .sr-edit-link{
            color:#8F1721;
            text-decoration-color: rgba(185,28,28,.45);
        }

        .sr-edit-link:hover{
            color:#B91C1C;
        }

        .sr-publish-btn{
            background: linear-gradient(135deg, #B91C1C 0%, #DC2626 100%);
            color:#FFFFFF;
            border:1px solid #C81E2A;
            box-shadow: 0 10px 22px rgba(185,28,28,.22);
        }

        .sr-publish-btn:hover{
            filter: brightness(1.04);
            transform: translateY(-1px);
        }

        .sr-unpublish-btn{
            background: #FFFFFF;
            color:#8F1721;
            border:1px solid #F1D4D8;
            box-shadow: 0 8px 18px rgba(127,16,27,.08);
        }

        .sr-unpublish-btn:hover{
            background:#FFF5F6;
            border-color:#E9B8BF;
            transform: translateY(-1px);
        }

        .sr-delete-link{
            color:#B91C1C;
        }

        .sr-delete-link:hover{
            color:#991B1B;
        }

        .sr-empty{
            color:#8A5B63;
        }
    </style>

    <div class="sr-rooms-shell rounded-3xl overflow-hidden">
        <div class="px-6 py-5 flex items-center justify-between border-b sr-divider-soft sr-head-soft">
            <div>
                <div class="text-xl font-extrabold sr-title">All Listings</div>
                <div class="text-sm sr-sub mt-1">Cover • price • status • actions</div>
            </div>
        </div>

        <div class="divide-y sr-divider-soft">
            @forelse($rooms as $room)
                @php
                    $img = $room->cover_image
                        ? asset('storage/'.$room->cover_image)
                        : 'https://placehold.co/120x90?text=Room';

                    $facilityList = $room->facilities;
                    if (is_string($facilityList)) {
                        $decoded = json_decode($facilityList, true);
                        $facilityList = is_array($decoded) ? $decoded : [];
                    }
                    if (!is_array($facilityList)) $facilityList = [];
                @endphp

                <div class="sr-row px-6 py-5 flex items-center justify-between gap-5">
                    <!-- LEFT: cover + info -->
                    <div class="flex items-center gap-4 min-w-0">
                        <img src="{{ $img }}" alt="cover"
                             class="sr-cover h-[72px] w-[110px] rounded-2xl object-cover"/>

                        <div class="min-w-0">
                            <div class="sr-room-name font-extrabold text-lg truncate">{{ $room->title }}</div>

                            <div class="text-sm sr-muted mt-1">
                                {{ ucfirst($room->room_type) }}
                                • {{ $room->city }}
                                • RM {{ number_format($room->price_monthly, 2) }}
                                •
                                @if($room->is_available)
                                    <span class="font-bold" style="color:#B91C1C;">Available</span>
                                @else
                                    <span class="font-bold" style="color:#7C4A52;">Not available</span>
                                @endif
                            </div>

                            @if(count($facilityList))
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach(array_slice($facilityList, 0, 4) as $f)
                                        <span class="sr-pill rounded-2xl px-3 py-1 text-xs font-extrabold">
                                            {{ $f }}
                                        </span>
                                    @endforeach

                                    @if(count($facilityList) > 4)
                                        <span class="sr-pill rounded-2xl px-3 py-1 text-xs font-extrabold">
                                            +{{ count($facilityList)-4 }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- RIGHT: status + actions -->
                    <div class="flex items-center gap-4 shrink-0">
                        @if($room->status === 'active')
                            <span class="sr-chip sr-chip-active rounded-2xl px-4 py-2 text-sm font-extrabold">
                                ACTIVE
                            </span>
                        @elseif($room->status === 'draft')
                            <span class="sr-chip sr-chip-draft rounded-2xl px-4 py-2 text-sm font-extrabold">
                                DRAFT
                            </span>
                        @else
                            <span class="sr-chip sr-chip-inactive rounded-2xl px-4 py-2 text-sm font-extrabold">
                                INACTIVE
                            </span>
                        @endif

                        <a href="{{ route('landlord.rooms.edit', $room->id) }}"
                           class="sr-edit-link font-extrabold underline underline-offset-4">
                            Edit
                        </a>

                        @if($room->status === 'active')
                            <form method="POST" action="{{ route('landlord.rooms.unpublish', $room->id) }}">
                                @csrf
                                <button class="sr-unpublish-btn rounded-xl px-4 py-2 text-sm font-extrabold transition">
                                    Unpublish
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('landlord.rooms.publish', $room->id) }}">
                                @csrf
                                <button class="sr-publish-btn rounded-xl px-4 py-2 text-sm font-extrabold transition">
                                    Publish
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('landlord.rooms.destroy', $room->id) }}"
                              onsubmit="return confirm('Delete this room?');">
                            @csrf
                            @method('DELETE')
                            <button class="sr-delete-link font-extrabold underline underline-offset-4">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-10 text-center sr-empty">
                    No rooms yet. Click “Add Room” to create your first listing.
                    <div class="mt-4">
                        <a href="{{ route('landlord.rooms.create') }}"
                           class="sr-btn rounded-2xl px-6 py-3 font-extrabold inline-flex items-center gap-2">
                            <span class="text-lg -mt-[1px]">+</span> Add Room
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection