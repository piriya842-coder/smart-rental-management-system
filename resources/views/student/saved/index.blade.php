@extends('layouts.student')

@section('title', 'Saved Rooms • Smart Rental')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;

  // ✅ UPDATED COLORS (landing page theme)
  $gold    = '#c92a2a';
  $cream   = '#fffafa';
  $choco   = '#4a2c2a';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';

  $heroFallback = asset('images/slider/slide1.jpg');

  // helpers (same logic as your rooms page)
  $roomImageUrl = function ($room) use ($heroFallback) {
    if (isset($room->cover_image) && $room->cover_image) {
      return str_starts_with($room->cover_image, 'http')
        ? $room->cover_image
        : Storage::url($room->cover_image);
    }

    if (isset($room->images) && $room->images && count($room->images)) {
      $img = $room->images[0];
      if (isset($img->path) && $img->path) return Storage::url($img->path);
      if (isset($img->image_path) && $img->image_path) return Storage::url($img->image_path);
      if (isset($img->url) && $img->url) return $img->url;
    }

    return $heroFallback;
  };

  $roomPrice = function ($room) {
    foreach (['price_monthly','price','monthly_rent','rent','amount'] as $col) {
      if (isset($room->$col) && $room->$col !== null && is_numeric($room->$col)) return (float)$room->$col;
    }
    return null;
  };

  $roomLocation = function ($room) {
    foreach (['location','area','address','city'] as $col) {
      if (isset($room->$col) && $room->$col) return (string)$room->$col;
    }
    return 'Near MSU';
  };

  $roomType = function ($room) {
    foreach (['type','room_type'] as $col) {
      if (isset($room->$col) && $room->$col) return (string)$room->$col;
    }
    return 'Room';
  };

  $titleCase = function ($value) {
    $v = trim((string)$value);
    if ($v === '') return $v;
    return ucwords(str_replace(['_', '-'], ' ', $v));
  };

  $roomDistanceKm = function ($room) {
    foreach (['distance_km','distance_to_msu_km','msu_distance_km'] as $col) {
      if (isset($room->$col) && $room->$col !== null && is_numeric($room->$col)) return (float) $room->$col;
    }
    return null;
  };

  $roomGender = function ($room) {
    foreach (['gender_preference','gender','preferred_gender'] as $col) {
      if (isset($room->$col) && $room->$col) return strtolower(trim((string)$room->$col));
    }
    return 'any';
  };

  $roomFacilities = function ($room) {
    if (isset($room->facilities) && $room->facilities) {
      try {
        $items = collect($room->facilities)->map(function($f){
          return $f->name ?? $f->title ?? $f->facility ?? null;
        })->filter()->values()->all();
        if (count($items)) return $items;
      } catch (\Throwable $e) {}
    }

    foreach (['facilities','amenities','facility'] as $col) {
      if (!isset($room->$col) || !$room->$col) continue;

      $raw = $room->$col;

      if (is_array($raw)) return array_values(array_filter($raw));
      if ($raw instanceof \Illuminate\Support\Collection) return $raw->filter()->values()->all();

      $decoded = null;
      try { $decoded = json_decode((string)$raw, true); } catch (\Throwable $e) {}
      if (is_array($decoded)) return array_values(array_filter(array_map('trim', $decoded)));

      $parts = array_values(array_filter(array_map('trim', explode(',', (string)$raw))));
      if (count($parts)) return $parts;
    }

    return [];
  };
@endphp

<!-- HEADER -->
<section class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-7 sm:p-9">
  <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold" style="color: {{ $choco }};">Saved Rooms</h1>
      <p class="mt-2 text-gray-700">
        Rooms you bookmarked (❤️). You can remove anytime.
      </p>
    </div>

    <div class="flex items-center gap-3">
      <div class="text-sm font-extrabold px-4 py-2 rounded-xl border border-[rgba(201,42,42,.10)] bg-white"
           style="color: {{ $choco }};">
        Total: {{ $rooms->count() }}
      </div>

      <a href="{{ route('student.rooms.index') }}"
         class="rounded-xl px-4 py-2 text-sm font-extrabold border border-[rgba(201,42,42,.12)] bg-white hover:bg-[#fff5f5] transition"
         style="color: {{ $choco }};">
        Browse Rooms
      </a>
    </div>
  </div>
