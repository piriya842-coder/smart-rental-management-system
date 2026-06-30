<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Landlord Application Pending - Smart Rental</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    .bubble {
      position: absolute;
      border-radius: 9999px;
      background: rgba(176, 132, 1, 0.10); /* gold tint */
      border: 1px solid rgba(104, 59, 43, 0.10); /* choco tint */
      animation: floaty 10s ease-in-out infinite;
      filter: blur(0.2px);
    }
    @keyframes floaty {
      0%,100% { transform: translateY(0px); opacity: .40; }
      50% { transform: translateY(-18px); opacity: .65; }
    }
  </style>
</head>

<body class="min-h-screen text-gray-900">

@php
  $gold  = '#B08401';
  $sand  = '#DED1BD';
  $cream = '#FAF6F2';
  $choco = '#683B2B';
@endphp

<div class="relative min-h-screen overflow-hidden"
     style="
      background:
        radial-gradient(900px 500px at 15% 25%, rgba(222,209,189,.65), transparent 60%),
        radial-gradient(700px 500px at 80% 75%, rgba(176,132,1,.25), transparent 55%),
        linear-gradient(135deg, {{ $cream }}, #F2E9DE);
     ">

  <!-- bubbles -->
  <span class="bubble" style="width:240px;height:240px;left:-60px;top:80px;animation-duration:12s;"></span>
  <span class="bubble" style="width:140px;height:140px;left:120px;top:420px;animation-duration:10s;"></span>
  <span class="bubble" style="width:180px;height:180px;right:-50px;top:140px;animation-duration:11s;"></span>
  <span class="bubble" style="width:120px;height:120px;right:220px;top:380px;animation-duration:9s;"></span>
  <span class="bubble" style="width:90px;height:90px;left:55%;top:110px;animation-duration:8s;"></span>

  <div class="min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-2xl rounded-3xl border shadow-2xl"
         style="background: rgba(255,255,255,0.85); border-color: rgba(0,0,0,0.06);">

      <div class="p-8 sm:p-10">

        <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold"
             style="background: rgba(176,132,1,0.12); color: {{ $choco }}; border: 1px solid rgba(176,132,1,0.18);">
          ✅ Application Submitted
        </div>

        <h1 class="mt-5 text-3xl sm:text-4xl font-extrabold tracking-tight"
            style="color: {{ $choco }};">
          Thanks! Your landlord application is pending.
        </h1>

        <p class="mt-4 text-gray-700 leading-relaxed">
          Admin will review your details first. For now, you cannot access the landlord dashboard until your status becomes
          <span class="font-extrabold" style="color: {{ $gold }};">approved</span>.
        </p>

        <div class="mt-7 flex flex-col sm:flex-row gap-3">
          <a href="{{ route('home') }}"
             class="rounded-xl px-6 py-3 text-sm font-bold text-white text-center transition shadow"
             style="background: {{ $choco }};"
             onmouseover="this.style.filter='brightness(0.92)'"
             onmouseout="this.style.filter='brightness(1)'">
            Go to Home
          </a>

          <a href="{{ route('register') }}"
             class="rounded-xl px-6 py-3 text-sm font-bold text-center transition shadow-sm"
             style="background: white; color: {{ $choco }}; border: 1px solid rgba(0,0,0,0.08);">
            Go to Register
          </a>

          <form method="POST" action="{{ route('logout') }}" class="sm:ml-auto">
            @csrf
            <button type="submit"
              class="w-full rounded-xl px-6 py-3 text-sm font-bold text-center transition shadow"
              style="background: {{ $gold }}; color: white;"
              onmouseover="this.style.filter='brightness(0.92)'"
              onmouseout="this.style.filter='brightness(1)'">
              Logout
            </button>
          </form>
        </div>

        <div class="mt-6 text-sm text-gray-600">
          Tip: After admin approves you, just login again and you will be able to access the landlord dashboard.
        </div>

      </div>
    </div>
  </div>

</div>

</body>
</html>
