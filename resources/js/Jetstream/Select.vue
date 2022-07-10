
<script>
export default {
  inheritAttrs: false,
}
</script>

<script setup>
import { computed, onMounted, onUnmounted, ref, useAttrs } from 'vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: [String, Number],
    options: [Array, Object],
    excludedId: {
      type: Number,
      default: -1,
    },
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: 'full',
    },
    height: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: Array,
        default: () => ['py-1', 'bg-white'],
    },
    inputClasses: {
        type: Array,
        default: () => ['p-1', 'px-2', 'appearance-none', 'outline-none w-full'],
    },
});

const main = ref(null);
const select = ref(null);
const open = ref(false);

const proxySelect = computed({
    get() {
      const id = props.options.findIndex((x) => x.id === props.modelValue);
      return id > -1 ? props.options[id]['name'] : input.value;
    },

    set(val) {
      const id = props.options.findIndex((x) => x.name === val);
      emit('update:modelValue', id > -1 ? props.options[id]['id'] : '');
    },
});

const input = ref(null);
const onInput = (e) => {
    input.value = e.target.value;
};

const filtered = computed(() => {
  return input.value !== null
    ? props.options.filter((option) => option.name.search(new RegExp(input.value, "i")) > -1)
    : props.options.filter((option) => option.id !== props.excludedId);
});

defineExpose({
    focus: () => {
        select.value.focus();
        open.value = true;
    }
});

const onKeydown = (e) => {
    if (open.value && e.key === 'Escape') {
        close();
    }
};
const close = () => {
    open.value = false;
    input.value = null;
};

onMounted(() => {
    document.addEventListener('keydown', onKeydown);
    if (_.find(useAttrs(), (item, key) => key === 'autofocus') > -1) {
      setTimeout(() => {
        select.value.focus();
        open.value = true;
      }, 100);
    }
});
onUnmounted(() => document.removeEventListener('keydown', onKeydown));

const widthClass = computed(() => {
    return {
        '48': 'w-48',
        'full': 'w-full',
    }[props.width.toString()];
});

const heightClass = computed(() => {
    return {
        '48': 'h-48',
        'full': 'h-full',
    }[props.height.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'origin-top-left left-0';
    }

    if (props.align === 'right') {
        return 'origin-top-right right-0';
    }

    return 'origin-top';
});

</script>

<style scoped>
.overflow-auto {
    overflow: auto;
}
</style>

<template>
  <div ref="main" class="relative" :class="$attrs.class">
    <div class="flex p-1 border border-gray-300 focus-within:border-indigo-300 focus-within:ring focus-within:ring-indigo-200 focus-within:ring-opacity-50 rounded-md shadow-sm">
      <input
        v-model="proxySelect"
        :id="$attrs.id"
        :autocomplete="$attrs.autocomplete"
        ref="select"
        :class="inputClasses"
        @focus="open = true"
      />
      <div>
          <button @click.prevent="proxySelect = ''; close();" class="cursor-pointer w-6 h-full flex items-center text-gray-400 outline-none focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x w-4 h-4">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
          </button>
      </div>
      <div class="text-gray-300 w-8 py-1 pl-2 pr-1 border-l flex items-center border-gray-200">
          <button @click.prevent="open = ! open; if (open) { select.focus(); }" class="cursor-pointer w-6 h-6 text-gray-600 dark:text-gray-400 outline-none focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-up w-4 h-4">
                  <polyline v-if="open" points="18 15 12 9 6 15"></polyline>
                  <polyline v-else points="18 9 12 15 6 9"></polyline>
              </svg>
          </button>
      </div>
    </div>

    <!-- Full Screen Overlay -->
    <div v-show="open" class="fixed inset-0 z-40" @click="close" />

    <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="transform opacity-0 scale-95"
        enter-to-class="transform opacity-100 scale-100"
        leave-active-class="transition ease-in duration-75"
        leave-from-class="transform opacity-100 scale-100"
        leave-to-class="transform opacity-0 scale-95"
    >
        <div
            v-show="open"
            class="absolute z-50 mt-2 rounded-md shadow-lg overflow-auto"
            :class="[widthClass, heightClass, alignmentClasses]"
        >
            <div class="rounded-md ring-1 ring-black ring-opacity-5" :class="contentClasses">
              <div v-for="option in filtered"
                :key="option.id"
                class="cursor-pointer w-full border-gray-100 rounded-t border-b hover:bg-teal-100"
                @click="proxySelect = option.name; close();"
                >
                  <div class="flex w-full items-center p-2 pl-2 border-transparent bg-white border-l-2 relative hover:bg-teal-600 hover:text-teal-100 hover:border-teal-600">
                      <div class="w-full items-center flex">
                          <div class="mx-2 leading-6">{{ option.name }}</div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
    </transition>
  </div>
</template>
