@extends('layouts.admin')

@section('title', 'Manage Users • Smart Rental')

@section('content')
@php
  // 🔥 RED ADMIN THEME (same as dashboard)
  $primary = '#7F1D1D';
  $primarySoft = '#9A5E5E';
  $accent = '#DC2626';
@endphp

<div class="rounded-3xl border border-[#F3CACA] shadow-lg overflow-hidden"
     style="background: linear-gradient(135deg, #FFF7F7, #FDEEEE);">

  <!-- Header -->
  <div class="p-6 md:p-8 border-b border-[#F3CACA]">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

      <div>
        <div class="text-xs font-black tracking-wider text-[#B98B8B]">ADMIN</div>

        <h1 class="text-3xl md:text-4xl font-extrabold mt-1" style="color: {{ $primary }};">
          Manage Users
        </h1>

        <p class="mt-2 font-semibold" style="color: {{ $primarySoft }};">
          View students & landlords, search quickly, and open user profiles.
        </p>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-3 gap-3 w-full lg:w-auto">

        <div class="rounded-2xl border border-[#F3D2D2] bg-white px-5 py-4 shadow-sm">
          <div class="text-xs font-black text-[#B98B8B]">STUDENTS</div>
          <div class="text-2xl font-extrabold mt-1 text-[#2D1414]">
            {{ $studentsCount }}
          </div>
        </div>

        <div class="rounded-2xl border border-[#F3D2D2] bg-white px-5 py-4 shadow-sm">
          <div class="text-xs font-black text-[#B98B8B]">LANDLORDS</div>
          <div class="text-2xl font-extrabold mt-1 text-[#2D1414]">
            {{ $landlordsCount }}
          </div>
        </div>

        <div class="rounded-2xl border border-[#F3D2D2] bg-white px-5 py-4 shadow-sm">
          <div class="text-xs font-black text-[#B98B8B]">PENDING</div>
          <div class="text-2xl font-extrabold mt-1 text-[#DC2626]">
            {{ $pendingCount }}
          </div>
        </div>

      </div>
    </div>

    <!-- Tabs + Search -->
    <div class="mt-7 flex flex-col xl:flex-row xl:items-center gap-4">

      <div class="inline-flex rounded-2xl border border-[#F3D2D2] bg-white p-1 w-full xl:w-auto">

        <a href="{{ route('admin.users.index', ['tab' => 'students']) }}"
           class="flex-1 xl:flex-none rounded-xl px-5 py-3 font-extrabold text-sm text-center transition
           {{ $tab === 'students' ? 'text-white shadow-md' : 'text-[#9A5E5E]' }}"
           @if($tab === 'students') style="background: linear-gradient(135deg,#DC2626,#B91C1C);" @endif>
          🎓 Students
        </a>

        <a href="{{ route('admin.users.index', ['tab' => 'landlords']) }}"
           class="flex-1 xl:flex-none rounded-xl px-5 py-3 font-extrabold text-sm text-center transition
           {{ $tab === 'landlords' ? 'text-white shadow-md' : 'text-[#9A5E5E]' }}"
           @if($tab === 'landlords') style="background: linear-gradient(135deg,#DC2626,#B91C1C);" @endif>
          🏠 Landlords
        </a>

      </div>

      <form method="GET" class="flex-1 flex gap-3">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <input name="q" value="{{ $q }}"
               placeholder="Search name / email / phone"
               class="w-full rounded-2xl border border-[#F0CFCF] bg-white px-5 py-3 font-semibold text-[#3B0A0A]
                      focus:outline-none focus:ring-2 focus:ring-[#FCA5A5]" />

        <button class="rounded-2xl px-6 py-3 font-extrabold text-white shadow-md transition hover:scale-105"
                style="background: linear-gradient(135deg,#DC2626,#B91C1C);">
          Search
        </button>
      </form>
    </div>
  </div>

  <!-- Table -->
  <div class="p-4 md:p-6">
    <div class="rounded-3xl border border-[#F3D2D2] bg-white overflow-hidden shadow-sm">

      <div class="p-4 border-b border-[#F3D2D2] font-extrabold text-[#7F1D1D]">
        {{ $tab === 'landlords' ? 'Landlords' : 'Students' }}
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">

          <thead style="background: rgba(220,38,38,.05);" class="text-[#B98B8B] font-black">
            <tr>
              <th class="text-left px-5 py-4">User</th>
              <th class="text-left px-5 py-4">Email</th>
              <th class="text-left px-5 py-4">Phone</th>
              <th class="text-left px-5 py-4">Status</th>

              @if($tab === 'landlords')
                <th class="text-left px-5 py-4">Rooms</th>
              @endif

              <th class="text-right px-5 py-4">Action</th>
            </tr>
          </thead>

          <tbody>
            @forelse($users as $u)
              @php
                $name  = $u->name ?? '—';
                $email = $u->email ?? '—';
                $phone = $u->phone ?? '—';

                $status = strtolower((string)($u->landlord_status ?? ''));
                if ($status === '') $status = 'pending';

                $isApproved = $status === 'approved';
                $isRejected = $status === 'rejected';

                $roomsCount = (int)($u->rooms_count ?? 0);
              @endphp

              <tr class="border-t border-[#F3E3E3] hover:bg-[#FFF5F5] transition">

                <td class="px-5 py-5">
                  <div class="font-extrabold text-[#2D1414]">{{ $name }}</div>
                  <div class="text-[#B98B8B] text-xs font-semibold">
                    Role: {{ $tab === 'landlords' ? 'Landlord' : 'Student' }}
                  </div>
                </td>

                <td class="px-5 py-5 text-[#7B4A4A] font-semibold break-words">
                  {{ $email }}
                </td>

                <td class="px-5 py-5 text-[#7B4A4A] font-semibold">
                  {{ $phone }}
                </td>

                <td class="px-5 py-5">
                  @if($tab === 'landlords')
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold
                      {{ $isApproved ? 'bg-green-100 text-green-800' : ($isRejected ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                      {{ ucfirst($status) }}
                    </span>

                    @if($isRejected && !empty($u->landlord_rejected_reason))
                      <div class="text-xs text-[#B98B8B] mt-1">
                        Reason: {{ $u->landlord_rejected_reason }}
                      </div>
                    @endif
                  @else
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold bg-blue-100 text-blue-800">
                      Registered
                    </span>
                  @endif
                </td>

                @if($tab === 'landlords')
                  <td class="px-5 py-5 text-[#7B4A4A] font-semibold">
                    {{ $roomsCount }}
                  </td>
                @endif

                <td class="px-5 py-5 text-right">
                  <a href="{{ route('admin.users.show', $u->id) }}"
                     class="inline-flex items-center gap-2 rounded-2xl border border-[#F3D2D2]
                            px-4 py-2 font-extrabold text-[#7F1D1D] hover:bg-[#FFF1F1] transition">
                    View <span class="text-[#E5BABA]">›</span>
                  </a>
                </td>

              </tr>

            @empty
              <tr>
                <td colspan="{{ $tab === 'landlords' ? 6 : 5 }}"
                    class="px-5 py-10 text-center text-[#B98B8B] font-semibold">
                  No users found.
                </td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>

      <div class="p-4 border-t border-[#F3D2D2]">
        {{ $users->links() }}
      </div>

    </div>
  </div>

</div>
@endsection