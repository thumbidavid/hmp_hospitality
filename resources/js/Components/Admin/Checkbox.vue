<script setup>
const props = defineProps({
    modelValue: {
        type: [Boolean, Array],
        default: false,
    },

    value: {
        default: null,
    },

    id: {
        type: String,
        default: () => `checkbox-${Math.random().toString(36).substring(2, 9)}`,
    },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
    <div class="flex items-center">
        <div class="relative flex items-center">
            <input
                type="checkbox"
                :id="id"
                :value="value"
                :checked="modelValue"
                @change="emit('update:modelValue', $event.target.checked)"
                class="peer sr-only"
            />

            <div
                :class="[
                    'flex h-5 w-5 items-center justify-center rounded-md border-[1.5px] transition-all duration-200 ease-in-out',
                    modelValue ? 'border-blue-600 bg-blue-600' : 'border-gray-300 bg-white',
                ]"
            >
                <svg v-if="modelValue" width="12" height="12" viewBox="0 0 14 14" fill="none">
                    <path
                        d="M11.6666 3.5L5.24992 9.91667L2.33325 7"
                        stroke="white"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </div>
        </div>

        <label :for="id" class="ms-3 cursor-pointer text-sm font-medium text-gray-700">
            <slot />
        </label>
    </div>
</template>
