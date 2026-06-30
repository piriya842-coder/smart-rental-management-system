@php
  $cream = '#FFF8FA';
  $gold  = '#D62828';
  $choco = '#3A1F2B';

  $userName = auth()->user()->name ?? 'Student';

  // Background image
  $msuBg = asset('images/msu/msu-building.jpg');

  // ✅ Saved rooms count badge
  $savedCount = 0;
  try {
    if (auth()->check() && auth()->user() && method_exists(auth()->user(), 'savedRooms')) {
      $savedCount = (int) auth()->user()->savedRooms()->count();
    }
  } catch (\Throwable $e) {
    $savedCount = 0;
  }

  // ✅ Notifications unread badge (1,2,99+)
  $unread = 0;
  try {
    $unread = auth()->check() ? (int) auth()->user()->unreadNotifications()->count() : 0;
  } catch (\Throwable $e) {
    $unread = 0;
  }
  $badge = $unread > 99 ? '99+' : (string)$unread;

  // ✅ Latest booking for floating chat button
  $latestBookingId = null;
  try {
    if (auth()->check() && auth()->user() && method_exists(auth()->user(), 'bookings')) {
      $latestBookingId = auth()->user()->bookings()->latest()->value('id');
    }
  } catch (\Throwable $e) {
    $latestBookingId = null;
  }
