@extends('layouts.landlord')

@section('title', 'Application Rejected • Smart Rental')

@section('content')
@php
  $choco = '#683B2B';
  $user = auth()->user();
@endphp

<div class="rounded-3xl border border-black/5 bg-white/90 shadow-sm p-8">
  <h1 class="text-3xl font-extrabold" style="color: {{ $choco }};">Application Rejected</h1>

  <p class="mt-3 text-gray-700">
    Your landlord application was rejected by the admin.
  </p>

  <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5">
    <div class="font-extrabold text-red-700">Rejection Reason</div>

    <div class="mt-2 text-red-800">
      {{ $user->landlord_rejected_reason ?: 'No reason provided.' }}
    </div>
  </div>

  <div class="mt-6 text-sm text-gray-600">
    If you believe this is a mistake, please contact the admin and update your details before re-applying (if your system supports re-apply later).
  </div>

  <div class="mt-8 flex flex-wrap gap-3">
    <a href="{{ route('home') }}"
       class="rounded-2xl px-5 py-3 text-sm font-extrabold text-white"
       style="background: {{ $choco }};">
      Back to Home
    </a>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"
              class="rounded-2xl px-5 py-3 text-sm font-extrabold border border-black/10 bg-white">
        Logout
      </button>
    </form>
  </div>
</div>
@endsection
