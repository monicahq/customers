<template>
    <footer class="text-xs text-center mb-8 mt-4">
        <div class="mb-2">
          {{ $t('Monica. All rights reserved. 2017 — :date.', { date: (new Date).getFullYear() }) }}
          {{ $t('Made from all over the world. We ❤️ you.') }}
        </div>
        <div>
          {{ $t('This site is open source.') }}
          <span v-html="$page.props.footer"></span>
        </div>
    </footer>
</template>
