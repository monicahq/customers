<script setup>
import { Link } from '@inertiajs/vue3';
import MonicaLogo from '@/Layouts/MonicaLogo.vue';
import OfficeLifeLogo from '@/Layouts/OfficeLifeLogo.vue';
</script>

<template>
    <Link :href="'/'">
        <div class="flex items-center">
            <OfficeLifeLogo class="mr-4" />
            <MonicaLogo />
        </div>
    </Link>
</template>
