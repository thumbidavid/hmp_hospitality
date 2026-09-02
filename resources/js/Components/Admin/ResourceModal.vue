<script setup>
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import ResourceForm from '@/Components/Admin/ResourceForm.vue'

const props = defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  isLoading: { type: Boolean, default: false },
  currentItem: { type: Object, required: true },
  formErrors: { type: Object, default: () => ({}) },
  fieldsConfig: { type: Array, required: true },
  submitButtonText: { type: String, default: 'Submit' },
  onClose: { type: Function, required: true },
  onSubmit: { type: Function, required: true },
  customBody: { type: [Object, Function], default: null },
  customBodyProps: { type: Object, default: () => ({}) },
})
</script>

<template>
  <div
    class="no-scrollbar relative max-h-[90vh] w-full overflow-y-auto rounded-3xl bg-white p-4 lg:p-11 dark:bg-gray-900"
  >
    <button
      @click="onClose"
      class="transition-color absolute top-5 right-5 z-10 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300"
    >
      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
        ></path>
      </svg>
    </button>

    <div class="pr-14">
      <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ title }}</h4>
      <p v-if="description" class="mb-6 text-sm text-gray-500 lg:mb-7 dark:text-gray-400">
        {{ description }}
      </p>
    </div>

    <div class="px-2">
      <!-- FIXED LINE: Changed v-model to :model-value -->
      <component
        v-if="customBody"
        :is="customBody"
        :model-value="currentItem"
        :errors="formErrors"
        v-bind="customBodyProps"
      />

      <ResourceForm
        v-else
        :model-value="currentItem"
        :fields-config="fieldsConfig"
        :errors="formErrors"
      />
    </div>

    <div class="flex justify-end gap-3 px-2 pt-8">
      <button
        @click="onClose"
        type="button"
        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300"
      >
        Cancel
      </button>
      <PrimaryButton @click="onSubmit" :disabled="isLoading" :class="{ 'opacity-25': isLoading }">
        <span v-if="isLoading">Processing...</span>
        <span v-else>{{ submitButtonText }}</span>
      </PrimaryButton>
    </div>
  </div>
</template>
