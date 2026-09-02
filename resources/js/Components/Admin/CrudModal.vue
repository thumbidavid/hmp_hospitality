<script setup>
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue'; // Your reusable text input
import InputLabel from '@/Components/InputLabel.vue'; // Your reusable label
import InputError from '@/Components/InputError.vue'; // Your reusable error component

const props = defineProps({
    // --- Configuration Props ---
    title: { type: String, required: true },
    description: { type: String, default: '' },
    fields: { type: Array, required: true },
    initialData: { type: Object, default: () => ({}) },
    submitRoute: { type: String, required: true }, // The route to post/put to
    isEditMode: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

// Initialize the Inertia form helper with fields from the config
const form = useForm(
    // Create a dynamic form object like { name: '', email: '', ... }
    props.fields.reduce((acc, field) => {
        acc[field.name] = props.initialData[field.name] || null;
        return acc;
    }, {})
);

const submit = () => {
    const options = {
        onSuccess: () => emit('close'),
        // You can add onBefore, onError, onFinish hooks here
    };

    if (props.isEditMode) {
        // For editing, we need to know the ID of the item
        form.put(route(props.submitRoute, props.initialData.id), options);
    } else {
        form.post(route(props.submitRoute), options);
    }
};
</script>

<template>
    <div class="max-h-[80vh] overflow-y-auto">
        <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">{{ title }}</h3>
        <p v-if="description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ description }}
        </p>

        <form @submit.prevent="submit" class="mt-6 space-y-4">
            <!-- Dynamically render form fields based on the 'fields' prop -->
            <div v-for="field in fields" :key="field.name">
                <InputLabel :for="field.name" :value="field.label" />
                <TextInput
                    :id="field.name"
                    :type="field.type || 'text'"
                    class="block w-full mt-1"
                    v-model="form[field.name]"
                    :placeholder="field.placeholder || ''"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors[field.name]" />
            </div>

            <!-- Modal Actions -->
            <div class="flex justify-end gap-3 pt-4">
                <button @click="$emit('close')" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Cancel
                </button>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ isEditMode ? 'Save Changes' : 'Create' }}
                </PrimaryButton>
            </div>
        </form>

        <!-- Close button (top right) -->
        <button @click="$emit('close')" class="absolute p-1 top-4 right-4 text-gray-400 rounded-full hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
</template>
