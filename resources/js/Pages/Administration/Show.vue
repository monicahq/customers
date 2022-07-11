<script setup>
import { ref } from 'vue';
import useClipboard from 'vue-clipboard3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MonicaLogo from '@/Layouts/MonicaLogo.vue';
import OfficeLifeLogo from '@/Layouts/OfficeLifeLogo.vue';
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import Plan from '@/Pages/Partials/Plan.vue';

defineProps({
  users: Object,
  licences: Object,
});

const licenceInput = ref(null);
const copied = ref(false);
const { toClipboard } = useClipboard();

const select = () => {
  licenceInput.value.focus();
  licenceInput.value.select();
};

const copyIntoClipboard = async (text) => {
  await toClipboard(text)
    .then(() => {
      copied.value = true;
      setTimeout(() => {
        copied.value = false;
      }, 2000);
    });
};
</script>

<template>
  <AppLayout title="Administration">
    <main class="sm:mt-18 relative">
      <div class="mx-auto max-w-4xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">

        <nav class="flex mb-6">
          <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
              <inertia-link href="/" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-100 dark:text-gray-400 dark:hover:text-white">
                <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>

                Home
              </inertia-link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <inertia-link href="#" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-100 md:ml-2 dark:text-gray-400 dark:hover:text-white">Users</inertia-link>
              </div>
            </li>
            <li aria-current="page">
              <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="ml-1 text-sm font-medium text-gray-400 md:ml-2 dark:text-gray-500">Details</span>
              </div>
            </li>
          </ol>
        </nav>

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
              <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Sync with Paddle
            </JetSecondaryButton>
            <JetSecondaryButton class="mr-5">
              <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              Lock user
            </JetSecondaryButton>
            <JetSecondaryButton class="mr-5">
              <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Delete user
            </JetSecondaryButton>
          </div>

          <!-- details -->
          <div class="border-t border-gray-200 dark:border-gray-700">
            <dl>
              <div class="odd:bg-white even:bg-gray-50 odd:dark:bg-gray-900 even:dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Full name
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                  {{ users.data.name }}
                </dd>
              </div>
              <div class="odd:bg-white even:bg-gray-50 odd:dark:bg-gray-900 even:dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Email address
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                  {{ users.data.email }}
                </dd>
              </div>
              <div class="odd:bg-white even:bg-gray-50 odd:dark:bg-gray-900 even:dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  OfficeLife subscription
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                  -
                </dd>
              </div>
              <div class="odd:bg-white even:bg-gray-50 odd:dark:bg-gray-900 even:dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Monica subscription
                </dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                  <ul v-for="licence in licences" :key="licence.id" role="list" class="border border-gray-200 dark:border-gray-700 rounded-md divide-y divide-gray-200 dark:divide-gray-700">
                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                      <div class="w-0 flex-1 flex items-center">
                        <span class="mr-4">Plan:</span>
                        <Plan :plan="licence.plan" />
                      </div>
                    </li>
                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                      <div class="w-0 flex-1 flex items-center">
                        <span>Licence key:</span>
                        <input class="truncate w-full inline-block rounded bg-gray-100 dark:bg-gray-900 px-3 py-2 ml-2" :value="licence.key" ref="licenceInput" type="text" @click.prevent="select" />
                      </div>
                      <div class="ml-4 flex-shrink-0">
                        <span v-if="copied" class="font-medium text-teal-600 dark:text-teal-400">
                          Copied!
                        </span>
                        <a v-else href="#" @click.prevent="copyIntoClipboard(licence.key)" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 cursor-pointer">
                          Copy
                        </a>
                      </div>
                    </li>
                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                      <div class="w-0 flex-1 flex items-center">
                        <span class="flex-1 w-0 truncate">
                          {{ $t('Valid until: :date', { date: licence.valid_until_at }) }}
                        </span>
                      </div>
                      <div class="ml-4 flex-shrink-0">
                        <a href="#" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                          Revoke key
                        </a>
                      </div>
                    </li>
                  </ul>
                </dd>
              </div>
            </dl>
          </div>
        </div>

      </div>
    </main>
  </AppLayout>
</template>
