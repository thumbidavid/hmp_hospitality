<script setup>
import { inject, computed } from 'vue';

const props = defineProps({
    orientation: {
        type: String, // 'horizontal' or 'vertical'
        default: 'horizontal'
    }
});

const { viewportTransform, pxPerMeter } = inject('floorPlan');
const isHorizontal = props.orientation === 'horizontal';

// Generate an array of ticks to be rendered
const ticks = computed(() => {
    const currentScale = viewportTransform.k;
    const offset = isHorizontal ? viewportTransform.x : viewportTransform.y;
    const canvasSize = 5000;

    // Determine the major tick interval based on zoom level to avoid clutter
    let intervalMeters = 1;
    if (pxPerMeter.value * currentScale < 40) intervalMeters = 5;
    if (pxPerMeter.value * 5 * currentScale < 40) intervalMeters = 10;
    if (pxPerMeter.value * 10 * currentScale < 40) intervalMeters = 50;

    const majorIntervalPx = intervalMeters * pxPerMeter.value;
    const minorIntervalPx = majorIntervalPx / 5;

    const result = [];
    for (let i = 0; i < canvasSize; i += minorIntervalPx) {
        const pos = i * currentScale + offset;
        const isMajor = i % majorIntervalPx === 0;

        // Only render ticks that are within the visible area to improve performance
        if (pos > -50 && pos < 3000) { // Assuming max screen width of 3000px
             result.push({
                pos,
                isMajor,
                label: isMajor ? `${i / pxPerMeter.value}m` : null
            });
        }
    }
    return result;
});
</script>

<template>
    <div
        class="ruler relative overflow-hidden bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 select-none"
        :class="{ 'h-6 border-b': isHorizontal, 'w-6 border-r': !isHorizontal }"
    >
        <div
            v-for="(tick, index) in ticks"
            :key="index"
            class="absolute text-gray-400"
            :style="isHorizontal ? { left: `${tick.pos}px` } : { top: `${tick.pos}px` }"
        >
            <div
                class="bg-gray-300 dark:bg-gray-600"
                :class="{
                    'w-px h-full': isHorizontal, 'h-px w-full': !isHorizontal,
                    'h-1/2': isHorizontal && !tick.isMajor, 'w-1/2': !isHorizontal && !tick.isMajor,
                }"
            ></div>
            <span
                v-if="tick.isMajor"
                class="absolute text-xs"
                :class="isHorizontal ? 'top-0.5 left-1' : 'top-1 left-0.5 -rotate-90 origin-top-left'"
            >{{ tick.label }}</span>
        </div>
    </div>
</template>
