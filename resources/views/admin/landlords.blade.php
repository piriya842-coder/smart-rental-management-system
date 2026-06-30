<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Landlord Approvals</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-50 text-green-700 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-black/5">
                <h3 class="font-bold text-lg mb-4">Pending Landlords</h3>

                @forelse($pending as $l)
                    <div class="flex items-center justify-between py-3 border-b last:border-b-0">
                        <div>
                            <div class="font-bold">{{ $l->name }}</div>
                            <div class="text-sm text-gray-600">{{ $l->email }} • {{ $l->company_name }}</div>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.landlords.approve', $l->id) }}">
                                @csrf
                                <button class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-bold">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.landlords.reject', $l->id) }}">
                                @csrf
                                <button class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-bold">Reject</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-600">No pending landlords.</div>
                @endforelse
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-black/5">
                <h3 class="font-bold text-lg mb-4">Approved Landlords</h3>

                @forelse($approved as $l)
                    <div class="py-2 border-b last:border-b-0">
                        <div class="font-bold">{{ $l->name }}</div>
                        <div class="text-sm text-gray-600">{{ $l->email }} • {{ $l->company_name }}</div>
                    </div>
                @empty
                    <div class="text-sm text-gray-600">No approved landlords yet.</div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
