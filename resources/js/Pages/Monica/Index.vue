<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import MonicaLogo from '@/Layouts/MonicaLogo.vue';
import LicenceDisplay from '@/Pages/Partials/LicenceDisplay.vue';
import Plan from '@/Pages/Partials/Plan.vue';
import JetButton from '@/Jetstream/Button.vue';
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue';

const props = defineProps({
  plans: Object,
  current_licence: Object,
  refresh: Boolean,
  links: Object,
});

const refresh = ref(_.debounce(() => doRefresh(), 1000));
const updateForm = useForm({
  plan_id: null,
});
const subscribeForm = useForm();

onMounted(() => {
  if (props.refresh) {
    (refresh.value)();
  }
});

onUnmounted(() => {
  refresh.value.cancel();
});

const currentPlan = computed(() => props.current_licence === null ? null : plan(props.current_licence.plan_id));
const newPlan = computed(() => plan(updateForm.plan_id));
const licenceCancelled = computed(() => props.current_licence.subscription_state === 'subscription_cancelled');

const plan = (id) => props.plans[props.plans.findIndex((x) => x.id === id)];

const doRefresh = () => {
  if (usePage().component.value === 'Monica/Index') {
    Inertia.reload({
      only: ['current_licence'],
      onFinish: () => {
        if (props.current_licence === null || props.current_licence.subscription_state === 'subscription_cancelled') {
          (refresh.value)();
        }
      },
    });
  }
};

const updatePlan = () => {
  updateForm.patch(route('monica.update'), {
    preserveScroll: true,
    onFinish: () => {
      updateForm.reset();
      updateForm.plan_id = null;
    }
  });
};

const subscribe = (planId) => {
  subscribeForm.post(route('monica.create', { plan: planId }));
};

</script>

<template>
  <AppLayout title="Monica’s subscription">
    <div class="sm:mt-18 relative">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">

        <div class="text-center mb-12 dark:text-gray-100">
          <MonicaLogo />
          <p class="text-sm">
            {{ $t('Monica is a great open source personal CRM. Monica allows people to keep track of everything that’s important about their friends and family.') }}
            <a href="https://monicahq.com" class="underline" rel="noopener noreferrer">https://monicahq.com</a>
          </p>
        </div>

        <!-- case: active subscription -->
        <template v-if="current_licence">
          <div v-if="! licenceCancelled" class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white dark:bg-gray-900 px-6 py-6 shadow-md dark:shadow-gray-700 sm:rounded-lg">
            <LicenceDisplay
              :licence="current_licence"
              :link="links.billing"
              @update-subscription="$inertia.patch(route('monica.update.paddle'))"
              @cancel-subscription="$inertia.patch(route('monica.cancel.paddle'))"
            >
              <Plan v-if="currentPlan" :plan="currentPlan" />
            </LicenceDisplay>
          </div>

          <!-- case: cancelled subscription -->
          <div v-else class="mb-4 text-center p-3 sm:p-3 w-full overflow-hidden bg-white dark:bg-gray-900 dark:text-gray-100 px-6 py-6 shadow-md dark:shadow-gray-700 sm:rounded-lg">
            <p class="mb-4">{{ $t('☠️ You have cancelled your subscription.') }}</p>
            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $t('You can always pick a new plan and start over, if you want.') }}</p>
          </div>
        </template>

        <div v-for="plan in plans" :key="plan.id">
          <div v-if="! currentPlan || plan.id !== currentPlan.id || licenceCancelled" class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white dark:bg-gray-900 px-6 py-6 shadow-md dark:shadow-gray-700 sm:rounded-lg flex items-center justify-between">
            <Plan :plan="plan" />

            <div class="text-center">
              <JetButton v-if="currentPlan && ! licenceCancelled" @click="updateForm.plan_id = plan.id">
                {{ $t('Switch') }}
              </JetButton>

              <JetButton v-else @click="subscribe(plan.id)">
                {{ $t('Choose') }}
              </JetButton>

              <p class="flex items-center text-xs mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="text-gray-800 dark:text-gray-200" v-html="links.paddle"></span>
              </p>
            </div>
          </div>
        </div>

        <!-- no licence yet -->
        <div v-if="! current_licence || ! licenceCancelled" class="text-gray-600 dark:text-gray-400">
          <div>{{ $t('You will be able to upgrade storage later.') }}</div>
        </div>

        <p class="text-gray-600 dark:text-gray-400 mt-8 mb-10">
          {{ $t('It might take a few seconds for your subscription to be processed.') }}
          {{ $t('Refresh this page once you’ve subscribed to see your licence key.') }}
          <span v-html="links.support"></span>
        </p>

      </div>
    </div>

    <!-- Change plan -->
    <JetConfirmationModal :show="updateForm.plan_id" @close="updateForm.plan_id = null">
        <template #title>
            {{ $t('Switch to plan') }}
        </template>

        <template #content>
            <p class="mb-5">{{ $t('Are you sure you would like to switch to this plan?') }}</p>

            <Plan :plan="newPlan" />
        </template>

        <template #footer>
            <JetSecondaryButton @click="updateForm.plan_id = null">
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
