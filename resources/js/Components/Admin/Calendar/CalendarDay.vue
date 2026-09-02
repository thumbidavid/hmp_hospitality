<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import {
    format,
    isBefore,
    setHours,
    setMinutes,
    isToday,
    startOfMinute,
    isSameDay,
    parseISO,
} from 'date-fns'

const props = defineProps(['events', 'staffList', 'selectedStaff', 'currentDate'])
const emit = defineEmits(['openUpdateModal', 'openCreateModalForSlot'])

const HOURS = Array.from({ length: 14 }, (_, i) => i + 8) // 8 AM to 9 PM
const now = ref(new Date())

// Update 'now' every minute for the timeline
let timer
onMounted(() => {
    timer = setInterval(() => {
        now.value = new Date()
    }, 60000)
})
onUnmounted(() => clearInterval(timer))

// --- LOGIC: FILTER BY SELECTED DAY ---
const dailyEvents = computed(() => {
    if (!props.events) return []
    return props.events.filter((event) => {
        // Ensure we only show events for the day currently selected on the calendar
        return isSameDay(parseISO(event.start), props.currentDate)
    })
})

const filteredStaff = computed(() =>
    props.selectedStaff ? [props.selectedStaff] : props.staffList
)

// --- TIMELINE LOGIC ---
const timelinePosition = computed(() => {
    if (!isToday(props.currentDate)) return null
    const hour = now.value.getHours()
    const minutes = now.value.getMinutes()
    if (hour < 8 || hour >= 22) return null

    // 80px per hour + 40px for the sticky header
    const pixelsFromTop = (hour - 8) * 80 + minutes * 1.33
    return `${pixelsFromTop + 40}px`
})

const isSlotPast = (h, m) => {
    const slot = setMinutes(setHours(new Date(props.currentDate), h), m)
    return isBefore(slot, startOfMinute(new Date()))
}

const getStatusColor = (status) => {
    const map = {
        upcoming: 'bg-blue-600 border-blue-700',
        checked_in: 'bg-indigo-600 border-indigo-700',
        ongoing: 'bg-amber-500 border-amber-600',
        completed: 'bg-green-600 border-green-700',
        cancelled: 'bg-gray-400 border-gray-500',
        missed: 'bg-rose-500 border-rose-600',
    }
    return map[status] || 'bg-brand-500'
}
</script>

