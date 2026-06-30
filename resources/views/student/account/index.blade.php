@extends('layouts.student')
@section('title','My Account • Smart Rental')
@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
  <div class="rounded-3xl border border-black/5 bg-white p-8 shadow-sm">
    <h1 class="text-3xl font-extrabold text-[#683B2B]">My Account</h1>
    <p class="mt-2 text-gray-700">Profile settings will be managed here (link to Breeze profile if needed).</p>

    <div class="mt-6">
      <a href="{{ route('profile.edit') }}"
         class="inline-flex rounded-xl px-5 py-3 text-sm font-extrabold text-white shadow"
         style="background:#B08401;">
        Update Profile
      </a>
    </div>
  </div>
</div>
@endsection
