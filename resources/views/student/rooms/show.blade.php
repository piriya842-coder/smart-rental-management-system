@extends('layouts.student')

@section('title', 'Room Details • Smart Rental')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Facades\Schema; // ✅ ADDED (needed for safe bookings check)

  $gold    = '#c92a2a';
  $choco   = '#4a2c2a';
  $cream   = '#fffafa';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';

  $deposit = 100;

  $price = is_numeric($room->price_monthly ?? null) ? (float)$room->price_monthly : 0;
  $totalDue = $deposit + $price;

  $roomType = $room->room_type ? ucwords(str_replace('_',' ', $room->room_type)) : 'Room';
  $gender = $room->gender_preference ? ucwords($room->gender_preference) : 'Any';

  $dist = is_numeric($room->distance_km ?? null) ? number_format((float)$room->distance_km, 1) : null;

  $address  = $room->address ?? '';
  $city     = $room->city ?? '';
  $state    = $room->state ?? '';
  $postcode = $room->postcode ?? '';
  $fullAddress = trim(implode(', ', array_filter([$address, $city, $state, $postcode])));

  // facilities safe (array/json/csv)
  $facilities = [];
  if (is_array($room->facilities ?? null)) {
    $facilities = $room->facilities;
  } elseif (!empty($room->facilities)) {
    $decoded = null;
    try { $decoded = json_decode((string)$room->facilities, true); } catch (\Throwable $e) {}
    $facilities = is_array($decoded) ? $decoded : array_map('trim', explode(',', (string)$room->facilities));
  }

  $facilities = collect($facilities)->map(function($x){
      $x = trim((string)$x);
      return $x === '' ? null : ucwords(str_replace(['_','-'], ' ', $x));
  })->filter()->values();

  $landlord = $room->landlord ?? null;
  $isVerified = false;
  if ($landlord) {
    if (!empty($landlord->landlord_status) && strtolower((string)$landlord->landlord_status) === 'approved') $isVerified = true;
    if (!empty($landlord->landlord_verified_at)) $isVerified = true;
  }

  // ============================
  // ✅ FIXED SLOTS LOGIC (ONLY THIS PART CHANGED)
  // ============================
  $roomTypeLower = strtolower((string)($room->room_type ?? 'single'));

  // capacity fallback
  $capacitySafe = is_numeric($room->capacity ?? null)
    ? (int)$room->capacity
    : ($roomTypeLower === 'shared' ? 4 : 1);

  // DB slots (if you store it)
  $slotsDb = is_numeric($room->available_slots ?? null) ? (int)$room->available_slots : null;

  // default to DB value first
  $slots = $slotsDb;

  // compute from bookings (truth source)
  $activeBooked = 0;

  try {
    if (class_exists(\App\Models\Booking::class) && Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'room_id')) {

      $q = \App\Models\Booking::query()->where('room_id', $room->id);

      // only count bookings that should block slots
      if (Schema::hasColumn('bookings', 'status')) {
        $q->whereIn('status', ['payment_submitted', 'paid']);
      }

      $activeBooked = (int) $q->count();

      if ($roomTypeLower === 'shared') {
        // shared: capacity - activeBooked
        $slots = max(0, $capacitySafe - $activeBooked);
      } else {
        // single/studio: if booked => 0 else 1
        $slots = $activeBooked > 0 ? 0 : 1;
      }
    }
  } catch (\Throwable $e) {
    // if anything fails, fallback:
    if ($slots === null) {
      $slots = ($roomTypeLower === 'shared') ? $capacitySafe : 1;
    }
  }

  // final fallback if still null
  if ($slots === null) {
    $slots = ($roomTypeLower === 'shared') ? $capacitySafe : 1;
  }

  // ✅ REPLACED AVAILABILITY PART (ONLY THIS PART CHANGED)
  // ✅ Availability rule:
  // - If slots > 0, consider available (especially shared rooms)
  // - Still respect landlord toggle IF it exists AND is explicitly false
  $isAvailableDb = $room->is_available ?? null;

  if ($isAvailableDb === null) {
    $isAvailableDb = true; // default
  } else {
    $isAvailableDb = (bool)$isAvailableDb;
  }

  // if landlord set it false, keep false
  $isAvailable = $isAvailableDb;

  // but if shared + slots>0 and landlord accidentally left checkbox unticked,
  // we allow availability by slots (optional business rule)
  if ($roomTypeLower === 'shared' && (int)$slots > 0) {
    $isAvailable = true;
  }

  // final hard rule: slots must be > 0
  $isAvailable = $isAvailable && ((int)$slots > 0);
  // ============================
  // ✅ END FIXED SLOTS LOGIC
  // ============================

  // ✅ Build gallery (cover + room_images)
  $images = collect();

  if (!empty($room->cover_image)) {
    $images->push(
      str_starts_with($room->cover_image, 'http')
        ? $room->cover_image
        : Storage::url($room->cover_image)
    );
  }

  if (isset($room->images) && $room->images && count($room->images)) {
    foreach ($room->images as $img) {
      if (!empty($img->path)) $images->push(Storage::url($img->path));
    }
  }

  $images = $images->unique()->values();

  if ($images->isEmpty()) {
    $images = collect([asset('images/room-placeholder.jpg')]);
  }

  $hero = $images[0];

  $messageUrl = route('student.messages.index', [
      'landlord_id' => $landlord?->id,
      'room_id' => $room->id
  ]);

  // ✅ Map query (best effort)
  $mapQuery = trim($fullAddress);
  if ($mapQuery === '') {
    $mapQuery = trim(implode(' ', array_filter([$city, $state])));
  }
  if ($mapQuery === '') {
    $mapQuery = 'MSU Shah Alam';
  }
  $mapQueryEnc = urlencode($mapQuery);

  // ✅ Option A: Standard rules (same for all rooms)
  $rules = [
    ['title' => 'Quiet Hours', 'desc' => 'Please keep noise low from 10:00 PM to 7:00 AM to respect other tenants.'],
    ['title' => 'Visitors', 'desc' => 'Visitors are allowed until 10:00 PM. Overnight visitors are not encouraged unless landlord approves.'],
    ['title' => 'Cleanliness', 'desc' => 'Keep shared areas clean after use (kitchen, living area, bathroom).'],
    ['title' => 'Smoking', 'desc' => 'Smoking is not allowed inside the room/unit.'],
    ['title' => 'Deposit', 'desc' => 'Deposit is refundable after contract ends (if no damage/unpaid bills).'],
  ];

  $titleText = $room->title ?? ($roomType . ' near MSU');

  // ✅ FIXED HERE: removed extra ')'
  $locationLabel = trim($city . ($state ? ', '.$state : ''));
  if ($locationLabel === '') $locationLabel = 'Near MSU';
