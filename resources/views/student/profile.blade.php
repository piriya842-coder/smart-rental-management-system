@extends('layouts.student')

@section('title', 'My Profile • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $cream   = '#fffafa';
  $choco   = '#4a2c2a';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';

  $accountId = 'T-' . str_pad((string)($user->id ?? 0), 6, '0', STR_PAD_LEFT);

  $lockedEmail     = $user->email ?? '-';
  $lockedStudentId = $user->student_id ?? '-';

  $p = $profile;
@endphp

<div class="max-w-3xl mx-auto">

  @if(session('success'))
    <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 font-semibold">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm overflow-hidden">

    <!-- Header -->
    <div class="p-6 md:p-8 border-b border-[rgba(201,42,42,.08)] bg-gradient-to-r from-white via-white to-[#fffafa]">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs font-black tracking-wider text-black/50">PROFILE</div>
          <h1 class="text-3xl md:text-4xl font-extrabold mt-1" style="color: {{ $choco }};">Profile</h1>
        </div>
        <a href="{{ route('student.dashboard') }}"
           class="rounded-2xl px-5 py-3 font-extrabold border border-[rgba(201,42,42,.12)] hover:bg-[#fff5f5] transition"
           style="color: {{ $choco }};">
          Back
        </a>
      </div>

      <!-- Center logo -->
      <div class="mt-6 flex flex-col items-center">
        <div class="h-20 w-20 rounded-full flex items-center justify-center shadow-sm overflow-hidden"
             style="background: {{ $softRed }}; border: 1px solid rgba(201,42,42,.10);">
          <img src="{{ asset('images/logo.png') }}"
               alt="Smart Rental Logo"
               class="h-[65%] w-[65%] object-contain">
        </div>

        <div class="mt-3 text-sm font-extrabold tracking-wide" style="color: {{ $choco }};">
          SMART RENTAL
        </div>
      </div>
    </div>

    <form method="POST" action="{{ url('/student/account') }}">
      @csrf

      <div class="p-6 md:p-8 space-y-6">

        <!-- PERSONAL INFORMATION -->
        <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white overflow-hidden">
          <div class="p-5 md:p-6 border-b border-[rgba(201,42,42,.10)] flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl flex items-center justify-center"
                 style="background: {{ $softRed }}; color: {{ $choco }};">👤</div>
            <div class="text-lg font-extrabold" style="color: {{ $choco }};">Personal Information</div>
          </div>

          <div class="p-5 md:p-6 divide-y divide-[rgba(201,42,42,.10)]">

            <!-- Account ID -->
            <div class="py-4 flex items-center justify-between gap-3">
              <div>
                <div class="text-sm font-bold text-black/60">Account ID</div>
                <div class="font-extrabold text-black/80" id="accountIdText">{{ $accountId }}</div>
              </div>
              <button type="button"
                      class="rounded-xl px-4 py-2 text-xs font-extrabold border border-[rgba(201,42,42,.12)] hover:bg-[#fff5f5] transition"
                      style="color: {{ $choco }};"
                      onclick="copyText('{{ $accountId }}')">
                Copy
              </button>
            </div>

            <!-- Name -->
            <div class="py-4 flex items-center justify-between gap-3">
              <div>
                <div class="text-sm font-bold text-black/60">Name</div>
                <div class="font-extrabold text-black/80">{{ $user->name }}</div>
              </div>
              <div class="text-xs font-extrabold text-black/40">Locked</div>
            </div>

            <!-- Email -->
            <div class="py-4 flex items-center justify-between gap-3">
              <div class="min-w-0">
                <div class="text-sm font-bold text-black/60">Email</div>
                <div class="font-extrabold text-black/80 truncate">{{ $lockedEmail }}</div>
              </div>
              <div class="text-xs font-extrabold text-black/40">Locked</div>
            </div>

            <!-- Student ID -->
            <div class="py-4 flex items-center justify-between gap-3">
              <div class="min-w-0">
                <div class="text-sm font-bold text-black/60">Student ID</div>
                <div class="font-extrabold text-black/80" id="studentIdText">{{ $lockedStudentId }}</div>
              </div>
              <button type="button"
                      class="rounded-xl px-4 py-2 text-xs font-extrabold border border-[rgba(201,42,42,.12)] hover:bg-[#fff5f5] transition"
                      style="color: {{ $choco }};"
                      onclick="copyText('{{ $lockedStudentId }}')">
                Copy
              </button>
            </div>

            <!-- NRIC -->
            <div class="py-4">
              <div class="text-sm font-bold text-black/60 mb-2">NRIC / Passport No.</div>
              <input type="text"
                     name="nric_passport"
                     value="{{ old('nric_passport', $profile->nric_passport ?? '') }}"
                     class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3"
                     placeholder="Type your IC / Passport No.">
            </div>

            <!-- Gender -->
            <div class="py-4">
              <div class="text-sm font-bold text-black/60 mb-2">Gender</div>
              @php $g = old('gender', $p->gender ?? $user->gender ?? ''); @endphp
              <select name="gender"
                      class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3">
                <option value="" {{ $g==''?'selected':'' }}>Select option</option>
                <option value="male" {{ $g=='male'?'selected':'' }}>Male</option>
                <option value="female" {{ $g=='female'?'selected':'' }}>Female</option>
              </select>
            </div>

            <!-- Contact Number -->
            <div class="py-4">
              <div class="text-sm font-bold text-black/60 mb-2">Contact Number</div>
              <input type="text" name="phone"
                     value="{{ old('phone', $p->phone ?? $user->phone) }}"
                     class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3"
                     placeholder="+6010xxxxxxx">
            </div>

            <!-- Nationality / Race / Religion -->
            <div class="py-4 grid grid-cols-1 md:grid-cols-3 gap-3">
              <div>
                <div class="text-sm font-bold text-black/60 mb-2">Nationality</div>
                <input type="text" name="nationality"
                       value="{{ old('nationality', $p->nationality) }}"
                       class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3"
                       placeholder="Malaysia">
              </div>
              <div>
                <div class="text-sm font-bold text-black/60 mb-2">Race</div>
                <input type="text" name="race"
                       value="{{ old('race', $p->race) }}"
                       class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3"
                       placeholder="e.g. Indian">
              </div>
              <div>
                <div class="text-sm font-bold text-black/60 mb-2">Religion</div>
                <input type="text" name="religion"
                       value="{{ old('religion', $p->religion) }}"
                       class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3"
                       placeholder="e.g. Hinduism">
              </div>
            </div>

            <!-- DOB -->
            @php
              $dobValue = old('date_of_birth');

              if ($dobValue === null && !empty($profile->date_of_birth)) {
                  $dobValue = \Carbon\Carbon::parse($profile->date_of_birth)->format('Y-m-d');
              }
            @endphp

            <div class="py-4">
              <div class="text-sm font-bold text-black/60 mb-2">Date of Birth</div>
              <input
                type="date"
                name="date_of_birth"
                value="{{ $dobValue }}"
                class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3"
              >
            </div>

          </div>
        </div>

        <!-- ADDRESS INFORMATION -->
        <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white overflow-hidden">
          <div class="p-5 md:p-6 border-b border-[rgba(201,42,42,.10)] flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl flex items-center justify-center"
                 style="background: {{ $softRed }}; color: {{ $choco }};">📍</div>
            <div class="text-lg font-extrabold" style="color: {{ $choco }};">Address Information</div>
          </div>

          <div class="p-5 md:p-6 space-y-4">
            <div>
              <div class="text-sm font-bold text-black/60 mb-2">Address Line 1</div>
              <input type="text" name="address_line1"
                     value="{{ old('address_line1', $p->address_line1) }}"
                     class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3">
            </div>

            <div>
              <div class="text-sm font-bold text-black/60 mb-2">Address Line 2</div>
              <input type="text" name="address_line2"
                     value="{{ old('address_line2', $p->address_line2) }}"
                     class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <div>
                <div class="text-sm font-bold text-black/60 mb-2">Postcode</div>
                <input type="text" name="postcode"
                       value="{{ old('postcode', $p->postcode) }}"
                       class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3">
              </div>
              <div>
                <div class="text-sm font-bold text-black/60 mb-2">City</div>
                <input type="text" name="city"
                       value="{{ old('city', $p->city) }}"
                       class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3">
              </div>
              <div>
                <div class="text-sm font-bold text-black/60 mb-2">State</div>
                <input type="text" name="state"
                       value="{{ old('state', $p->state) }}"
                       class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3">
              </div>
            </div>

            <div>
              <div class="text-sm font-bold text-black/60 mb-2">Country</div>
              <input type="text" name="country"
                     value="{{ old('country', $p->country ?? 'Malaysia') }}"
                     class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3">
            </div>
          </div>
        </div>

        <!-- EMERGENCY CONTACT -->
        <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white overflow-hidden">
          <div class="p-5 md:p-6 border-b border-[rgba(201,42,42,.10)] flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl flex items-center justify-center"
                 style="background: {{ $softRed }}; color: {{ $choco }};">🧑‍🧑‍🧒</div>
            <div class="text-lg font-extrabold" style="color: {{ $choco }};">Emergency Contact</div>
          </div>

          <div class="p-5 md:p-6 space-y-4">
            <div>
              <div class="text-sm font-bold text-black/60 mb-2">Name</div>
              <input type="text" name="emergency_name"
                     value="{{ old('emergency_name', $p->emergency_name) }}"
                     class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <div class="text-sm font-bold text-black/60 mb-2">Contact Number</div>
                <input type="text" name="emergency_phone"
                       value="{{ old('emergency_phone', $p->emergency_phone) }}"
                       class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3">
              </div>
              <div>
                <div class="text-sm font-bold text-black/60 mb-2">Relationship</div>
                <input type="text" name="emergency_relationship"
                       value="{{ old('emergency_relationship', $p->emergency_relationship) }}"
                       class="w-full rounded-2xl border-[rgba(201,42,42,.10)] bg-[#fffafa] focus:border-[rgba(201,42,42,.20)] focus:ring-0 px-4 py-3">
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-end">
          <a href="{{ route('student.dashboard') }}"
             class="rounded-2xl px-6 py-3 font-extrabold border border-[rgba(201,42,42,.12)] hover:bg-[#fff5f5] transition text-center"
             style="color: {{ $choco }};">
            Close
          </a>

          <button type="submit"
                  class="rounded-2xl px-6 py-3 font-extrabold text-white hover:brightness-95 transition"
                  style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
            Save
          </button>
        </div>

      </div>
    </form>
  </div>
</div>

<script>
  function copyText(text) {
    if (!text) return;
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text);
      alert('Copied!');
      return;
    }
    const t = document.createElement('textarea');
    t.value = text;
    document.body.appendChild(t);
    t.select();
    document.execCommand('copy');
    t.remove();
    alert('Copied!');
  }
</script>
@endsection