<script setup>
import { computed } from 'vue'
import {
  startOfWeek,
  endOfWeek,
  eachDayOfInterval,
  format,
  isSameDay,
  parseISO,
  isToday,
} from 'date-fns'

const props = defineProps(['events', 'currentDate', 'selectedStaff'])
defineEmits(['openUpdateModal'])

const weekDays = computed(() => {
  const start = startOfWeek(props.currentDate, { weekStartsOn: 1 })
  const end = endOfWeek(props.currentDate, { weekStartsOn: 1 })
  return eachDayOfInterval({ start, end })
})

const getEventsForDay = (day) => {
  return props.events
    .filter((event) => {
      const eventDate = parseISO(event.start)
      const matchesStaff = props.selectedStaff ? event.resourceId === props.selectedStaff.id : true
      return isSameDay(eventDate, day) && matchesStaff
    })
    .sort((a, b) => new Date(a.start) - new Date(b.start))
}

const getStatusColor = (status) => {
  const map = {
    upcoming: 'bg-blue-500',
    checked_in: 'bg-yellow-500',
    ongoing: 'bg-orange-500',
    completed: 'bg-green-600',
    cancelled: 'bg-gray-400',
    missed: 'bg-red-500',
  }
  return map[status] || 'bg-brand-500'
}
</script>

<template>
  <div
    class="grid h-full grid-cols-7 overflow-hidden rounded-xl border-l bg-white dark:border-gray-800 dark:bg-gray-900"
  >
    <div
      v-for="day in weekDays"
      :key="day.toString()"
      class="flex min-h-0 flex-col border-r dark:border-gray-800"
    >
      <!-- Day Header -->
      <div
        class="border-b p-2 text-center dark:border-gray-800"
        :class="
          isToday(day) ? 'bg-brand-50/50 dark:bg-brand-500/10' : 'bg-gray-50 dark:bg-gray-800/50'
        "
      >
        <p class="text-[10px] font-bold text-gray-400 uppercase">{{ format(day, 'EEE') }}</p>
      </div>

      <!-- Event Stack -->
      <div class="custom-scrollbar flex-1 space-y-1 overflow-y-auto p-1">
        <span
          :class="[
            'flex h-5 w-5 items-center justify-center rounded-full text-[11px] font-bold',
            isToday(day) ? 'bg-brand-500 text-white' : 'text-gray-400',
          ]"
        >
          {{ format(day, 'd') }}
        </span>

        <div
          v-for="event in getEventsForDay(day)"
          :key="event.id"
          @click="$emit('openUpdateModal', event)"
          :class="getStatusColor(event.extendedProps.status)"
          class="cursor-pointer rounded p-1.5 shadow-sm transition-all hover:brightness-110"
        >
          <p class="truncate text-[9px] leading-tight font-bold text-white">
            {{ event.extendedProps.clientName }}
          </p>
          <p class="truncate text-[8px] text-white/80">
            {{ format(parseISO(event.start), 'h:mm a') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
