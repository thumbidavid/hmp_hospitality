<script setup>
import { inject, ref } from 'vue';

// Inject the entire "brain" to get access to its state and methods
const {
    tool, setTool, isSnap, showGrid, gridMeters, pxPerMeter,
    autoArrangeSettings, autoArrange, clearAll,
    saveToJson, loadFromJson,
    backgroundImage, loadBackgroundImage
} = inject('floorPlan');

// Template refs for programmatically clicking hidden file inputs
const fileInput = ref(null);
const bgFileInput = ref(null);
</script>

<template>
    <aside class="hidden w-72 flex-shrink-0 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:block">
        <!-- The custom-scrollbar class can be a global helper for styling scrollbars if you have one -->
        <div class="custom-scrollbar h-full space-y-6 overflow-y-auto">

            <!-- Tools -->
            <div>
                <h3 class="panel-h3">Tools</h3>
                <div class="grid grid-cols-3 gap-2">
                    <button @click="setTool('select')" :class="['tool-btn', {'tool-btn-active': tool === 'select'}]">Select</button>
                    <button @click="setTool('rect')" :class="['tool-btn', {'tool-btn-active': tool === 'rect'}]">Rect</button>
                    <button @click="setTool('circle')" :class="['tool-btn', {'tool-btn-active': tool === 'circle'}]">Circle</button>
                    <button @click="setTool('text')" :class="['tool-btn', {'tool-btn-active': tool === 'text'}]">Text</button>
                </div>
            </div>

            <!-- Grid & View -->
            <div>
                <h3 class="panel-h3">Grid & View</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="inspector-label">Scale (px/m)</label>
                        <input type="number" v-model.number="pxPerMeter" class="inspector-input w-24">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="inspector-label">Grid size (m)</label>
                        <select v-model.number="gridMeters" class="inspector-input w-24">
                            <option value="0.5">0.5 m</option>
                            <option value="1">1 m</option>
                            <option value="2">2 m</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button @click="showGrid = !showGrid" class="small-tool-btn">{{ showGrid ? 'Hide' : 'Show' }} Grid</button>
                        <button @click="isSnap = !isSnap" class="small-tool-btn">Snap: {{ isSnap ? 'On' : 'Off' }}</button>
                    </div>
                </div>
            </div>

            <!-- Background -->
            <div>
                <h3 class="panel-h3">Background</h3>
                <div class="space-y-4">
                    <button @click="bgFileInput.click()" class="small-tool-btn">Upload Image</button>
                    <input type="file" ref="bgFileInput" @change="loadBackgroundImage" class="hidden" accept="image/*">
                    <div>
                        <label class="inspector-label">Opacity: {{ Math.round(backgroundImage.opacity * 100) }}%</label>
                        <input type="range" v-model.number="backgroundImage.opacity" min="0" max="1" step="0.05" class="w-full h-2 cursor-pointer appearance-none rounded-lg bg-gray-200 dark:bg-gray-700">
                    </div>
                </div>
            </div>

            <!-- Auto Arrange -->
            <div>
                <h3 class="panel-h3">Auto Arrange</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="inspector-label">Rows</label>
                            <input type="number" v-model.number="autoArrangeSettings.rows" class="inspector-input">
                        </div>
                        <div>
                            <label class="inspector-label">Cols</label>
                            <input type="number" v-model.number="autoArrangeSettings.cols" class="inspector-input">
                        </div>
                    </div>
                    <button @click="autoArrange" class="small-tool-btn">Generate Grid</button>
                </div>
            </div>

            <!-- Project -->
            <div>
                <h3 class="panel-h3">Project</h3>
                <div class="space-y-2">
                    <button @click="saveToJson" class="small-tool-btn">Save to JSON</button>
                    <button @click="fileInput.click()" class="small-tool-btn">Load from JSON</button>
                    <input type="file" ref="fileInput" @change="loadFromJson" class="hidden" accept=".json">
                    <button @click="clearAll" class="small-tool-btn !border-error-500/50 !text-error-500 hover:!bg-error-500 hover:!text-white">Clear Canvas</button>
                </div>
            </div>

        </div>
    </aside>
</template>

<style scoped>
/* Scoped styles ensure they only apply to this component */
.panel-h3 {
    @apply mb-3 text-lg font-semibold text-gray-800 dark:text-white/90;
}
.tool-btn {
    @apply rounded-lg border border-gray-200 bg-white p-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800;
}
.tool-btn-active {
    @apply !border-brand-500 !bg-brand-50 !text-brand-600 dark:!border-brand-500 dark:!bg-brand-500/10 dark:!text-brand-400;
}
.inspector-label {
    @apply text-sm text-gray-500 dark:text-gray-400;
}
.inspector-input {
    @apply w-full rounded-lg border border-gray-200 bg-gray-50 p-2 text-sm text-gray-800 focus:border-brand-500 focus:ring-0 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90;
}
.small-tool-btn {
    @apply w-full rounded-lg border border-gray-200 bg-white p-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800;
}
</style>