<template>
    <div
        class="flex h-full flex-col overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900"
    >
        <!-- Grid Container -->
        <div class="custom-scrollbar relative flex-1 overflow-auto">
            <div
                class="relative grid"
                :style="`grid-template-columns: 4rem repeat(${filteredStaff.length}, minmax(var(--col-min-width), 1fr))`"
                style="--col-min-width: 250px"
                :class="[
                    // RESPONSIVE COLUMN VISIBILITY LOGIC
                    filteredStaff.length === 1 ? 'w-full' : 'min-w-max lg:min-w-full',
                ]"
            >
                <!-- STICKY HEADER ROW (Z-40 to stay above cards) -->
                <div
                    class="sticky top-0 left-0 z-50 flex h-10 items-center justify-center border-r border-b bg-gray-50/95 text-[10px] font-black text-gray-400 uppercase backdrop-blur dark:border-gray-700 dark:bg-gray-800/95"
                >
                    Time
                </div>

                <div
                    v-for="s in filteredStaff"
                    :key="s.id"
                    class="sticky top-0 z-40 flex h-10 items-center justify-center gap-2 border-b border-l bg-gray-50/95 backdrop-blur dark:border-gray-700 dark:bg-gray-800/95"
                >
                    <div
                        class="border-brand-200 h-6 w-6 overflow-hidden rounded-full border-2 bg-white shadow-sm"
                    >
                        <img
                            v-if="s.photo_url"
                            :src="s.photo_url"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center bg-gray-100 text-[10px] font-bold text-gray-400"
                        >
                            {{ s.firstName[0] }}
                        </div>
                    </div>
                    <span
                        class="truncate text-xs font-black tracking-tight text-gray-700 uppercase dark:text-white"
                    >
                        {{ s.firstName }}
                    </span>
                </div>

                <!-- LIVE RED TIMELINE (Z-30) -->
                <div
                    v-if="timelinePosition"
                    :style="{ top: timelinePosition }"
                    class="pointer-events-none absolute right-0 left-0 z-30 flex items-center transition-all duration-700"
                >
                    <div
                        class="h-3 w-3 rounded-full border-2 border-white bg-red-500 shadow-lg dark:border-gray-900"
                    ></div>
                    <div
                        class="h-0.5 flex-1 bg-red-500/60 shadow-[0_0_8px_rgba(239,68,68,0.5)]"
                    ></div>
                </div>

                <!-- TIME GUTTER (Sticky Left) -->
                <div
                    class="sticky left-0 z-20 border-r bg-gray-50/95 backdrop-blur dark:border-gray-700 dark:bg-gray-900/95"
                >
                    <div
                        v-for="h in HOURS"
                        :key="h"
                        class="flex h-20 items-start justify-center border-b pt-2 dark:border-gray-800"
                    >
                        <span class="font-mono text-[10px] font-black text-gray-400 uppercase">
                            {{ h > 12 ? h - 12 : h }} {{ h >= 12 ? 'PM' : 'AM' }}
                        </span>
                    </div>
                </div>

                <!-- SPECIALIST COLUMNS -->
                <div
                    v-for="s in filteredStaff"
                    :key="s.id"
                    class="group relative border-l dark:border-gray-800/50"
                >
                    <!-- LEAVE OVERLAY -->
                    <div
                        v-if="s.on_leave"
                        class="absolute inset-0 z-30 flex items-center justify-center bg-gray-100/40 backdrop-blur-[1px] dark:bg-black/60"
                    >
                        <span
                            class="rotate-12 rounded-lg border-2 border-red-500 bg-white px-3 py-1 text-[10px] font-black text-red-500 uppercase shadow-xl dark:bg-gray-900"
                        >
                            Not Working Today
                        </span>
                    </div>

                    <!-- 15-MIN SLOTS -->
                    <div v-for="h in HOURS" :key="h" class="h-20 border-b dark:border-gray-800">
                        <div
                            v-for="m in [0, 15, 30, 45]"
                            :key="m"
                            @click="
                                !s.on_leave &&
                                !isSlotPast(h, m) &&
                                $emit('openCreateModalForSlot', {
                                    resourceId: s.id,
                                    start: setMinutes(setHours(new Date(props.currentDate), h), m),
                                })
                            "
                            :class="[
                                isSlotPast(h, m)
                                    ? 'cursor-not-allowed bg-gray-50/30 dark:bg-white/[0.02]'
                                    : 'hover:bg-brand-500/5 cursor-pointer',
                                'h-5 border-b border-dotted border-gray-100 transition-colors dark:border-gray-800/40',
                            ]"
                        ></div>
                    </div>

                    <!-- APPOINTMENT CARDS (Filtered to Daily Events) -->
                    <div
                        v-for="app in dailyEvents.filter(
                            (e) => Number(e.resourceId) === Number(s.id)
                        )"
                        :key="app.id"
                        @click.stop="$emit('openUpdateModal', app)"
                        :class="getStatusColor(app.extendedProps.status)"
                        class="group/card absolute right-1.5 left-1.5 z-10 cursor-pointer rounded-2xl border-l-4 p-2.5 text-white shadow-md transition-all hover:scale-[1.02] hover:shadow-xl active:scale-95"
                        :style="`top: ${(new Date(app.start).getHours() - 8) * 80 + new Date(app.start).getMinutes() * 1.33 + 42}px;
                     height: ${((new Date(app.end) - new Date(app.start)) / 60000) * 1.33 - 4}px;
                     min-height: 35px;`"
                    >
                        <div class="flex h-full flex-col overflow-hidden">
                            <p
                                class="truncate text-[11px] leading-tight font-black tracking-tighter uppercase"
                            >
                                {{ app.extendedProps.clientName }}
                            </p>
                            <p class="mt-0.5 truncate text-[9px] font-bold opacity-80">
                                {{ app.extendedProps.serviceName }}
                            </p>

                            <div
                                class="mt-auto flex items-center justify-between opacity-0 transition-opacity group-hover/card:opacity-100"
                            >
                                <span class="text-[8px] font-bold tracking-widest uppercase">
                                    {{ format(parseISO(app.start), 'h:mm') }} -
                                    {{ format(parseISO(app.end), 'h:mma') }}
                                </span>
                                <div class="flex gap-1">
                                    <span
                                        v-if="app.extendedProps.confirmation_sent"
                                        title="Confirmed"
                                        class="text-[10px]"
                                        >💬</span
                                    >
                                    <span
                                        v-if="app.extendedProps.reminder_sent"
                                        title="Reminded"
                                        class="text-[10px]"
                                        >⏰</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Responsive column adjustments */
@media (max-width: 640px) {
    .relative.grid {
        --col-min-width: 100vw;
    }
}
@media (min-width: 641px) and (max-width: 1024px) {
    .relative.grid {
        --col-min-width: 50%;
    }
}
@media (min-width: 1025px) and (max-width: 1280px) {
    .relative.grid {
        --col-min-width: 33.33%;
    }
}
@media (min-width: 1281px) {
    .relative.grid {
        --col-min-width: 25%;
    }
}

.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
    border: 2px solid transparent;
    background-clip: content-box;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #334155;
    background-clip: content-box;
}
</style>
