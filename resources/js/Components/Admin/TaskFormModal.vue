<script setup>
import { useForm } from '@inertiajs/vue3';
import { useKanbanStore } from '@/Stores/kanbanStore';
import Modal from '@/Components/Modal.vue'; // shared modal wrapper
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import MultiSelect from '@/Components/MultiSelect.vue';
import Dropzone from '@/Components/Dropzone.vue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

const props = defineProps({
  isEditMode: { type: Boolean, default: false },
  initialData: { type: Object, default: () => ({}) },
  maxWidth: { type: String, default: '2xl' }, // same API as CrudModal
});
const emit = defineEmits(['close']);
const kanbanStore = useKanbanStore();

const form = useForm({
  title: props.initialData.title || '',
  description: props.initialData.description || '',
  due_date: props.initialData.due_date || null,
  column_id: props.initialData.column_id || 1,
  tag: props.initialData.tag || '',
  assignees: props.initialData.assignees || [],
  attachments: [],
});

const submit = () => {
  form.transform(data => ({
    ...data,
    assignees: data.assignees.map(user => user.value),
  }));

  const options = {
    preserveScroll: true,
    onSuccess: (page) => {
      const task = page.props.flash.task;
      if (props.isEditMode) kanbanStore.updateTask(task);
      else kanbanStore.addTask(task);
      emit('close');
    },
  };

  if (props.isEditMode) form.put(route('tasks.update', props.initialData.id), options);
  else form.post(route('tasks.store'), options);
};
</script>

<template>
  <Modal :maxWidth="maxWidth" @close="$emit('close')">
    <template #body>
      <div class="flex flex-col max-h-[85vh]">
        <!-- HEADER -->
        <div
          class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 -mx-6 -mt-6 mb-6 flex items-center justify-between"
        >
          <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">
            {{ isEditMode ? 'Edit Task' : 'Add New Task' }}
          </h3>
          <button
            @click="$emit('close')"
            type="button"
            class="p-1 text-gray-400 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <!-- BODY -->
        <div class="flex-grow overflow-y-auto -mx-6 px-6">
          <form
            @submit.prevent="submit"
            id="task-form"
            class="space-y-5"
          >
            <!-- Title -->
            <div>
              <InputLabel for="title" value="Task Title" />
              <TextInput
                id="title"
                v-model="form.title"
                type="text"
                class="block w-full mt-1"
                required
              />
            </div>

            <!-- Description -->
            <div>
              <InputLabel for="description" value="Description" />
              <TextArea
                id="description"
                v-model="form.description"
                class="block w-full mt-1"
              />
            </div>

            <!-- Due Date -->
            <div>
              <InputLabel for="due_date" value="Due Date" />
              <flat-pickr
                v-model="form.due_date"
                class="w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                :config="{ dateFormat: 'Y-m-d' }"
              />
            </div>

            <!-- Column -->
            <div>
              <InputLabel for="column_id" value="Column" />
              <SelectInput
                id="column_id"
                v-model="form.column_id"
                class="block w-full mt-1"
              >
                <option value="1">Todo</option>
                <option value="2">In Progress</option>
                <option value="3">Done</option>
              </SelectInput>
            </div>

            <!-- Tag -->
            <div>
              <InputLabel for="tag" value="Tag" />
              <TextInput
                id="tag"
                v-model="form.tag"
                type="text"
                class="block w-full mt-1"
              />
            </div>

            <!-- Assignees -->
            <div>
              <InputLabel for="assignees" value="Assignees" />
              <MultiSelect
                v-model="form.assignees"
                placeholder="Select team members"
              />
            </div>

            <!-- Attachments -->
            <div>
              <InputLabel value="Attachments" />
              <Dropzone v-model="form.attachments" />
            </div>
          </form>
        </div>

        <!-- FOOTER -->
        <div
          class="px-6 pt-5 border-t border-gray-200 dark:border-gray-700 -mx-6 -mb-6 mt-6 flex justify-end gap-3"
        >
          <button
            @click="$emit('close')"
            type="button"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600"
          >
            Cancel
          </button>
          <PrimaryButton
            type="submit"
            form="task-form"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            {{ isEditMode ? 'Save Changes' : 'Create Task' }}
          </PrimaryButton>
        </div>
      </div>
    </template>
  </Modal>
</template>
