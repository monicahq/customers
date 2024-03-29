<script setup>
import { ref, watch, onMounted } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue';
import JetButton from '@/Jetstream/Button.vue';
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import JetInput from '@/Jetstream/Input.vue';
import JetCheckbox from '@/Jetstream/Checkbox.vue';
import JetLabel from '@/Jetstream/Label.vue';
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue';
import WebauthnLogin from '@/Pages/Webauthn/WebauthnLogin.vue';

const props = defineProps({
  canResetPassword: Boolean,
  status: String,
  providers: Array,
  providersName: Object,
  publicKey: Object,
  userName: String,
});
const webauthn = ref(true);
const publicKeyRef = ref(null);

const form = useForm({
  email: '',
  password: '',
  remember: false,
});
const providerForm = useForm({});

watch(() => props.publicKey, (value) => {
  publicKeyRef.value = value;
});

onMounted(() => {
  publicKeyRef.value = props.publicKey;
});

const submit = () => {
  form.transform(data => ({
    ...data,
    remember: form.remember ? 'on' : '',
  })).post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};

const open = (provider) => {
  providerForm.transform(() => ({
    redirect: location.href,
    remember: form.remember ? 'on' : '',
  })).get(route('login.provider', { driver: provider }), {
    preserveScroll: true,
    onFinish: () => {
      providerForm.reset();
    },
  });
};

const reload = () => {
  publicKeyRef.value = null;
  webauthn.value = true;
  router.reload({only: ['publicKey']});
};
</script>

<style scoped>
.auth-provider {
  width: 15px;
  height: 15px;
  margin-right: 8px;
  top: 2px;
}
.w-43 {
  width: 43%;
}
</style>

<template>
    <Head :title="$t('Log in')" />

    <JetAuthenticationCard>
        <template #logo>
            <JetAuthenticationCardLogo />
        </template>

        <JetValidationErrors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <div v-if="publicKey && webauthn">
            <div class="mb-4 text-lg text-gray-900 text-center">
                {{ userName }}
            </div>
            <div class="mb-4 max-w-xl text-gray-600 dark:text-gray-400">
                {{ $t('Connect with your security key') }}
            </div>

            <WebauthnLogin :remember="true" :public-key="publicKeyRef" />

            <JetSecondaryButton class="mr-2 mt-4" @click.prevent="webauthn = false">
                {{ $t('Use your password') }}
            </JetSecondaryButton>
        </div>

        <form v-else @submit.prevent="submit">
            <div>
                <JetLabel for="email" value="Email" />
                <JetInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                />
            </div>

            <div class="mt-4">
                <JetLabel for="password" value="Password" />
                <JetInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <JetCheckbox v-model:checked="form.remember" name="remember" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                      {{ $t('Remember me') }}
                    </span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900">
                    {{ $t('Forgot your password?') }}
                </Link>

                <JetButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t('Log in') }}
                </JetButton>
            </div>

            <div class="block mt-4">
                <p v-if="providers.length > 0" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    {{ $t('Login with:') }}
                </p>
                <div class="flex flex-wrap">
                    <JetSecondaryButton v-for="provider in providers" :key="provider" class="mr-2" :href="route('login.provider', { driver: provider })" @click.prevent="open(provider)">
                        <img :src="`/img/auth/${provider}.svg`" alt="" class="auth-provider relative" />
                        {{ providersName[provider] }}
                    </JetSecondaryButton>
                </div>
            </div>

            <div v-if="publicKeyRef" class="block mt-4">
                <JetSecondaryButton class="mr-2" @click.prevent="reload">
                    {{ $t('Use your security key') }}
                </JetSecondaryButton>
            </div>

        </form>
    </JetAuthenticationCard>
</template>
