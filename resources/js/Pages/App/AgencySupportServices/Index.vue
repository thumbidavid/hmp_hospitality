<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    services: Array
})

// Field configuration matching the exact 2D structure of useResourceCRUD
const serviceFormFields = [
    [
        { name: 'name', label: 'Service Name', component: 'TextInput', props: { placeholder: 'e.g. Airport coordination' } }
    ],
    [
        { name: 'slug', label: 'Slug (Auto-generated if blank)', component: 'TextInput', props: { placeholder: 'e.g. airport-coordination' } },
        { name: 'sort_order', label: 'Sort Order', component: 'TextInput', props: { type: 'number', placeholder: '0' } }
    ]
]

const crud = useResourceCRUD({
    resourceName: 'Support Service',
    entityName: 'agency_support_service',
    routeNames: {
        store: 'app.admin.agency-services.store',
        update: 'app.admin.agency-services.update',
        destroy: 'app.admin.agency-services.destroy',
    },
    initialFormState: () => ({
        id: null,
        name: '',
        slug: '',
        sort_order: 0
    }),
    fieldsConfig: serviceFormFields,
})

// Inline modal open trigger
const editService = (item) => {
    crud.openEditModal(item)
}

const columns = ref([
    { key: 'name', label: 'Service Name' },
    { key: 'slug', label: 'Slug' },
    { key: 'sort_order', label: 'Sort Order' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="Agency Support Service Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Agency Support Service Management" />

        <DataTable :data="services" :columns="columns" @edit-item="editService" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="crud.openCreateModal()">Create Service</PrimaryButton>
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

            <template #cell-sort_order="{ item }">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    {{ item.sort_order }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>
