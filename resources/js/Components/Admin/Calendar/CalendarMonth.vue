<script setup>
import { computed } from 'vue'
import {
  startOfMonth,
  endOfMonth,
  startOfWeek,
  endOfWeek,
  eachDayOfInterval,
  format,
  isSameDay,
  isSameMonth,
  parseISO,
  isToday,
} from 'date-fns'

const props = defineProps(['events', 'currentDate', 'selectedStaff'])
defineEmits(['openUpdateModal'])

const days = computed(() => {
  const monthStart = startOfMonth(props.currentDate)
  const monthEnd = endOfMonth(monthStart)
  const startDate = startOfWeek(monthStart, { weekStartsOn: 1 })
  const endDate = endOfWeek(monthEnd, { weekStartsOn: 1 })
  return eachDayOfInterval({ start: startDate, end: endDate })
})

const getEventsForDay = (day) => {
  return props.events.filter((event) => {
    const eventDate = parseISO(event.start)
    const matchesStaff = props.selectedStaff ? event.resourceId === props.selectedStaff.id : true
    return isSameDay(eventDate, day) && matchesStaff
  })
}
</script>

<template>
  <div
    class="flex h-full flex-col overflow-hidden rounded-xl border bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
  >
    <!-- Weekday Labels -->
    <div class="grid grid-cols-7 border-b bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
      <div
        v-for="label in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
        :key="label"
        class="py-2 text-center text-[10px] font-bold tracking-wider text-gray-400 uppercase"
      >
        {{ label }}
      </div>
    </div>

    <!-- Days Grid -->
    <div class="grid min-h-0 flex-1 auto-rows-fr grid-cols-7">
      <div
        v-for="day in days"
        :key="day.toString()"
        class="flex min-h-0 flex-col border-r border-b p-1 dark:border-gray-800"
        :class="!isSameMonth(day, currentDate) ? 'bg-gray-50/30 dark:bg-white/[0.01]' : ''"
      >
        <div class="mb-1 flex items-center justify-between">
          <span
            :class="[
              'flex h-5 w-5 items-center justify-center rounded-full text-[11px] font-bold',
              isToday(day) ? 'bg-brand-500 text-white' : 'text-gray-400',
            ]"
          >
            {{ format(day, 'd') }}
          </span>
          <span v-if="getEventsForDay(day).length > 0" class="text-brand-400 text-[9px] font-bold">
            {{ getEventsForDay(day).length }} Jobs
          </span>
        </div>

        <div class="custom-scrollbar flex-1 space-y-0.5 overflow-y-auto">
          <div
            v-for="event in getEventsForDay(day).slice(0, 3)"
            :key="event.id"
            @click="$emit('openUpdateModal', event)"
            class="hover:bg-brand-50 dark:hover:bg-brand-500/10 cursor-pointer truncate rounded border border-gray-200 bg-gray-100 px-1 py-0.5 text-[9px] text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
          >
            {{ event.extendedProps.clientName }}
          </div>
          <p
            v-if="getEventsForDay(day).length > 3"
            class="text-center text-[8px] text-gray-400 italic"
          >
            +{{ getEventsForDay(day).length - 3 }} more
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
