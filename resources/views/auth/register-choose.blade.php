<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register • Smart Rental</title>

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
    <!-- main centered card (logo INSIDE) -->
    <div class="grid md:grid-cols-2 overflow-hidden rounded-[28px] shadow-2xl border border-white/10 bg-white/8 backdrop-blur-xl min-h-[520px] max-h-[640px]">
      <!-- LEFT PANEL -->
      <div class="relative p-9 md:p-10 flex flex-col justify-center"
           style="background: linear-gradient(135deg, rgba(127,29,29,.90) 0%, rgba(220,38,38,.72) 100%);">

        <!-- logo inside left panel -->
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
            Welcome.
          </h1>
          <p class="mt-4 text-white/90 leading-relaxed max-w-md">
            Find student-friendly rooms around MSU with clear listings, trusted information, and an easy experience from search to booking.
          </p>

          <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('login') }}"
               class="ring-brand inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-extrabold text-[#7F1D1D] hover:bg-[#FEF2F2] transition">
              Login
            </a>
          </div>

          <div class="mt-6 text-xs text-white/75">
            Landlord applications are reviewed before listing rooms to keep the platform trusted.
          </div>
        </div>
      </div>

      <!-- RIGHT PANEL -->
      <div class="p-8 md:p-10 flex flex-col justify-center"
           style="background: rgba(61,35,37,.66);">
        <div class="flex items-center justify-between">
          <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white border border-white/15">
            Select role
          </div>
          <div class="text-xs text-white/80">
            Already registered?
            <a class="font-extrabold text-[#FECACA] hover:underline" href="{{ route('login') }}">Login</a>
          </div>
        </div>

        <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white">
          Create your account
        </h2>
        <p class="mt-2 text-sm text-white/80">
          Register as a student, or apply as a landlord (verification required).
        </p>

        <div class="mt-6 space-y-4">
          <!-- Student card -->
          <a href="{{ route('register.student') }}"
             class="group block rounded-2xl border border-white/10 bg-white/10 p-6 shadow-sm hover:bg-white/14 hover:shadow-md transition hover:-translate-y-0.5">
            <div class="flex items-start justify-between gap-4">
              <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-2xl bg-white/10 border border-white/10 grid place-items-center">
                  <!-- icon user -->
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="text-[#FDE68A]">
                    <path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 11a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </div>

                <div>
                  <div class="flex items-center gap-2">
                    <div class="text-lg font-extrabold text-white">Student</div>
                    <span class="rounded-full bg-[#FDE68A]/20 px-2.5 py-1 text-[11px] font-bold text-[#FFF7ED]">
                      For MSU students
                    </span>
                  </div>
                  <div class="mt-2 text-sm text-white/80 max-w-lg">
                    Explore rooms, compare facilities, and save favourites for faster decisions.
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2 text-sm font-extrabold text-[#FAF6F2]">
                Continue <span class="transition group-hover:translate-x-1">→</span>
              </div>
            </div>
          </a>

          <!-- Landlord card -->
          <a href="{{ route('register.landlord') }}"
             class="group block rounded-2xl border border-white/10 bg-white/10 p-6 shadow-sm hover:bg-white/14 hover:shadow-md transition hover:-translate-y-0.5">
            <div class="flex items-start justify-between gap-4">
              <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-2xl bg-white/10 border border-white/10 grid place-items-center">
                  <!-- icon home -->
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="text-[#FCA5A5]">
                    <path d="M3 11l9-8 9 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 10v11h14V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>

                <div>
                  <div class="flex items-center gap-2">
                    <div class="text-lg font-extrabold text-white">Landlord</div>
                    <span class="rounded-full bg-[#FCA5A5]/18 px-2.5 py-1 text-[11px] font-bold text-[#FFF1F2]">
                      Verification required
                    </span>
                  </div>
                  <div class="mt-2 text-sm text-white/80 max-w-lg">
                    Submit your details for review before posting and managing listings.
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2 text-sm font-extrabold text-[#FAF6F2]">
                Apply <span class="transition group-hover:translate-x-1">→</span>
              </div>
            </div>
          </a>
        </div>

        <div class="mt-6 text-xs text-white/70">
          Admin will approve landlord accounts before rooms can be published.
        </div>
      </div>
    </div>
  </div>
</body>
</html>