<script setup>
import CalendarDay from './CalendarDay.vue'
import CalendarWeek from './CalendarWeek.vue'
import CalendarMonth from './CalendarMonth.vue'

const props = defineProps({
  view: { type: String, default: 'day' },
  events: Array,
  currentDate: Date,
  selectedStaff: Object,
  staffList: Array,
})

defineEmits(['openUpdateModal', 'openCreateModalForSlot'])

const components = {
  day: CalendarDay,
  week: CalendarWeek,
  month: CalendarMonth,
}
</script>

<template>
  <component
    :is="components[view]"
    v-bind="props"
    @open-update-modal="$emit('openUpdateModal', $event)"
    @open-create-modal-for-slot="$emit('openCreateModalForSlot', $event)"
  />
</template>
