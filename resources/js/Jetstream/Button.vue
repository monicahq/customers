<script setup>
defineProps({
  type: {
    type: String,
    default: 'submit',
  },
});
</script>

<template>
  <button :type="type" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 hover:dark:bg-gray-300 active:bg-gray-900 active:dark:bg-gray-100 focus:outline-none focus:border-gray-900 focus:dark:border-gray-100 focus:ring focus:ring-gray-300 focus:dark:ring-gray-700 disabled:opacity-25 transition">
    <slot />
  </button>
</template>