@endphp

<div class="max-w-6xl mx-auto">

  <!-- Top -->
  <div class="flex items-center justify-between mb-4">
    <a href="{{ route('student.rooms.index') }}" class="font-semibold hover:opacity-80" style="color: {{ $choco }};">
      ← Back to Rooms
    </a>

    <div class="hidden sm:flex items-center gap-2 text-xs font-bold px-3 py-2 rounded-xl border border-[rgba(201,42,42,.10)] bg-white/80"
         style="color: {{ $choco }};">
      Verified listing • Smart Rental
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- LEFT -->
    <div class="lg:col-span-7 space-y-6">

      <!-- Hero + Gallery -->
      <div class="rounded-3xl overflow-hidden border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm">
        <div class="relative">
          <!-- ✅ Smaller hero height for less blur -->
          <img src="{{ $hero }}"
               alt="Room photo"
               class="w-full h-[260px] sm:h-[300px] object-cover"
               onerror="this.onerror=null; this.src='{{ asset('images/room-placeholder.jpg') }}';">

          <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent"></div>

          @if($isVerified)
            <div class="absolute top-4 left-4 rounded-full px-4 py-2 text-xs font-extrabold text-white shadow"
                 style="background: rgba(74,44,42,.92);">
              ✅ Verified Listing
            </div>
          @endif

          <div class="absolute top-4 right-4 rounded-full px-4 py-2 text-xs font-extrabold text-white shadow"
               style="background: rgba(201,42,42,.92);">
            RM {{ number_format($price, 0) }} / month
          </div>

          <div class="absolute bottom-4 left-4 rounded-full px-4 py-2 text-xs font-extrabold text-white shadow"
               style="background: rgba(0,0,0,.55);">
            {{ $isAvailable ? 'Available' : 'Not Available' }}
          </div>
        </div>

        <!-- ✅ Thumbnails (click opens lightbox) -->
        @if($images->count() > 1)
          <div class="p-4 border-t border-[rgba(201,42,42,.08)] bg-white/70">
            <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
              @foreach($images->take(12) as $i => $thumb)
                <button type="button"
                        class="block h-16 rounded-2xl overflow-hidden border border-[rgba(201,42,42,.10)] hover:opacity-90 transition"
                        onclick="srOpenLightbox({{ $i }})"
                        title="View photo">
                  <img src="{{ $thumb }}" class="w-full h-full object-cover"
                       onerror="this.onerror=null; this.src='{{ asset('images/room-placeholder.jpg') }}';">
                </button>
              @endforeach
            </div>

            <div class="mt-4 rounded-2xl border border-[rgba(201,42,42,.10)] px-4 py-3 text-xs font-semibold bg-white/80"
                 style="color: {{ $choco }};">
              💡 Tip: For more photos, viewing appointment, or questions, click
              <span class="font-extrabold">“Message Landlord”</span>.
            </div>
          </div>
        @else
          <div class="p-4 border-t border-[rgba(201,42,42,.08)] bg-white/70">
            <div class="rounded-2xl border border-[rgba(201,42,42,.10)] px-4 py-3 text-xs font-semibold bg-white/80"
                 style="color: {{ $choco }};">
              💡 Tip: For more photos, viewing appointment, or questions, click
              <span class="font-extrabold">“Message Landlord”</span>.
            </div>
          </div>
        @endif

        <!-- Title + meta -->
        <div class="p-6">
          <h1 class="text-2xl sm:text-3xl font-extrabold leading-tight" style="color: {{ $choco }};">
            {{ $titleText }}
          </h1>

          <div class="mt-3 text-sm text-gray-700 flex flex-wrap items-center gap-x-2 gap-y-2">
            <span class="inline-flex items-center gap-1">📍 <span>{{ $locationLabel }}</span></span>
            <span class="opacity-50">•</span>
            <span class="inline-flex items-center gap-1">🏠 <span>{{ $roomType }}</span></span>
            <span class="opacity-50">•</span>
            <span class="inline-flex items-center gap-1">👥 <span>{{ $gender }}</span></span>
            <span class="opacity-50">•</span>
            <span class="inline-flex items-center gap-1">🧭
              @if($dist !== null)
                <span>{{ $dist }} km to MSU</span>
              @else
                <span>— km to MSU</span>
              @endif
            </span>
          </div>

          <div class="mt-5">
            <div class="text-sm font-extrabold" style="color: {{ $choco }};">Description</div>
            <p class="mt-2 text-gray-700 leading-relaxed">
              {{ $room->description ?: 'No description provided.' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Facilities -->
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6">
        <div class="flex items-center justify-between gap-3">
          <div class="text-lg font-extrabold" style="color: {{ $choco }};">Facilities</div>
          <div class="text-xs font-bold text-gray-500">What’s included</div>
        </div>

        @if($facilities->count())
          <div class="mt-4 flex flex-wrap gap-2">
            @foreach($facilities as $f)
              <span class="text-xs font-bold px-3 py-2 rounded-full border border-[rgba(201,42,42,.10)]"
                    style="background: {{ $softRed }}; color: {{ $choco }};">
                {{ $f }}
              </span>
            @endforeach
          </div>
        @else
          <div class="mt-3 text-gray-500">Facilities not listed.</div>
        @endif
      </div>

      <!-- ✅ Map preview card -->
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-[rgba(201,42,42,.08)]">
          <div class="flex items-center justify-between gap-3">
            <div class="text-lg font-extrabold" style="color: {{ $choco }};">Map Preview</div>
            <div class="text-xs font-bold text-gray-500">{{ $mapQuery }}</div>
          </div>
          <div class="mt-2 text-sm text-gray-700">
            This preview is based on the room’s saved address/city. For exact directions, message the landlord.
          </div>
        </div>

        <div class="bg-white">
          <iframe
            title="Map preview"
            class="w-full h-[260px]"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps?q={{ $mapQueryEnc }}&output=embed">
          </iframe>
        </div>

        <div class="p-5 border-t border-[rgba(201,42,42,.08)] bg-white/80">
          <a href="https://www.google.com/maps/search/?api=1&query={{ $mapQueryEnc }}"
             target="_blank"
             class="inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-extrabold text-white shadow-sm"
             style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
            Open in Google Maps
          </a>
        </div>
      </div>

    </div>

    <!-- RIGHT -->
    <div class="lg:col-span-5">
      <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm p-6 lg:sticky lg:top-6 space-y-5">

        <!-- Price -->
        <div>
          <div class="text-sm font-bold text-gray-600">Monthly Rent</div>
          <div class="mt-1 text-4xl font-extrabold" style="color: {{ $gold }};">
            RM {{ number_format($price, 0) }}
            <span class="text-base font-extrabold text-gray-700">/ month</span>
          </div>

          <div class="mt-3 flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-extrabold text-white"
                  style="background: {{ $isVerified ? $choco : '#8A8A8A' }};">
              {{ $isVerified ? '✅ Verified Landlord' : 'Not verified' }}
            </span>

            <span class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-extrabold text-white"
                  style="background: {{ $isAvailable ? '#1B9A59' : '#444' }};">
              {{ $isAvailable ? '✅ Available' : '❌ Not Available' }}
            </span>
          </div>
        </div>

        <!-- Quick info -->
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-4" style="background: {{ $softRed }};">
            <div class="text-xs font-bold text-gray-600">Gender</div>
            <div class="mt-1 font-extrabold" style="color: {{ $choco }};">{{ $gender }}</div>
          </div>

          <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-4" style="background: {{ $softRed }};">
            <div class="text-xs font-bold text-gray-600">Available Slots</div>
            <div class="mt-1 font-extrabold" style="color: {{ $choco }};">{{ $slots !== null ? $slots : '-' }}</div>
          </div>

          <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-4" style="background: {{ $softRed }};">
            <div class="text-xs font-bold text-gray-600">Capacity</div>
            <div class="mt-1 font-extrabold" style="color: {{ $choco }};">
              {{ is_numeric($room->capacity ?? null) ? (int)$room->capacity . ' person(s)' : '-' }}
            </div>
          </div>

          <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-4" style="background: {{ $softRed }};">
            <div class="text-xs font-bold text-gray-600">Distance</div>
            <div class="mt-1 font-extrabold" style="color: {{ $choco }};">
              {{ $dist !== null ? $dist.' km' : '-' }}
            </div>
          </div>
        </div>

        <!-- Address -->
        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-4" style="background: {{ $softRed }};">
          <div class="text-xs font-bold text-gray-600">Full Address</div>
          <div class="mt-1 font-semibold text-gray-800">
            {{ $fullAddress !== '' ? $fullAddress : 'Address not provided.' }}
          </div>
        </div>

        <!-- Booking breakdown -->
        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5 bg-white">
          <div class="text-base font-extrabold" style="color: {{ $choco }};">
            Booking Payment (Deposit + 1 month)
          </div>

          <div class="mt-3 space-y-2 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-gray-700 font-semibold">Deposit</span>
              <span class="font-extrabold" style="color: {{ $choco }};">RM {{ number_format($deposit, 0) }}</span>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-gray-700 font-semibold">1 month rent</span>
              <span class="font-extrabold" style="color: {{ $choco }};">RM {{ number_format($price, 0) }}</span>
            </div>

            <div class="pt-3 mt-3 border-t border-[rgba(201,42,42,.10)] flex items-center justify-between">
              <span class="font-extrabold text-gray-800">Total due now</span>
              <span class="font-extrabold" style="color: {{ $gold }};">RM {{ number_format($totalDue, 0) }}</span>
            </div>

            <div class="text-xs text-gray-600 mt-2">
              Deposit is returned after contract ends (if no damage / unpaid dues).
            </div>
          </div>
        </div>

        <!-- ✅ Rules & Notes (Option A) -->
        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5 bg-white">
          <div class="flex items-center justify-between gap-3">
            <div class="text-base font-extrabold" style="color: {{ $choco }};">Rules & Notes</div>
            <div class="text-xs font-bold text-gray-500">Standard rental guidelines</div>
          </div>

          <div class="mt-4 space-y-3">
            @foreach($rules as $r)
              <div class="rounded-xl border border-[rgba(201,42,42,.08)] p-3" style="background: {{ $softRed }};">
                <div class="text-sm font-extrabold" style="color: {{ $choco }};">{{ $r['title'] }}</div>
                <div class="mt-1 text-sm text-gray-700 leading-relaxed">{{ $r['desc'] }}</div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- ✅ Buttons (FIXED Book Now) -->
        <div class="relative z-50 pointer-events-auto">
          <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('student.bookings.create', ['room' => $room->id]) }}"
               class="relative z-[999] pointer-events-auto rounded-xl px-4 py-3 text-sm font-extrabold text-white shadow-sm text-center block"
               style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
              Book Now
            </a>

            <a href="{{ $messageUrl }}"
               class="rounded-xl px-4 py-3 text-sm font-extrabold text-white text-center shadow-sm"
               style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
              💬 Message Landlord
            </a>
          </div>
        </div>

        <!-- Landlord -->
        <div class="rounded-2xl border border-[rgba(201,42,42,.10)] p-5 bg-white">
          <div class="text-sm font-extrabold text-gray-700">Landlord</div>
          <div class="mt-1 text-lg font-extrabold" style="color: {{ $choco }};">
            {{ $landlord?->name ?? 'Landlord' }}
          </div>
          <div class="text-sm text-gray-600">
            {{ $isVerified ? 'Approved / Verified' : 'Not verified' }}
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- ✅ Lightbox -->
<div id="srLightbox"
     class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background: rgba(0,0,0,.72);">
  <button type="button" onclick="srCloseLightbox()"
          class="absolute top-5 right-5 h-11 w-11 rounded-full bg-white/95 grid place-items-center font-extrabold shadow">
    ✕
  </button>

  <button type="button" onclick="srPrev()"
          class="hidden sm:grid absolute left-5 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full bg-white/95 place-items-center font-extrabold shadow">
    ‹
  </button>

  <button type="button" onclick="srNext()"
          class="hidden sm:grid absolute right-5 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full bg-white/95 place-items-center font-extrabold shadow">
    ›
  </button>

  <div class="w-full max-w-4xl">
    <img id="srLightboxImg"
         src=""
         alt="Room photo"
         class="w-full max-h-[78vh] object-contain rounded-2xl shadow-lg bg-black/10"
         onerror="this.onerror=null; this.src='{{ asset('images/room-placeholder.jpg') }}';">

    <div class="mt-3 flex items-center justify-between text-white text-sm">
      <div id="srLightboxCount" class="font-bold"></div>
      <div class="opacity-90">Tip: press ESC to close</div>
    </div>
  </div>