</section>

<!-- GRID -->
<section class="mt-8">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    @forelse($rooms as $room)
      @php
        $img   = $roomImageUrl($room);
        $price = $roomPrice($room);
        $loc   = $roomLocation($room);
        $type  = $titleCase($roomType($room));
        $dist  = $roomDistanceKm($room);
        $gen   = $roomGender($room);
        $facs  = $roomFacilities($room);

        $title = $room->title ?? $room->name ?? ($type . ' near MSU');
        $facBadges = collect($facs)->map(fn($x) => trim((string)$x))->filter()->take(5)->values();

        $genderLabel = 'Any gender';
        if ($gen === 'male') $genderLabel = 'Male only';
        if ($gen === 'female') $genderLabel = 'Female only';
      @endphp

      <div class="rounded-3xl overflow-hidden border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm hover:shadow-md transition">
        <div class="h-52 bg-cover bg-center relative" style="background-image:url('{{ $img }}');">
          <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-black/10 to-transparent"></div>

          <!-- ❤️ UNSAVE -->
          <form method="POST" action="{{ route('student.bookmarks.toggle', $room->id) }}"
                class="absolute top-4 left-4 z-10">
            @csrf
            <button type="submit"
                    class="h-10 w-10 rounded-full grid place-items-center shadow ring-1 ring-black/10 bg-white/90"
                    title="Remove from saved">
              ❤️
            </button>
          </form>

          @if($price !== null)
            <div class="absolute top-4 right-4 rounded-full px-4 py-2 text-xs font-extrabold text-white shadow"
                 style="background: rgba(201,42,42,.92);">
              RM {{ number_format((float)$price, 0) }} / mo
            </div>
          @endif
        </div>

        <div class="p-6">
          <div class="font-extrabold text-lg" style="color: {{ $choco }};">
            {{ $title }}
          </div>

          <div class="mt-2 text-sm text-gray-700 flex flex-wrap gap-2">
            📍 {{ $loc }} • 🏠 {{ $type }} •
            @if($dist !== null) {{ number_format($dist,1) }} km @else — km @endif
          </div>

          <div class="mt-3 text-xs font-bold px-3 py-2 rounded-full border border-[rgba(201,42,42,.10)]"
               style="background: {{ $softRed }}; color: {{ $choco }};">
            👥 {{ $genderLabel }}
          </div>

          <!-- Facilities -->
          <div class="mt-4">
            <div class="text-xs font-bold text-gray-600">Facilities</div>
            <div class="mt-2 flex flex-wrap gap-2">
              @foreach($facBadges as $f)
                <span class="text-xs font-semibold px-3 py-1 rounded-full border border-[rgba(201,42,42,.10)]"
                      style="background: {{ $softRed }}; color: {{ $choco }};">
                  {{ ucwords(str_replace(['_', '-'], ' ', $f)) }}
                </span>
              @endforeach
            </div>
          </div>

          <!-- Buttons -->
          <div class="mt-5 grid grid-cols-2 gap-3">
            <a href="{{ route('student.rooms.show', $room->id ?? $room) }}"
               class="rounded-xl px-4 py-2 text-sm font-extrabold text-white text-center"
               style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
              View
            </a>

            <form method="POST" action="{{ route('student.bookmarks.toggle', $room->id) }}">
              @csrf
              <button type="submit"
                      class="rounded-xl px-4 py-2 text-sm font-extrabold border border-[rgba(201,42,42,.12)] bg-white hover:bg-[#fff5f5]"
                      style="color: {{ $choco }};">
                Remove
              </button>
            </form>
          </div>
        </div>
      </div>

    @empty
      <div class="md:col-span-3 text-center p-8 bg-white rounded-3xl border border-[rgba(201,42,42,.08)]">
        <div class="font-extrabold text-lg" style="color: {{ $choco }};">No saved rooms yet</div>
        <p class="mt-2 text-gray-600">Go browse and save rooms ❤️</p>
      </div>
    @endforelse

  </div>
</section>
@endsection