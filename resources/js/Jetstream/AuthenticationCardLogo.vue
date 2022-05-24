<script setup>
import { Link } from '@inertiajs/inertia-vue3';
</script>

<template>
    <Link :href="'/'">
        <div class="flex items-center">
          <img loading="lazy" src="/img/officelife-logo.svg" alt="officelife logo" class="mb-3 mr-4 mx-auto" height="100"
                width="100"
          />

          <img loading="lazy" src="/img/monica-logo.svg" alt="officelife logo" class="mb-3 mx-auto" height="100"
                width="100"
          />
        </div>
    </Link>
</template>
