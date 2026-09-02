<script setup>
import { format, startOfWeek, endOfWeek } from 'date-fns'
import { computed } from 'vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'

const props = defineProps(['currentView', 'currentDate'])
const emit = defineEmits(['change-view', 'navigate', 'create'])

const formattedDate = computed(() => {
  if (props.currentView === 'day') return format(props.currentDate, 'EEEE, MMMM do yyyy')
  if (props.currentView === 'week') {
    const start = startOfWeek(props.currentDate, { weekStartsOn: 1 })
    const end = endOfWeek(props.currentDate, { weekStartsOn: 1 })
    return `${format(start, 'MMM d')} - ${format(end, 'MMM d, yyyy')}`
  }
  return format(props.currentDate, 'MMMM yyyy')
})
</script>

<template>
  <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
    <div class="flex items-center gap-2 rounded-xl bg-gray-100 p-1 dark:bg-gray-800">
      <button
        v-for="v in ['day', 'week', 'month']"
        :key="v"
        @click="$emit('change-view', v)"
        :class="
          currentView === v ? 'text-brand-500 bg-white shadow-sm dark:bg-gray-700' : 'text-gray-500'
        "
        class="rounded-lg px-4 py-1.5 text-xs font-bold uppercase transition-all"
      >
        {{ v }}
      </button>
    </div>

    <div class="flex items-center gap-4">
      <div class="flex items-center overflow-hidden rounded-lg border dark:border-gray-700">
        <button
          @click="$emit('navigate', 'prev')"
          class="border-r p-2 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
        >
          ◀
        </button>
        <button
          @click="$emit('navigate', 'next')"
          class="p-2 hover:bg-gray-50 dark:hover:bg-gray-800"
        >
          ▶
        </button>
      </div>
      <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ formattedDate }}</h2>
    </div>

    <PrimaryButton @click="$emit('create')">+ New Booking</PrimaryButton>
  </div>
</template>
