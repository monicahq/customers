<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import JetActionMessage from '@/Jetstream/ActionMessage.vue';
import JetButton from '@/Jetstream/Button.vue';
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import JetActionSection from '@/Jetstream/ActionSection.vue';
import JetInput from '@/Jetstream/Input.vue';
import JetInputError from '@/Jetstream/InputError.vue';
import JetLabel from '@/Jetstream/Label.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm();
const providerForm = useForm();
const errors = computed(() => usePage().props.value.errors);

const deleteProvider = (provider) => {
    form.delete(route('provider.delete', {driver: provider}), {
        errorBag: 'deleteProvider',
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const open = (provider) => {
    const url = route('login.provider', { driver: provider });
    let href = location.href.split('#')[0];
    providerForm.get(`${url}?redirect=${href}#socialite`, {
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
    <JetActionSection id="socialite" @submitted="updatePassword">
        <template #title>
            OAuth connections
        </template>

        <template #description>
            Manage accounts you have linked to your Customers account.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                You can add more account to log in to our service with one click.
            </div>

            <div class="mt-5 space-y-6">
            <div v-for="provider in providers" :key="provider" class="flex items-center">

                <img :src="`/img/auth/${provider}.svg`" alt="" class="auth-provider relative" />
                <span class="mr-3 text-sm text-gray-600">
                {{ providersName[provider] }}
                </span>

                <template v-if="userTokens.findIndex(driver => driver.driver === provider) > -1">
                    <JetSecondaryButton class="mr-3" @click.prevent="deleteProvider(provider)">
                      Disconnect
                    </JetSecondaryButton>

                    <JetInputError :message="form.errors[provider]" class="mt-4" />
                </template>

                <template v-else>
                    <JetButton class="mr-3" :href="route('login.provider', { driver: provider })" @click.prevent="open(provider)">
                      Connect
                    </JetButton>

                    <JetInputError v-if="errors[provider]" :message="errors[provider]" class="mt-4" />
                </template>
            </div>
            </div>

        </template>

    </JetActionSection>
</template>
