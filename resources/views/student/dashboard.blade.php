{{-- resources/views/student/dashboard.blade.php --}}
@extends('layouts.student')

@section('title', 'Student Home • Smart Rental')

@section('content')
@php
  use Illuminate\Support\Facades\Schema;
  use Illuminate\Support\Facades\Storage;

  $hero = asset('images/slider/slide1.jpg');

  // Landing-page style colors
  $primary = '#D62828';      // main red
  $primaryDark = '#B91C1C';  // darker red
  $soft = '#FFF5F5';         // soft red background
  $textDark = '#3B1F1F';     // dark text
  $muted = '#FDECEC';        // muted light red

  // MSU Shah Alam (approx). Used ONLY if room has lat/lng.
  $msuLat = 3.0738;
  $msuLng = 101.4952;

  $RoomModel = class_exists(\App\Models\Room::class) ? \App\Models\Room::class : null;

  $featuredRooms = collect();
if ($RoomModel) {
  $q = $RoomModel::query();

  if (Schema::hasColumn('rooms', 'is_published')) {
    $q->where('is_published', 1);
  } elseif (Schema::hasColumn('rooms', 'status')) {
    $q->whereIn('status', ['published', 'active', 1]);
  }

  // ✅ show ONLY approved listings
  if (Schema::hasColumn('rooms', 'verification_status')) {
    $q->where('verification_status', 'approved');
  }

  if (method_exists($RoomModel, 'images')) $q->with('images');
  if (method_exists($RoomModel, 'facilities')) $q->with('facilities');

  $featuredRooms = $q->latest()->take(3)->get();
}

  // ✅ saved ids (for heart state)
  $savedIds = [];
  try {
    if (auth()->check() && auth()->user() && method_exists(auth()->user(), 'savedRooms')) {
      $savedIds = auth()->user()->savedRooms()->pluck('rooms.id')->toArray();
    }
  } catch (\Throwable $e) {
    $savedIds = [];
  }

  // ---------- helpers ----------
  $roomImageUrl = function ($room) use ($hero) {
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

    return $hero;
  };

  $roomPrice = function ($room) {
    foreach (['price_monthly','price', 'monthly_rent', 'rent', 'amount'] as $col) {
      if (isset($room->$col) && $room->$col !== null) return $room->$col;
    }
    return null;
  };

  $roomLocation = function ($room) {
    foreach (['location', 'area', 'address'] as $col) {
      if (isset($room->$col) && $room->$col) return $room->$col;
    }
    return 'Near MSU';
  };

  $roomType = function ($room) {
    foreach (['room_type', 'type'] as $col) {
      if (isset($room->$col) && $room->$col) return $room->$col;
    }
    return 'Room';
  };

  $titleCase = function ($value) {
    $v = trim((string)$value);
    if ($v === '') return $v;
    return ucwords(str_replace(['_', '-'], ' ', $v));
  };

  // Distance
  $roomDistanceKm = function ($room) use ($msuLat, $msuLng) {
    foreach (['distance_km', 'distance_to_msu_km', 'msu_distance_km'] as $col) {
      if (isset($room->$col) && $room->$col !== null && is_numeric($room->$col)) return (float)$room->$col;
    }

    $lat = null; $lng = null;
    foreach (['latitude', 'lat'] as $c)  if (isset($room->$c) && is_numeric($room->$c)) $lat = (float)$room->$c;
    foreach (['longitude', 'lng', 'lon'] as $c) if (isset($room->$c) && is_numeric($room->$c)) $lng = (float)$room->$c;
    if ($lat === null || $lng === null) return null;

    $toRad = fn($deg) => $deg * (M_PI / 180);
    $R = 6371;
    $dLat = $toRad($msuLat - $lat);
    $dLng = $toRad($msuLng - $lng);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos($toRad($lat)) * cos($toRad($msuLat)) *
         sin($dLng/2) * sin($dLng/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $R * $c;
  };

  // Facilities
  $roomFacilities = function ($room) {
    if (isset($room->facilities) && $room->facilities) {
      try {
        $items = collect($room->facilities)->map(function($f){
          return $f->name ?? $f->title ?? $f->facility ?? null;
        })->filter()->values()->all();
        if (count($items)) return $items;
      } catch (\Throwable $e) {}
    }

    foreach (['facilities', 'amenities', 'facility'] as $col) {
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

  // Announcement popup
  $latestAnnouncement = null;
  try {
    if (class_exists(\App\Models\Announcement::class)) {
      $latestAnnouncement = \App\Models\Announcement::query()
        ->where('is_active', 1)
        ->latest()
        ->first();
    }
  } catch (\Throwable $e) {
    $latestAnnouncement = null;
  }
@endphp

@if($latestAnnouncement)
  @php
    $aTitle = $latestAnnouncement->title ?? 'Announcement';
    $aBody  = $latestAnnouncement->message ?? '';
    $aTime  = $latestAnnouncement->created_at ?? null;
    $annId  = $latestAnnouncement->id ?? null;
  @endphp

  <div id="annOverlay" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

    <div class="relative min-h-full flex items-start justify-center px-4 pt-24 pb-8">
      <div class="w-full max-w-sm rounded-3xl border border-black/10 bg-white shadow-2xl overflow-hidden">

        <div class="px-5 py-4 border-b border-black/5"
             style="background: linear-gradient(90deg, rgba(214,40,40,.12), rgba(255,245,245,1));">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="text-[11px] font-black tracking-wider text-black/55 flex items-center gap-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl"
                      style="background: rgba(214,40,40,.15);">📣</span>
                ANNOUNCEMENT
              </div>

              <div class="mt-2 font-extrabold text-base leading-snug" style="color: {{ $textDark }};">
                {{ $aTitle }}
              </div>

              @if($aTime)
                <div class="mt-1 text-xs text-black/50">
                  {{ \Carbon\Carbon::parse($aTime)->format('d M Y, h:i A') }}
                </div>
              @endif
            </div>

            <button type="button" id="annCloseBtn"
                    class="shrink-0 h-10 w-10 rounded-2xl border border-black/10 bg-white hover:bg-black/5 transition grid place-items-center"
                    aria-label="Close announcement">✕</button>
          </div>
        </div>

        <div class="px-5 py-5">
          <div class="text-sm text-black/75 leading-relaxed whitespace-pre-line">
            {{ $aBody }}
          </div>

          <div class="mt-4 flex justify-end">
            <button type="button" id="annOkBtn"
                    class="rounded-2xl px-5 py-2.5 text-xs font-extrabold text-white shadow-sm transition"
                    style="background: {{ $primary }};"
                    onmouseover="this.style.filter='brightness(0.92)'"
                    onmouseout="this.style.filter='brightness(1)'">
              Okay
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
    (function () {
      const annId = @json($annId);
      if (!annId) return;

      const overlay = document.getElementById('annOverlay');
      const closeBtn = document.getElementById('annCloseBtn');
      const okBtn = document.getElementById('annOkBtn');

      const key = 'sr_ann_dismiss_' + annId;
      const ONE_DAY_MS = 24 * 60 * 60 * 1000;

      const hideNow = () => {
        try { localStorage.setItem(key, String(Date.now())); } catch(e) {}
        overlay.classList.add('hidden');
      };

      try {
        const last = localStorage.getItem(key);
        const lastTs = last ? parseInt(last, 10) : 0;

        if (!lastTs || (Date.now() - lastTs) > ONE_DAY_MS) {
          overlay.classList.remove('hidden');
        }

        closeBtn.addEventListener('click', hideNow);
        okBtn.addEventListener('click', hideNow);

      } catch (e) {
        overlay.classList.remove('hidden');
        closeBtn.addEventListener('click', () => overlay.classList.add('hidden'));
        okBtn.addEventListener('click', () => overlay.classList.add('hidden'));
      }
    })();
  </script>
@endif

<!-- HERO -->
<section class="relative">
  <div class="relative overflow-hidden rounded-3xl shadow-xl border border-black/5">
    <div class="h-[420px] sm:h-[480px] bg-cover bg-center"
         style="background-image:url('{{ $hero }}')"></div>

    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/45 to-black/20"></div>

    <div class="absolute inset-0 flex items-center">
      <div class="px-6 sm:px-12 lg:px-14 max-w-2xl">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold text-white ring-1 ring-white/20">
          Smart Rental • MSU Student Housing
        </div>

        <h1 class="mt-5 text-4xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
          Find your next student home with confidence.
        </h1>

        <p class="mt-4 text-white/85 text-base sm:text-lg leading-relaxed">
          Browse verified listings, compare key details, and shortlist rooms that match your needs —
          designed for <span class="font-semibold text-white">MSU students</span>.
        </p>

        <div class="mt-7 flex flex-col sm:flex-row gap-3">
          <a href="{{ route('student.rooms.index') }}"
             class="rounded-xl px-6 py-3 text-sm font-bold text-white transition text-center shadow"
             style="background: {{ $primary }};"
             onmouseover="this.style.filter='brightness(0.92)'"
             onmouseout="this.style.filter='brightness(1)'">
            View Rooms
          </a>

          <a href="{{ route('student.help') }}"
             class="rounded-xl bg-white/10 px-6 py-3 text-sm font-bold text-white ring-1 ring-white/25 hover:bg-white/15 transition text-center">
            Learn More
          </a>
        </div>
      </div>
    </div>

    <div class="absolute -bottom-1 left-0 right-0">
      <svg viewBox="0 0 1440 120" class="w-full h-[70px] sm:h-[90px]">
        <path fill="{{ $soft }}" fill-opacity="1"
              d="M0,64L60,64C120,64,240,64,360,69.3C480,75,600,85,720,80C840,75,960,53,1080,48C1200,43,1320,53,1380,58.7L1440,64L1440,120L1380,120C1320,120,1200,120,1080,120C960,120,840,120,720,120C600,120,480,120,360,120C240,120,120,120,60,120L0,120Z"></path>
      </svg>
    </div>
  </div>
</section>

<!-- SEARCH -->
<section class="mt-8">
  <div class="rounded-3xl border border-black/5 bg-white/90 shadow-sm p-7 sm:p-9">
    <div>
      <h2 class="text-2xl sm:text-3xl font-extrabold" style="color: {{ $textDark }};">
        Search verified rooms near MSU
      </h2>
      <p class="mt-2 text-gray-700">
        Filter by location, room type, budget, and gender preference.
      </p>
    </div>

    <form method="GET" action="{{ route('student.rooms.index') }}" class="mt-7 grid grid-cols-1 md:grid-cols-12 gap-4">
      <div class="md:col-span-5">
        <label class="text-sm font-semibold text-gray-700">Location</label>
        <input name="location" placeholder="e.g. MSU / Shah Alam / Section 13"
               class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-100"
               style="background: {{ $soft }};" />
      </div>

      <div class="md:col-span-3">
        <label class="text-sm font-semibold text-gray-700">Room Type</label>
        <select name="type"
                class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-100"
                style="background: {{ $soft }};">
          <option value="">Any</option>
          <option value="Single">Single</option>
          <option value="Shared">Shared</option>
          <option value="Studio">Studio</option>
          <option value="Master">Master</option>
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="text-sm font-semibold text-gray-700">Max Budget (RM)</label>
        <input name="budget" placeholder="e.g. 450"
               class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-100"
               style="background: {{ $soft }};" />
      </div>

      <div class="md:col-span-2">
        <label class="text-sm font-semibold text-gray-700">Gender</label>
        <select name="gender"
                class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-100"
                style="background: {{ $soft }};">
          <option value="">Any</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
        </select>
      </div>

      <div class="md:col-span-12 flex flex-col lg:flex-row lg:items-end gap-4 mt-2">
        <div class="lg:ml-auto w-full lg:w-72">
          <label class="text-sm font-semibold text-gray-700">Sort</label>
          <select name="sort"
                  class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-100"
                  style="background: {{ $soft }};">
            <option value="latest">Latest</option>
            <option value="price_asc">Lowest price</option>
            <option value="price_desc">Highest price</option>
          </select>
        </div>

        <button type="submit"
                class="w-full lg:w-auto rounded-xl px-6 py-3 text-sm font-extrabold text-white shadow-sm transition"
                style="background: {{ $primary }};"
                onmouseover="this.style.filter='brightness(0.92)'"
                onmouseout="this.style.filter='brightness(1)'">
          Search
        </button>
      </div>
    </form>
  </div>
</section>

<!-- FEATURED ROOMS -->
<section class="mt-10">
  <div class="flex items-end justify-between gap-4">
    <div>
      <h2 class="text-2xl sm:text-3xl font-extrabold" style="color: {{ $textDark }};">
        Featured rooms near MSU
      </h2>
      <p class="mt-2 text-gray-700">
        Verified listings by landlords — clear pricing, distance, facilities, and photos.
      </p>
    </div>

    <a href="{{ route('student.rooms.index') }}"
       class="hidden sm:inline-flex font-bold hover:opacity-80 transition"
       style="color: {{ $primaryDark }};">
      View more →
    </a>
  </div>

  <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    @forelse($featuredRooms as $room)
      @php
        $img   = $roomImageUrl($room);
        $price = $roomPrice($room);
        $loc   = $roomLocation($room);
        $type  = $titleCase($roomType($room));
        $dist  = $roomDistanceKm($room);
        $facs  = $roomFacilities($room);

        $title = $room->title ?? $room->name ?? ($type . ' near MSU');

        $facBadges = collect($facs)->map(fn($x) => trim((string)$x))->filter()->take(5)->values();

        $gPref = isset($room->gender_preference) ? strtolower((string)$room->gender_preference) : '';
        $gLabel = $gPref === 'male' ? 'Male only' : ($gPref === 'female' ? 'Female only' : ($gPref === 'any' ? 'Any gender' : null));

        $isSaved = in_array($room->id ?? null, $savedIds, true);
      @endphp

      <div class="rounded-3xl overflow-hidden border border-black/5 bg-white/90 shadow-sm hover:shadow-md transition">
        <div class="h-52 bg-cover bg-center relative" style="background-image:url('{{ $img }}');">
          <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-black/10 to-transparent"></div>

          @auth
            <form method="POST" action="{{ route('student.bookmarks.toggle', $room->id) }}"
                  class="absolute top-4 left-4 z-10">
              @csrf
              <button type="submit"
                      class="h-10 w-10 rounded-full grid place-items-center shadow ring-1 ring-black/10 transition"
                      style="background: rgba(255,255,255,.92);"
                      title="{{ $isSaved ? 'Unsave' : 'Save' }}">
                <span class="text-lg" style="line-height:1;">
                  {!! $isSaved ? '❤️' : '🤍' !!}
                </span>
              </button>
            </form>
          @endauth

          @if($price !== null)
            <div class="absolute top-4 right-4 rounded-full px-4 py-2 text-xs font-extrabold text-white shadow"
                 style="background: rgba(214,40,40,.92);">
              RM {{ number_format((float)$price, 0) }} / month
            </div>
          @endif
        </div>

        <div class="p-6">
          <div class="font-extrabold text-lg leading-snug" style="color: {{ $textDark }};">
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
                <span>Distance not set</span>
              @endif
            </span>
            @if($gLabel)
              <span class="opacity-50">•</span>
              <span class="inline-flex items-center gap-1">👥 <span>{{ $gLabel }}</span></span>
            @endif
          </div>

          <div class="mt-4">
            <div class="text-xs font-bold text-gray-600">Facilities</div>
            @if($facBadges->count())
              <div class="mt-2 flex flex-wrap gap-2">
                @foreach($facBadges as $f)
                  <span class="text-xs font-semibold px-3 py-1 rounded-full border border-black/10"
                        style="background: {{ $muted }};">
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
               style="background: {{ $primaryDark }};"
               onmouseover="this.style.filter='brightness(0.92)'"
               onmouseout="this.style.filter='brightness(1)'">
              View Details
            </a>
          </div>
        </div>
      </div>

    @empty
      @for($i=1; $i<=3; $i++)
        <div class="rounded-3xl overflow-hidden border border-black/5 bg-white/90 shadow-sm">
          <div class="h-52 bg-cover bg-center" style="background-image:url('{{ $hero }}');"></div>
          <div class="p-6">
            <div class="font-extrabold text-lg" style="color: {{ $textDark }};">Room listing will appear here</div>
            <div class="mt-2 text-sm text-gray-700">Once landlords upload rooms, students will see them here.</div>
            <div class="mt-5">
              <a href="{{ route('student.rooms.index') }}"
                 class="block w-full rounded-xl px-4 py-2 text-sm font-extrabold text-white text-center"
                 style="background: {{ $primaryDark }};">
                View Rooms
              </a>
            </div>
          </div>
        </div>
      @endfor
    @endforelse
  </div>
</section>

<!-- Floating chat -->
<a href="{{ route('student.messages.index') }}"
   class="fixed bottom-6 right-6 h-14 w-14 rounded-full text-white grid place-items-center shadow-lg hover:scale-[1.03] transition z-30 ring-1 ring-black/10"
   style="background: {{ $primary }};"
   title="Messages">
  💬
</a>

@endsection