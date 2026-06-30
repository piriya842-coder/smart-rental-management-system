<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Smart Rental</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    :root{
      --sr-red: #c92a2a;
      --sr-red-dark: #a61e1e;
      --sr-red-soft: #fdf2f2;
      --sr-brown-dark: #4a2c2a;
      --sr-brown: #6a3d3a;
      --sr-cream: #fffafa;
      --sr-text: #2d1f1f;
    }

    body{
      background: linear-gradient(180deg, #fff7f7 0%, #fffafa 35%, #fdf6f6 100%);
      color: var(--sr-text);
    }

    .sr-home-topbar{
      background:
        linear-gradient(135deg,
          rgba(255,255,255,.97) 0%,
          rgba(255,247,247,.98) 30%,
          rgba(255,242,242,.98) 68%,
          rgba(255,250,250,.97) 100%);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(201,42,42,.08);
      box-shadow:
        0 8px 24px rgba(201,42,42,.05),
        inset 0 -1px 0 rgba(255,255,255,.7);
    }

    .sr-home-logo-wrap{
      width: 50px;
      height: 50px;
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #ffffff 0%, #fff6f6 50%, #fdf0f0 100%);
      border: 1px solid rgba(201,42,42,.10);
      box-shadow:
        0 10px 22px rgba(201,42,42,.08),
        inset 0 1px 0 rgba(255,255,255,.92);
      overflow: hidden;
      flex-shrink: 0;
    }

    .sr-home-logo-wrap img{
      height: 30px;
      width: auto;
      object-fit: contain;
      display: block;
      filter: contrast(1.03);
    }

    .sr-home-nav-link{
      color: #5b2f2f;
      transition: all .2s ease;
    }

    .sr-home-nav-link:hover{
      color: var(--sr-red);
    }

    .sr-btn-primary{
      background: linear-gradient(135deg, var(--sr-red) 0%, var(--sr-red-dark) 100%);
      color: #fff;
      box-shadow: 0 10px 22px rgba(201,42,42,.18);
      transition: all .2s ease;
    }

    .sr-btn-primary:hover{
      transform: translateY(-1px);
      filter: brightness(.97);
      box-shadow: 0 14px 26px rgba(201,42,42,.22);
    }

    .sr-btn-outline{
      background: rgba(255,255,255,.9);
      color: #6a3d3a;
      border: 1px solid rgba(201,42,42,.14);
      transition: all .2s ease;
    }

    .sr-btn-outline:hover{
      background: #fff5f5;
      color: var(--sr-red);
      border-color: rgba(201,42,42,.22);
    }

    .sr-about-card{
      background: linear-gradient(180deg, #ffffff 0%, #fff9f9 100%);
      border: 1px solid rgba(201,42,42,.08);
      box-shadow: 0 10px 24px rgba(80,32,32,.05);
      transition: all .25s ease;
    }

    .sr-about-card:hover{
      transform: translateY(-4px);
      box-shadow: 0 18px 34px rgba(80,32,32,.09);
    }

    .sr-about-icon{
      width: 52px;
      height: 52px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, rgba(201,42,42,.10) 0%, rgba(201,42,42,.16) 100%);
      border: 1px solid rgba(201,42,42,.10);
      box-shadow: inset 0 1px 0 rgba(255,255,255,.70);
    }

    .sr-home-footer{
      background: linear-gradient(135deg, #6b2d2d 0%, #522323 45%, #3f1d1d 100%);
      box-shadow: 0 -10px 24px rgba(50,20,20,.12);
    }

    .sr-footer-logo{
      background: rgba(255,255,255,.12);
      border: 1px solid rgba(255,255,255,.12);
      box-shadow: inset 0 1px 0 rgba(255,255,255,.15);
    }

    .sr-hero-overlay{
      background: linear-gradient(
        90deg,
        rgba(34, 24, 24, 0.72) 0%,
        rgba(34, 24, 24, 0.46) 42%,
        rgba(34, 24, 24, 0.20) 100%
      );
    }

    .sr-hero-glow-left{
      background: rgba(255,255,255,.10);
      filter: blur(60px);
    }

    .sr-hero-glow-right{
      background: rgba(255,255,255,.06);
      filter: blur(70px);
    }
  </style>
</head>

<body class="text-gray-900">

@php
  $hero = asset('images/slider/slide1.jpg');

  $red   = '#c92a2a';
  $brown = '#4a2c2a';
  $cream = '#fffafa';

  $dashRoute = null;
  if(auth()->check()){
      $role = auth()->user()->role;
      if($role === 'student') $dashRoute = route('student.dashboard');
      elseif($role === 'landlord') $dashRoute = route('landlord.dashboard');
      elseif($role === 'admin') $dashRoute = route('admin.dashboard');
      else $dashRoute = route('home');
  }
@endphp

<!-- NAVBAR -->
<header class="w-full sr-home-topbar">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex h-[82px] items-center justify-between gap-4">

      <!-- Brand -->
      <a href="{{ route('home') }}" class="flex items-center gap-4">
        <div class="sr-home-logo-wrap">
          <img src="{{ asset('images/logo.png') }}" alt="Smart Rental Logo">
        </div>

        <div class="leading-tight">
          <div class="text-[20px] font-extrabold tracking-tight" style="color: {{ $brown }};">
            Smart Rental
          </div>
          <div class="text-xs font-extrabold -mt-0.5" style="color: #9c5d5d;">
            MSU Student Accommodation
          </div>
        </div>
      </a>

      <!-- Menu (Desktop) -->
      <nav class="hidden md:flex items-center gap-10 text-sm font-semibold">
        <a href="{{ route('home') }}" class="sr-home-nav-link">Home</a>
        <a href="{{ route('rooms.index') }}" class="sr-home-nav-link">Rooms</a>
        <a href="{{ route('student.help') }}" class="sr-home-nav-link">Help</a>
      </nav>

      <!-- Auth buttons -->
      <div class="flex items-center gap-2">
        @auth
          <a href="{{ $dashRoute }}"
             class="sr-btn-primary rounded-xl px-5 py-2.5 text-sm font-extrabold">
            Dashboard
          </a>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
              class="sr-btn-outline rounded-xl px-5 py-2.5 text-sm font-extrabold">
              Logout
            </button>
          </form>
        @else
          <a href="{{ route('login') }}"
             class="sr-btn-outline rounded-xl px-5 py-2.5 text-sm font-extrabold">
            Login
          </a>

          <a href="{{ route('register') }}"
             class="sr-btn-primary rounded-xl px-5 py-2.5 text-sm font-extrabold">
            Register
          </a>
        @endauth
      </div>

    </div>
  </div>
</header>

<!-- HERO -->
<section class="relative">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="relative overflow-hidden rounded-3xl shadow-xl border border-black/5">

      <!-- Image -->
      <div class="h-[460px] sm:h-[520px] bg-cover bg-center"
           style="background-image:url('{{ $hero }}')"></div>

      <!-- Overlay -->
      <div class="absolute inset-0 sr-hero-overlay"></div>

      <!-- Decorative soft glow -->
      <div class="absolute left-[-60px] top-[80px] h-[240px] w-[240px] rounded-full sr-hero-glow-left"></div>
      <div class="absolute right-[-40px] bottom-[50px] h-[220px] w-[220px] rounded-full sr-hero-glow-right"></div>

      <!-- Content -->
      <div class="absolute inset-0 flex items-center">
        <div class="px-6 sm:px-12 lg:px-14 max-w-2xl">

          <div class="inline-flex items-center gap-2 rounded-full bg-white/12 px-4 py-2 text-xs font-semibold text-white ring-1 ring-white/25 backdrop-blur-sm">
            Smart Rental • MSU Student Housing
          </div>

          <h1 class="mt-5 text-4xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
            Find your next student home with confidence.
          </h1>

          <p class="mt-4 text-white/90 text-base sm:text-lg leading-relaxed">
            Browse verified listings, compare important details, and shortlist rooms that match your needs — designed for
            <span class="font-semibold text-white">MSU students</span>.
          </p>

          <div class="mt-7 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('rooms.index') }}"
               class="sr-btn-primary rounded-xl px-6 py-3 text-sm font-bold text-center">
              View Rooms
            </a>

            <a href="#about"
               class="rounded-xl bg-white/10 px-6 py-3 text-sm font-bold text-white ring-1 ring-white/25 hover:bg-white/15 transition text-center backdrop-blur-sm">
              Learn More
            </a>
          </div>

          <div class="mt-6 text-sm text-white">
            Sign in to unlock the full Rooms experience and personalized features.
          </div>

        </div>
      </div>

      <!-- WAVE DIVIDER -->
      <div class="absolute -bottom-1 left-0 right-0">
        <svg viewBox="0 0 1440 120" class="w-full h-[70px] sm:h-[90px]">
          <path fill="{{ $cream }}" fill-opacity="1"
                d="M0,64L60,64C120,64,240,64,360,69.3C480,75,600,85,720,80C840,75,960,53,1080,48C1200,43,1320,53,1380,58.7L1440,64L1440,120L1380,120C1320,120,1200,120,1080,120C960,120,840,120,720,120C600,120,480,120,360,120C240,120,120,120,60,120L0,120Z">
          </path>
        </svg>
      </div>

    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about" class="pb-14">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 p-8 sm:p-10 shadow-sm">

      <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-center"
          style="color: {{ $brown }};">
        About Smart Rental
      </h2>

      <p class="mt-4 text-gray-700 leading-relaxed max-w-3xl mx-auto text-center">
        Smart Rental is a web-based student accommodation platform focused on helping MSU students discover rooms quickly and clearly.
        Users can browse listings, compare important details such as price, location, and facilities, and shortlist options in one place.
      </p>

      <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Card 1 -->
        <div class="sr-about-card rounded-2xl p-6">
          <div class="sr-about-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color: {{ $red }};" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M15 19v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M11 7a4 4 0 11-8 0 4 4 0 018 0zm10 12v-1a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
          </div>
          <div class="mt-4 font-extrabold text-lg" style="color: {{ $brown }};">Student-friendly</div>
          <div class="mt-1 text-sm text-gray-700">Designed around MSU student needs with a simple and clear rental experience.</div>
        </div>

        <!-- Card 2 -->
        <div class="sr-about-card rounded-2xl p-6">
          <div class="sr-about-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color: {{ $red }};" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M9 17h6M9 13h6M9 9h6M7 3h10a2 2 0 012 2v14l-3-2-3 2-3-2-3 2V5a2 2 0 012-2z"/>
            </svg>
          </div>
          <div class="mt-4 font-extrabold text-lg" style="color: {{ $brown }};">Clear information</div>
          <div class="mt-1 text-sm text-gray-700">Compare key room details quickly, including facilities, pricing, and location.</div>
        </div>

        <!-- Card 3 -->
        <div class="sr-about-card rounded-2xl p-6">
          <div class="sr-about-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color: {{ $red }};" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 10-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2zm3-10V9a3 3 0 116 0v2H9z"/>
            </svg>
          </div>
          <div class="mt-4 font-extrabold text-lg" style="color: {{ $brown }};">Secure access</div>
          <div class="mt-1 text-sm text-gray-700">Login is required for full features, giving users a more protected and organized experience.</div>
        </div>

      </div>

    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="sr-home-footer text-[#fff8f6]">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">

      <div class="flex items-center gap-3">
        <div class="sr-footer-logo h-10 w-10 rounded-2xl grid place-items-center">
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

</body>
</html>