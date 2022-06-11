<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import MonicaLogo from '@/Layouts/MonicaLogo.vue';

const props = defineProps({
    data: Object,
    refresh: Boolean,
});

const refresh = ref(_.debounce(() => doRefresh(), 1000));

onMounted(() => {
    if (props.refresh) {
        (refresh.value)();
    }
});
onUnmounted(() => {
    refresh.value.cancel();
});

const doRefresh = () => {
    if (usePage().component.value === 'Monica/Index') {
        Inertia.reload({
            only: ['data'],
            onFinish: () => {
                if (props.data.current_licence === '' || props.data.current_licence.subscription_state === 'subscription_cancelled') {
                    (refresh.value)();
                }
            },
        });
    }
};

</script>

<template>
   <AppLayout title="Monica’s subscription">
    <div class="sm:mt-18 relative">
        <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">

          <div class="text-center mb-12">
            <MonicaLogo />
            <p class="text-sm">
              {{ $t('Monica is a great open source personal CRM. Monica allows people to keep track of everything that’s important about their friends and family.') }}
              <a href="https://monicahq.com" class="underline">https://monicahq.com</a>
            </p>
          </div>

          <!-- current licence, if defined -->
          <div v-if="data.current_licence">

            <!-- case: active subscription -->
            <div v-if="data.current_licence.subscription_state !== 'subscription_cancelled'" class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg">
              <p class="mb-6 text-center">{{ $t('🎉 You have an active subscription.') }}</p>

              <p class="mb-4">{{ $t('This is your licence key:') }}
                <span class="overflow-hidden w-full inline-block rounded bg-gray-200 px-3 py-2">
                {{ data.current_licence.key }}
                </span>
              </p>

              <div class="mb-4 bg-blue-100 flex rounded-lg p-4">
                <div>
                  <p class="font-bold mb-2">{{ $t('How to use your key:') }}</p>
                  <ul class="ml-4">
                    <li><span class="text-blue-500">{{ $t('1.') }} </span>  {{ $t('Go to :link', { link: '<a href="https://app.monicahq.com/settings/billing" class="underline">https://app.monicahq.com/settings/billing</a>' }) }}</li>
                    <li><span class="text-blue-500">{{ $t('2.') }} </span>  {{ $t('Locate the Licence key section') }}</li>
                    <li><span class="text-blue-500">{{ $t('3.') }} </span>  {{ $t('Paste the licence key shown above.') }}</li>
                    <li><span class="text-blue-500">{{ $t('4.') }} </span>  {{ $t('Enjoy!') }}</li>
                  </ul>
                </div>
              </div>

              <p class="mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                {{ $t('The licence key will automatically renew on :date.', { date: data.current_licence.valid_until_at }) }}
              </p>

              <p>

                <a :href="data.current_licence.paddle_update_url" class="mr-2 cursor-pointer focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>

                  {{ $t('Update payment details') }}
                </a>

                <a :href="data.current_licence.paddle_cancel_url" class="cursor-pointer focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>

                  {{ $t('Stop') }}
                </a>
              </p>
            </div>

            <!-- case: cancelled subscription -->
            <div v-if="data.current_licence.subscription_state == 'subscription_cancelled'">
              <div class="mb-4 text-center p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg">
                <p class="mb-4">{{ $t('☠️ You have cancelled your subscription.') }}</p>
                <p class="text-gray-600 text-sm">{{ $t('You can always pick a new plan and start over, if you want.') }}</p>
              </div>

              <div v-for="plan in data.plans" :key="plan.id" class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg flex items-center justify-between">
                <div>
                  <h3 class="text-lg">{{ plan.friendly_name }} - <span class="text-sm text-gray-500">USD ${{ plan.price }} / {{ plan.frequency }}</span></h3>
                  <p class="text-gray-600 text-sm">{{ plan.description }}</p>
                </div>

                <div class="text-center">
                  <a :href="plan.url.pay_link" class="cursor-pointer block mb-2 focus:shadow-outline-gray items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                    {{ $t('Choose') }}
                  </a>

                  <p class="flex items-center text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    {{ $t('Secure payment by :link', { link: '<a href="https://paddle.com" class="ml-1">Paddle</a>' }) }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- no licence yet -->
          <div v-else>
            <div v-for="plan in data.plans" :key="plan.id" class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg flex items-center justify-between">
              <div>
                <h3 class="text-lg">{{ plan.friendly_name }} - <span class="text-sm text-gray-500">USD ${{ plan.price }} / {{ plan.frequency }}</span></h3>
                <p class="text-gray-600 text-sm">{{ plan.description }}</p>
              </div>

              <div class="text-center">
                <a :href="plan.url.pay_link" class="mb-1 cursor-pointer focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                  {{ $t('Choose') }}
                </a>
                <p class="flex items-center text-xs">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                    {{ $t('Secure payment by :link', { link: '<a href="https://paddle.com" class="ml-1">Paddle</a>' }) }}
                </p>
              </div>
            </div>

            <div>{{ $t('You will be able to upgrade storage later.') }}</div>
          </div>

          <p class="text-gray-6 mt-8 mb-10">
            {{ $t('It might take a few seconds for your subscription to be processed.') }}
            {{ $t('Refresh this page once you’ve subscribed to see your licence key.') }}
            {{ $t('If you experience issues after purchase, please contact us at support@monicahq.com.') }}
          </p>

        </div>
      </div>
   </AppLayout>
</template>
