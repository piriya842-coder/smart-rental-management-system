<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login • Smart Rental</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    /* ----------------------------
      Rich MSU-inspired background
    ---------------------------- */
    .bg-hero {
      background:
        radial-gradient(1200px 800px at 18% 10%, rgba(153,27,27,.42), transparent 60%),
        radial-gradient(900px 700px at 85% 18%, rgba(239,68,68,.26), transparent 55%),
        radial-gradient(1000px 800px at 55% 90%, rgba(251,191,188,.18), transparent 60%),
        linear-gradient(180deg, #211617 0%, #341b1f 35%, #1f1517 100%);
      position: relative;
      overflow: hidden;
    }

    /* ----------------------------
      Bubble Animation
    ---------------------------- */
    .bubble-wrap {
      position: absolute;
      inset: 0;
      pointer-events: none;
      overflow: hidden;
      isolation: isolate;
    }

    .bubble {
      position: absolute;
      border-radius: 9999px;
      opacity: .95;
      mix-blend-mode: screen;
      filter: blur(.2px);
      transform: translate3d(0,0,0);
      animation: floaty var(--dur, 12s) ease-in-out infinite;
      background:
        radial-gradient(circle at 30% 30%, rgba(255,255,255,.75), rgba(255,255,255,.10) 40%, rgba(255,255,255,0) 65%),
        radial-gradient(circle at 70% 70%, rgba(255,255,255,.18), rgba(255,255,255,0) 60%);
      box-shadow:
        0 18px 60px rgba(0,0,0,.25),
        inset 0 1px 2px rgba(255,255,255,.25);
    }

    .b1 { background-color: rgba(185,28,28,.28); }
    .b2 { background-color: rgba(251,191,188,.24); }
    .b3 { background-color: rgba(254,242,242,.20); }
    .b4 { background-color: rgba(127,29,29,.22); }

    @keyframes floaty {
      0%,100% { transform: translate3d(0,0,0) scale(1); }
      50% { transform: translate3d(var(--x, 28px), var(--y, -26px), 0) scale(1.05); }
    }

    @keyframes drift {
      0% { transform: translateX(-3%); }
      50% { transform: translateX(3%); }
      100% { transform: translateX(-3%); }
    }

    /* card entrance */
    .pop { animation: popIn .55s cubic-bezier(.2,.85,.25,1) both; }
    @keyframes popIn {
      from { opacity: 0; transform: translateY(12px) scale(.98); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* keep single screen */
    .fit-card { min-height: 520px; max-height: 640px; }

    /* focus ring brand */
    .ring-brand:focus {
      outline: none;
      box-shadow: 0 0 0 4px rgba(239,68,68,.28);
    }

    /* inputs look nicer */
    .input-dark {
      background: rgba(255,255,255,.12);
      border: 1px solid rgba(255,255,255,.15);
      color: #fff;
    }

    .input-dark::placeholder {
      color: rgba(255,255,255,.55);
    }

    .input-dark:focus {
      outline: none;
      border-color: rgba(255,255,255,.28);
      box-shadow: 0 0 0 4px rgba(239,68,68,.22);
      background: rgba(255,255,255,.14);
    }
  </style>
</head>

<body class="bg-hero text-gray-900 min-h-screen flex items-center justify-center px-4 py-10">

  <!-- animated bubble layer -->
  <div class="bubble-wrap" style="animation: drift 12s ease-in-out infinite;">
    <div class="bubble b1" style="width:240px;height:240px;left:5%;top:12%;--dur:10s;--x:28px;--y:-26px;"></div>
    <div class="bubble b2" style="width:360px;height:360px;left:68%;top:8%;--dur:13s;--x:-22px;--y:22px;"></div>
    <div class="bubble b3" style="width:280px;height:280px;left:16%;top:62%;--dur:12s;--x:26px;--y:18px;"></div>
    <div class="bubble b4" style="width:200px;height:200px;left:78%;top:70%;--dur:9s;--x:-18px;--y:-24px;"></div>
    <div class="bubble b2" style="width:160px;height:160px;left:46%;top:22%;--dur:8s;--x:16px;--y:-14px;"></div>
    <div class="bubble b1" style="width:140px;height:140px;left:86%;top:34%;--dur:7s;--x:-14px;--y:16px;"></div>
  </div>

  <!-- CONTENT -->
  <div class="w-full max-w-5xl pop fit-card">

    <div class="grid md:grid-cols-2 overflow-hidden rounded-[28px] shadow-2xl border border-white/10 bg-white/8 backdrop-blur-xl min-h-[520px] max-h-[640px]">

      <!-- LEFT PANEL -->
      <div class="relative p-9 md:p-10 flex flex-col justify-center"
           style="background: linear-gradient(135deg, rgba(127,29,29,.90) 0%, rgba(220,38,38,.72) 100%);">

        <div class="flex items-center gap-3 mb-8">
          <div class="h-12 w-12 rounded-2xl flex items-center justify-center overflow-hidden"
               style="background:linear-gradient(135deg,#F3E8E2,#EADAD2); border:1px solid rgba(255,255,255,.14); box-shadow:0 6px 18px rgba(0,0,0,.12), inset 0 1px 0 rgba(255,255,255,.35);">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Smart Rental Logo"
                 class="h-[60%] w-[60%] object-contain">
          </div>
          <div class="leading-tight text-white">
            <div class="text-lg font-extrabold tracking-tight">Smart Rental</div>
            <div class="text-xs text-white/80 -mt-0.5">MSU Student Accommodation</div>
          </div>
        </div>

        <div class="text-white">
          <h1 class="text-4xl font-extrabold tracking-tight leading-tight">
            Welcome back.
          </h1>

          <p class="mt-4 text-white/90 leading-relaxed max-w-md">
            Login to explore rooms, manage applications, and access your dashboard.
          </p>

          <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('register') }}"
               class="ring-brand inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-extrabold text-[#7F1D1D] hover:bg-[#FEF2F2] transition">
              Create new account
            </a>

            <a href="{{ route('home') }}"
               class="ring-brand inline-flex items-center justify-center rounded-xl bg-white/12 px-5 py-3 text-sm font-extrabold text-white hover:bg-white/18 transition border border-white/15">
              Go to Home
            </a>
          </div>

          <div class="mt-6 text-xs text-white/75">
            Tip: Landlords may need admin approval before accessing landlord dashboard.
          </div>
        </div>
      </div>

      <!-- RIGHT PANEL -->
      <div class="p-8 md:p-10 flex flex-col justify-center"
           style="background: rgba(61,35,37,.66);">

        <div class="flex items-center justify-between">
          <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white border border-white/15">
            Login
          </div>
          <div class="text-xs text-white/80">
            Don’t have an account?
            <a class="font-extrabold text-[#FECACA] hover:underline" href="{{ route('register') }}">Register</a>
          </div>
        </div>

        <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white">
          Sign in to your account
        </h2>
        <p class="mt-2 text-sm text-white/80">
          Use your email and password to continue.
        </p>

        {{-- Session status --}}
        @if (session('status'))
          <div class="mt-5 rounded-xl bg-green-500/15 border border-green-400/25 px-4 py-3 text-sm text-white">
            {{ session('status') }}
          </div>
        @endif

        {{-- Errors --}}
        @if ($errors->any())
          <div class="mt-5 rounded-xl bg-red-500/15 border border-red-400/25 px-4 py-3 text-sm text-white">
            <div class="font-bold mb-1">Please fix:</div>
            <ul class="list-disc pl-5 space-y-1">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
          @csrf

          <div>
            <label class="block text-sm font-bold text-white">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="mt-2 w-full rounded-xl px-4 py-3 input-dark"
                   placeholder="you@example.com">
          </div>

          <div>
            <label class="block text-sm font-bold text-white">Password</label>
            <input type="password" name="password" required
                   class="mt-2 w-full rounded-xl px-4 py-3 input-dark"
                   placeholder="••••••••">
          </div>

          <div class="flex items-center justify-between pt-1">
            <label class="inline-flex items-center gap-2 text-sm text-white/80">
              <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/30 bg-white/10">
              Remember me
            </label>

            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}"
                 class="text-sm font-bold text-[#FECACA] hover:underline">
                Forgot password?
              </a>
            @endif
          </div>

          <button type="submit"
                  class="w-full rounded-xl px-5 py-3 text-sm font-extrabold text-white transition"
                  style="background: rgba(127,29,29,.92); border: 1px solid rgba(255,255,255,.10);"
                  onmouseover="this.style.filter='brightness(0.92)'"
                  onmouseout="this.style.filter='brightness(1)'">
            Log In
          </button>

          <div class="pt-2 text-center text-xs text-white/65">
            By logging in, you agree to use Smart Rental responsibly.
          </div>
        </form>

      </div>
    </div>
  </div>
</body>
</html>