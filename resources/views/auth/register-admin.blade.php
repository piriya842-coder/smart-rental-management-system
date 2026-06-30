<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Register • Smart Rental</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#faf7f4] text-gray-900">
  <div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-xl rounded-3xl border border-black/5 bg-white shadow-sm p-8 sm:p-10">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-extrabold text-[#2b1d17]">Admin Registration</h1>
          <p class="mt-1 text-sm text-gray-700">Demo admin account (we can lock later).</p>
        </div>
        <a href="{{ route('register') }}" class="text-sm font-bold text-[#3b2a22] hover:underline">Back</a>
      </div>

      @if ($errors->any())
        <div class="mt-4 rounded-2xl bg-red-50 p-4 text-sm text-red-700">
          <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('register.admin.store') }}" class="mt-6 space-y-4">
        @csrf

        <div>
          <label class="text-sm font-semibold">Full Name</label>
          <input name="name" value="{{ old('name') }}" required
                 class="mt-1 w-full rounded-xl border border-black/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3b2a22]/30">
        </div>

        <div>
          <label class="text-sm font-semibold">Email</label>
          <input type="email" name="email" value="{{ old('email') }}" required
                 class="mt-1 w-full rounded-xl border border-black/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3b2a22]/30">
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="text-sm font-semibold">Password</label>
            <input type="password" name="password" required
                   class="mt-1 w-full rounded-xl border border-black/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3b2a22]/30">
          </div>
          <div>
            <label class="text-sm font-semibold">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                   class="mt-1 w-full rounded-xl border border-black/10 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#3b2a22]/30">
          </div>
        </div>

        <button class="w-full rounded-xl bg-[#3b2a22] px-6 py-3 text-sm font-extrabold text-white hover:bg-[#2f211b] transition">
          Create Admin Account
        </button>

        <p class="text-center text-sm text-gray-700">
          Already have an account?
          <a class="font-bold text-[#3b2a22] hover:underline" href="{{ route('login') }}">Login</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>