</div>

<script>
  const srImgs = @json($images->values());
  let srIndex = 0;

  function srOpenLightbox(i) {
    srIndex = i || 0;
    const lb = document.getElementById('srLightbox');
    const img = document.getElementById('srLightboxImg');
    const count = document.getElementById('srLightboxCount');

    img.src = srImgs[srIndex] || '';
    count.textContent = `${srIndex + 1} / ${srImgs.length}`;

    lb.classList.remove('hidden');
    lb.classList.add('flex');
    document.body.style.overflow = 'hidden';
  }

  function srCloseLightbox() {
    const lb = document.getElementById('srLightbox');
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    document.body.style.overflow = '';
  }

  function srPrev() {
    if (!srImgs.length) return;
    srIndex = (srIndex - 1 + srImgs.length) % srImgs.length;
    document.getElementById('srLightboxImg').src = srImgs[srIndex];
    document.getElementById('srLightboxCount').textContent = `${srIndex + 1} / ${srImgs.length}`;
  }

  function srNext() {
    if (!srImgs.length) return;
    srIndex = (srIndex + 1) % srImgs.length;
    document.getElementById('srLightboxImg').src = srImgs[srIndex];
    document.getElementById('srLightboxCount').textContent = `${srIndex + 1} / ${srImgs.length}`;
  }

  document.addEventListener('keydown', function(e) {
    const lb = document.getElementById('srLightbox');
    if (lb.classList.contains('hidden')) return;

    if (e.key === 'Escape') srCloseLightbox();
    if (e.key === 'ArrowLeft') srPrev();
    if (e.key === 'ArrowRight') srNext();
  });

  // click outside image closes
  document.getElementById('srLightbox').addEventListener('click', function(e){
    if (e.target && e.target.id === 'srLightbox') srCloseLightbox();
  });
</script>
@endsection