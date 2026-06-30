@extends('layouts.student')

@section('title', 'Browse Rooms • Smart Rental')
@section('page_title', 'Browse Rooms')
@section('page_subtitle', 'Compare price, type, and location — pick your best match.')

@section('top_actions')
    <a href="{{ route('student.dashboard') }}"
       class="sr-outline rounded-2xl px-5 py-3 text-sm font-extrabold">
        Back
    </a>
@endsection

@section('content')

    <!-- FILTER BAR -->
    <div class="sr-card rounded-3xl p-5 md:p-6 mb-6">
        <form method="GET" action="{{ route('student.rooms.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-5">
                <input name="q" value="{{ $q ?? '' }}" class="sr-input" placeholder="Search title / city / state">
            </div>

            <div class="md:col-span-3">
                <select name="room_type" class="sr-select">
                    <option value="">Room Type (Any)</option>
                    <option value="single" {{ ($roomType ?? '')==='single'?'selected':'' }}>Single</option>
                    <option value="shared" {{ ($roomType ?? '')==='shared'?'selected':'' }}>Shared</option>
                    <option value="master" {{ ($roomType ?? '')==='master'?'selected':'' }}>Master</option>
                    <option value="studio" {{ ($roomType ?? '')==='studio'?'selected':'' }}>Studio</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <input name="max_price" value="{{ $maxPrice ?? '' }}" class="sr-input" placeholder="Max RM">
            </div>

            <div class="md:col-span-2">
                <button class="sr-btn w-full rounded-2xl px-5 py-3 font-extrabold">Apply</button>
            </div>

            <div class="md:col-span-3">
                <select name="gender" class="sr-select">
                    <option value="">Gender (Any)</option>
                    <option value="any" {{ ($gender ?? '')==='any'?'selected':'' }}>Any</option>
                    <option value="male" {{ ($gender ?? '')==='male'?'selected':'' }}>Male</option>
                    <option value="female" {{ ($gender ?? '')==='female'?'selected':'' }}>Female</option>
                </select>
            </div>

            <div class="md:col-span-9 flex items-center gap-2">
                <a href="{{ route('student.rooms.index') }}" class="sr-outline rounded-2xl px-5 py-3 font-extrabold text-center">
                    Reset
                </a>
                <div class="sr-muted text-sm">
                    Showing <b>{{ $rooms->total() }}</b> available listings
                </div>
            </div>
        </form>
    </div>

    <!-- LIST -->
    @if($rooms->count() === 0)
        <div class="sr-card rounded-3xl p-10 text-center">
            <div class="text-xl font-extrabold">No rooms found</div>
            <div class="sr-muted mt-2">Try different keywords or increase your budget.</div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($rooms as $room)
                @php
                    $img = $room->cover_image ? asset('storage/'.$room->cover_image) : 'https://placehold.co/800x600?text=Room';
                @endphp

                <a href="{{ route('student.rooms.show', $room) }}"
                   class="sr-card rounded-3xl overflow-hidden hover:shadow-[0_30px_90px_rgba(0,0,0,.10)] transition">
                    <div class="h-44 w-full">
                        <img src="{{ $img }}" alt="cover" class="h-full w-full object-cover">
                    </div>

                    <div class="p-5">
                        <div class="font-extrabold text-lg line-clamp-1">{{ $room->title }}</div>
                        <div class="sr-muted text-sm mt-1 line-clamp-1">
                            {{ ucfirst($room->room_type) }} • {{ $room->city }}, {{ $room->state }}
                        </div>

                        <div class="mt-4 flex items-end justify-between gap-3">
                            <div>
                                <div class="sr-muted text-xs">Monthly</div>
                                <div class="text-2xl font-extrabold">RM {{ number_format($room->price_monthly, 0) }}</div>
                            </div>

                            <div class="sr-outline rounded-2xl px-4 py-2 font-extrabold">
                                View
                            </div>
                        </div>

                        <div class="mt-3 sr-muted text-xs">
                            {{ $room->distance_km ? $room->distance_km.' km to MSU' : 'Near MSU' }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $rooms->links() }}
        </div>
    @endif

@endsection