@endphp

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'MSU Student Portal • Smart Rental')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    @keyframes sr-marquee {
      0% { transform: translateX(100%); }
      100% { transform: translateX(-100%); }
    }

    .sr-marquee { overflow: hidden; }

    .sr-marquee .track{
      display:inline-block;
      white-space:nowrap;
      animation: sr-marquee 22s linear infinite;
    }

    summary::-webkit-details-marker{ display:none; }

    .sr-student-topbar{
      background:
        linear-gradient(135deg, #ffffff 0%, #fff5f5 52%, #fffafa 100%);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(201,42,42,.08);
      box-shadow:
        0 8px 24px rgba(201,42,42,.05),
        inset 0 -1px 0 rgba(255,255,255,.75);
    }

    .sr-student-logo-wrap{
      width: 52px;
      height: 52px;
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      background:
        linear-gradient(135deg, #ffffff 0%, #fff6f6 50%, #fdf0f0 100%);
      border: 1px solid rgba(201,42,42,.10);
      box-shadow:
        0 10px 22px rgba(201,42,42,.08),
        inset 0 1px 0 rgba(255,255,255,.92);
      overflow: hidden;
      flex-shrink: 0;
    }

    .sr-student-logo-wrap img{
      height: 31px;
      width: auto;
      object-fit: contain;
      display: block;
      filter: contrast(1.03);
    }

    .sr-student-brand-title{
      font-size: 20px;
      font-weight: 900;
      line-height: 1.05;
      letter-spacing: -.02em;
      color: #4a2c2a;
    }

    .sr-student-brand-sub{
      margin-top: 4px;
      font-size: 12px;
      font-weight: 800;
      color: #9c5d5d;
      letter-spacing: .01em;
    }

    .sr-student-nav-link{
      position: relative;
      color: #5b2f2f;
      transition: all .2s ease;
    }

    .sr-student-nav-link:hover{
      color: #D62828;
    }

    .sr-student-account{
      background: rgba(255,255,255,.95);
      border: 1px solid rgba(201,42,42,.08);
      box-shadow:
        0 8px 18px rgba(201,42,42,.05),
        inset 0 1px 0 rgba(255,255,255,.8);
    }

    .sr-student-subbar{
      background:
        linear-gradient(135deg, #ffffff 0%, #fff5f5 100%);
      border-bottom: 1px solid rgba(201,42,42,.06);
      box-shadow: 0 6px 18px rgba(201,42,42,.03);
    }

    .sr-student-icon-btn{
      background: rgba(255,255,255,.95);
      border: 1px solid rgba(201,42,42,.08);
      box-shadow:
        0 8px 18px rgba(201,42,42,.04),
        inset 0 1px 0 rgba(255,255,255,.85);
    }

    .sr-student-icon-btn:hover{
      background: #fff5f5;
    }

    .sr-badge{
      background: linear-gradient(135deg, #c92a2a 0%, #a61e1e 100%);
      box-shadow: 0 8px 16px rgba(201,42,42,.22);
    }

    .sr-dropdown{
      border: 1px solid rgba(201,42,42,.08);
      box-shadow: 0 18px 34px rgba(58,31,43,.12);
    }

    .sr-dropdown-link:hover{
      background: #fff5f5;
    }

    .sr-student-footer{
      background: linear-gradient(135deg, #6b2d2d 0%, #522323 45%, #3f1d1d 100%);
      box-shadow: 0 -10px 24px rgba(50,20,20,.12);
    }

    .sr-footer-logo{
      background: rgba(255,255,255,.16);
      border: 1px solid rgba(255,255,255,.18);
      box-shadow: inset 0 1px 0 rgba(255,255,255,.25);
    }

    .sr-chat-fab{
      background: linear-gradient(135deg, #c92a2a 0%, #a61e1e 100%);
      box-shadow:
        0 16px 28px rgba(201,42,42,.22),
        inset 0 1px 0 rgba(255,255,255,.20);
      color: #ffffff;
    }

    .sr-chat-fab:hover{
      transform: scale(1.06);
      filter: brightness(1.03);
    }
  </style>
</head>

<body class="bg-[#FFF8FA] text-gray-900 min-h-screen">

  <!-- BACKGROUND -->
  <div class="fixed inset-0 -z-10">
    <div class="absolute inset-0"
         style="background:
           radial-gradient(900px 320px at 12% 14%, rgba(214,40,40,.11), transparent 60%),
           radial-gradient(800px 320px at 86% 16%, rgba(59,130,246,.10), transparent 60%),
           radial-gradient(700px 300px at 50% 90%, rgba(244,114,182,.08), transparent 60%),
           linear-gradient({{ $cream }}, {{ $cream }});">
    </div>

    <div class="absolute inset-0 bg-cover bg-center opacity-[0.55]"
         style="background-image:url('{{ $msuBg }}');"></div>

    <div class="absolute inset-0" style="background: rgba(255,255,255,0.10);"></div>
    <div class="absolute inset-0" style="background: rgba(0,0,0,0.04);"></div>
  </div>

  <!-- NAVBAR -->
  <header class="sticky top-0 z-50 sr-student-topbar">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="h-[82px] flex items-center justify-between gap-4">

        <!-- LEFT: Brand -->
        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-4">
          <div class="sr-student-logo-wrap">
            <img src="{{ asset('images/logo.png') }}" alt="Smart Rental Logo">
          </div>

          <div class="leading-tight">
            <div class="sr-student-brand-title">
              Smart Rental
            </div>
            <div class="sr-student-brand-sub">
              MSU Student Portal
            </div>
          </div>
        </a>

        <!-- CENTER -->
        <nav class="hidden md:flex items-center gap-9 text-sm font-semibold">
          <a href="{{ route('student.dashboard') }}" class="sr-student-nav-link">Home</a>
          <a href="{{ route('student.rooms.index') }}" class="sr-student-nav-link">Rooms</a>
          <a href="{{ route('student.help') }}" class="sr-student-nav-link">Help</a>
        </nav>

        <!-- RIGHT -->
        <div class="flex items-center gap-2">

          <!-- Notifications -->
          <a href="{{ route('student.notifications.index') }}"
             class="relative inline-flex items-center justify-center h-10 w-10 rounded-xl sr-student-icon-btn transition"
             title="Notifications">
            <span class="text-lg">🔔</span>

            @if($unread > 0)
              <span class="absolute -top-1 -right-1 min-w-[20px] h-[20px] px-1 rounded-full text-[11px] font-extrabold grid place-items-center text-white shadow sr-badge"
                    style="line-height: 1;">
                {{ $badge }}
              </span>
            @endif
          </a>

          <!-- Saved Rooms -->
          <a href="{{ route('student.bookmarks.index') }}"
             class="relative inline-flex items-center justify-center h-10 w-10 rounded-xl sr-student-icon-btn transition"
             title="Saved Rooms">
            <span class="text-lg">❤️</span>

            @if($savedCount > 0)
              <span class="absolute -top-1 -right-1 min-w-[20px] h-[20px] px-1 rounded-full text-[11px] font-extrabold grid place-items-center text-white shadow sr-badge"
                    style="line-height: 1;">
                {{ $savedCount }}
              </span>
            @endif
          </a>

          <!-- Account dropdown -->
          <details class="relative">
            <summary class="cursor-pointer hidden sm:flex items-center gap-3 rounded-xl px-4 py-2 hover:bg-[#fff5f5] transition sr-student-account">
              <div class="text-xs text-gray-500 leading-none">Signed in</div>
              <div class="text-sm font-extrabold leading-none" style="color: {{ $choco }};">
                {{ $userName }}
              </div>
              <div class="text-gray-400">▾</div>
            </summary>

            <summary class="cursor-pointer sm:hidden inline-flex items-center justify-center h-10 w-10 rounded-xl sr-student-icon-btn transition">
              <span class="text-sm font-extrabold" style="color: {{ $choco }};">☰</span>
            </summary>

            <div class="absolute right-0 mt-3 w-72 rounded-2xl bg-white overflow-hidden sr-dropdown">
              <div class="px-4 py-3 border-b border-black/5">
                <div class="text-xs text-gray-500">Account</div>
                <div class="text-sm font-extrabold" style="color: {{ $choco }};">{{ $userName }}</div>
              </div>

              <a class="flex items-center gap-2 px-4 py-3 text-sm font-semibold sr-dropdown-link transition"
                 href="{{ route('student.account') }}">👤 My Profile</a>

              <a class="flex items-center gap-2 px-4 py-3 text-sm font-semibold sr-dropdown-link transition"
                 href="{{ route('student.password.edit') }}">🔐 Change Password</a>

              <a class="flex items-center gap-2 px-4 py-3 text-sm font-semibold sr-dropdown-link transition"
                 href="{{ route('student.bookings.index') }}">📌 Booking History</a>

              <a class="flex items-center gap-2 px-4 py-3 text-sm font-semibold sr-dropdown-link transition"
                 href="{{ route('student.payments.index') }}">🧾 Transaction History</a>

              <a class="flex items-center gap-2 px-4 py-3 text-sm font-semibold sr-dropdown-link transition"
                 href="{{ route('student.contracts.index') }}">📄 Contract</a>

              <a class="flex items-center gap-2 px-4 py-3 text-sm font-semibold sr-dropdown-link transition"
                 href="{{ route('student.monthly-rents.index') }}">💳 Monthly Rent</a>

              <a class="flex items-center gap-2 px-4 py-3 text-sm font-semibold sr-dropdown-link transition"
                 href="{{ route('student.notifications.index') }}">🔔 Notifications</a>

              <div class="h-px bg-black/5"></div>

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left flex items-center gap-2 px-4 py-3 text-sm font-semibold sr-dropdown-link transition"
                        style="color: {{ $choco }};">
                  ⎋ Logout
                </button>
              </form>
            </div>
          </details>

        </div>
      </div>
    </div>
  </header>

  <!-- Sticky running info bar -->
  <div class="sticky top-[82px] z-40 sr-student-subbar">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-2">
      <div class="sr-marquee text-xs text-gray-700">
        <div class="track">
          Smart Rental • Verified listings near MSU • Clear pricing • Facilities & photos • Save favourites • Track bookings & transactions •
        </div>
      </div>
    </div>
  </div>

  <!-- PAGE CONTENT -->
  <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
  </main>

  <!-- FOOTER -->
  <footer class="mt-14 sr-student-footer">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 text-[#FFF8F3]">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4">

        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-2xl grid place-items-center sr-footer-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Smart Rental Logo" class="h-5 w-auto object-contain">
          </div>
          <div>
            <div class="text-base font-extrabold">Smart Rental</div>
            <div class="text-xs opacity-85">MSU Student Portal</div>
          </div>
        </div>

        <div class="text-sm font-semibold opacity-90 text-center">
          Your space. Your comfort. Your choice.
        </div>

        <div class="text-xs sm:text-sm opacity-85">
          © {{ date('Y') }} Smart Rental. All rights reserved.
        </div>

      </div>
    </div>
  </footer>

  <!-- FLOATING CHAT BUTTON -->
  <a href="{{ $latestBookingId ? route('student.messages.index', ['booking' => $latestBookingId]) : route('student.messages.index') }}"
     class="fixed bottom-6 right-6 z-50 h-16 w-16 rounded-full flex items-center justify-center shadow-xl transition sr-chat-fab"
     title="Open Messages">
    <span class="text-[32px] leading-none">💬</span>
  </a>

</body>
</html>