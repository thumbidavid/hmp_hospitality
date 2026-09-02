<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    settings: Array
})

// Field configuration matching the exact 2D structure of useResourceCRUD
const settingFormFields = [
    [
        { name: 'name', label: 'Setting Tag Name', component: 'TextInput', props: { placeholder: 'e.g. City & Business' } }
    ],
    [
        { name: 'slug', label: 'Slug (Auto-generated if blank)', component: 'TextInput', props: { placeholder: 'e.g. city-business' } }
    ]
]

const crud = useResourceCRUD({
    resourceName: 'Setting',
    entityName: 'setting',
    routeNames: {
        store: 'app.admin.settings.store',
        update: 'app.admin.settings.update',
        destroy: 'app.admin.settings.destroy',
    },
    initialFormState: () => ({
        id: null,
        name: '',
        slug: ''
    }),
    fieldsConfig: settingFormFields,
})

// Inline modal open trigger
const editSetting = (item) => {
    crud.openEditModal(item)
}

const columns = ref([
    { key: 'name', label: 'Setting Tag Name' },
    { key: 'slug', label: 'Slug' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="Best For Tag Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Best For Tag Management" />

        <DataTable :data="settings" :columns="columns" @edit-item="editSetting" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="crud.openCreateModal()">Create Tag</PrimaryButton>
            </template>

            <template #cell-name="{ item }">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ item.name }}
                </span>
            </template>

            <template #cell-slug="{ item }">
                <span class="font-mono text-xs text-gray-500 dark:text-gray-400">
                    {{ item.slug }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>