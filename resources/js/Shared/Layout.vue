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
  <main class="bg-gray-50">
    <div class="min-h-full">
      <div class="fixed top-0 z-10 w-full">
        <!-- main nav -->
        <nav class="max-w-8xl mx-auto flex h-10 items-center justify-between border-b bg-gray-50 px-3 sm:px-6">
          sdfal
        </nav>
      </div>
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
