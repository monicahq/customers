<template>
    <jet-action-section>
        <template #title>
            Security keys
        </template>

        <template #description>
            Add additional security to your account using a security key.
        </template>

        <template #content>
            <h3 v-if="!register" class="text-lg font-medium text-gray-900">
                Use a security key (Webauthn, or FIDO) to increase your account security.
            </h3>
            <h3 v-else class="text-lg font-medium text-gray-900">
                Register a new key.
            </h3>

            <div v-if="!isSupported">
                {{ notSupportedMessage() }}
            </div>

            <div v-else-if="register">
                <RegisterKey :register="register" :errorMessage="errorMessage" :form="registerForm" @start="start" @stop="register = false" @register="registerWaitForKey" />
            </div>

            <div v-else class="mt-5 space-y-6">
                <div v-if="webauthnKeys.length === 0">
                    No keys registered yet
                </div>
                <div v-else v-for="key in webauthnKeys" :key="key.id" class="flex items-center mb-2">
                    <div class="text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            stroke="currentColor"
                            class="w-8 h-8"
                            viewBox="-50 0 700 600">
                            <g transform="matrix(42.857142857142854,0,0,42.857142857142854,0,0)">
                              <g>
                                <polyline points="5.62 7.38 11.5 1.5 13.5 3.5"></polyline>
                                <line x1="9.25" y1="3.75" x2="11" y2="5.5"></line>
                                <circle cx="3.5" cy="9.5" r="3"></circle>
                              </g>
                            </g>
                        </svg>
                    </div>

                    <div class="ml-3 w-48">
                        <div class="text-sm text-gray-600">
                            {{ key.name }}
                        </div>

                        <div class="text-xs text-gray-500">
                            <span>
                                Last active {{ key.last_active }}
                            </span>
                        </div>
                    </div>

                    <div class="ml-3 text-sm">
                        <JetSecondaryButton class="pointer text-indigo-400 hover:text-indigo-600" href="" @click.prevent="keyBeingUpdated = key.id">
                            Update
                        </JetSecondaryButton>
                        <jet-confirms-password @confirmed="keyBeingDeleted = key.id">
                            <JetSecondaryButton class="ml-2 pointer text-indigo-400 hover:text-indigo-600" href="">
                                Delete
                            </JetSecondaryButton>
                        </jet-confirms-password>
                    </div>
                </div>

                <div class="flex items-center mt-5">
                    <jet-confirms-password @confirmed="showRegisterModal">
                        <jet-button type="button">
                            Register a new key
                        </jet-button>
                    </jet-confirms-password>
                </div>
            </div>

            <webauthn-delete-modal :keyid="keyBeingDeleted" @close="keyBeingDeleted = null" />
            <webauthn-update-modal :keyid="keyBeingUpdated" :name="nameUpdate" @close="keyBeingUpdated = null" />
        </template>
    </jet-action-section>

</template>

<script>
    import { defineComponent } from 'vue'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
    import JetActionSection from '@/Jetstream/ActionSection.vue';
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetConfirmsPassword from '@/Jetstream/ConfirmsPassword.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import RegisterKey from './Partials/RegisterKey.vue'
    import WebauthnDeleteModal from './Partials/WebauthnDeleteModal.vue'
    import WebauthnUpdateModal from './Partials/WebauthnUpdateModal.vue'
    import * as WebAuthn from '../../../../vendor/asbiin/laravel-webauthn/resources/js/webauthn.js';
    import { useForm } from '@inertiajs/inertia-vue3'

    export default defineComponent({
        components: {
            JetConfirmationModal,
            JetActionSection,
            JetDialogModal,
            JetLabel,
            JetInput,
            JetDangerButton,
            JetSecondaryButton,
            JetConfirmsPassword,
            JetButton,
            RegisterKey,
            WebauthnDeleteModal,
            WebauthnUpdateModal,
        },

        props: {
            webauthnKeys: {
                type: Array,
                default: () => [],
            },
            publicKey: {
                type: Object,
                default: null,
            },
        },

        data() {
            return {
                isSupported: true,
                errorMessage: '',
                webauthn: null,

                register: false,
                registerForm: useForm({
                    name: '',
                }),

                keyBeingDeleted: null,
                keyBeingUpdated: null,
            };
        },

        computed: {
            nameUpdate() {
                return this.keyBeingUpdated ? this.webauthnKeys.find(key => key.id === this.keyBeingUpdated).name : '';
            },
        },

        mounted() {
            this.errorMessage = '';
            this.webauthn = new WebAuthn((name, message) => {
                this.errorMessage = this._errorMessage(name, message);
            });

            if (! this.webauthn.webAuthnSupport()) {
                this.isSupported = false;
                this.errorMessage = this.notSupportedMessage();
            }

            if (this.publicKey) {
                this.showRegisterModal();
                this.registerWaitForKey(this.publicKey);
            }
        },

        methods: {
            _errorMessage(name, message) {
                switch (name) {
                    case 'InvalidStateError':
                        return 'This key is already registered. It’s not necessary to register it again.';
                    case 'NotAllowedError':
                        return 'The operation either timed out or was not allowed.';
                    default:
                        return message;
                }
            },

            notSupportedMessage() {
                switch (this.webauthn.notSupportedMessage()) {
                    case 'not_supported':
                        return 'Your browser doesn’t currently support WebAuthn.';
                    case 'not_secured':
                        return 'WebAuthn only supports secure connections. Please load this page with https scheme.';
                    default:
                        return '';
                }
            },

            showRegisterModal() {
                this.errorMessage = '';
                this.register  = true;
            },

            start() {
                this.errorMessage = '';
                this.registerForm.clearErrors();
            },

            registerWaitForKey(publicKey) {
                this.$nextTick(() => this.webauthn.register(
                    publicKey,
                    (data) => { this.webauthnRegisterCallback(data); }
                ));
            },

            webauthnRegisterCallback(data) {
                this.registerForm.transform((form) => ({
                    ...form,
                    ...data
                }))
                .post(route('webauthn.store'), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        this.register = false;
                    },
                    onError: (error) => {
                        this.errorMessage = error.email ? error.email : error.data.errors.webauthn;
                    }
                });
            },
        }
    })
</script>
