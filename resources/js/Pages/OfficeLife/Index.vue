<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import useClipboard from 'vue-clipboard3';
import AppLayout from '@/Layouts/AppLayout.vue';
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import JetActionMessage from '@/Jetstream/ActionMessage.vue';

const props = defineProps({
    data: Object,
    refresh: Boolean,
});

const localPlans = ref([]);

const refresh = ref(_.debounce(() => doRefresh(), 1000));

const licence = ref(null);
const copied = ref(false);
const { toClipboard } = useClipboard();

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

const select = () => {
  licence.value.focus();
  licence.value.select();
}

const copyIntoClipboard = async (text) => {
    await toClipboard(text)
      .then(() => {
          copied.value = true;
          setTimeout(() => {
              copied.value = false;
          }, 2000);
      });
};

const checkPrice = (plan) => {
    axios.post(plan.url.price, { quantity: plan.quantity })
        .then((response) => {
          this.localPlans[this.localPlans.findIndex((x) => x.id === plan.id)]['price'] = response.data.price;
          this.localPlans[this.localPlans.findIndex((x) => x.id === plan.id)]['url']['pay_link'] = response.data.pay_link;
        });
};

</script>

<template>
   <AppLayout title="OfficeLife’s Subscriptions">
    <div class="sm:mt-18 relative">
        <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">

          <div class="text-center mb-12">
            <img loading="lazy" src="/img/officelife-logo.svg" alt="officelife logo" class="mb-3 mx-auto" height="150"
                  width="150"
            />
            <p class="text-sm">OfficeLife is an Employee Operation plateform. It manages everything employees do in a company. From projects to holidays to 1:1s to teams. <a href="https://officelife.io" class="underline">https://officelife.io</a></p>
          </div>

          <!-- current licence, if defined -->
          <div v-if="data.current_licence">

            <!-- case: active subscription -->
            <div v-if="data.current_licence.subscription_state !== 'subscription_cancelled'" class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg">
              <p class="mb-6 text-center">🎉 You have an active subscription.</p>

              <div class="mb-4">
                <div class="flex">
                  <p class="flex-auto">
                    This is your licence key:
                  </p>
                  <JetActionMessage :on="copied" class="mr-6">
                      Copied!
                  </JetActionMessage>
                </div>

                <div class="flex">
                  <input class="truncate w-full inline-block rounded bg-gray-200 px-3 py-2 mr-3" :value="data.current_licence.key" ref="licence" type="text" @click.prevent="select" />
                  <JetSecondaryButton title="Copy licence into your clipboard" @click.prevent="copyIntoClipboard(data.current_licence.key)">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" class="w-4 mr-1"><g transform="matrix(2.857142857142857,0,0,2.857142857142857,0,0)"><g><path d="M9.5,1.5H11a1,1,0,0,1,1,1v10a1,1,0,0,1-1,1H3a1,1,0,0,1-1-1V2.5a1,1,0,0,1,1-1H4.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><rect x="4.5" y="0.5" width="5" height="2.5" rx="1" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></rect><line x1="4.5" y1="5.5" x2="9.5" y2="5.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></line><line x1="4.5" y1="8" x2="9.5" y2="8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></line><line x1="4.5" y1="10.5" x2="9.5" y2="10.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></line></g></g></svg>
                      Copy
                  </JetSecondaryButton>
                </div>
              </div>

              <div class="mb-4 bg-blue-100 flex rounded-lg p-4">
                <div>
                  <p class="font-bold mb-2">How to use your key:</p>
                  <ul class="ml-4">
                    <li><span class="text-blue-500">1. </span>  Go to <a href="https://app.monicahq.com/settings/billing" class="underline">https://app.monicahq.com/settings/billing</a></li>
                    <li><span class="text-blue-500">2. </span>  Locate the Licence key section</li>
                    <li><span class="text-blue-500">3. </span>  Paste the licence key shown above.</li>
                    <li><span class="text-blue-500">4. </span>  Enjoy!</li>
                  </ul>
                </div>
              </div>

              <p class="mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                The licence key will automatically renew on {{ data.current_licence.valid_until_at }}.
              </p>

              <p>
                <a :href="data.current_licence.paddle_update_url" class="mr-2 cursor-pointer focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>

                  Update payment details
                </a>

                <a :href="data.current_licence.paddle_cancel_url" class="cursor-pointer focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>

                  Stop
                </a>
              </p>
            </div>

            <!-- case: cancelled subscription -->
            <div v-if="data.current_licence.subscription_state == 'subscription_cancelled'">
              <div class="mb-4 text-center p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg">
                <p class="mb-4">☠️ You have cancelled your subscription.</p>
                <p class="text-gray-600 text-sm">You can always pick a new plan and start over, if you want.</p>
              </div>

              <div v-for="plan in data.plans" :key="plan.id" class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg flex items-center justify-between">
                <div>
                  <h3 class="text-lg">{{ plan.friendly_name }} - <span class="text-sm text-gray-500">USD ${{ plan.price }} / {{ plan.frequency }}</span></h3>
                  <p class="text-gray-600 text-sm">{{ plan.description }}</p>
                </div>

                <div class="text-center">
                  <a :href="plan.url.pay_link" class="cursor-pointer block mb-2 focus:shadow-outline-gray items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                    Choose
                  </a>

                  <p class="flex items-center text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Secure payment by <a href="https://paddle.com" class="ml-1">Paddle</a>
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

                  <span>seats</span>
                </div>

                <div class="text-center">
                  <a :href="plan.url.pay_link" class="mb-1 cursor-pointer focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                    Subscribe for ${{ plan.price }}
                  </a>

                  <p class="flex items-center text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Secure payment by <a href="https://paddle.com" class="ml-1">Paddle</a>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <p class="text-gray-6 mt-8 mb-10">
            It might take a few seconds for your subscription to be processed.
            Refresh this page once you've subscribed to see your licence key.
            If you experience issues after purchase, please contact us at support@monicahq.com.
          </p>

        </div>
      </div>
   </AppLayout>
</template>
