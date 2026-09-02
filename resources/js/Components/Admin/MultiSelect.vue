<template>
    <div class="relative" ref="multiSelectRef">
        <div
            @click="toggleDropdown"
            class="dark:bg-dark-900 shadow-theme-xs focus-within:border-brand-300 flex min-h-11 w-full cursor-pointer flex-wrap items-center rounded-lg border border-gray-300 bg-transparent px-4 py-1.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900"
        >
            <!-- Show placeholder if nothing is in the modelValue array -->
            <span v-if="modelValue.length === 0" class="text-gray-400">Select modules...</span>

            <div class="flex flex-auto flex-wrap items-center gap-2">
                <div
                    v-for="value in modelValue"
                    :key="value"
                    class="group flex h-[30px] items-center justify-center rounded-full border border-blue-100 bg-blue-50 px-3 text-xs font-bold text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300"
                >
                    <span>{{ getLabel(value) }}</span>
                    <button @click.stop="toggleItem(value)" class="pl-2 hover:text-blue-900">
                        <svg
                            width="12"
                            height="12"
                            viewBox="0 0 14 14"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M3.40717 3.40815L10.5916 10.5926M10.5916 3.40815L3.40717 10.5926"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </button>
                </div>
            </div>
            <svg
                class="ml-auto transition-transform"
                :class="{ 'rotate-180': isOpen }"
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
            >
                <path
                    d="M4.79175 7.39551L10.0001 12.6038L15.2084 7.39551"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </div>

        <transition
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                class="absolute z-50 mt-1 w-full rounded-lg border bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800"
            >
                <ul class="custom-scrollbar max-h-60 overflow-y-auto p-1">
                    <li
                        v-for="option in options"
                        :key="option.value"
                        @click="toggleItem(option.value)"
                        class="flex cursor-pointer items-center justify-between rounded-md px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                        :class="{
                            'bg-blue-50/50 dark:bg-blue-900/10': modelValue.includes(option.value),
                        }"
                    >
                        <span class="text-sm dark:text-gray-200">{{ option.label }}</span>
                        <svg
                            v-if="modelValue.includes(option.value)"
                            class="h-4 w-4 text-blue-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="3"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
    options: { type: Array, required: true },
    modelValue: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:modelValue'])
const isOpen = ref(false)
const multiSelectRef = ref(null)

const toggleDropdown = () => (isOpen.value = !isOpen.value)

const toggleItem = (value) => {
    const newValue = [...props.modelValue]
    const index = newValue.indexOf(value)

    if (index === -1) {
        newValue.push(value)
    } else {
        newValue.splice(index, 1)
    }

    console.log('[MultiSelect] Emitting selection:', newValue)
    emit('update:modelValue', newValue)
}

const getLabel = (value) => {
    const option = props.options.find((opt) => opt.value === value)
    return option ? option.label : value
}

const handleClickOutside = (event) => {
    if (multiSelectRef.value && !multiSelectRef.value.contains(event.target)) {
        isOpen.value = false
    }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>
