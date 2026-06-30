<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Register • Smart Rental</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    /* ===== Bubble animation (smooth + visible) ===== */
    @keyframes floatA {
      0%   { transform: translate3d(0,0,0) scale(1); }
      50%  { transform: translate3d(26px,-18px,0) scale(1.06); }
      100% { transform: translate3d(0,0,0) scale(1); }
    }
    @keyframes floatB {
      0%   { transform: translate3d(0,0,0) scale(1); }
      50%  { transform: translate3d(-22px,16px,0) scale(1.05); }
      100% { transform: translate3d(0,0,0) scale(1); }
    }
    @keyframes floatC {
      0%   { transform: translate3d(0,0,0) scale(1); }
      50%  { transform: translate3d(10px,22px,0) scale(1.04); }
      100% { transform: translate3d(0,0,0) scale(1); }
    }

    .f1 { animation: floatA 12s ease-in-out infinite; }
    .f2 { animation: floatB 15s ease-in-out infinite; }
    .f3 { animation: floatA 18s ease-in-out infinite; }
    .f4 { animation: floatB 20s ease-in-out infinite; }
    .f5 { animation: floatC 22s ease-in-out infinite; }

    /* ===== Softer glossy bubble look ===== */
    .bubble-gloss {
      background:
        radial-gradient(circle at 28% 24%, rgba(255,255,255,.38), transparent 34%),
        radial-gradient(circle at 70% 72%, rgba(255,255,255,.10), transparent 54%),
        linear-gradient(145deg, rgba(255,255,255,.10), rgba(255,255,255,.02));
      box-shadow:
        inset 0 1px 0 rgba(255,255,255,.28),
        inset 0 -10px 24px rgba(255,255,255,.03),
        0 10px 26px rgba(0,0,0,.08);
      backdrop-filter: blur(4px);
    }
  </style>
</head>

