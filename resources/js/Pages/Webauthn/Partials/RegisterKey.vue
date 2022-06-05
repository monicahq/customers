<template>
    <div :class="{hidden: !register}">
        <jet-input-error :message="form.errors.register" class="mt-2" />

        <div class="mt-4" v-show="!processing || form.errors.name">
            <jet-label for="name" value="Key name" />
            <jet-input type="text" class="mt-1 block w-3/4" placeholder="Name"
                id="name" ref="name"
                v-model="form.name"
                required
                @keyup.enter="start()" />

            <jet-input-error :message="form.errors.name" class="mt-2" />
        </div>

        <div class="mt-4" v-show="step === '2'">
            <webauthn-wait-for-key
                :error-message="error"
                :form="form"
                @retry="start()"
            />
        </div>

        <div class="flex items-center mt-5">
            <jet-secondary-button @click="stop()">
                Cancel
            </jet-secondary-button>

            <jet-button class="ml-2" @click="start()" :class="{ 'opacity-25': processing }" :disabled="processing">
                Submit
            </jet-button>
        </div>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import WebauthnWaitForKey from './WebauthnWaitForKey.vue'

    export default defineComponent({
        components: {
            JetDialogModal,
            JetLabel,
            JetInput,
            JetInputError,
            JetSecondaryButton,
            JetButton,
            WebauthnWaitForKey,
        },

        props: {
            register: {
                type: Boolean,
                default: false,
            },
            errorMessage: {
                type: String,
                default: '',
            },
            form: {
                type: Object,
                default: null,
            },
        },

        emits: ['start', 'register'],

        data() {
            return {
                step: null,
                error: '',
            };
        },

        mounted() {
            this.error = this.errorMessage
        },

        watch: {
            register(val) {
              if (val) {
                this.open();
              }
            },
            errorMessage(value) {
                this.error = value
            },
        },

        computed: {
            processing: function () {
                return this.step === '2' || this.form.processing;
            }
        },

        methods: {
            open() {
                this.step = '1';

                this.stop();
                this.error = '';
                this.form.reset();

                this.$nextTick(() => this.$refs.name.focus(), 250);
            },

            close() {
                this.step = null;
                this.stop();
            },

            start() {
                this.step = '2';
                this.error = '';

                this.$emit('start');
                axios.post(route('webauthn.store.options'))
                    .then(response => {
                        if (response.data !== undefined) {
                            this.registerWaitForKey(response.data.publicKey);
                        } else {
                            this.$nextTick(() => this.registerWaitForKey(response.props.publicKey));
                        }
                    })
                    .catch(error => {
                        this.stop();
                        this.error = error.response.data.errors[0];
                    });
            },

            registerWaitForKey(publicKey) {
                if (this.step === '2') {
                    this.$emit('register', publicKey);
                }
            },

            stop() {
                this.$emit('stop');
            },
        }
    })
</script>