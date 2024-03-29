<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import JetButton from '@/Jetstream/Button.vue';
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import JetActionSection from '@/Jetstream/ActionSection.vue';
import JetInputError from '@/Jetstream/InputError.vue';

const form = useForm({});
const providerForm = useForm({});
const errors = computed(() => usePage().props.errors);

const deleteProvider = (provider) => {
  form.delete(route('provider.delete', {driver: provider}), {
    errorBag: 'deleteProvider',
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
};

const open = (provider) => {
  providerForm.transform(() => ({
    redirect: location.href,
  })).get(route('login.provider', { driver: provider }), {
    preserveScroll: true,
    onFinish: () => {
      providerForm.reset();
    },
  });
};

defineProps({
  providers: Array,
  providersName: Object,
  userTokens: Array,
});
</script>

<style scoped>
.auth-provider {
  width: 15px;
  height: 15px;
  margin-right: 8px;
  top: 2px;
}
</style>

<template>
    <JetActionSection id="socialite">
        <template #title>
            {{ $t('External connections') }}
        </template>

        <template #description>
            {{ $t('Manage accounts you have linked to your Customers account.') }}
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                {{ $t('You can add more account to log in to our service with one click.') }}
            </div>

            <div class="mt-5 space-y-6">
            <div v-for="provider in providers" :key="provider" class="flex items-center">

                <img :src="`/img/auth/${provider}.svg`" alt="" class="auth-provider relative" />
                <span class="mr-3 text-sm text-gray-600 dark:text-gray-400">
                  {{ providersName[provider] }}
                </span>

                <template v-if="userTokens.findIndex(driver => driver.driver === provider) > -1">
                    <JetSecondaryButton class="mr-3" @click.prevent="deleteProvider(provider)">
                      {{ $t('Disconnect') }}
                    </JetSecondaryButton>

                    <JetInputError :message="form.errors[provider]" class="mt-4" />
                </template>

                <template v-else>
                    <JetButton class="mr-3" @click.prevent="open(provider)">
                      {{ $t('Connect') }}
                    </JetButton>

                    <JetInputError v-if="errors[provider]" :message="errors[provider]" class="mt-4" />
                </template>
            </div>
            </div>

        </template>

    </JetActionSection>
</template>
