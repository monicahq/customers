<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import { trans } from 'laravel-vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import OfficeLifeLogo from '@/Layouts/OfficeLifeLogo.vue';
import LicenceDisplay from '@/Pages/Partials/LicenceDisplay.vue';

const props = defineProps({
    data: Object,
    refresh: Boolean,
});

const localPlans = ref([]);
const refresh = ref(_.debounce(() => doRefresh(), 1000));

onMounted(() => {
    localPlans.value = props.data.plans;
    if (props.refresh) {
        (refresh.value)();
    }
});

onUnmounted(() => {
    refresh.value.cancel();
});

const doRefresh = () => {
    if (usePage().component.value === 'OfficeLife/Index') {
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

const checkPrice = (plan) => {
    axios.post(plan.url.price, { quantity: plan.quantity })
        .then((response) => {
          this.localPlans[this.localPlans.findIndex((x) => x.id === plan.id)]['price'] = response.data.price;
          this.localPlans[this.localPlans.findIndex((x) => x.id === plan.id)]['url']['pay_link'] = response.data.pay_link;
        });
};

const paddle = () => {
    return trans('Secure payment by <link>Paddle</link>')
      .replace('<link>', '<a href="https://paddle.com" class="ml-1" rel="noopener noreferrer">')
      .replace('</link>', '</a>');
};

</script>

<template>
   <AppLayout title="OfficeLife’s subscription">
    <div class="sm:mt-18 relative">
        <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">

          <div class="text-center mb-12">
            <OfficeLifeLogo />
            <p class="text-sm">
              {{ $t('OfficeLife is an Employee Operation plateform. It manages everything employees do in a company. From projects to holidays to 1:1s to teams.') }}
              <a href="https://officelife.io" class="underline" rel="noopener noreferrer">https://officelife.io</a>
            </p>
          </div>

          <!-- current licence, if defined -->
          <div v-if="data.current_licence">

            <!-- case: active subscription -->
            <div v-if="data.current_licence.subscription_state !== 'subscription_cancelled'" class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg">
              <LicenceDisplay :licence="data.current_licence" :url="'https://app.officelife.io/settings/billing'" />
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
                  <a :href="plan.url.pay_link" rel="noopener noreferrer" class="cursor-pointer block mb-2 focus:shadow-outline-gray items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                    {{ $t('Choose') }}
                  </a>

                  <p class="flex items-center text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span v-html="paddle()"></span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- no licence yet -->
          <div v-else>
            <div v-for="plan in localPlans" :key="plan.id" class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg flex items-center justify-between">
              <div>
                <h3 class="text-lg">{{ plan.friendly_name }} - <span class="text-sm text-gray-500">USD ${{ plan.single_price }} / {{ plan.frequency }}</span></h3>
                <p class="text-gray-600 text-sm">{{ plan.description }}</p>
              </div>

              <div class="flex">

                <div class="flex items-center mr-6">
                  <input
                    v-model="plan.quantity"
                    class="rounded-md border-gray-300 border text-center mr-2 w-20 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    type="number"
                    min="0"
                    max="10000"
                    @keyup="checkPrice(plan)"
                  />

                  <span>{{ $t('seats') }}</span>
                </div>

                <div class="text-center">
                  <a :href="plan.url.pay_link" rel="noopener noreferrer" class="mb-1 cursor-pointer focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                    {{ $t('Subscribe for :price', { price: plan.price }) }}
                  </a>

                  <p class="flex items-center text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span v-html="paddle()"></span>
                  </p>
                </div>
              </div>
            </div>
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
