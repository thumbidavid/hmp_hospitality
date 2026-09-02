<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <!-- Replaced existing class with the new default class from the template -->
    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
