<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import Pagination from './Pagination.vue';

defineProps({
  receipts: Object,
});
</script>

<template>
  <div v-if="receipts.meta.total > 0">
    <p class="mb-4 dark:text-gray-200">
      {{ $t("All the receipts for all your subscriptions") }}
    </p>

    <Pagination :items="receipts" class="mb-12">
      <ul class="mb-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
        <li v-for="receipt in receipts.data" :key="receipt.id" class="flex justify-between item-list border-b border-gray-200 dark:border-gray-700 px-5 py-2 hover:bg-slate-50 hover:dark:bg-slate-800">
          <div>
            <span class="mr-3 text-gray-400 dark:text-gray-400 text-sm">
              {{ receipt.paid_at }}
            </span>
            <span class="font-serif dark:text-gray-100">
              {{ receipt.amount }}
            </span>
          </div>
          <Link :href="receipt.receipt_url" class="flex items-center dark:text-gray-300" target="_blank" rel="noopener noreferrer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>

            <span class="underline">{{ $t("View receipt") }}</span>
          </Link>
        </li>
      </ul>
    </Pagination>

  </div>
</template>
