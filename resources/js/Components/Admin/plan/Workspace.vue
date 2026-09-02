<script setup>
import { inject, ref, onMounted, onUnmounted, computed, provide } from 'vue';
import FloorObject from '@/Components/plan/FloorObject.vue';
import TransformHandles from '@/Components/plan/TransformHandles.vue';
import Minimap from '@/Components/plan/Minimap.vue';
import Ruler from '@/Components/plan/Ruler.vue';

const {
    objects, deselectAll, viewportTransform, tool, dragState, gridPatternSize, showGrid,
    isDragging, startDrag, drag, endDrag, selectedObject, backgroundImage,
    history, historyIndex, undo, redo
} = inject('floorPlan');

const viewportEl = ref(null);
const canvasSVG = ref(null);
provide('viewportEl', viewportEl);

const svgStyle = computed(() => ({
    transform: `translate(${viewportTransform.x}px, ${viewportTransform.y}px) scale(${viewportTransform.k})`
}));
const isCreating = computed(() => dragState.value?.type === 'create');
const isBoxSelecting = computed(() => dragState.value?.type === 'box-select');

const screenToSVGPoint = (clientX, clientY) => {
    if (!canvasSVG.value) return { x: 0, y: 0 };
    const pt = canvasSVG.value.createSVGPoint();
    const svgRect = canvasSVG.value.getBoundingClientRect();
    pt.x = clientX - svgRect.left;
    pt.y = clientY - svgRect.top;
    const screenCTM = canvasSVG.value.getScreenCTM();
    return screenCTM ? pt.matrixTransform(screenCTM.inverse()) : pt;
};

const handleBackgroundMouseDown = (event) => {
    const startPoint = screenToSVGPoint(event.clientX, event.clientY);
    if (tool.value === 'select') {
        deselectAll();
        startDrag('box-select', startPoint);
    } else if (['rect', 'circle', 'text'].includes(tool.value)) {
        startDrag('create', startPoint, { tool: tool.value });
    }
};

// This handler is now specifically for panning, initiated by the middle mouse button
const handlePanStart = (event) => {
    if (event.button === 1) { // Middle mouse button
        startDrag('pan', {x: event.clientX, y: event.clientY});
    }
};

const handleMouseMove = (event) => { if (isDragging.value) drag({x: event.clientX, y: event.clientY}); };
const handleMouseUp = () => { if (isDragging.value) endDrag(); };
const onDragStart = (type, event, payload = {}) => startDrag(type, screenToSVGPoint(event.clientX, event.clientY), payload);

const handleWheel = (event) => {
    event.preventDefault();
    const factor = event.deltaY > 0 ? 0.95 : 1.05;
    const point = screenToSVGPoint(event.clientX, event.clientY);
    drag('zoom', { factor, point }); // Use drag logic for zooming
};

onMounted(() => { window.addEventListener('mousemove', handleMouseMove); window.addEventListener('mouseup', handleMouseUp); });
onUnmounted(() => { window.removeEventListener('mousemove', handleMouseMove); window.removeEventListener('mouseup', handleMouseUp); });
</script>

<template>
    <main class="flex flex-1 flex-col rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-shrink-0 items-center justify-between gap-4 border-b border-gray-200 p-3 px-5 dark:border-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Canvas</h3>
            <div class="flex items-center gap-2">
                <button @click="undo" :disabled="historyIndex <= 0" class="tool-btn">Undo</button>
                <button @click="redo" :disabled="historyIndex >= history.length - 1" class="tool-btn">Redo</button>
            </div>
        </div>

        <!-- CORRECTED: Main Canvas Area Layout -->
        <div class="relative flex-1 overflow-hidden" ref="viewportEl" @wheel="handleWheel" @mousedown="handlePanStart">
            <!-- Rulers -->
            <Ruler orientation="horizontal" class="absolute top-0 left-6 right-0 z-30" />
            <Ruler orientation="vertical" class="absolute top-6 bottom-0 left-0 z-30" />
            <div class="absolute top-0 left-0 z-30 h-6 w-6 border-b border-r border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"></div>

            <!-- SVG Viewport (offset by rulers) -->
            <div class="absolute top-6 left-6 right-0 bottom-0 overflow-hidden">
                <svg ref="canvasSVG" id="workspace-svg" class="w-full h-full" :style="svgStyle">
                    <defs>
                        <!-- CORRECTED: Grid pattern with more visible stroke -->
                        <pattern id="gridpattern" :width="gridPatternSize" :height="gridPatternSize" patternUnits="userSpaceOnUse">
                            <path v-if="showGrid" :d="`M ${gridPatternSize} 0 L 0 0 0 ${gridPatternSize}`" class="stroke-gray-200 dark:stroke-gray-700/50" stroke-width="1"/>
                        </pattern>
                    </defs>
                    <rect width="5000" height="5000" fill="url(#gridpattern)" @mousedown="handleBackgroundMouseDown"/>

                    <image v-if="backgroundImage.src" :href="backgroundImage.src" :width="backgroundImage.width" :height="backgroundImage.height" :opacity="backgroundImage.opacity" />

                    <g id="objects-layer">
                        <FloorObject v-for="obj in objects" :key="obj.id" :object-data="obj" @drag-start="onDragStart" />
                    </g>

                    <g id="ui-layer" class="pointer-events-none">
                        <rect v-if="isCreating" :x="dragState.ghost.x" :y="dragState.ghost.y" :width="dragState.ghost.width" :height="dragState.ghost.height" class="fill-brand-500/20 stroke-brand-600" stroke-width="1.5" stroke-dasharray="4 4"/>
                        <rect v-if="isBoxSelecting" :x="dragState.startPoint.x" :y="dragState.startPoint.y" :width="dragState.endPoint.x - dragState.startPoint.x" :height="dragState.endPoint.y - dragState.startPoint.y" class="fill-brand-500/10 stroke-brand-600" stroke-width="1" stroke-dasharray="3 3"/>
                    </g>

                    <TransformHandles v-if="selectedObject" :object-data="selectedObject" @drag-start="onDragStart" />
                </svg>
            </div>

            <Minimap />
        </div>
    </main>
</template>

<style scoped>
.tool-btn {
    @apply rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800;
}
</style>
