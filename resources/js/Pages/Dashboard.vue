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
            <h2 class="text-center mb-8 text-lg">Please choose a product first.</h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-10">
              <!-- left -->
              <div class="text-center p-3 sm:p-3 mt-6 w-full overflow-hidden bg-white px-6 py-6 shadow-md sm:max-w-md sm:rounded-lg">

                <img loading="lazy" src="/img/officelife-logo.svg" alt="officelife logo" class="mb-3 mx-auto" height="150"
                    width="150"
                />

                <inertia-link :href="'officelife'" class="mb-4 focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900">
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

            <div v-if="receipts.length > 0">
              <p class="mb-4">All the receipts for all your subscriptions</p>

              <ul class="mb-12 rounded-lg border border-gray-200 bg-white">
                <li v-for="receipt in receipts" :key="receipt.id" class="flex justify-between item-list border-b border-gray-200 px-5 py-2 hover:bg-slate-50">
                  <div>
                    <span class="mr-3 text-gray-400 text-sm">{{ receipt.paid_at }}</span>
                    <span class="font-serif">{{ receipt.currency }} {{ receipt.amount }}</span>
                  </div>
                  <a :href="receipt.receipt_url" class="flex items-center" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>

                    <span class="underline">View receipt</span>
                  </a>
                </li>
              </ul>
            </div>

            <div class="rounded-lg border border-red-600 bg-red-50 p-5">
              <p class="text-center text-xl mb-4 font-semibold text-red-500">⚠️ Danger zone</p>
              <p class="mb-6">
                <span v-if="state != 'loading'" @click="destroy()" class="focus:shadow-outline-gray inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:outline-none active:bg-gray-900 cursor-pointer">Delete your billing account</span>
                <span v-if="state == 'loading'"> Loading… </span>
              </p>
              <p>This will immediately:</p>
              <ul class="ml-5 mb-2">
                <li>- Cancel all your active subscriptions,</li>
                <li>- Delete your account on https://customers.monicahq.com.</li>
              </ul>

              <p class="font-semibold text-red-500">You WILL still have to delete your account on Monica or OfficeLife.</p>
            </div>
          </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  components: {
    AppLayout,
  },

  props: {
    receipts: {
      type: Object,
      default: null,
    },
    destroy_account: {
      type: String,
      default: null,
    },
  },

  data() {
    return {
      loadingState: '',
    };
  },

  methods: {
    destroy() {
      if (
        confirm(
          "Are you sure? There is no turning back.",
        )
      ) {
        this.loadingState = 'loading';

        axios
          .delete(this.destroy_account)
          .then((response) => {
            this.$inertia.visit(response.data.data);
          })
          .catch((error) => {
            this.loadingState = null;
          });
      }
    },
  },
};
</script>
