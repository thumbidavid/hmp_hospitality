<script setup>
import { computed, inject } from 'vue';

const props = defineProps({
    objectData: { type: Object, required: true }
});

const { selectedIds, selectObject, isDragging, selectedObject } = inject('floorPlan');
const emit = defineEmits(['drag-start']);

const isSelected = computed(() => selectedIds.value.includes(props.objectData.id));

const handleMouseDown = (event) => {
    if (props.objectData.isLocked) return;
    selectObject(props.objectData.id, event.shiftKey);
    emit('drag-start', 'move', event);
}
</script>

<template>
    <g
        :transform="`translate(${objectData.x}, ${objectData.y})`"
        @mousedown.stop="handleMouseDown"
        class="floor-item-group"
        :data-id="objectData.id"
        :class="{
            'cursor-move': !isDragging && !objectData.isLocked,
            'cursor-grabbing': isDragging,
            'cursor-not-allowed': objectData.isLocked,
            'opacity-60': objectData.isLocked
        }"
    >
        <g :transform="`rotate(${objectData.rot || 0}, ${objectData.w / 2}, ${objectData.h / 2})`">
            <!-- RENDER SHAPES -->
            <rect
                v-if="objectData.type === 'rect'"
                :width="objectData.w"
                :height="objectData.h"
                :fill="objectData.fill"
                class="shape"
                :class="{ 'selection-box': isSelected && !selectedObject, 'collision-effect': objectData.isColliding }"
            />
            <circle
                v-if="objectData.type === 'circle'"
                :r="objectData.w / 2"
                :cx="objectData.w / 2"
                :cy="objectData.h / 2"
                :fill="objectData.fill"
                class="shape"
                :class="{ 'selection-box': isSelected && !selectedObject, 'collision-effect': objectData.isColliding }"
            />

            <!-- RENDER TEXT -->
            <text
                v-if="objectData.type === 'text'"
                :x="objectData.w / 2"
                :y="objectData.h / 2"
                :font-size="objectData.meta.fontSize"
                :fill="objectData.fill"
                class="pointer-events-none select-none"
                text-anchor="middle"
                dominant-baseline="central"
            >{{ objectData.meta.content }}</text>

            <text
                v-if="objectData.type !== 'text'"
                :x="objectData.w / 2"
                :y="objectData.h / 2"
                class="pointer-events-none select-none fill-gray-900 text-sm dark:fill-white"
                text-anchor="middle"
                dominant-baseline="middle"
            >{{ objectData.meta.name }}</text>
        </g>
    </g>
</template>

<style scoped>
.shape {
    @apply stroke-gray-800 transition-all duration-75 dark:stroke-gray-900;
    stroke-width: 1;
}
.selection-box {
    @apply !stroke-brand-500;
    stroke-width: 2;
}
.collision-effect {
    @apply !stroke-error-500;
    stroke-width: 2.5px;
    filter: drop-shadow(0 0 6px rgb(239 68 68 / 0.7));
}
</style>
