<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Landlord Register • Smart Rental</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    @keyframes floatA {
      0% { transform: translate3d(0,0,0) scale(1); }
      50% { transform: translate3d(22px,-14px,0) scale(1.05); }
      100% { transform: translate3d(0,0,0) scale(1); }
    }
    @keyframes floatB {
      0% { transform: translate3d(0,0,0) scale(1); }
      50% { transform: translate3d(-18px,16px,0) scale(1.06); }
      100% { transform: translate3d(0,0,0) scale(1); }
    }
    @keyframes floatC {
      0% { transform: translate3d(0,0,0) scale(1); }
      50% { transform: translate3d(10px,22px,0) scale(1.04); }
      100% { transform: translate3d(0,0,0) scale(1); }
    }

    .b1{animation:floatA 12s ease-in-out infinite}
    .b2{animation:floatB 15s ease-in-out infinite}
    .b3{animation:floatA 18s ease-in-out infinite}
    .b4{animation:floatB 20s ease-in-out infinite}
    .b5{animation:floatC 22s ease-in-out infinite}

    /* premium glass bubble */
    .bubble-gloss {
      background:
        radial-gradient(circle at 28% 24%, rgba(255,255,255,.35), transparent 35%),
        radial-gradient(circle at 70% 72%, rgba(255,255,255,.10), transparent 55%),
        linear-gradient(145deg, rgba(255,255,255,.10), rgba(255,255,255,.02));
      box-shadow:
        inset 0 1px 0 rgba(255,255,255,.25),
        inset 0 -10px 24px rgba(255,255,255,.03),
        0 10px 26px rgba(0,0,0,.08);
      backdrop-filter: blur(4px);
    }
  </style>
</head>

