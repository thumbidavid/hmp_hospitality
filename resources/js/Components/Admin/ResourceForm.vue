<script setup>
import { computed, watch } from 'vue'
import InputLabel from '@/Components/Admin/InputLabel.vue'
import InputError from '@/Components/Admin/InputError.vue'
import TextInput from '@/Components/Admin/TextInput.vue'
import NumberInput from '@/Components/Admin/NumberInput.vue'
import TextArea from '@/Components/Admin/TextArea.vue'
import SelectInput from '@/Components/Admin/SelectInput.vue'
import MultiSelect from '@/Components/Admin/MultiSelect.vue'
import Checkbox from '@/Components/Admin/Checkbox.vue'
import CheckboxGroup from '@/Components/Admin/CheckboxGroup.vue'
import Dropzone from '@/Components/Admin/Dropzone.vue'
import RichTextEditor from '@/Components/Admin/RichTextEditor.vue'
import FileUploader from './FileUploader.vue'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'

const componentMap = {
    TextInput,
    NumberInput,
    TextArea,
    SelectInput,
    MultiSelect,
    Dropzone,
    flatPickr,
    Checkbox,
    CheckboxGroup,
    FileUploader,
    RichTextEditor
}

const props = defineProps({
    modelValue: { type: Object, required: true },
    fieldsConfig: { type: Array, required: true },
    errors: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['update:modelValue'])

// Use a computed property for two-way binding with v-model
const formData = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
})

// DEBUG: Watch logo field
watch(
    () => formData.value?.logo,
    (newVal) => {
        console.log('FORM: v-model logo changed →', newVal)
        console.log('FORM: is File? →', newVal instanceof File)
        console.log('FORM: name →', newVal?.name)
        console.log('FORM: size →', newVal?.size)
    },
    { immediate: true }
)
</script>

<template>
    <div class="space-y-5">
        <!-- Render rows -->
        <div v-for="(row, rowIndex) in fieldsConfig" :key="rowIndex" class="grid grid-cols-1 gap-5"
            :class="`sm:grid-cols-${row.length}`">
            <!-- Render fields within each row -->
            <div v-for="field in row" :key="field.name">
                <InputLabel :for="field.name" :value="field.label" />
                <component :is="componentMap[field.component]" :id="field.name" v-model="formData[field.name]"
                    class="mt-1 block w-full" v-bind="field.props">
                    <option v-if="field.component === 'SelectInput' && field.props?.placeholder" value="" disabled>
                        {{ field.props.placeholder }}
                    </option>
                </component>
                <InputError class="mt-2" :message="errors[field.name]" />
            </div>
        </div>
    </div>
</template>
