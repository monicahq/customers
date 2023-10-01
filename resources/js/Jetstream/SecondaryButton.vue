<script setup>
defineProps({
  type: {
    type: String,
    default: 'button',
  },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-100 hover:dark:bg-gray-700 shadow-sm dark:shadow-gray-700 hover:text-gray-500 hover:dark:text-gray-300 focus:outline-none focus:border-blue-300 focus:dark:border-blue-700 focus:ring focus:ring-blue-200 focus:dark:ring-blue-800 active:text-gray-800 active:dark:text-gray-200 active:bg-gray-50 active:dark:bg-gray-900 disabled:opacity-25 transition">
        <slot />
    </button>
</template>
