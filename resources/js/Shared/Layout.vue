<style lang="scss" scoped>
main {
  color: #343a4b;
}

.icon-search {
  left: 8px;
  top: 8px;
}
</style>

<template>
  <main>
    <div class="">
      <nav class="max-w-4xl mx-auto flex h-10 items-center justify-between border-b bg-gray-50 px-3 sm:px-6">
        <div>
          <span class="inline mr-6"><inertia-link :href="'dashboard'" class="underline">Home</inertia-link></span>
          <span class="inline mr-6"><inertia-link :href="'officelife'" class="underline">OfficeLife's subscription</inertia-link></span>
          <span class="inline mr-36"><inertia-link :href="'monica'" class="underline">Monica's subscription</inertia-link></span>
        </div>

        <div>
          <span v-if="$page.props.user.instance_administrator" class="inline mr-6"><inertia-link :href="'administration'" class="underline">Administration</inertia-link></span>
          <span class="inline"><inertia-link :href="'logout'" class="underline">Logout</inertia-link></span>
        </div>
      </nav>
      <main class="relative mt-10">
        <slot />
      </main>
    </div>

    <toaster />
  </main>
</template>

<script>
import Toaster from '@/Shared/Toaster';

export default {
  components: {
    Toaster,
  },

  mounted() {
    if (localStorage.success) {
      this.flash(localStorage.success, 'success');
      localStorage.removeItem('success');
    }
  },
};
</script>
