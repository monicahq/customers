<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Buy license') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
      <div class="grid gap-6">
        <!-- left -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

          <div class="p-5 border-b border-gray-300">
            <p class="mb-2 text-xl">New license for Jan 2, 2022 - Jan 3, 2023</p>

            <p class="text-gray-600">Rate: USD $9 per active user per month.</p>
          </div>

          <div class="p-5 flex justify-between border-b border-gray-300">
            <div>
              <p class="text-sm mb-2">Active users</p>
              <x-input id="first_name" class="inline-block w-28 text-center" type="text" name="first_name" :value="old('first_name')" required autofocus />

              <span class="ml-2">x USD $108.00 yearly</span>
            </div>
            <div class="text-right">
              <p class="text-sm mb-2">Subtotal for the new license</p>
              <p>USD$ 89,400.00</p>
            </div>
          </div>

          <!-- customer information -->

          <div class="p-5">
            <p>Customer information</p>
            <p>asldkfjas;d</p>
            <p>asldkfjas;d</p>
            <p>asldkfjas;d</p>
          </div>
        </div>

        <!-- right -->
        <div class=" bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <!-- all the pages -->
          <div class="border-b border-gray-200 mb-8">
            sdfasdfs
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
