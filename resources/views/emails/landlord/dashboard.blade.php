<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Landlord Dashboard</h2>
  </x-slot>

  <div class="py-10">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm sm:rounded-lg p-6">
        Welcome Landlord, {{ auth()->user()->name }} ✅
      </div>
    </div>
  </div>
</x-app-layout>
