<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MonicaLogo from '@/Layouts/MonicaLogo.vue';
import OfficeLifeLogo from '@/Layouts/OfficeLifeLogo.vue';
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import Licence from './Partials/Licence.vue';

const props = defineProps({
  users: Object,
  licences: Object,
});

const monicaLicences = computed(() => _.filter(props.licences, (licence) => licence.plan.product === 'Monica'));
const officeLifeLicences = computed(() => _.filter(props.licences, (licence) => licence.plan.product === 'OfficeLife'));
</script>

<template>
  <AppLayout title="Administration">
    <template #header>
      <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Administration</h2>
        </li>
        <li>
          <div class="flex items-center">
            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <Link :href="route('administration.index')" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-100 md:ml-2 dark:text-gray-400 hover:dark:text-white">
              Users
            </Link>
          </div>
        </li>
        <li aria-current="page">
          <div class="flex items-center">
            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 md:ml-2 hover:dark:text-gray-100">
              Details
            </span>
          </div>
        </li>
      </ol>
    </template>

    <div>
      <div class="max-w-5xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-900 shadow dark:shadow-gray-700 overflow-hidden sm:rounded-lg">
          <!-- name -->
          <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 dark:text-gray-100">
                {{ users.data.name }}
              </h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ $t('Active since :date.', { date: users.data.created_at }) }}
              </p>
            </div>

            <div class="flex">
              <OfficeLifeLogo />
              <MonicaLogo />
            </div>
          </div>

          <!-- main actions -->
          <div class="px-4 py-5 sm:px-6 flex justify-center items-center">
            <JetSecondaryButton class="mr-5">
              <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Sync with Paddle
            </JetSecondaryButton>
            <JetSecondaryButton class="mr-5">
              <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              Lock user
            </JetSecondaryButton>
            <JetSecondaryButton class="mr-5">
              <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Delete user
            </JetSecondaryButton>
          </div>

          <!-- details -->
          <div class="border-t border-gray-200 dark:border-gray-700">
            <dl>
              <div class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Full name
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                  {{ users.data.name }}
                </dd>
              </div>
              <div class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Email address
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                  {{ users.data.email }}
                </dd>
              </div>
              <div class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  OfficeLife subscriptions
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                  <span v-if="officeLifeLicences.length === 0">
                    —
                  </span>
                  <template v-else v-for="licence in officeLifeLicences" :key="licence.id">
                    <Licence :licence="licence" />
                  </template>
                </dd>
              </div>
              <div class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Monica subscriptions
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                  <span v-if="monicaLicences.length === 0">
                    —
                  </span>
                  <template v-else v-for="licence in monicaLicences" :key="licence.id">
                    <Licence :licence="licence" />
                  </template>
                </dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
