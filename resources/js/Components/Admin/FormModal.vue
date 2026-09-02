<script setup>
import { useForm } from '@inertiajs/vue3';

// --- Reusable Components ---
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

// --- All POSSIBLE Form Components ---
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import MultiSelect from '@/Components/MultiSelect.vue';
import Dropzone from '@/Components/Dropzone.vue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

// This map allows us to render any component by name
const componentMap = {
    TextInput,
    TextArea,
    SelectInput,
    MultiSelect,
    Dropzone,
    flatPickr,
};

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
    // These two props provide backward compatibility for all pages
    fields: { type: Array, default: null }, // For simple, single-column forms
    fieldRows: { type: Array, default: null }, // For complex, multi-column forms
    initialData: { type: Object, default: () => ({}) },
    submitRoute: { type: String, required: true },
    isEditMode: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

const finalFieldRows = props.fieldRows ? props.fieldRows : (props.fields ? [props.fields] : []);
const allFields = finalFieldRows.flat();

const form = useForm(
    allFields.reduce((acc, field) => {
        acc[field.name] = props.initialData[field.name] || (field.component === 'MultiSelect' ? [] : null);
        return acc;
    }, {})
);

const submit = () => {
    form.transform(data => {
        const transformedData = { ...data };
        allFields.forEach(field => {
            if (field.component === 'MultiSelect' && Array.isArray(transformedData[field.name])) {
                transformedData[field.name] = transformedData[field.name].map(item => item.value);
            }
        });
        return transformedData;
    });

    const options = {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => emit('close'),
    };

    if (props.isEditMode) {
        form.put(route(props.submitRoute, props.initialData.id), options);
    } else {
        form.post(route(props.submitRoute), options);
    }
};
</script>

<template>
    <!-- This template uses the simple, non-scrolling layout from your preferred version -->
    <div class="max-h-[80vh] overflow-y-auto p-6">
        <h3 class="text-lg font-medium text-gray-800 dark:text-white/90">{{ title }}</h3>
        <p v-if="description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ description }}</p>

        <form @submit.prevent="submit" id="form-modal-form" class="mt-6 space-y-5">
            <!-- This part is now upgraded to handle rows and dynamic components -->
            <div v-for="(row, rowIndex) in finalFieldRows" :key="rowIndex" class="grid grid-cols-1 gap-5" :class="`sm:grid-cols-${row.length}`">
                <div v-for="field in row" :key="field.name">
                    <InputLabel :for="field.name" :value="field.label" />
                    <component
                        :is="componentMap[field.component]"
                        :id="field.name"
                        v-model="form[field.name]"
                        class="block w-full mt-1"
                        v-bind="field.props"
                    >
                        <option v-if="field.component === 'SelectInput' && field.props?.placeholder" value="" disabled>{{ field.props.placeholder }}</option>
                    </component>
                    <InputError class="mt-2" :message="form.errors[field.name]" />
                </div>
            </div>
        </form>

        <!-- Modal Actions -->
        <div class="flex justify-end gap-3 pt-6">
            <button @click="$emit('close')" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-300">Cancel</button>
            <PrimaryButton type="submit" form="form-modal-form" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ isEditMode ? 'Save Changes' : 'Create' }}
            </PrimaryButton>
        </div>

        <!-- Close button -->
        <button @click="$emit('close')" class="absolute p-1 top-4 right-4 text-gray-400 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
</template>