<body class="min-h-screen text-white">

  <div class="fixed inset-0 -z-20 bg-gradient-to-br from-[#211617] via-[#341b1f] to-[#1f1517]"></div>
  <div class="fixed inset-0 -z-20 bg-[radial-gradient(ellipse_at_top,rgba(185,28,28,0.20),transparent_55%),radial-gradient(ellipse_at_bottom,rgba(251,191,188,0.12),transparent_60%)]"></div>

  <!-- IMPROVED BUBBLES -->
  <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden opacity-95">

    <!-- Big blurred glow bubbles -->
    <div class="b1 absolute -top-24 left-10 h-72 w-72 rounded-full bg-white/35 blur-3xl"></div>
    <div class="b2 absolute top-24 right-16 h-[26rem] w-[26rem] rounded-full bg-red-400/16 blur-3xl"></div>
    <div class="b3 absolute bottom-10 left-24 h-[28rem] w-[28rem] rounded-full bg-rose-200/14 blur-3xl"></div>
    <div class="b4 absolute bottom-16 right-20 h-72 w-72 rounded-full bg-red-900/12 blur-3xl"></div>

    <!-- Medium premium bubbles -->
    <div class="b2 bubble-gloss absolute top-[22%] left-[8%] h-24 w-24 rounded-full bg-white/10 ring-1 ring-white/18"></div>
    <div class="b1 bubble-gloss absolute top-[54%] right-[10%] h-24 w-24 rounded-full bg-white/8 ring-1 ring-white/16"></div>
    <div class="b3 bubble-gloss absolute bottom-[18%] left-[54%] h-16 w-16 rounded-full bg-rose-100/10 ring-1 ring-white/14"></div>
    <div class="b4 bubble-gloss absolute bottom-[10%] right-[38%] h-14 w-14 rounded-full bg-white/10 ring-1 ring-white/14"></div>
    <div class="b5 bubble-gloss absolute top-[18%] right-[18%] h-28 w-28 rounded-full bg-rose-200/10 ring-1 ring-rose-100/16"></div>
    <div class="b3 bubble-gloss absolute bottom-[26%] left-[16%] h-20 w-20 rounded-full bg-white/10 ring-1 ring-white/16"></div>
    <div class="b2 bubble-gloss absolute top-[66%] right-[18%] h-16 w-16 rounded-full bg-white/10 ring-1 ring-white/16"></div>

    <!-- Small bubbles -->
    <div class="b1 bubble-gloss absolute top-[30%] left-[24%] h-12 w-12 rounded-full bg-white/12 ring-1 ring-white/16"></div>
    <div class="b2 bubble-gloss absolute top-[40%] right-[26%] h-10 w-10 rounded-full bg-white/12 ring-1 ring-white/16"></div>
    <div class="b3 bubble-gloss absolute top-[72%] left-[28%] h-10 w-10 rounded-full bg-white/10 ring-1 ring-white/14"></div>
    <div class="b4 bubble-gloss absolute bottom-[18%] right-[12%] h-12 w-12 rounded-full bg-white/10 ring-1 ring-white/14"></div>
    <div class="b5 bubble-gloss absolute bottom-[36%] left-[68%] h-9 w-9 rounded-full bg-rose-100/10 ring-1 ring-white/14"></div>
    <div class="b2 bubble-gloss absolute top-[14%] left-[52%] h-10 w-10 rounded-full bg-white/12 ring-1 ring-white/16"></div>
    <div class="b1 bubble-gloss absolute bottom-[12%] left-[44%] h-8 w-8 rounded-full bg-white/10 ring-1 ring-white/14"></div>
  </div>

  <div class="min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-2xl">

      <div class="mb-5 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="h-11 w-11 rounded-2xl flex items-center justify-center overflow-hidden"
               style="background:linear-gradient(135deg,#F3E8E2,#EADAD2); border:1px solid rgba(255,255,255,.14); box-shadow:0 6px 18px rgba(0,0,0,.12), inset 0 1px 0 rgba(255,255,255,.35);">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Smart Rental Logo"
                 class="h-[60%] w-[60%] object-contain">
          </div>
          <div>
            <div class="text-lg font-extrabold leading-tight">Smart Rental</div>
            <div class="text-xs text-white/70 -mt-0.5">MSU Student Accommodation</div>
          </div>
        </div>

        <div class="flex gap-2">
          <a href="{{ route('register') }}"
             class="rounded-xl bg-white/10 px-4 py-2 text-sm font-bold text-white ring-1 ring-white/20 hover:bg-white/15 transition">
            Back
          </a>
          <a href="{{ route('login') }}"
             class="rounded-xl bg-white px-4 py-2 text-sm font-extrabold text-[#7F1D1D] hover:bg-[#FEF2F2] transition">
            Login
          </a>
        </div>
      </div>

      <div class="rounded-[26px] border border-white/18 bg-white/12 backdrop-blur-2xl shadow-[0_25px_90px_rgba(0,0,0,.35)] overflow-hidden">
        <div class="p-7 sm:p-9">

          <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-bold text-white ring-1 ring-white/15">
            Landlord Application
          </div>

          <h1 class="mt-4 text-3xl font-extrabold leading-tight">
            Apply as a Landlord
          </h1>
          <p class="mt-1 text-sm text-white/70">
            Your account will be <span class="font-bold text-white">reviewed by admin</span> before you can list rooms.
          </p>

          @if ($errors->any())
            <div class="mt-5 rounded-2xl bg-red-500/15 p-4 text-sm text-red-100 ring-1 ring-red-300/30">
              <div class="font-bold mb-1">Please fix the following:</div>
              <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('register.landlord.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
              <div class="sm:col-span-2">
                <label class="text-sm font-bold">Full Name *</label>
                <input name="name" value="{{ old('name') }}" required
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45">
              </div>

              <div class="sm:col-span-2">
                <label class="text-sm font-bold">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45">
              </div>

              <div class="sm:col-span-2">
                <label class="text-sm font-bold">Company / Business Name *</label>
                <input name="company_name" value="{{ old('company_name') }}" required
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45"
                       placeholder="e.g., Seri Murni Homestay">
              </div>

              <div>
                <label class="text-sm font-bold">Phone</label>
                <input name="phone" value="{{ old('phone') }}"
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45">
              </div>

              <div>
                <label class="text-sm font-bold">Business Address</label>
                <input name="address" value="{{ old('address') }}"
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45"
                       placeholder="e.g., Seremban, Negeri Sembilan">
              </div>

              <div class="sm:col-span-2">
                <label class="text-sm font-bold">Verification Document *</label>
                <input type="file" name="verification_document" required accept=".jpg,.jpeg,.png,.pdf"
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white file:mr-4 file:rounded-lg file:border-0 file:bg-white/90 file:px-4 file:py-2 file:text-sm file:font-bold file:text-[#7F1D1D] outline-none focus:ring-2 focus:ring-red-400/45">
                <p class="mt-2 text-xs text-white/65">
                  Upload IC, passport, business registration, or other supporting document. Accepted: JPG, JPEG, PNG, PDF (max 5MB).
                </p>
              </div>

              <div>
                <label class="text-sm font-bold">Password *</label>
                <input type="password" name="password" required
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45">
              </div>

              <div>
                <label class="text-sm font-bold">Confirm Password *</label>
                <input type="password" name="password_confirmation" required
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45">
              </div>
            </div>

            <button class="w-full rounded-xl bg-gradient-to-r from-[#7F1D1D] to-[#DC2626]
                           px-6 py-3.5 text-sm font-extrabold text-white hover:brightness-110 transition
                           shadow-[0_16px_45px_rgba(239,68,68,.22)]">
              Submit Application
            </button>

            <p class="text-center text-sm text-white/70">
              Already have an account?
              <a class="font-extrabold text-white hover:underline" href="{{ route('login') }}">Login</a>
            </p>
          </form>
        </div>
      </div>

      <div class="mt-6 text-center text-xs text-white/55">
        Smart Rental • MSU Student Accommodation
      </div>
    </div>
  </div>
</body>
</html>