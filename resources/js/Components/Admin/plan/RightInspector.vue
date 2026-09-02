<script setup>
import { inject, computed } from 'vue';

// Inject the central state and methods from the "brain"
const { selectedObject, toggleLock, bringForward, sendBackward, duplicateSelected, pxPerMeter } = inject('floorPlan');

// Writable computed properties for clean two-way data binding with unit conversion
const widthInMeters = computed({
  get: () => selectedObject.value ? (selectedObject.value.w / pxPerMeter.value).toFixed(2) : 0,
  set: (val) => { if (selectedObject.value) selectedObject.value.w = Number(val) * pxPerMeter.value; }
});
const heightInMeters = computed({
  get: () => selectedObject.value ? (selectedObject.value.h / pxPerMeter.value).toFixed(2) : 0,
  set: (val) => { if (selectedObject.value) selectedObject.value.h = Number(val) * pxPerMeter.value; }
});
const areaInMeters = computed(() => {
    if (!selectedObject.value) return '0.00';
    const area = parseFloat(widthInMeters.value) * parseFloat(heightInMeters.value);
    return area.toFixed(2);
});
</script>

<template>
    <aside class="hidden w-72 flex-shrink-0 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] xl:block">
        <div class="custom-scrollbar h-full space-y-6 overflow-y-auto">
            <h3 class="panel-h3">Inspector</h3>

            <div v-if="selectedObject" class="space-y-4">
                <!-- General Properties -->
                <div>
                    <label class="inspector-label">Name</label>
                    <input type="text" v-model="selectedObject.meta.name" class="inspector-input" />
                </div>

                <!-- Text-Specific Properties -->
                <div v-if="selectedObject.type === 'text'" class="space-y-4">
                    <div>
                        <label class="inspector-label">Content</label>
                        <textarea v-model="selectedObject.meta.content" rows="2" class="inspector-input"></textarea>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="inspector-label">Font Size (px)</label>
                        <input type="number" v-model.number="selectedObject.meta.fontSize" class="inspector-input w-24" />
                    </div>
                </div>

                <!-- Shape Properties (Not for Text) -->
                <div v-if="selectedObject.type !== 'text'" class="space-y-4">
                    <div>
                        <label class="inspector-label">Exhibitor</label>
                        <input type="text" v-model="selectedObject.meta.exhibitor" class="inspector-input" />
                    </div>
                    <div>
                        <label class="inspector-label">Booth #</label>
                        <input type="text" v-model="selectedObject.meta.number" class="inspector-input" />
                    </div>
                </div>

                <!-- Dimensions -->
                <div class="space-y-4 rounded-xl border border-gray-100 p-3 dark:border-gray-800">
                     <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="inspector-label">Width (m)</label>
                            <input type="number" step="0.1" v-model="widthInMeters" class="inspector-input" />
                        </div>
                        <div>
                            <label class="inspector-label">Height (m)</label>
                            <input type="number" step="0.1" v-model="heightInMeters" class="inspector-input" />
                        </div>
                    </div>
                    <div v-if="selectedObject.type !== 'text'" class="text-xs text-gray-500">
                        Area: {{ areaInMeters }} m²
                    </div>
                </div>

                <!-- Transform & Color -->
                <div class="space-y-4">
                    <div>
                        <label class="inspector-label">Rotation (°)</label>
                        <input type="number" v-model.number="selectedObject.rot" class="inspector-input" />
                    </div>
                    <div>
                        <label class="inspector-label">Color</label>
                        <input type="color" v-model="selectedObject.fill" class="h-10 w-full rounded-lg border border-gray-200 dark:border-gray-700" />
                    </div>
                </div>

                <div>
                    <label class="inspector-label">Notes</label>
                    <textarea v-model="selectedObject.meta.notes" rows="3" class="inspector-input" placeholder="Add custom notes..."></textarea>
                </div>

                <!-- Actions -->
                <div>
                    <h3 class="panel-h3 mt-6">Actions</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <button @click="toggleLock" class="small-tool-btn">{{ selectedObject.isLocked ? 'Unlock' : 'Lock' }}</button>
                        <button @click="duplicateSelected" class="small-tool-btn">Duplicate</button>
                        <button @click="bringForward" class="small-tool-btn">Forward</button>
                        <button @click="sendBackward" class="small-tool-btn">Backward</button>
                    </div>
                </div>
            </div>

            <!-- Placeholder when nothing is selected -->
            <div v-else class="flex h-full items-center justify-center">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">No Item Selected</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Click an item on the canvas to see its properties.</p>
                </div>
            </div>
        </div>
    </aside>
</template>

<style scoped>
/* Using styles from LeftPalette for consistency */
.panel-h3 { @apply text-base font-semibold text-gray-800 dark:text-white/90; }
.inspector-label { @apply text-sm text-gray-500 dark:text-gray-400; }
.inspector-input { @apply w-full rounded-lg border border-gray-200 bg-gray-50 p-2 text-sm text-gray-800 focus:border-brand-500 focus:ring-0 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90; }
.small-tool-btn { @apply w-full rounded-lg border border-gray-200 bg-white p-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800; }
</style>
