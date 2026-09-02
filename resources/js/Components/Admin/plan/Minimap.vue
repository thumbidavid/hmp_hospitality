<script setup>
import { inject, computed } from 'vue';

// Inject the core state and the ref to the main workspace viewport element
const { objects, viewportTransform } = inject('floorPlan');
const viewportEl = inject('viewportEl');

// Define the static size of the minimap display area
const minimapWidth = 208;
const minimapHeight = 128;

// --- COMPUTED PROPERTIES ---

// 1. Calculate the total bounding box that contains all objects on the main canvas.
//    This determines what area we need to show in the minimap.
const contentBBox = computed(() => {
    if (objects.value.length === 0) {
        // Provide a default size if there are no objects
        return { x: 0, y: 0, width: 2000, height: 1500 };
    }
    const xCoords = objects.value.map(o => o.x);
    const yCoords = objects.value.map(o => o.y);
    const rightCoords = objects.value.map(o => o.x + o.w);
    const bottomCoords = objects.value.map(o => o.y + o.h);

    const minX = Math.min(...xCoords);
    const minY = Math.min(...yCoords);
    const maxX = Math.max(...rightCoords);
    const maxY = Math.max(...bottomCoords);

    const padding = 200; // Add some visual padding around the content
    return {
        x: minX - padding,
        y: minY - padding,
        width: (maxX - minX) + padding * 2,
        height: (maxY - minY) + padding * 2,
    };
});

// 2. Calculate the scale factor needed to fit the `contentBBox` into the minimap's dimensions.
const scale = computed(() => {
    const scaleX = minimapWidth / contentBBox.value.width;
    const scaleY = minimapHeight / contentBBox.value.height;
    return Math.min(scaleX, scaleY); // Use the smaller scale factor to fit everything
});

// 3. Generate the SVG `transform` string for the group of mini-objects.
//    This scales and translates all the mini-shapes to fit correctly.
const groupTransform = computed(() => {
    const translateX = -contentBBox.value.x * scale.value;
    const translateY = -contentBBox.value.y * scale.value;
    return `translate(${translateX}, ${translateY}) scale(${scale.value})`;
});

// 4. Calculate the position and size of the rectangle that represents the main viewport.
const viewportRect = computed(() => {
    // If the main viewport element isn't mounted yet, return an empty object.
    if (!viewportEl.value) return { x: 0, y: 0, width: 0, height: 0 };

    // Get the actual client dimensions of the main workspace viewport
    const mainViewportWidth = viewportEl.value.clientWidth;
    const mainViewportHeight = viewportEl.value.clientHeight;

    return {
        // Scale the main viewport's size down by its own zoom and up by the minimap's scale
        width: (mainViewportWidth / viewportTransform.k) * scale.value,
        height: (mainViewportHeight / viewportTransform.k) * scale.value,
        // Calculate the position based on the main viewport's pan, offset by the content's origin
        x: (-viewportTransform.x - contentBBox.value.x) * scale.value,
        y: (-viewportTransform.y - contentBBox.value.y) * scale.value,
    };
});
</script>

<template>
    <div class="pointer-events-none absolute bottom-5 right-5 z-40 hidden rounded-2xl border border-gray-200 bg-white/80 p-2 shadow-lg backdrop-blur-sm dark:border-gray-800 dark:bg-gray-900/80 lg:block">
        <svg :width="minimapWidth" :height="minimapHeight">
            <!-- Group for all the scaled-down shapes -->
            <g :transform="groupTransform">
                <rect
                    v-for="obj in objects"
                    :key="'mini-' + obj.id"
                    :x="obj.x"
                    :y="obj.y"
                    :width="obj.w"
                    :height="obj.h"
                    :fill="obj.fill"
                    opacity="0.7"
                />
            </g>

            <!-- The rectangle showing the current viewport -->
            <rect
                :x="viewportRect.x"
                :y="viewportRect.y"
                :width="viewportRect.width"
                :height="viewportRect.height"
                class="fill-brand-500/30 stroke-brand-600 dark:stroke-brand-400"
                stroke-width="2"
            />
        </svg>
    </div>
</template>
