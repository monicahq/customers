<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Dashboard') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <p class="mb-2">OfficeLife requires a license key. This customer portal lets you purchase a license for your company, or renew a license.</p>
          <p>Your license won't automatically renew.</p>
        </div>

        <!-- cta -->
        <div class="text-center p-6">
          <a href="/purchase" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            {{ __('Purchase a license') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
