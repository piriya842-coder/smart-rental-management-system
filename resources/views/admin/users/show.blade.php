@extends('layouts.admin')

@section('title', 'User Profile • Smart Rental')

@section('content')
@php
  // 🔥 RED ADMIN THEME (same as dashboard)
  $primary = '#7F1D1D';
  $primarySoft = '#9A5E5E';
  $accent = '#DC2626';

  $name = $user->name ?? 'User';
  $email = $user->email ?? '—';
  $phone = $user->phone ?? '—';
  $roleText = ucfirst((string)($role ?? 'user'));

  $landlordStatus = strtolower((string)($user->landlord_status ?? ''));
  if ($landlordStatus === '') $landlordStatus = 'pending';

  $isApproved = $landlordStatus === 'approved';
  $isRejected = $landlordStatus === 'rejected';
@endphp

<div class="rounded-3xl border border-[#F3CACA] shadow-lg overflow-hidden"
     style="background: linear-gradient(135deg, #FFF7F7, #FDEEEE);">

  <!-- Header -->
  <div class="p-6 md:p-8 border-b border-[#F3CACA]">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">

      <div class="min-w-0">
        <div class="text-xs font-black tracking-wider text-[#B98B8B]">ADMIN</div>

        <h1 class="mt-1 text-3xl md:text-4xl font-extrabold leading-tight"
            style="color: {{ $primary }};">
          {{ $name }}
        </h1>

        <div class="mt-3 flex flex-wrap items-center gap-3">
          @if(($role ?? '') === 'landlord')
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold
              {{ $isApproved ? 'bg-green-100 text-green-800' : ($isRejected ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
              {{ ucfirst($landlordStatus) }}
            </span>
          @else
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold bg-blue-100 text-blue-800">
              Registered
            </span>
          @endif

          <span class="text-sm font-extrabold text-[#9A5E5E]">
            Role: {{ $roleText }}
          </span>
        </div>

        @if(($role ?? '') === 'landlord' && $isRejected && !empty($user->landlord_rejected_reason))
          <div class="mt-2 text-sm text-[#9A5E5E]">
            <span class="font-extrabold">Reject Reason:</span> {{ $user->landlord_rejected_reason }}
          </div>
        @endif
      </div>

      <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.index', ['tab' => ($role ?? '') === 'landlord' ? 'landlords' : 'students']) }}"
           class="rounded-2xl px-5 py-3 font-extrabold border border-[#F3D2D2] bg-white/80 text-[#7F1D1D] hover:bg-white transition shadow-sm">
          ← Back
        </a>
      </div>

    </div>
  </div>

  <!-- Body -->
  <div class="p-6 md:p-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

      <!-- Details -->
      <div class="lg:col-span-7">
        <div class="rounded-3xl border border-[#F3D2D2] bg-white p-6 shadow-sm">
          <div class="font-extrabold text-lg text-[#7F1D1D]">User Details</div>

          <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- EMAIL -->
            <div class="rounded-2xl border border-[#F3D2D2] bg-[#FFF3F3] p-5">
              <div class="text-xs font-black text-[#B98B8B] tracking-wider">EMAIL</div>
              <div class="mt-1 font-extrabold text-base md:text-lg leading-snug break-words text-[#2D1414]">
                {{ $email }}
              </div>
            </div>

            <!-- PHONE -->
            <div class="rounded-2xl border border-[#F3D2D2] bg-[#FFF3F3] p-5">
              <div class="text-xs font-black text-[#B98B8B] tracking-wider">PHONE</div>
              <div class="mt-1 font-extrabold text-base md:text-lg leading-snug break-words text-[#2D1414]">
                {{ $phone }}
              </div>
            </div>

            <!-- JOINED -->
            <div class="rounded-2xl border border-[#F3D2D2] bg-[#FFF3F3] p-5">
              <div class="text-xs font-black text-[#B98B8B] tracking-wider">JOINED</div>
              <div class="mt-1 font-extrabold text-base md:text-lg leading-snug text-[#2D1414]">
                {{ optional($user->created_at)->format('d M Y, h:i A') ?? '—' }}
              </div>
            </div>

            <!-- USER ID -->
            <div class="rounded-2xl border border-[#F3D2D2] bg-[#FFF3F3] p-5">
              <div class="text-xs font-black text-[#B98B8B] tracking-wider">USER ID</div>
              <div class="mt-1 font-extrabold text-base md:text-lg leading-snug text-[#2D1414]">
                #{{ $user->id }}
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Activity -->
      <div class="lg:col-span-5">
        <div class="rounded-3xl border border-[#F3D2D2] bg-white p-6 h-full shadow-sm">
          <div class="font-extrabold text-lg text-[#7F1D1D]">Activity Summary</div>

          <div class="mt-5 grid gap-4"
               style="grid-template-columns: repeat({{ ($role ?? '') === 'landlord' ? 3 : 2 }}, minmax(0, 1fr));">

            @if(($role ?? '') === 'landlord')
              <div class="rounded-2xl border border-[#F3D2D2] bg-[#FFF3F3] p-5">
                <div class="text-xs font-black text-[#B98B8B] tracking-wider">TOTAL ROOMS</div>
                <div class="mt-2 text-3xl font-extrabold text-[#2D1414]">
                  {{ $roomsCount ?? 0 }}
                </div>
              </div>
            @endif

            <div class="rounded-2xl border border-[#F3D2D2] bg-[#FFF3F3] p-5">
              <div class="text-xs font-black text-[#B98B8B] tracking-wider">TOTAL BOOKINGS</div>
              <div class="mt-2 text-3xl font-extrabold text-[#2D1414]">
                {{ $bookingsCount ?? 0 }}
              </div>
            </div>

            <div class="rounded-2xl border border-[#F3D2D2] bg-[#FFF3F3] p-5">
              <div class="text-xs font-black text-[#B98B8B] tracking-wider">TOTAL PAYMENTS</div>
              <div class="mt-2 text-3xl font-extrabold text-[#DC2626]">
                {{ $paymentsCount ?? 0 }}
              </div>
            </div>

          </div>

          <div class="mt-5 text-sm text-[#9A5E5E] leading-relaxed font-semibold">
            This summary helps admin quickly audit user activity and detect suspicious behaviour.
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection