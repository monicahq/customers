<script setup>
import JetSectionTitle from './SectionTitle.vue';

defineProps({
  danger: {
    type: Boolean,
    default: false,
  },
});
</script>

<template>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <JetSectionTitle>
            <template #title>
                <slot name="title" />
            </template>
            <template #description>
                <slot name="description" />
            </template>
        </JetSectionTitle>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="px-4 py-5 sm:p-6 shadow dark:shadow-gray-700 sm:rounded-lg" :class="danger ? ['border-red-600 dark:border-red-400 bg-red-50 dark:bg-red-900'] : ['bg-white dark:bg-gray-900']">
                <slot name="content" />
            </div>
        </div>
    </div>
</template>
