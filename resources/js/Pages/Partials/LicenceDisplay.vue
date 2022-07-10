<script setup>
import { ref } from 'vue';
import { trans } from 'laravel-vue-i18n';
import useClipboard from 'vue-clipboard3';
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import JetActionMessage from '@/Jetstream/ActionMessage.vue';

defineProps({
    licence: Object,
    link: String,
});

const licenceInput = ref(null);
const copied = ref(false);
const { toClipboard } = useClipboard();

const select = () => {
  licenceInput.value.focus();
  licenceInput.value.select();
}

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
  <div>
    <p class="mb-4 text-center">{{ $t('🎉 You have an active subscription.') }}</p>

    <slot />

    <p class="mb-4 mt-4">
      <div class="flex">
        <p class="flex-auto">
          {{ $t('This is your licence key:') }}
        </p>
        <JetActionMessage :on="copied" class="mr-6">
            {{ $t('Copied!') }}
        </JetActionMessage>
      </div>

      <div class="flex">
        <input class="truncate w-full inline-block rounded bg-gray-200 px-3 py-2 mr-3" :value="licence.key" ref="licenceInput" type="text" @click.prevent="select" />
        <JetSecondaryButton :title="$t('Copy licence into your clipboard')" @click.prevent="copyIntoClipboard(licence.key)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" class="w-4 mr-1"><g transform="matrix(2.857142857142857,0,0,2.857142857142857,0,0)"><g><path d="M9.5,1.5H11a1,1,0,0,1,1,1v10a1,1,0,0,1-1,1H3a1,1,0,0,1-1-1V2.5a1,1,0,0,1,1-1H4.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><rect x="4.5" y="0.5" width="5" height="2.5" rx="1" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></rect><line x1="4.5" y1="5.5" x2="9.5" y2="5.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></line><line x1="4.5" y1="8" x2="9.5" y2="8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></line><line x1="4.5" y1="10.5" x2="9.5" y2="10.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></line></g></g></svg>
            {{ $t('Copy') }}
        </JetSecondaryButton>
      </div>
    </p>

    <div class="mb-4 bg-blue-100 flex rounded-lg p-4">
      <div>
        <p class="font-bold mb-2">{{ $t('How to use your key:') }}</p>
        <ul class="ml-4">
          <li><span class="text-blue-500">1. </span>  <span v-html="link"></span></li>
          <li><span class="text-blue-500">2. </span>  {{ $t('Locate the Licence key section') }}</li>
          <li><span class="text-blue-500">3. </span>  {{ $t('Paste the licence key shown above.') }}</li>
          <li><span class="text-blue-500">4. </span>  {{ $t('Enjoy!') }}</li>
        </ul>
      </div>
    </div>

    <p class="mb-4 flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
      </svg>
      {{ $t('The licence key will automatically renew on :date.', { date: licence.valid_until_at_formatted }) }}
    </p>

    <p>

      <a :href="licence.paddle_update_url" rel="noopener noreferrer" class="mr-2 cursor-pointer focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>

        {{ $t('Update payment details') }}
      </a>

      <a :href="licence.paddle_cancel_url" rel="noopener noreferrer" class="cursor-pointer focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>

        {{ $t('Cancel subscription') }}
      </a>
    </p>
  </div>
</template>
