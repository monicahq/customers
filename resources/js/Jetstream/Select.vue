
<script setup>
import { onMounted, computed, ref } from 'vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  options: {
    type: [Array, Object],
    default: () => [],
  },
  excludedId: {
    type: Number,
    default: -1,
  },
});

onMounted(() => {
    if (select.value.hasAttribute('autofocus')) {
        select.value.focus();
    }
});

const select = ref(null);

const proxySelect = computed({
    get() {
        return props.modelValue;
    },

    set(val) {
        emit('update:modelValue', val);
    },
});

const filterExclude = (options) => {
  return options.filter((option) => option.id !== props.excludedId);
};

defineExpose({ focus: () => select.value.focus() });

</script>

<template>
  <select
    ref="select"
    v-model="proxySelect"
    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
  >
    <template v-if="Array.isArray(options)">
      <option
        v-for="option in filterExclude(options)"
        :key="option.id"
        :value="option.id"
      >
        {{ option.name }}
      </option>
    </template>
    <template v-else>
      <optgroup
        v-for="(optgroup, index) in options"
        :key="index"
        :label="optgroup.name"
      >
        <option
          v-for="option in filterExclude(optgroup.options)"
          :key="option.id"
          :value="option.id"
        >
          {{ option.name }}
        </option>
      </optgroup>
    </template>
  </select>
</template>
