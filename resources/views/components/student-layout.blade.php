@php
  $cream = '#FAF6F2';
  $gold  = '#B08401';
  $choco = '#683B2B';

  $userName = auth()->user()->name ?? 'Student';

  // Active route helper
  $r = request()->route() ? request()->route()->getName() : '';
  $is = fn($name) => $r === $name;

  // Use your existing hero image (you already have slide1)
  $msuBg = asset('images/slider/slide1.jpg');
@endphp

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Student Portal • Smart Rental' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    /* tiny premium animation - announcement ticker */
    .sr-ticker{ overflow:hidden; position:relative; }
    .sr-ticker > div{
      display:inline-block; white-space:nowrap;
      animation: sr-marquee 18s linear infinite;
    }
    @keyframes sr-marquee{
      0%{ transform: translateX(100%); }
      100%{ transform: translateX(-100%); }
    }
  </style>
</head>

<body class="bg-[#FAF6F2] text-gray-900 min-h-screen">

  <!-- TOP NAV -->
  <header class="sticky top-0 z-50 bg-white/85 backdrop-blur border-b border-black/5">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="h-16 flex items-center justify-between gap-4">

        <!-- Brand -->
        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-xl grid place-items-center text-white shadow"
               style="background: {{ $choco }};">
            <!-- House icon -->
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M3 11.5L12 4l9 7.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M5 10.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>

          <div class="leading-tight">
            <div class="text-lg font-extrabold tracking-tight" style="color: {{ $choco }};">
              Smart Rental
            </div>
            <div class="text-xs text-gray-600 -mt-0.5">
              MSU Student Portal
            </div>
          </div>
        </a>

        <!-- Center menu (professional tabs, NOT boxed pills) -->
        <nav class="hidden lg:flex items-center gap-7 text-sm font-semibold" style="color: {{ $choco }};">
          <a href="{{ route('student.dashboard') }}"
             class="hover:opacity-80 transition {{ $is('student.dashboard') ? 'font-extrabold underline underline-offset-8' : '' }}">
            Dashboard
          </a>

          <a href="{{ route('student.rooms.index') }}"
             class="hover:opacity-80 transition {{ $is('student.rooms.index') ? 'font-extrabold underline underline-offset-8' : '' }}">
            Rooms
          </a>

          <a href="{{ route('student.bookings') }}"
             class="hover:opacity-80 transition {{ $is('student.bookings') ? 'font-extrabold underline underline-offset-8' : '' }}">
            Bookings
          </a>

          <a href="{{ route('student.payments') }}"
             class="hover:opacity-80 transition {{ $is('student.payments') ? 'font-extrabold underline underline-offset-8' : '' }}">
            Payment History
          </a>

          <a href="{{ route('student.chat') }}"
             class="hover:opacity-80 transition {{ $is('student.chat') ? 'font-extrabold underline underline-offset-8' : '' }}">
            Chat
          </a>

          <a href="{{ route('student.contact') }}"
             class="hover:opacity-80 transition {{ $is('student.contact') ? 'font-extrabold underline underline-offset-8' : '' }}">
            Contact
          </a>
        </nav>

        <!-- Right actions -->
        <div class="flex items-center gap-2">

          <!-- Primary CTA -->
          <a href="{{ route('student.rooms.index') }}"
             class="hidden sm:inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-extrabold text-white shadow-sm"
             style="background: {{ $gold }};"
             onmouseover="this.style.filter='brightness(0.92)'"
             onmouseout="this.style.filter='brightness(1)'">
            <span>🔎</span> Browse Rooms
          </a>

          <!-- Signed in badge -->
          <div class="hidden md:flex items-center gap-2 rounded-xl border px-3 py-2 bg-white"
               style="border-color: rgba(0,0,0,0.10);">
            <div class="text-xs text-gray-500">Signed in</div>
            <div class="text-sm font-extrabold" style="color: {{ $choco }};">
              {{ $userName }}
            </div>
          </div>

          <a href="{{ route('student.account') }}"
             class="rounded-xl border px-4 py-2 text-sm font-bold bg-white hover:bg-gray-50 transition"
             style="border-color: rgba(0,0,0,0.12); color: {{ $choco }};">
            Profile
          </a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
              class="rounded-xl border px-4 py-2 text-sm font-bold bg-white hover:bg-gray-50 transition"
              style="border-color: rgba(0,0,0,0.12); color: {{ $choco }};">
              Logout
            </button>
          </form>
        </div>

      </div>
    </div>

    <!-- subtle announcement ticker -->
    <div class="bg-white/70 border-t border-black/5">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-2">
        <div class="sr-ticker text-xs text-gray-600">
          <div>
            📌 Smart Rental for MSU students — browse verified listings, compare price & facilities, and shortlist your favourite rooms.
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- PAGE -->
  <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-7">
    {{ $slot }}
  </main>

  <!-- FOOTER (simple, dark brown, no clutter) -->
  <footer class="mt-12" style="background: {{ $choco }};">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 text-[#FAF6F2]">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-xl grid place-items-center text-white"
               style="background: {{ $gold }};">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M3 11.5L12 4l9 7.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M5 10.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div>
            <div class="font-extrabold text-lg">Smart Rental</div>
            <div class="text-xs opacity-80">MSU Student Accommodation Portal</div>
          </div>
        </div>

        <div class="text-xs opacity-80">
          © {{ date('Y') }} Smart Rental. All rights reserved.
        </div>

      </div>
    </div>
  </footer>

</body>
</html>
