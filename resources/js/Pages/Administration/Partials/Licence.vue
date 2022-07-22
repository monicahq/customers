<script setup>
import { ref } from 'vue';
import useClipboard from 'vue-clipboard3/dist/esm/index.js';
import Plan from '@/Pages/Partials/Plan.vue';

defineProps({
  licence: Object,
});

const licenceInput = ref(null);
const copied = ref(false);
const { toClipboard } = useClipboard();

const select = () => {
  licenceInput.value.focus();
  licenceInput.value.select();
};

const copyIntoClipboard = async (text) => {
  await toClipboard(text)
    .then(() => {
      copied.value = true;
      setTimeout(() => {
        copied.value = false;
      }, 2000);
    });
};
</script>

<template>
  <ul role="list" class="border border-gray-200 dark:border-gray-700 rounded-md divide-y divide-gray-200 dark:divide-gray-700">
    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
      <div class="w-0 flex-1 flex items-center">
        <span class="mr-4">Plan:</span>
        <Plan :plan="licence.plan" />
      </div>
    </li>
    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
      <div class="w-0 flex-1 flex items-center">
        <span>Licence key:</span>
        <input class="truncate w-full inline-block rounded bg-gray-100 dark:bg-gray-900 px-3 py-2 ml-2" :value="licence.key" ref="licenceInput" type="text" @click.prevent="select" />
      </div>
      <div class="ml-4 flex-shrink-0">
        <span v-if="copied" class="font-medium text-teal-600 dark:text-teal-400">
          {{ $t('Copied!') }}
        </span>
        <a v-else href="#" @click.prevent="copyIntoClipboard(licence.key)" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 cursor-pointer">
          {{ $t('Copy') }}
        </a>
      </div>
    </li>
    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
      <div class="w-0 flex-1 flex items-center">
        <span class="flex-1 w-0 truncate">
          {{ $t('Created at: :date', { date: licence.created_at }) }}
        </span>
      </div>
    </li>
    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
      <div class="w-0 flex-1 flex items-center">
        <span class="flex-1 w-0 truncate">
          {{ $t('Valid until: :date', { date: licence.valid_until_at }) }}
        </span>
      </div>
      <div class="ml-4 flex-shrink-0">
        <a href="#" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
          Revoke key
        </a>
      </div>
    </li>
  </ul>
</template>
