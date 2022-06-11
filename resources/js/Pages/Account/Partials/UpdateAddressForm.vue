<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import JetButton from '@/Jetstream/Button.vue';
import JetFormSection from '@/Jetstream/FormSection.vue';
import JetInput from '@/Jetstream/Input.vue';
import JetInputError from '@/Jetstream/InputError.vue';
import JetLabel from '@/Jetstream/Label.vue';
import JetActionMessage from '@/Jetstream/ActionMessage.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    address_line_1: props.user.address_line_1,
    address_line_2: props.user.address_line_2,
    city: props.user.city,
    postal_code: props.user.postal_code,
    country: props.user.country,
    state: props.user.state,
});

const updateAddress = () => {
    form.patch(route('user-address.update'), {
        errorBag: 'updateAddress',
        preserveScroll: true,
    });
};

</script>

<template>
    <JetFormSection @submitted="updateAddress">
        <template #title>
            {{ $t('Account address') }}
        </template>

        <template #description>
            {{ $t('Update your account’s address.') }}
        </template>

        <template #form>
            <!-- Street -->
            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="address1" value="Street" />
                <JetInput
                    id="address1"
                    v-model="form.address_line_1"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="street"
                />
                <JetInputError :message="form.errors.address_line_1" class="mt-2" />
            </div>

            <!-- Address -->
            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="address2" value="Additional information" />
                <JetInput
                    id="address2"
                    v-model="form.address_line_2"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="street"
                />
                <JetInputError :message="form.errors.address_line_2" class="mt-2" />
            </div>

            <!-- City -->
            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="city" value="City" />
                <JetInput
                    id="city"
                    v-model="form.city"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="city"
                />
                <JetInputError :message="form.errors.city" class="mt-2" />
            </div>

            <!-- Postal code -->
            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="postal_code" value="Postal code" />
                <JetInput
                    id="postal_code"
                    v-model="form.postal_code"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="postal_code"
                />
                <JetInputError :message="form.errors.postal_code" class="mt-2" />
            </div>

            <!-- Country -->
            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="country" value="Country" />
                <JetInput
                    id="country"
                    v-model="form.country"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="country"
                />
                <JetInputError :message="form.errors.country" class="mt-2" />
            </div>

            <!-- State -->
            <div class="col-span-6 sm:col-span-4">
                <JetLabel for="state" value="State" />
                <JetInput
                    id="state"
                    v-model="form.state"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="state"
                />
                <JetInputError :message="form.errors.state" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <JetActionMessage :on="form.recentlySuccessful" class="mr-3">
                {{ $t('Saved.') }}
            </JetActionMessage>

            <JetButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('Save') }}
            </JetButton>
        </template>
    </JetFormSection>
</template>
