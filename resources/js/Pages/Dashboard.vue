<script setup>
import JetButtonLink from '@/Jetstream/ButtonLink.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import JetButton from '@/Jetstream/Button.vue';
import MonicaLogo from '@/Layouts/MonicaLogo.vue';
import OfficeLifeLogo from '@/Layouts/OfficeLifeLogo.vue';
import Receipts from '@/Pages/Partials/Receipts.vue';

defineProps({
  receipts: Array,
});
</script>

<style lang="scss" scoped>
.item-list {
  &:hover:first-child {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }

  &:last-child {
    border-bottom: 0;
  }

  &:hover:last-child {
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
  }
}
</style>

<template>
    <AppLayout title="Home">
        <div class="sm:mt-18 relative">
          <div class="mx-auto max-w-4xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
            <h2 class="text-center mb-8 text-lg dark:text-gray-100">
              {{ $t('Please choose a product first.') }}
            </h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-10">
              <!-- left -->
              <div class="text-center p-3 sm:p-3 mt-6 w-full overflow-hidden bg-white dark:bg-gray-900 px-6 py-6 shadow-md dark:shadow-gray-700 sm:max-w-md sm:rounded-lg">
                <OfficeLifeLogo />
                <JetButtonLink :href="route('officelife.index')">
                  {{ $t('Manage your subscription') }}
                </JetButtonLink>
              </div>

              <!-- right -->
              <div class="text-center p-3 sm:p-3 mt-6 w-full overflow-hidden bg-white dark:bg-gray-900 px-6 py-6 shadow-md dark:shadow-gray-700 sm:max-w-md sm:rounded-lg">
                <MonicaLogo />
                <JetButtonLink :href="route('monica.index')">
                  {{ $t('Manage your subscription') }}
                </JetButtonLink>
              </div>
            </div>

            <Receipts :receipts="receipts" />

          </div>
        </div>
    </AppLayout>
</template>
