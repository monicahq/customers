<script setup>
import { trans } from 'laravel-vue-i18n';

const link = () => {
  return trans('You can <link>read the code here</link>.')
    .replace('<link>', '<a href="https://github.com/monicahq/customers" class="underline" rel="noopener noreferrer">')
    .replace('</link>', '</a>');
};

</script>

<template>
    <footer class="text-xs text-center mb-8 mt-4">
        <div class="mb-2">
          {{ $t('Monica. All rights reserved. 2017 — :date.', { date: (new Date).getFullYear() }) }}
          {{ $t('Made from all over the world. We ❤️ you.') }}
        </div>
        <div>
          {{ $t('This site is open source.') }}
          <span v-html="link()"></span>
        </div>
    </footer>
</template>
