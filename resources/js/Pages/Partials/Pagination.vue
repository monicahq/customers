<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';

const props = defineProps({
  items: Object,
});

const firstItem = computed(() => props.items.data.length > 0 ? (props.items.meta.current_page - 1) * props.items.meta.per_page + 1 : null);
const lastItem = computed(() => props.items.data.length > 0 ? firstItem.value + props.items.data.length - 1 : null);
</script>

<template>
  <div>

    <slot />

    <nav v-if="items.meta.current_page !== 1 || items.meta.last_page > 1" role="navigation" :aria-label="$t('Pagination Navigation')" class="flex items-center justify-between">
      <div class="flex justify-between flex-1 sm:hidden">
        <span v-if="items.meta.current_page === 1" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 cursor-default leading-5 rounded-md"
          v-html="$t('pagination.previous')">
        </span>
        <Link v-else :href="items.links.prev" preserve-scroll class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 dark:ring-gray-700 focus:border-blue-300 focus:dark:border-blue-700 active:bg-gray-100 active:dark:bg-gray-900 active:text-gray-700 active:dark:text-gray-300 transition ease-in-out duration-150">
          <span v-html="$t('pagination.previous')"></span>
        </Link>

        <Link v-if="items.meta.last_page > 1" :href="items.links.next" preserve-scroll class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 dark:ring-gray-700 focus:border-blue-300 focus:dark:border-blue-700 active:bg-gray-100 active:dark:bg-gray-900 active:text-gray-700 active:dark:text-gray-300 transition ease-in-out duration-150">
          <span v-html="$t('pagination.next')"></span>
        </Link>
        <span v-else class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 cursor-default leading-5 rounded-md"
          v-html="$t('pagination.next')">
        </span>
      </div>

      <p class="hidden text-sm w-full text-gray-700 dark:text-gray-300 leading-5">
        <span v-if="firstItem">
          {{
            $t("Showing :first to :last of :total results", {
              first: firstItem,
              last: lastItem,
              total: items.meta.total,
            })
          }}
        </span>
        <span v-else>
          {{
            $t("Showing :count of :total results", {
              count: items.data.length,
              total: items.meta.total,
            })
          }}
        </span>
      </p>

      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div class="relative z-0 inline-flex shadow-sm rounded-md">
          <span v-if="items.meta.current_page === 1" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 cursor-default rounded-l-md leading-5"
            aria-hidden="true" aria-disabled="true" :aria-label="$t('pagination.previous')">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </span>
          <Link v-else :href="items.links.prev" preserve-scroll rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-l-md leading-5 hover:text-gray-400 hover:dark:text-gray-600 focus:z-10 focus:outline-none focus:ring ring-gray-300 dark:ring-gray-700 focus:border-blue-300 focus:dark:border-blue-700 active:bg-gray-100 active:dark:bg-gray-900 active:text-gray-500 transition ease-in-out duration-150"
            :aria-label="$t('pagination.previous')">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </Link>

          <template v-for="(link, id) in items.meta.links" :key="id">
            <span v-if="link.url === null" aria-disabled="true"
              class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 dark:text-gray-500 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 cursor-default leading-5"
              v-html="link.label">
            </span>
            <span v-else-if="link.active" aria-current="page" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-500 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 cursor-default leading-5"
              v-html="link.label">
            </span>
            <Link v-else :href="link.url" preserve-scroll class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 dark:ring-gray-700 focus:border-blue-300 focus:dark:border-blue-700 active:bg-gray-100 active:dark:bg-gray-900 active:text-gray-700 active:dark:text-gray-300 transition ease-in-out duration-150"
              :aria-label="$t('Go to page :page', { page: link.label })">
              <span v-html="link.label"></span>
            </Link>
          </template>

          <Link v-if="items.meta.current_page < items.meta.last_page"
            :href="items.links.next" preserve-scroll rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 dark:ring-gray-700 focus:border-blue-300 focus:dark:border-blue-700 active:bg-gray-100 active:dark:bg-gray-900 active:text-gray-500 transition ease-in-out duration-150"
            :aria-label="$t('pagination.next')">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </Link>
          <span v-else class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 cursor-default rounded-r-md leading-5"
            aria-hidden="true" aria-disabled="true" :aria-label="$t('pagination.next')">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </span>
        </div>
      </div>
    </nav>
  </div>
</template>
