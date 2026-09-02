<script setup>
import { computed } from 'vue';

// Define emitted events and new props
defineEmits(['close']);
const props = defineProps({
    maxWidth: {
        type: String,
        default: '2xl', // Default width
    },
});

// Compute the width class based on the prop
const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl', // This is the default
    }[props.maxWidth];
});
</script>

<template>
  <div class="fixed inset-0 flex items-center justify-center overflow-y-auto modal z-99999">
    <div
      class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
      aria-hidden="true"
      @click="$emit('close')"
    ></div>

    <!-- MODIFIED: The slot now wraps a div that controls width and styling -->
    <div
        :class="maxWidthClass"
        class="relative w-full p-0 m-4 bg-white rounded-2xl shadow-lg dark:bg-gray-800"
    >
        <slot name="body"></slot>
    </div>
  </div>
</template>
