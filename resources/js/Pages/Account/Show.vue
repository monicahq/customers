<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import JetSectionBorder from '@/Jetstream/SectionBorder.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import UpdateAddressForm from './Partials/UpdateAddressForm.vue';
</script>

<template>
    <AppLayout title="Account">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Account
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div>
                    <UpdateProfileInformationForm :user="$page.props.user" />

                    <JetSectionBorder />
                </div>

                <div>
                    <UpdateAddressForm class="mt-10 sm:mt-0" :user="$page.props.user" />

                    <JetSectionBorder />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
