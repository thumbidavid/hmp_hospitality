<script setup>
const props = defineProps({
    objectData: { type: Object, required: true }
});
const emit = defineEmits(['drag-start']);

const handleMouseDown = (type, event, payload = {}) => {
    emit('drag-start', type, event, payload);
};
</script>

<template>
    <g :transform="`translate(${objectData.x}, ${objectData.y})`">
        <g :transform="`rotate(${objectData.rot || 0}, ${objectData.w / 2}, ${objectData.h / 2})`">
            <!-- Bounding box outline -->
            <rect
                :width="objectData.w"
                :height="objectData.h"
                fill="none"
                class="pointer-events-none stroke-brand-500"
                stroke-width="2"
                stroke-dasharray="4 3"
            />

            <!-- Resize Handles -->
            <rect
                @mousedown.stop="handleMouseDown('resize', $event, { handle: 'se' })"
                :x="objectData.w - 6"
                :y="objectData.h - 6"
                width="12"
                height="12"
                class="handle cursor-nwse-resize"
            />
            <!-- You would add the other 3 resize handles here following the same pattern -->

            <!-- Rotation Handle -->
            <g :transform="`translate(${objectData.w / 2}, -30)`">
                <line y2="20" class="pointer-events-none stroke-gray-500" stroke-width="1.5" />
                <circle
                    @mousedown.stop="handleMouseDown('rotate', $event)"
                    r="8"
                    class="handle cursor-alias"
                />
            </g>
        </g>
    </g>
</template>

<style scoped>
.handle {
    @apply fill-white stroke-brand-600 dark:stroke-brand-400;
    stroke-width: 1.5;
}
.handle:hover {
    @apply fill-brand-50;
}
</style>
