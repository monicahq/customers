
<script setup>
import { ref, watch, nextTick, computed, onMounted } from 'vue';
import JetLabel from '@/Jetstream/Label.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetButton from '@/Jetstream/Button.vue'
import WaitForKey from './WaitForKey.vue'

const props = defineProps({
    errorMessage: String,
    form: Object,
});

const emit = defineEmits(['start', 'register']);

const registering = ref(false);
const error = ref('');
const nameInput = ref(null);

onMounted(() => {
    error.value = props.errorMessage;
    props.form.reset();
    nameInput.value.focus();
});

watch(() => props.errorMessage, (value) => {
    error.value = value;
});

const processing = computed(() => registering.value === true || props.form.processing);

const begin = () => {
    registering.value = true;
    error.value = '';

    emit('start');
    axios.post(route('webauthn.store.options'))
        .then(response => {
            if (response.data !== undefined) {
                registerWaitForKey(response.data.publicKey);
            } else {
                nextTick().then(() => registerWaitForKey(response.props.publicKey));
            }
        })
        .catch(e => {
            stop();
            error.value = e.response.data.errors[0];
        });
};

const registerWaitForKey = (publicKey) => {
    if (registering.value === true) {
        emit('register', publicKey);
    }
};

const stop = () => {
    registering.value = false;
    emit('stop');
};
</script>

<template>
    <form @submit.prevent="begin">
        <JetInputError :message="form.errors.register" class="mt-2" />

        <div class="mt-4" v-show="!processing || form.errors.name">
            <JetLabel for="name" value="Key name" />
            <JetInput type="text" class="mt-1 block w-3/4"
                id="name" ref="nameInput"
                v-model="form.name"
                required
                @keyup.enter="begin()" />

            <JetInputError :message="form.errors.name" class="mt-2" />
        </div>

        <div class="mt-4" v-show="registering">
            <WaitForKey
                :error-message="error"
                :form="form"
                @retry="begin()"
            />
        </div>

        <div class="flex items-center mt-5">
            <JetSecondaryButton @click="stop()">
                {{ $t('Cancel') }}
            </JetSecondaryButton>

            <JetButton class="ml-2" :class="{ 'opacity-25': processing }" :disabled="processing">
                {{ $t('Submit') }}
            </JetButton>
        </div>
    </form>
</template>