<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue';
import JetButton from '@/Jetstream/Button.vue';
import JetInput from '@/Jetstream/Input.vue';
import JetLabel from '@/Jetstream/Label.vue';
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue';
import WebauthnLogin from '@/Pages/Webauthn/WebauthnLogin.vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};

defineProps({
    two_factor: Boolean,
    remember: Boolean,
    publicKey: Object,
});
</script>

<template>
    <Head title="Two-factor Confirmation" />

    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>

        <div class="mb-4 text-sm text-gray-600">
            <template v-if="! recovery">
                {{ $t('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
            </template>

            <template v-else>
                {{ $t('Please confirm access to your account by entering one of your emergency recovery codes.') }}
            </template>
        </div>

        <div v-if="two_factor">
            <div class="flex items-center justify-end mt-4">
                <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer" @click.prevent="toggleRecovery">
                    <template v-if="! recovery">
                        {{ $t('Use a recovery code') }}
                    </template>

                    <template v-else>
                        {{ $t('Use an authentication code') }}
                    </template>
                </button>

                <JetButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t('Log in') }}
                </JetButton>
            </div>
        </div>
    </JetAuthenticationCard>
</template>
