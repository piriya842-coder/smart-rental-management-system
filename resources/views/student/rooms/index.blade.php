@extends('layouts.student')

@section('title', 'Browse Rooms • Smart Rental')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;

  // landing-page style colors
  $gold  = '#c92a2a';
  $cream = '#fffafa';
  $choco = '#4a2c2a';
  $softRed = '#fdf2f2';
  $redDark = '#a61e1e';

  $heroFallback = asset('images/slider/slide1.jpg');

  $p = $meta['params'] ?? [];
  $hasFilters = $meta['hasFilters'] ?? false;
  $count = $meta['count'] ?? 0;
  $note  = $meta['note'] ?? null;

  $showForm = (!$hasFilters) || (request()->boolean('edit'));

  $roomImageUrl = function ($room) use ($heroFallback) {
    if (isset($room->cover_image) && $room->cover_image) {
      $path = (string) $room->cover_image;

      if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;

      return Storage::url($path);
    }

    if (isset($room->images) && $room->images && count($room->images)) {
      $img = $room->images->firstWhere('is_cover', 1) ?? $room->images[0];
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
    foreach (['address','city','area'] as $col) {
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

  $roomGender = function ($room) {
    foreach (['gender_preference','gender','preferred_gender'] as $col) {
      if (isset($room->$col) && $room->$col) return strtolower(trim((string)$room->$col));
    }
    return 'any';
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

  // fixed facilities parsing (same idea as dashboard)
  $roomFacilities = function ($room) {
    if (isset($room->facilities) && $room->facilities) {
      try {
        if (is_array($room->facilities) || $room->facilities instanceof \Illuminate\Support\Collection) {
          $items = collect($room->facilities)->map(function($f){
            return $f->name ?? $f->title ?? $f->facility ?? (is_string($f) ? $f : null);
          })->filter()->values()->all();

          if (count($items)) return $items;
        }
      } catch (\Throwable $e) {}
    }

    foreach (['facilities','amenities','facility'] as $col) {
      if (!isset($room->$col) || !$room->$col) continue;

      $raw = $room->$col;

      if (is_array($raw)) return array_values(array_filter(array_map(fn($v) => trim((string)$v), $raw)));

      if ($raw instanceof \Illuminate\Support\Collection) {
        return $raw->map(function($f){
          return is_object($f)
            ? ($f->name ?? $f->title ?? $f->facility ?? null)
            : trim((string)$f);
        })->filter()->values()->all();
      }

      $decoded = null;
      try { $decoded = json_decode((string)$raw, true); } catch (\Throwable $e) {}

      if (is_array($decoded)) {
        return array_values(array_filter(array_map(fn($v) => trim((string)$v), $decoded)));
      }

      $parts = array_values(array_filter(array_map('trim', explode(',', (string)$raw))));
      if (count($parts)) return $parts;
    }

    return [];
  };

  $editFiltersUrl = route('student.rooms.index', array_merge(request()->query(), ['edit' => 1]));
@endphp

<section class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-7 sm:p-9">
  <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold" style="color: {{ $choco }};">Browse Rooms</h1>
      <p class="mt-2 text-gray-700">
        All verified rooms uploaded by landlords
        @if($hasFilters)
          <span class="font-semibold">(filtered by your preferences)</span>.
        @else
          <span class="font-semibold">(showing all rooms)</span>.
        @endif
      </p>

      @if($note)
        <div class="mt-3 inline-flex items-center gap-2 text-xs font-bold px-3 py-2 rounded-xl border border-[rgba(201,42,42,.12)]"
             style="color: {{ $choco }}; background: {{ $softRed }};">
          <span>⭐</span>
          <span>{{ $note }}</span>
        </div>
      @endif
    </div>

    <div class="flex items-center gap-3">
      <div class="text-sm font-extrabold px-4 py-2 rounded-xl border border-[rgba(201,42,42,.10)] bg-white"
           style="color: {{ $choco }};">
        Showing {{ $count }} result{{ $count === 1 ? '' : 's' }}
      </div>

      @if($hasFilters)
        <a href="{{ $editFiltersUrl }}"
           class="rounded-xl px-4 py-2 text-sm font-extrabold border border-[rgba(201,42,42,.12)] bg-white hover:bg-[#fff5f5] transition"
           style="color: {{ $choco }};">
          Edit Filters
        </a>
      @endif
    </div>
  </div>

  @if($showForm)
    <form method="GET" action="{{ route('student.rooms.index') }}"
          class="mt-7 grid grid-cols-1 md:grid-cols-12 gap-4">

      <div class="md:col-span-4">
        <label class="text-sm font-semibold text-gray-700">Location</label>
        <input name="location" value="{{ $p['location'] ?? '' }}" placeholder="e.g. Seksyen 7 / MSU Gate / Shah Alam"
               class="mt-2 w-full rounded-2xl border border-[rgba(201,42,42,.10)] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]"
               style="background: {{ $softRed }};" />
      </div>

      <div class="md:col-span-2">
        <label class="text-sm font-semibold text-gray-700">Room Type</label>
        <select name="type"
                class="mt-2 w-full rounded-2xl border border-[rgba(201,42,42,.10)] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]"
                style="background: {{ $softRed }};">
          @php $typeVal = strtolower(trim($p['type'] ?? '')); @endphp
          <option value="" {{ $typeVal==='' ? 'selected' : '' }}>Any</option>
          <option value="Single" {{ $typeVal==='single' ? 'selected' : '' }}>Single</option>
          <option value="Shared" {{ $typeVal==='shared' ? 'selected' : '' }}>Shared</option>
          <option value="Studio" {{ $typeVal==='studio' ? 'selected' : '' }}>Studio</option>
          <option value="Master" {{ $typeVal==='master' ? 'selected' : '' }}>Master</option>
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="text-sm font-semibold text-gray-700">Max Budget (RM)</label>
        <input name="budget" value="{{ $p['budget'] ?? '' }}" placeholder="e.g. 550"
               class="mt-2 w-full rounded-2xl border border-[rgba(201,42,42,.10)] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]"
               style="background: {{ $softRed }};" />
      </div>

      <div class="md:col-span-2">
        <label class="text-sm font-semibold text-gray-700">Gender</label>
        <select name="gender"
                class="mt-2 w-full rounded-2xl border border-[rgba(201,42,42,.10)] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]"
                style="background: {{ $softRed }};">
          @php $gVal = strtolower(trim($p['gender'] ?? '')); @endphp
          <option value="" {{ $gVal==='' ? 'selected' : '' }}>Any</option>
          <option value="male" {{ $gVal==='male' ? 'selected' : '' }}>Male Only</option>
          <option value="female" {{ $gVal==='female' ? 'selected' : '' }}>Female Only</option>
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="text-sm font-semibold text-gray-700">Sort</label>
        <select name="sort"
                class="mt-2 w-full rounded-2xl border border-[rgba(201,42,42,.10)] px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgba(201,42,42,.10)]"
                style="background: {{ $softRed }};">
          @php $sVal = strtolower(trim($p['sort'] ?? 'latest')); @endphp
          <option value="latest" {{ $sVal==='latest' ? 'selected' : '' }}>Newest</option>
          <option value="recommend" {{ $sVal==='recommend' ? 'selected' : '' }}>Recommended</option>
          <option value="nearest" {{ $sVal==='nearest' ? 'selected' : '' }}>Nearest</option>
          <option value="price_low" {{ $sVal==='price_low' ? 'selected' : '' }}>Price: Low to High</option>
          <option value="price_high" {{ $sVal==='price_high' ? 'selected' : '' }}>Price: High to Low</option>
        </select>
      </div>

      <div class="md:col-span-12 flex flex-col sm:flex-row gap-3 mt-2">
        <button type="submit"
                class="w-full sm:w-auto rounded-xl px-6 py-3 text-sm font-extrabold text-white shadow-sm transition"
                style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);"
                onmouseover="this.style.filter='brightness(0.92)'"
                onmouseout="this.style.filter='brightness(1)'">
          Search
        </button>

        <a href="{{ route('student.rooms.index') }}"
           class="w-full sm:w-auto rounded-xl px-6 py-3 text-sm font-extrabold text-center border border-[rgba(201,42,42,.12)] bg-white hover:bg-[#fff5f5] transition"
           style="color: {{ $choco }};">
          Reset
        </a>
      </div>
    </form>
  @endif
</section>

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

        $genderLabel = 'Any Gender';
        if ($gen === 'male') $genderLabel = 'Male Only';
        if ($gen === 'female') $genderLabel = 'Female Only';

        $isSaved = (int)($room->is_saved ?? 0) > 0;
      @endphp

      <div class="rounded-3xl overflow-hidden border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm hover:shadow-md transition">
        <div class="h-52 bg-cover bg-center relative" style="background-image:url('{{ $img }}');">
          <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-black/10 to-transparent"></div>

          <form method="POST" action="{{ route('student.bookmarks.toggle', $room->id) }}"
                class="absolute top-4 left-4 z-10">
            @csrf
            <button type="submit"
              class="h-10 w-10 rounded-full grid place-items-center shadow ring-1 ring-black/10 bg-white/90 hover:bg-white transition"
              title="{{ $isSaved ? 'Remove from saved' : 'Save room' }}">
              <span style="font-size:18px; line-height:1;">
                {!! $isSaved ? '❤️' : '🤍' !!}
              </span>
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
          <div class="font-extrabold text-lg leading-snug" style="color: {{ $choco }};">
            {{ $title }}
          </div>

          <div class="mt-2 text-sm text-gray-700 flex flex-wrap items-center gap-x-2 gap-y-1">
            <span class="inline-flex items-center gap-1">📍 <span>{{ $loc }}</span></span>
            <span class="opacity-50">•</span>
            <span class="inline-flex items-center gap-1">🏠 <span>{{ $type }}</span></span>
            <span class="opacity-50">•</span>
            <span class="inline-flex items-center gap-1">
              🧭
              @if($dist !== null)
                <span>{{ number_format($dist, 1) }} km to MSU</span>
              @else
                <span>— km to MSU</span>
              @endif
            </span>
          </div>

          <div class="mt-3 inline-flex items-center gap-2 text-xs font-bold px-3 py-2 rounded-full border border-[rgba(201,42,42,.12)]"
               style="color: {{ $choco }}; background: {{ $softRed }};">
            👥 {{ $genderLabel }}
          </div>

          <div class="mt-4">
            <div class="text-xs font-bold text-gray-600">Facilities</div>

            @if($facBadges->count())
              <div class="mt-2 flex flex-wrap gap-2">
                @foreach($facBadges as $f)
                  <span class="text-xs font-semibold px-3 py-1 rounded-full border border-[rgba(201,42,42,.10)]"
                        style="background: {{ $softRed }}; color: {{ $choco }};">
                    {{ ucwords(str_replace(['_', '-'], ' ', $f)) }}
                  </span>
                @endforeach
              </div>
            @else
              <div class="mt-2 text-sm text-gray-500">Facilities not listed.</div>
            @endif
          </div>

          <div class="mt-5">
            <a href="{{ route('student.rooms.show', $room->id ?? $room) }}"
               class="block w-full rounded-xl px-4 py-2 text-sm font-extrabold text-white text-center transition"
               style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);"
               onmouseover="this.style.filter='brightness(0.92)'"
               onmouseout="this.style.filter='brightness(1)'">
              View Details
            </a>
          </div>
        </div>
      </div>

    @empty
      <div class="md:col-span-3 rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-8">
        <div class="text-lg font-extrabold" style="color: {{ $choco }};">No rooms found</div>
        <p class="mt-2 text-gray-700">
          Try changing budget / gender / type, or choose “Recommended” to see best matches.
        </p>
        <div class="mt-5">
          <a href="{{ route('student.rooms.index') }}"
             class="inline-flex rounded-xl px-6 py-3 text-sm font-extrabold text-white"
             style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
            Reset Filters
          </a>
        </div>
      </div>
    @endforelse
  </div>
</section>

<a href="{{ route('student.messages.index') }}"
   class="fixed bottom-6 right-6 h-14 w-14 rounded-full text-white grid place-items-center shadow-lg hover:scale-[1.03] transition z-30 ring-1 ring-black/10"
   style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);"
   title="Messages">
  💬
</a>
@endsection