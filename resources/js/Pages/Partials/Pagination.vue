<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import ChevronLeft from './ChevronLeft.vue';
import ChevronRight from './ChevronRight.vue';

const props = defineProps({
  items: Object,
  withSummary: {
    type: Boolean,
    default: true,
  },
});

const firstItem = computed(() => props.items.data.length > 0 ? (props.items.meta.current_page - 1) * props.items.meta.per_page + 1 : null);
const lastItem = computed(() => props.items.data.length > 0 ? firstItem.value + props.items.data.length - 1 : null);

const commonClasses = 'relative inline-flex items-center px-4 py-2 text-sm font-medium bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 leading-5';
const linkClasses = 'hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 dark:ring-gray-700 focus:border-blue-300 focus:dark:border-blue-700 active:bg-gray-100 active:dark:bg-gray-900 active:text-gray-700 active:dark:text-gray-300 transition ease-in-out duration-150';

</script>

<template>
  <nav v-if="items.meta.current_page !== 1 || items.meta.last_page > 1" role="navigation" :aria-label="$t('Pagination Navigation')" class="flex items-center justify-between">
    <div class="flex justify-between flex-1 sm:hidden">
      <span v-if="items.meta.current_page === 1" :class="[commonClasses, 'rounded-md', 'text-gray-500', 'cursor-default']">
        <span v-html="$t('pagination.previous')"></span>
      </span>
      <Link v-else :href="items.links.prev" preserve-scroll
        :class="[commonClasses, linkClasses, 'rounded-md', 'text-gray-700', 'dark:text-gray-300']">
        <span v-html="$t('pagination.previous')"></span>
      </Link>

      <Link v-if="items.meta.last_page > 1" :href="items.links.next" preserve-scroll
        :class="[commonClasses, linkClasses, 'ml-3', 'rounded-md', 'text-gray-700', 'dark:text-gray-300']">
        <span v-html="$t('pagination.next')"></span>
      </Link>
      <span v-else :class="[commonClasses, 'ml-3', 'rounded-md', 'text-gray-500', 'cursor-default']">
        <span v-html="$t('pagination.next')"></span>
      </span>
    </div>

    <div class="hidden sm:flex-1 sm:flex sm:flex-col sm:items-center sm:justify-between">
      <p v-if="withSummary" class="text-sm text-gray-700 dark:text-gray-300 leading-5">
        <span v-if="firstItem">
          {{
            $t('Showing :first to :last of :total results', {
              first: firstItem,
              last: lastItem,
              total: items.meta.total,
            })
          }}
        </span>
        <span v-else>
          {{
            $t('Showing :count of :total results', {
              count: items.data.length,
              total: items.meta.total,
            })
          }}
        </span>
      </p>

      <div class="relative z-0 inline-flex shadow-sm rounded-md">
        <span v-if="items.meta.current_page === 1"
          :class="[commonClasses, 'px-2', 'rounded-l-md', 'text-gray-500', 'focus:z-10', 'cursor-default']"
          :aria-label="$t('pagination.previous')" aria-hidden="true" aria-disabled="true">
          <ChevronLeft />
        </span>
        <Link v-else :href="items.links.prev" preserve-scroll rel="prev"
          :class="[commonClasses, linkClasses, 'px-2', 'rounded-l-md', 'text-gray-500', 'hover:text-gray-400', 'hover:dark:text-gray-600', 'focus:z-10']"
          :aria-label="$t('pagination.previous')">
          <ChevronLeft />
        </Link>

        <template v-for="(link, id) in items.meta.links" :key="id">
          <span v-if="link.url === null" aria-disabled="true" :class="[commonClasses, '-ml-px', 'text-gray-700', 'dark:text-gray-500', 'cursor-default']"
            v-html="link.label">
          </span>
          <span v-else-if="link.active" aria-current="page" :class="[commonClasses, '-ml-px', 'text-gray-500', 'bg-gray-100', 'dark:bg-gray-800', 'cursor-default']"
            v-html="link.label">
          </span>
          <Link v-else :href="link.url" preserve-scroll
            :class="[commonClasses, linkClasses, '-ml-px', 'text-gray-700', 'dark:text-gray-300', 'focus:z-10']"
            :aria-label="$t('Go to page :page', { page: link.label })">
            <span v-html="link.label"></span>
          </Link>
        </template>

        <Link v-if="items.meta.current_page < items.meta.last_page" :href="items.links.next" preserve-scroll rel="next"
          :class="[commonClasses, linkClasses, '-ml-px', 'px-2', 'rounded-r-md', 'text-gray-500', 'hover:text-gray-400', 'hover:dark:text-gray-600', 'focus:z-10']"
          :aria-label="$t('pagination.next')">
          <ChevronRight />
        </Link>
        <span v-else :class="[commonClasses, linkClasses, '-ml-px', 'px-2', 'rounded-r-md', 'text-gray-500', 'focus:z-10', 'cursor-default']"
          :aria-label="$t('pagination.next')" aria-hidden="true" aria-disabled="true">
          <ChevronRight />
        </span>
      </div>
    </div>
  </nav>
</template>
