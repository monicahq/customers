<x-guest-layout>
  <x-auth-card>
    <x-slot name="logo">
      <a href="/">
        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
      </a>
    </x-slot>

    <!-- Validation Errors -->
    <x-auth-validation-errors class="mb-4" :errors="$errors" />

    <form method="POST" action="{{ route('register') }}">
      @csrf

      <!-- Name -->
      <div>
        <x-label for="first_name" :value="__('First name')" />

        <x-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus />
      </div>

      <!-- Name -->
      <div class="mt-4">
        <x-label for="last_name" :value="__('Last name')" />

        <x-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autofocus />
      </div>

      <!-- Email Address -->
      <div class="mt-4">
        <x-label for="email" :value="__('Email')" />

        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
      </div>

      <!-- Password -->
      <div class="mt-4">
        <x-label for="password" :value="__('Password')" />

        <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
      </div>

      <!-- Confirm Password -->
      <div class="mt-4">
        <x-label for="password_confirmation" :value="__('Confirm Password')" />

        <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
      </div>

      <!-- Company name -->
      <div class="mt-4">
        <x-label for="company_name" :value="__('Company name')" />

        <x-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required autofocus />
      </div>

      <!-- Number of employees -->
      <div class="mt-4">
        <x-label for="total_number_of_employees" :value="__('Number of employees')" />

        <x-input id="total_number_of_employees" class="block mt-1 w-full" type="text" name="total_number_of_employees" :value="old('total_number_of_employees')" required autofocus />
      </div>

      <!-- Address -->
      <div class="mt-4">
        <x-label for="address_line_1" :value="__('Address line 1 (optional)')" />

        <x-input id="address_line_1" class="block mt-1 w-full" type="text" name="address_line_1" :value="old('address_line_1')" />
      </div>

      <div class="mt-4">
        <x-label for="address_line_2" :value="__('Address line 2 (optional)')" />

        <x-input id="address_line_2" class="block mt-1 w-full" type="text" name="address_line_2" :value="old('address_line_2')" />
      </div>

      <div class="mt-4">
        <x-label for="city" :value="__('City')" />

        <x-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" required />
      </div>

      <div class="mt-4">
        <x-label for="state" :value="__('State')" />

        <x-input id="state" class="block mt-1 w-full" type="text" name="state" :value="old('state')" required />
      </div>

      <div class="mt-4">
        <x-label for="postal_code" :value="__('Postal code')" />

        <x-input id="postal_code" class="block mt-1 w-full" type="text" name="postal_code" :value="old('postal_code')" required />
      </div>

      <div class="mt-4">
        <x-label for="country" :value="__('Country')" />

        <x-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country')" required />
      </div>

      <div class="flex items-center justify-end mt-4">
        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
          {{ __('Already registered?') }}
        </a>

        <x-button class="ml-4">
          {{ __('Register') }}
        </x-button>
      </div>
    </form>
  </x-auth-card>
</x-guest-layout>
