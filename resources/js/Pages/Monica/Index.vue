<style lang="scss" scoped>
</style>

<template>
  <layout>
    <main class="sm:mt-18 relative h-screen">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <h2 class="mb-4">
          Which plan is right for you?
        </h2>

        <div v-for="plan in data" :key="plan.id" class="mb-4 p-3 sm:p-3 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:rounded-lg flex items-center justify-between">
          <div>
            <h3 class="text-lg">{{ plan.friendly_name }} - <span class="text-sm text-gray-500">USD ${{ plan.price }} / {{ plan.frequency }}</span></h3>
            <p class="text-gray-600 text-sm">{{ plan.description }}</p>
          </div>

          <a :href="plan.pay_link" class="focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
            Choose
          </a>
        </div>

        <div>You will be able to upgrade storage later.</div>
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
    data: {
      type: Object,
      default: null,
    },
  },
};
</script>