<body class="min-h-screen text-white">

  <!-- ===== Background (same rich red theme) ===== -->
  <div class="fixed inset-0 -z-30 bg-gradient-to-br from-[#211617] via-[#341b1f] to-[#1f1517]"></div>

  <!-- warm glow layer -->
  <div class="fixed inset-0 -z-30 bg-[radial-gradient(ellipse_at_top,rgba(185,28,28,0.22),transparent_55%),radial-gradient(ellipse_at_bottom,rgba(251,191,188,0.12),transparent_60%)]"></div>

  <!-- ===== Bubbles (improved only) ===== -->
  <div class="fixed inset-0 -z-20 pointer-events-none overflow-hidden opacity-95">

    <!-- Big blurred glow bubbles -->
    <div class="f1 absolute -top-28 left-8 h-[24rem] w-[24rem] rounded-full bg-white/38 blur-3xl"></div>
    <div class="f2 absolute top-10 right-10 h-[28rem] w-[28rem] rounded-full bg-red-400/18 blur-3xl"></div>
    <div class="f3 absolute bottom-10 left-10 h-[30rem] w-[30rem] rounded-full bg-rose-200/18 blur-3xl"></div>
    <div class="f4 absolute bottom-8 right-12 h-[24rem] w-[24rem] rounded-full bg-red-950/16 blur-3xl"></div>

    <!-- Medium premium bubbles -->
    <div class="f2 bubble-gloss absolute top-24 left-[14%] h-24 w-24 rounded-full bg-white/10 ring-1 ring-white/22"></div>
    <div class="f3 bubble-gloss absolute top-32 left-[40%] h-20 w-20 rounded-full bg-rose-100/10 ring-1 ring-white/18"></div>
    <div class="f1 bubble-gloss absolute top-24 right-[16%] h-28 w-28 rounded-full bg-rose-200/10 ring-1 ring-rose-100/18"></div>
    <div class="f5 bubble-gloss absolute top-[46%] right-[10%] h-24 w-24 rounded-full bg-white/10 ring-1 ring-white/20"></div>
    <div class="f4 bubble-gloss absolute top-[58%] left-[8%] h-28 w-28 rounded-full bg-white/8 ring-1 ring-white/18"></div>

    <!-- Extra medium bubbles -->
    <div class="f3 bubble-gloss absolute top-[18%] right-[40%] h-20 w-20 rounded-full bg-rose-100/8 ring-1 ring-white/16"></div>
    <div class="f2 bubble-gloss absolute top-[62%] right-[34%] h-24 w-24 rounded-full bg-white/8 ring-1 ring-white/14"></div>
    <div class="f1 bubble-gloss absolute bottom-[24%] left-[38%] h-20 w-20 rounded-full bg-rose-200/10 ring-1 ring-rose-100/16"></div>

    <!-- Small bubbles -->
    <div class="f1 bubble-gloss absolute top-[20%] left-[55%] h-14 w-14 rounded-full bg-white/10 ring-1 ring-white/18"></div>
    <div class="f2 bubble-gloss absolute top-[26%] left-[70%] h-10 w-10 rounded-full bg-white/12 ring-1 ring-white/18"></div>
    <div class="f3 bubble-gloss absolute top-[38%] left-[62%] h-12 w-12 rounded-full bg-rose-100/10 ring-1 ring-white/16"></div>

    <div class="f4 bubble-gloss absolute top-[64%] left-[20%] h-12 w-12 rounded-full bg-white/10 ring-1 ring-white/18"></div>
    <div class="f5 bubble-gloss absolute top-[72%] left-[35%] h-10 w-10 rounded-full bg-white/12 ring-1 ring-white/18"></div>
    <div class="f2 bubble-gloss absolute bottom-[18%] left-[55%] h-16 w-16 rounded-full bg-white/8 ring-1 ring-white/16"></div>

    <div class="f3 bubble-gloss absolute bottom-[12%] right-[25%] h-12 w-12 rounded-full bg-white/10 ring-1 ring-white/18"></div>
    <div class="f4 bubble-gloss absolute bottom-[20%] right-[12%] h-16 w-16 rounded-full bg-white/8 ring-1 ring-white/16"></div>
    <div class="f5 bubble-gloss absolute bottom-[28%] right-[40%] h-10 w-10 rounded-full bg-rose-100/10 ring-1 ring-white/16"></div>

    <!-- Extra tiny bubbles -->
    <div class="f1 bubble-gloss absolute top-[46%] left-[48%] h-9 w-9 rounded-full bg-white/12 ring-1 ring-white/16"></div>
    <div class="f2 bubble-gloss absolute bottom-[38%] right-[18%] h-9 w-9 rounded-full bg-white/10 ring-1 ring-white/14"></div>
    <div class="f3 bubble-gloss absolute bottom-[10%] left-[22%] h-8 w-8 rounded-full bg-white/12 ring-1 ring-white/14"></div>

  </div>

  <!-- ===== Page Content ===== -->
  <div class="min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-2xl">

      <!-- Brand row -->
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

      <!-- Main form card -->
      <div class="rounded-[26px] border border-white/18 bg-white/12 backdrop-blur-2xl
                  shadow-[0_25px_90px_rgba(0,0,0,.35)] overflow-hidden">

        <div class="p-7 sm:p-9">
          <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-bold text-white ring-1 ring-white/15">
              Student Registration
            </div>
            <h1 class="mt-4 text-3xl font-extrabold text-white leading-tight">
              Create your student account
            </h1>
            <p class="mt-1 text-sm text-white/70">
              Fill in your details. Fields marked <span class="font-bold text-white">*</span> are required.
            </p>
          </div>

          @if ($errors->any())
            <div class="mt-5 rounded-2xl bg-red-500/15 p-4 text-sm text-red-100 ring-1 ring-red-300/30">
              <div class="font-bold mb-1">Please fix the following:</div>
              <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('register.student.store') }}" class="mt-6 space-y-5">
            @csrf

            <div>
              <label class="text-sm font-bold text-white">Full Name *</label>
              <input name="name" value="{{ old('name') }}" required
                     class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                            placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45"
                     placeholder="e.g., Piriya">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label class="text-sm font-bold text-white">Age</label>
                <input name="age" value="{{ old('age') }}" inputmode="numeric"
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                              placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45"
                       placeholder="e.g., 21">
              </div>

              <div>
                <label class="text-sm font-bold text-white">Gender</label>
                <select name="gender"
                        class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                               outline-none focus:ring-2 focus:ring-red-400/45">
                  <option value="" class="text-black">Select gender</option>
                  <option value="male"   {{ old('gender') === 'male' ? 'selected' : '' }} class="text-black">Male</option>
                  <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }} class="text-black">Female</option>
                  <option value="other"  {{ old('gender') === 'other' ? 'selected' : '' }} class="text-black">Other</option>
                </select>
              </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label class="text-sm font-bold text-white">Student ID *</label>
                <input name="student_id" value="{{ old('student_id') }}" required
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                              placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45"
                       placeholder="e.g., 012025020771">
              </div>

              <div>
                <label class="text-sm font-bold text-white">Programme *</label>
                <input name="programme" value="{{ old('programme') }}" required
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                              placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45"
                       placeholder="e.g., BCS">
              </div>
            </div>

            <div class="space-y-4">
              <div>
                <label class="text-sm font-bold text-white">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                              placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45"
                       placeholder="example@student.msu.edu.my">
              </div>

              <div>
                <label class="text-sm font-bold text-white">Phone</label>
                <input name="phone" value="{{ old('phone') }}"
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                              placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45"
                       placeholder="e.g., 01X-XXXXXXX">
              </div>
            </div>

            <!-- Address -->
            <div class="space-y-4">
              <div>
                <label class="text-sm font-bold text-white">Address Line 1</label>
                <input name="address_line1" value="{{ old('address_line1') }}"
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                              placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45"
                       placeholder="House / Block / Street">
              </div>

              <div>
                <label class="text-sm font-bold text-white">Address Line 2</label>
                <input name="address_line2" value="{{ old('address_line2') }}"
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                              placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45"
                       placeholder="Area / Landmark (optional)">
              </div>

              <div class="grid gap-4 sm:grid-cols-3">
                <div>
                  <label class="text-sm font-bold text-white">City</label>
                  <input name="city" value="{{ old('city') }}"
                         class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                                placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45">
                </div>
                <div>
                  <label class="text-sm font-bold text-white">State</label>
                  <input name="state" value="{{ old('state') }}"
                         class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                                placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45">
                </div>
                <div>
                  <label class="text-sm font-bold text-white">Postcode</label>
                  <input name="postcode" value="{{ old('postcode') }}"
                         class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                                placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45">
                </div>
              </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label class="text-sm font-bold text-white">Password *</label>
                <input type="password" name="password" required
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                              placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45"
                       placeholder="Minimum 8 characters">
              </div>
              <div>
                <label class="text-sm font-bold text-white">Confirm Password *</label>
                <input type="password" name="password_confirmation" required
                       class="mt-1 w-full rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-white
                              placeholder-white/35 outline-none focus:ring-2 focus:ring-red-400/45">
              </div>
            </div>

            <button
              class="w-full rounded-xl bg-gradient-to-r from-[#7F1D1D] to-[#DC2626]
                     px-6 py-3.5 text-sm font-extrabold text-white
                     hover:brightness-110 transition
                     shadow-[0_16px_45px_rgba(239,68,68,.22)]">
              Create Student Account
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