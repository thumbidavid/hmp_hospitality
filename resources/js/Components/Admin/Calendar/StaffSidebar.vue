<script setup>
const props = defineProps(['staff', 'selectedStaffId'])
defineEmits(['select-staff'])
</script>

<template>
  <div class="no-scrollbar flex items-center gap-2 overflow-x-auto py-1">
    <button
      @click="$emit('select-staff', null)"
      :class="
        !selectedStaffId
          ? 'bg-brand-500 text-white'
          : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
      "
      class="rounded-xl px-4 py-2 text-xs font-bold whitespace-nowrap transition-colors"
    >
      All Specialists
    </button>
    <button
      v-for="s in staff"
      :key="s.id"
      @click="$emit('select-staff', s)"
      :class="
        selectedStaffId === s.id
          ? 'bg-brand-500 text-white'
          : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
      "
      class="flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold whitespace-nowrap transition-colors"
    >
      <div class="h-5 w-5 overflow-hidden rounded-full bg-gray-300">
        <img v-if="s.photo_url" :src="s.photo_url" class="h-full w-full object-cover" />
      </div>
      {{ s.firstName }}
    </button>
  </div>
</template>
