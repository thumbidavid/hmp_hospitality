<script setup>
import { computed } from 'vue'
import InputLabel from '@/Components/Admin/InputLabel.vue'
import TextInput from '@/Components/Admin/TextInput.vue'
import SelectInput from '@/Components/Admin/SelectInput.vue'
import TextArea from '@/Components/Admin/TextArea.vue'
import MultiSelect from '@/Components/Admin/MultiSelect.vue'
import Dropzone from '@/Components/Admin/Dropzone.vue'

const props = defineProps({
  modelValue: Object,
  categories: Array,
  serviceOptions: Array, // Expected format: { value: id, label: name, price: price }
  serviceCategories: Array,
  staffOptions: Array,
  errors: Object,
})

// --- PRICING GUIDE LOGIC ---
const pricingAnalysis = computed(() => {
  let totalValue = 0
  let hasChoiceSlots = false

  props.modelValue.slots.forEach((slot) => {
    if (slot.slot_type === 'fixed' && slot.service_id) {
      const service = props.serviceOptions.find((s) => s.value == slot.service_id)
      if (service) totalValue += parseFloat(service.price) * slot.quantity
    } else if (slot.slot_type === 'choice') {
      hasChoiceSlots = true
    }
  })

  const packagePrice = parseFloat(props.modelValue.package_price) || 0
  const savings = totalValue - packagePrice
  const discountPercent = totalValue > 0 ? Math.round((savings / totalValue) * 100) : 0

  return {
    totalValue,
    savings,
    discountPercent,
    hasChoiceSlots,
  }
})

const handleTypeChange = (index) => {
  const slot = props.modelValue.slots[index]
  if (slot.slot_type === 'fixed') slot.category_id = ''
  else slot.service_id = ''
}

const addSlot = () => {
  props.modelValue.slots.push({
    slot_name: `Slot ${props.modelValue.slots.length + 1}`,
    slot_type: 'fixed',
    service_id: '',
    category_id: '',
    quantity: 1,
  })
}
const removeSlot = (index) => {
  if (props.modelValue.slots.length > 1) props.modelValue.slots.splice(index, 1)
}
</script>

<template>
  <div class="space-y-6 text-left">
    <!-- Pricing Guide Alert (Business Intelligence) -->
    <div
      v-if="pricingAnalysis.totalValue > 0"
      class="flex items-center justify-between rounded-2xl border p-4 transition-all duration-300"
      :class="
        pricingAnalysis.savings < 0
          ? 'border-red-100 bg-red-50 text-red-700'
          : 'border-blue-100 bg-blue-50 text-blue-700'
      "
    >
      <div class="flex items-center gap-3">
        <div class="text-2xl">
          {{ pricingAnalysis.savings < 0 ? '⚠️' : '💡' }}
        </div>
        <div>
          <p class="text-sm font-bold">
            {{ pricingAnalysis.savings < 0 ? 'Pricing Warning: Loss Detected' : 'Pricing Guide' }}
          </p>
          <p class="text-xs opacity-90">
            Total Value of services: <strong>KES {{ pricingAnalysis.totalValue }}</strong
            >.
            {{
              pricingAnalysis.savings > 0
                ? `Client saves KES ${pricingAnalysis.savings} (${pricingAnalysis.discountPercent}% off).`
                : 'Package is currently more expensive than individual services.'
            }}
          </p>
        </div>
      </div>
      <div
        v-if="pricingAnalysis.hasChoiceSlots"
        class="rounded bg-white/50 px-2 py-1 text-[10px] font-bold uppercase"
      >
        Choice-Based
      </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
      <div>
        <InputLabel value="Package Name" />
        <TextInput
          v-model="modelValue.package_name"
          class="w-full"
          placeholder="e.g., Bridal Bliss Bundle"
        />
      </div>
      <div>
        <InputLabel value="Package Category" />
        <SelectInput v-model="modelValue.package_category_id" :options="categories" />
      </div>
    </div>

    <div
      class="grid grid-cols-1 gap-5 rounded-2xl border bg-gray-50 p-5 md:grid-cols-3 dark:border-gray-800 dark:bg-white/5"
    >
      <div>
        <InputLabel value="Visit Logic" />
        <SelectInput
          v-model="modelValue.visit_type"
          :options="[
            { value: 'single', label: 'One Session' },
            { value: 'multi', label: 'Multi-Visit (Credits)' },
          ]"
        />
      </div>
      <div v-if="modelValue.visit_type === 'multi'">
        <InputLabel value="Session Count" />
        <TextInput type="number" v-model="modelValue.max_visits" class="w-full" />
      </div>
      <div>
        <InputLabel value="Final Package Price" />
        <TextInput
          type="number"
          v-model="modelValue.package_price"
          class="text-brand-600 w-full font-bold"
        />
      </div>
    </div>

    <!-- ... (Rest of Slot Builder remains the same) ... -->
    <div class="space-y-4">
      <div class="flex items-center justify-between border-b pb-2 dark:border-gray-800">
        <h4 class="text-xs font-bold text-gray-400 uppercase">Included Services (Slots)</h4>
        <button
          @click="addSlot"
          type="button"
          class="text-brand-500 hover:text-brand-600 flex items-center gap-1 text-xs font-bold"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" />
          </svg>
          Add Slot
        </button>
      </div>

      <div
        v-for="(slot, index) in modelValue.slots"
        :key="index"
        class="relative rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"
      >
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
          <div>
            <InputLabel value="Slot Title" class="text-[10px]" />
            <TextInput v-model="slot.slot_name" />
          </div>
          <div>
            <InputLabel value="Logic" class="text-[10px]" />
            <SelectInput
              v-model="slot.slot_type"
              :options="[
                { value: 'fixed', label: 'Fixed Item' },
                { value: 'choice', label: 'Choice' },
              ]"
              @change="handleTypeChange(index)"
            />
          </div>
          <div>
            <InputLabel
              :value="slot.slot_type === 'fixed' ? 'Select Service' : 'Select Category'"
              class="text-[10px]"
            />
            <SelectInput
              v-if="slot.slot_type === 'fixed'"
              v-model="slot.service_id"
              :options="serviceOptions"
            />
            <SelectInput v-else v-model="slot.category_id" :options="serviceCategories" />
          </div>
          <div>
            <InputLabel value="Qty" class="text-[10px]" />
            <TextInput type="number" v-model="slot.quantity" />
          </div>
        </div>
        <button
          v-if="modelValue.slots.length > 1"
          @click="removeSlot(index)"
          type="button"
          class="absolute -top-2 -right-2 rounded-full border border-gray-100 bg-white p-1.5 text-red-500 shadow-md dark:border-gray-700 dark:bg-gray-800"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" />
          </svg>
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
      <div>
        <InputLabel value="Restrict to Specialists (Optional)" />
        <MultiSelect v-model="modelValue.staff_ids" :options="staffOptions" />
      </div>
      <div>
        <InputLabel value="Package Cover" />
        <Dropzone v-model="modelValue.image" :options="{ maxFiles: 1 }" />
      </div>
    </div>
  </div>
</template>
