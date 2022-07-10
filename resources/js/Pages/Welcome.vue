<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import JetApplicationLogo from '@/Jetstream/ApplicationLogo.vue';
import Footer from '@/Layouts/Footer.vue';

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String,
});
</script>

<template>
    <Head :title="$t('Welcome')" />

    <div class="flex flex-col justify-center min-h-screen bg-gray-100 dark:bg-gray-700 sm:items-center sm:pt-0">
        <nav v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            <Link v-if="$page.props.user" :href="route('dashboard')" class="text-sm text-gray-700 dark:text-gray-400 underline">
                {{ $t('Home') }}
            </Link>

            <template v-else>
                <Link :href="route('login')" class="text-sm text-gray-700 dark:text-gray-400 underline">
                    {{ $t('Log in') }}
                </Link>

                <Link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-gray-700 dark:text-gray-400 underline">
                    {{ $t('Register') }}
                </Link>
            </template>
        </nav>

        <main class="max-w-6xl mx-auto sm:mt-24 mb-10 sm:px-6 lg:px-8 flex-grow">

            <div class="mx-auto max-w-4xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-10">
                  <!-- left -->
                  <div class="text-center p-3 sm:p-3 mt-6 w-full overflow-hidden px-6 py-6 sm:max-w-md dark:bg-gray-700 dark:text-gray-200">

                    <h2 class="text-2xl mb-4">
                      {{ $t('Welcome to the Monica and OfficeLife customer portal!') }}
                    </h2>

                    <p>{{ $t('Here you can purchase and manage your subscriptions with ease.') }}</p>

                    <JetApplicationLogo />

                    <div class="text-center inline-block">
                      <ul>
                        <li class="mb-2 flex items-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                          </svg>

                          {{ $t('Cancel anytime') }}
                        </li>
                        <li class="mb-2 flex items-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                          </svg>

                          {{ $t('No long-term commitment') }}
                        </li>
                        <li class="mb-2 flex items-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                          </svg>

                          {{ $t('Support open source projects') }}
                        </li>
                      </ul>
                    </div>

                  </div>

                  <!-- right -->
                  <div class="text-center p-3 sm:p-3 mt-6 w-full flex items-center justify-center overflow-hidden bg-white dark:bg-gray-900 px-6 py-6 shadow-md sm:max-w-md sm:rounded-lg">

                    <div>
                      <p v-if="canRegister" class="text-lg font-bold mb-3 dark:text-gray-200">{{ $t('New on this site?') }}</p>

                      <Link v-if="canRegister" :href="route('register')" class="mb-14 focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 dark:bg-gray-700 dark:text-gray-400 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                        {{ $t('Create an account') }}
                      </Link>

                      <p class="text-lg font-bold mb-3 dark:text-gray-200">{{ $t('Returning user?') }}</p>

                      <Link :href="route('login')" class="mb-4 focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 dark:bg-gray-700 dark:text-gray-400 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                        {{ $t('Login') }}
                      </Link>

                    </div>

                  </div>
                </div>
            </div>
        </main>

        <Footer />
    </div>
</template>
