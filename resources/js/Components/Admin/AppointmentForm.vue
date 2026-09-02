<script setup>
import { computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { addMinutes, format, parseISO, isValid } from 'date-fns'
import InputLabel from '@/Components/Admin/InputLabel.vue'
import TextInput from '@/Components/Admin/TextInput.vue'
import SelectInput from '@/Components/Admin/SelectInput.vue'
import TextArea from '@/Components/Admin/TextArea.vue'

const props = defineProps({
  modelValue: Object,
  clients: Array,
  staff: Array,
  services: Array,
  packages: Array,
  errors: Object,
})

// --- DYNAMIC END TIME CALCULATION ---
watch(
  () => [props.modelValue.service_id, props.modelValue.start],
  () => {
    if (!props.modelValue.service_id || !props.modelValue.start) return
    const service = props.services.find((s) => s.id == props.modelValue.service_id)
    if (service) {
      const startDate = parseISO(props.modelValue.start)
      if (isValid(startDate)) {
        const endDate = addMinutes(startDate, service.duration_minutes)
        props.modelValue.end = format(endDate, "yyyy-MM-dd'T'HH:mm")
      }
    }
  },
  { deep: true }
)

// --- WHATSAPP LOGIC ---
const handleMessage = (type) => {
  window.open(
    type === 'confirmation' ? props.modelValue.confirmation_url : props.modelValue.reminder_url,
    '_blank'
  )
  router.patch(
    route('admin.operations.appointments.mark-sent', { appointment: props.modelValue.id, type }),
    {},
    { preserveScroll: true }
  )
}

// --- DATA MAPPING ---
const selectedClient = computed(() => props.clients.find((c) => c.id == props.modelValue.client_id))
const selectedPackage = computed(() =>
  props.packages?.find((p) => p.id == props.modelValue.package_id)
)

const clientOptions = computed(() =>
  props.clients.map((c) => ({ value: c.id, label: `${c.first_name} ${c.last_name}` }))
)
const staffOptions = computed(() =>
  props.staff.map((s) => ({ value: s.id, label: `${s.firstName} ${s.lastName}` }))
)
const serviceOptions = computed(() =>
  props.services.map((s) => ({ value: s.id, label: `${s.service_name} (${s.duration_minutes}m)` }))
)
const packageOptions = computed(
  () => props.packages?.map((p) => ({ value: p.id, label: p.package_name })) || []
)
</script>

<template>
  <div class="space-y-6 text-left">
    <!-- 1. SMART ACTION BAR -->
    <div
      v-if="modelValue.id"
      class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/5"
    >
      <div class="flex gap-2">
        <button
          v-if="!modelValue.confirmation_sent"
          @click="handleMessage('confirmation')"
          class="rounded-xl bg-green-500 px-3 py-2 text-xs font-black text-white uppercase shadow-sm transition-all hover:bg-green-600"
        >
          Confirm via WA
        </button>
        <button
          v-if="!modelValue.reminder_sent"
          @click="handleMessage('reminder')"
          class="rounded-xl bg-blue-500 px-3 py-2 text-xs font-black text-white uppercase shadow-sm transition-all hover:bg-blue-600"
        >
          Send Reminder
        </button>
        <span
          v-if="modelValue.confirmation_sent && modelValue.reminder_sent"
          class="flex items-center gap-1 text-xs font-bold text-green-500"
        >
          ✓ All Notifications Sent
        </span>
      </div>
      <a
        v-if="modelValue.location !== 'spa' && selectedClient?.primary_location"
        :href="selectedClient.primary_location"
        target="_blank"
        class="bg-brand-500 rounded-xl px-3 py-2 text-xs font-black text-white uppercase shadow-sm"
      >
        📍 Navigate
      </a>
    </div>

    <!-- 2. CORE FIELDS -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
      <div>
        <InputLabel value="Client" /><SelectInput
          v-model="modelValue.client_id"
          :options="clientOptions"
        />
      </div>
      <div>
        <InputLabel value="Specialist" /><SelectInput
          v-model="modelValue.staff_id"
          :options="staffOptions"
        />
      </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
      <div class="md:col-span-1">
        <InputLabel value="Service" /><SelectInput
          v-model="modelValue.service_id"
          :options="serviceOptions"
        />
      </div>
      <div class="md:col-span-1">
        <InputLabel value="Link to Package (Optional)" /><SelectInput
          v-model="modelValue.package_id"
          :options="packageOptions"
        />
      </div>
    </div>

    <!-- PACKAGE CHOICE LOGIC -->
    <div
      v-if="selectedPackage"
      class="space-y-4 rounded-2xl border border-purple-100 bg-purple-50 p-5 dark:border-purple-800 dark:bg-purple-900/10"
    >
      <h4 class="text-[10px] font-black tracking-widest text-purple-700 uppercase">
        Selected Package Slots
      </h4>
      <div v-for="slot in selectedPackage.slots" :key="slot.id" class="flex flex-col gap-1">
        <span class="text-xs font-bold text-gray-500">{{ slot.slot_name }}</span>
        <div
          v-if="slot.slot_type === 'fixed'"
          class="text-sm font-black text-gray-800 dark:text-white"
        >
          {{ slot.service.service_name }}
        </div>
        <SelectInput
          v-else
          v-model="modelValue.package_choices[slot.id]"
          :options="slot.category.services.map((s) => ({ value: s.id, label: s.service_name }))"
        />
      </div>
    </div>

    <!-- 3. TIMING -->
    <div
      class="grid grid-cols-1 gap-5 rounded-2xl border bg-gray-50 p-5 md:grid-cols-2 dark:border-gray-700 dark:bg-gray-800/50"
    >
      <div>
        <InputLabel value="Start Time" /><TextInput
          type="datetime-local"
          v-model="modelValue.start"
        />
      </div>
      <div>
        <InputLabel value="End Time (Calculated)" /><TextInput
          type="datetime-local"
          v-model="modelValue.end"
          readonly
          class="bg-gray-100"
        />
      </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
      <div>
        <InputLabel value="Location" /><SelectInput
          v-model="modelValue.location"
          :options="[
            { value: 'spa', label: 'Spa' },
            { value: 'primary', label: 'Home' },
            { value: 'secondary', label: 'Office' },
          ]"
        />
      </div>
      <div>
        <InputLabel value="Status" /><SelectInput
          v-model="modelValue.status"
          :options="[
            { value: 'upcoming', label: 'Upcoming' },
            { value: 'checked_in', label: 'Arrived' },
            { value: 'ongoing', label: 'Service In Progress' },
            { value: 'completed', label: 'Finished' },
            { value: 'cancelled', label: 'Cancelled' },
            { value: 'missed', label: 'No-Show' },
          ]"
        />
      </div>
    </div>

    <TextArea
      v-model="modelValue.notes"
      label="Special Instructions"
      placeholder="Allergies, preferences..."
    />
  </div>
</template>
