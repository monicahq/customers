<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import { trans } from 'laravel-vue-i18n';
import { Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MonicaLogo from '@/Layouts/MonicaLogo.vue';
import LicenceDisplay from '@/Pages/Partials/LicenceDisplay.vue';
import JetButton from '@/Jetstream/Button.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue';

const props = defineProps({
    data: Object,
    refresh: Boolean,
});

const refresh = ref(_.debounce(() => doRefresh(), 1000));
const newPlanId = ref(null);
const updateForm = useForm();

onMounted(() => {
    if (props.refresh) {
        (refresh.value)();
    }
});

onUnmounted(() => {
    refresh.value.cancel();
});

const currentPlan = computed(() => props.data.plans[props.data.plans.findIndex((x) => x.id === props.data.current_licence.plan_id)]);
const newPlan = computed(() => props.data.plans[props.data.plans.findIndex((x) => x.id === newPlanId.value)]);

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

const paddle = () => {
    return trans('Secure payment by <link>Paddle</link>')
      .replace('<link>', '<a href="https://paddle.com" class="underline" rel="noopener noreferrer">')
      .replace('</link>', '</a>');
};

const updatePlan = () => {
    updateForm.transform(() => ({
        plan_id: newPlanId.value,
    }))
    .patch(route('monica.update'), {
        preserveScroll: true,
        onFinish: () => {
          newPlanId.value = null;
        }
    });
}

</script>

<template>
  <AppLayout title="Monica’s subscription">
    <div class="sm:mt-18 relative">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
          <MonicaLogo />
          <p class="text-sm">
            {{ $t('Monica is a great open source personal CRM. Monica allows people to keep track of everything that’s important about their friends and family.') }}
            <a href="https://monicahq.com" class="underline" rel="noopener noreferrer">https://monicahq.com</a>
          </p>
        </div>

          <!-- case: active subscription -->
          <div v-if="data.current_licence.subscription_state !== 'subscription_cancelled'"
            class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg">
            <LicenceDisplay :licence="data.current_licence" :plan="currentPlan" :url="'https://app.monicahq.com/settings/billing'" />
          </div>

          <!-- case: cancelled subscription -->
          <div v-else class="mb-4 text-center p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg">
            <p class="mb-4">{{ $t('☠️ You have cancelled your subscription.') }}</p>
            <p class="text-gray-600 text-sm">{{ $t('You can always pick a new plan and start over, if you want.') }}</p>
          </div>

          <div v-for="plan in data.plans" :key="plan.id">
            <div v-if="plan.id !== currentPlan.id" class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg flex items-center justify-between">
              <div>
                <h3 class="text-lg">{{ plan.friendly_name }} — <span class="text-sm text-gray-500">{{ plan.price }} / {{ plan.frequency }}</span></h3>
                <p class="text-gray-600 text-sm">{{ plan.description }}</p>
              </div>

              <div class="text-center">
                <JetButton v-if="data.current_licence" @click="newPlanId = plan.id">
                  {{ $t('Switch') }}
                </JetButton>

                <Link v-else :href="plan.url.pay_link" rel="noopener noreferrer" class="cursor-pointer block mb-2 focus:shadow-outline-gray items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
                  {{ $t('Choose') }}
                </Link>

                <p class="flex items-center text-xs">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                  <span v-html="paddle()"></span>
                </p>
              </div>
            </div>
          </div>

          <!-- no licence yet -->
          <div v-if="data.current_licence.subscription_state !== 'subscription_cancelled'">
            <div>{{ $t('You will be able to upgrade storage later.') }}</div>
          </div>

          <p class="text-gray-6 mt-8 mb-10">
            {{ $t('It might take a few seconds for your subscription to be processed.') }}
            {{ $t('Refresh this page once you’ve subscribed to see your licence key.') }}
            {{ $t('If you experience issues after purchase, please contact us at support@monicahq.com.') }}
          </p>

        </div>
      </div>

      <!-- Change plan -->
      <JetConfirmationModal :show="newPlanId" @close="newPlanId = null">
          <template #title>
              {{ $t('Switch to plan') }}
          </template>

          <template #content>
              {{ $t('Are you sure you would like to switch to this plan?') }}

              <div>
                <h3 class="text-lg">{{ newPlan.friendly_name }} — <span class="text-sm text-gray-500">{{ newPlan.price }} / {{ newPlan.frequency }}</span></h3>
                <p class="text-gray-600 text-sm">{{ newPlan.description }}</p>
              </div>

          </template>

          <template #footer>
              <JetSecondaryButton @click="newPlanId = null">
                  {{ $t('Cancel') }}
              </JetSecondaryButton>

              <JetButton
                  class="ml-3"
                  :class="{ 'opacity-25': updateForm.processing }"
                  :disabled="updateForm.processing"
                  @click="updatePlan"
              >
                  {{ $t('OK') }}
              </JetButton>
          </template>
      </JetConfirmationModal>

   </AppLayout>
</template>
