<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Pages/Partials/Pagination.vue';

defineProps({
  users: Object,
  last_week_count: Number,
  last_month_count: Number,
});
</script>

<template>
  <AppLayout title="Administration">
    <template #header>
      <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Administration
          </h2>
        </li>
        <li>
          <div class="flex items-center">
            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 md:ml-2 hover:dark:text-gray-100">
              Users
            </span>
          </div>
        </li>
      </ol>
    </template>

    <div>
      <div class="max-w-5xl mx-auto py-10 sm:px-6 lg:px-8">
        <!-- stats -->
        <div class="grid grid-cols-3 gap-4 mb-10">
          <div class="shadow-md sm:rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 ml-5 text-gray-400 dark:text-gray-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <div class="pl-3 pt-5 pb-5 pr-5">
              <p class="text-sm text-gray-500 dark:text-gray-100 font-medium">
                Total number of accounts
              </p>
              <h2 class="text-xl dark:text-gray-400">
                {{ users.meta.total }}
              </h2>
            </div>
          </div>

          <div class="shadow-md sm:rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 ml-5 text-gray-400 dark:text-gray-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <div class="pl-3 pt-5 pb-5 pr-5">
              <p class="text-sm text-gray-500 dark:text-gray-100 font-medium">
                New accounts last week
              </p>
              <h2 class="text-xl dark:text-gray-400">
                {{ last_week_count }}
              </h2>
            </div>
          </div>

          <div class="shadow-md sm:rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 ml-5 text-gray-400 dark:text-gray-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
            </svg>

            <div class="pl-3 pt-5 pb-5 pr-5">
              <p class="text-sm text-gray-500 dark:text-gray-100 font-medium">
                New accounts last month
              </p>
              <h2 class="text-xl dark:text-gray-400">
                {{ last_month_count }}
              </h2>
            </div>
          </div>
        </div>

        <!-- table -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-4">
          <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <caption class="hidden">
              Users
            </caption>
            <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
              <tr>
                <th scope="col" class="px-6 py-3">
                  ID
                </th>
                <th scope="col" class="px-6 py-3">
                  Name
                </th>
                <th scope="col" class="px-6 py-3">
                  Email address
                </th>
                <th scope="col" class="px-6 py-3">
                  Created at
                </th>
                <th scope="col" class="px-6 py-3">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users.data" :key="user.id" class="border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 hover:dark:bg-gray-900 odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-700">
                <td class="px-6 py-4">
                  {{ user.id }}
                </td>
                <td class="px-6 py-4">
                  {{ user.name }}
                </td>
                <td class="px-6 py-4">
                  {{ user.email }}
                </td>
                <td class="px-6 py-4">
                  {{ user.created_at }}
                </td>
                <td class="px-6 py-4">
                  <Link :href="route('administration.user.show', { user: user.id })" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                    View
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :items="users" />

      </div>
    </div>
  </AppLayout>
</template>
