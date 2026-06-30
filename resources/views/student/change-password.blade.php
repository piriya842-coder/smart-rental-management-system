@extends('layouts.student')

@section('title', 'Change Password • Smart Rental')

@section('content')
@php
  $gold    = '#c92a2a';
  $cream   = '#fffafa';
  $choco   = '#4a2c2a';
  $redDark = '#a61e1e';
  $softRed = '#fdf2f2';
@endphp

<div class="max-w-3xl mx-auto">

  @if(session('success'))
    <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800 font-semibold">
      ✅ {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800">
      <div class="font-extrabold mb-2">Please fix the following:</div>
      <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $error)
          <li class="font-semibold">{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="rounded-3xl border border-[rgba(201,42,42,.08)] bg-white/90 shadow-sm overflow-hidden">

    <!-- HEADER -->
    <div class="p-7 md:p-9 border-b border-[rgba(201,42,42,.08)] bg-gradient-to-r from-white via-white to-[#fffafa]">
      <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
          <div class="text-xs font-black tracking-wider text-black/50">ACCOUNT SECURITY</div>
          <h1 class="text-3xl md:text-4xl font-extrabold mt-1" style="color: {{ $choco }};">
            Change Password
          </h1>
          <p class="mt-2 text-black/60 leading-relaxed max-w-2xl">
            Update your password to keep your Smart Rental account secure.
          </p>
        </div>

        <a href="{{ route('student.dashboard') }}"
           class="rounded-2xl px-6 py-3 font-extrabold border border-[rgba(201,42,42,.12)] hover:bg-[#fff5f5] transition text-center"
           style="color: {{ $choco }};">
          Back to Dashboard
        </a>
      </div>
    </div>

    <!-- BODY -->
    <div class="p-7 md:p-9">
      <form method="POST" action="{{ route('student.password.update') }}">
        @csrf

        <div class="space-y-5">

          <!-- Current Password -->
          <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white p-6">
            <label class="block text-sm font-bold text-black/60 mb-2">
              Current Password
            </label>
            <input type="password"
                   name="current_password"
                   class="w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-3 focus:border-[rgba(201,42,42,.20)] focus:ring-0"
                   placeholder="Enter your current password">
          </div>

          <!-- New Password -->
          <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white p-6">
            <label class="block text-sm font-bold text-black/60 mb-2">
              New Password
            </label>
            <input type="password"
                   name="password"
                   class="w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-3 focus:border-[rgba(201,42,42,.20)] focus:ring-0"
                   placeholder="Enter your new password">
            <div class="mt-2 text-xs text-black/45 font-semibold">
              Password must be at least 8 characters.
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="rounded-3xl border border-[rgba(201,42,42,.10)] bg-white p-6">
            <label class="block text-sm font-bold text-black/60 mb-2">
              Confirm New Password
            </label>
            <input type="password"
                   name="password_confirmation"
                   class="w-full rounded-2xl border border-[rgba(201,42,42,.10)] bg-[#fffafa] px-4 py-3 focus:border-[rgba(201,42,42,.20)] focus:ring-0"
                   placeholder="Confirm your new password">
          </div>

        </div>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:justify-end">
          <a href="{{ route('student.dashboard') }}"
             class="rounded-2xl px-6 py-3 font-extrabold border border-[rgba(201,42,42,.12)] hover:bg-[#fff5f5] transition text-center"
             style="color: {{ $choco }};">
            Cancel
          </a>

          <button type="submit"
                  class="rounded-2xl px-7 py-3 font-extrabold text-white shadow-sm hover:brightness-95 transition"
                  style="background: linear-gradient(135deg, {{ $gold }} 0%, {{ $redDark }} 100%);">
            Update Password
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection