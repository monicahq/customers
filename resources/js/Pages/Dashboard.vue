<style lang="scss" scoped>
</style>

<template>
  <layout>
    <main class="sm:mt-18 relative h-screen">
      <div class="mx-auto max-w-4xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <h2 class="text-center mb-8 text-lg">Please choose a product first.</h2>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <!-- left -->
          <div class="text-center p-3 sm:p-3 mt-6 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:max-w-md sm:rounded-lg">

            <img loading="lazy" src="/img/officelife-logo.svg" alt="officelife logo" class="mb-3 mx-auto" height="150"
                 width="150"
            />

            <inertia-link class="mb-4 focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
              Manage your subscription
            </inertia-link>

          </div>

          <!-- right -->
          <div class="text-center p-3 sm:p-3 mt-6 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:max-w-md sm:rounded-lg">

            <img loading="lazy" src="/img/monica-logo.svg" alt="officelife logo" class="mb-3 mx-auto" height="150"
                 width="150"
            />

            <inertia-link :href="'monica'" class="mb-4 focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
              Manage your subscription
            </inertia-link>

          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';

export default {
  components: {
    Layout,
  },

  props: {
    layoutData: {
      type: Object,
      default: null,
    },
    data: {
      type: Object,
      default: null,
    },
  },
};
</script>
