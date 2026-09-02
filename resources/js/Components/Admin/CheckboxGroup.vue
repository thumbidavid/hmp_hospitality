<script setup>
import Checkbox from './Checkbox.vue'

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    options: {
        type: Array,
        required: true,
        // Format: [{ label: 'Catering', value: 'catering' }]
    },
    columns: {
        type: Number,
        default: 2,
    },
})

const emit = defineEmits(['update:modelValue'])

/**
 * We handle the update from Checkbox.vue manually to ensure the
 * array mutation is reactive and bubbles up to ResourceForm.
 */
const onCheckboxChange = (updatedArray) => {
    console.log('[CheckboxGroup] New Selection:', updatedArray)
    emit('update:modelValue', updatedArray)
}

const gridClass =
    {
        1: 'grid-cols-1',
        2: 'grid-cols-1 sm:grid-cols-2',
        3: 'grid-cols-1 sm:grid-cols-3',
    }[props.columns] || 'grid-cols-2'
</script>

<template>
    <div
        :class="[
            'grid gap-4 rounded-2xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-900/50',
            gridClass,
        ]"
    >
        <div v-for="option in options" :key="option.value" class="flex items-center">
            <Checkbox
                :id="`opt-${option.value}`"
                :value="option.value"
                :checked="modelValue"
                @update:checked="onCheckboxChange"
            >
                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                    {{ option.label }}
                </span>
            </Checkbox>
        </div>
    </div>
</template>
