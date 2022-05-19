Index.vue<style lang="scss" scoped>
</style>

<template>
  <layout>
    <main class="sm:mt-18 relative">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
          <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
              <tr>
                <th scope="col" class="px-6 py-3">
                  First name
                </th>
                <th scope="col" class="px-6 py-3">
                  Last name
                </th>
                <th scope="col" class="px-6 py-3">
                  Email address
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in data.users" :key="user.id" class="border-b dark:bg-gray-800 dark:border-gray-700 odd:bg-white even:bg-gray-50 odd:dark:bg-gray-800 even:dark:bg-gray-700">
                <td class="px-6 py-4">
                  {{  user.first_name  }}
                </td>
                <td class="px-6 py-4">
                  {{  user.last_name  }}
                </td>
                <td class="px-6 py-4">
                  {{ user.email }}
                </td>
              </tr>
            </tbody>
          </table>
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
    data: {
      type: Object,
      default: null,
    },
  }
};
</script>
