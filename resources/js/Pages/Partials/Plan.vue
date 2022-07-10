<script setup>
defineProps({
  plan: Object,
});
</script>

<template>
  <div>
    <h3 class="text-lg">
      {{ plan.friendly_name }} —
      <span class="text-sm text-gray-500">
       {{ plan.price }} / {{ plan.frequency }}
      </span>
    </h3>
    <p class="text-gray-600 text-sm">{{ plan.description }}</p>
  </div>
</template>
