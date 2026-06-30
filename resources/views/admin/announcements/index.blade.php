@extends('layouts.admin')

@section('title','Announcements')

@section('content')
@php
    $primary = '#7F1D1D';
    $primarySoft = '#9A5E5E';
    $accent = '#DC2626';
    $accentSoft = '#B91C1C';
@endphp

<div class="p-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <div class="text-xs font-black tracking-wider text-[#B98B8B]">SYSTEM</div>
            <h1 class="text-3xl font-extrabold text-[#7F1D1D]">Announcements</h1>
            <p class="text-sm text-[#9A5E5E] mt-1 font-semibold">
                Manage and publish announcements for all users.
            </p>
        </div>

        <a href="{{ route('admin.announcements.create') }}"
           class="rounded-2xl px-6 py-3 font-extrabold text-white shadow-md transition hover:scale-[1.02]"
           style="background: linear-gradient(135deg, {{ $accent }}, {{ $accentSoft }});">
            + Create
        </a>
    </div>

    <!-- LIST -->
    <div class="space-y-4">
        @foreach($announcements as $a)

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-[#F3CACA] hover:shadow-md transition">

                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                    <!-- LEFT -->
                    <div>
                        <h2 class="font-extrabold text-xl text-[#2D1414]">
                            {{ $a->title }}
                        </h2>

                        <p class="text-sm text-[#B98B8B] mt-1 font-semibold">
                            {{ $a->created_at->format('d M Y') }}
                        </p>
                    </div>

                    <!-- DELETE BUTTON -->
                    <form method="POST"
                          action="{{ route('admin.announcements.destroy',$a) }}">
                        @csrf
                        @method('DELETE')

                        <button class="rounded-xl px-4 py-2 text-sm font-extrabold text-red-600 border border-red-200 bg-red-50 hover:bg-red-100 transition">
                            Delete
                        </button>
                    </form>

                </div>

                <!-- MESSAGE -->
                <div class="mt-4 text-[#7B4A4A] font-semibold leading-relaxed">
                    {{ $a->message }}
                </div>

            </div>

        @endforeach
    </div>

    <!-- PAGINATION -->
    <div class="mt-6">
        {{ $announcements->links() }}
    </div>

</div>
@endsection