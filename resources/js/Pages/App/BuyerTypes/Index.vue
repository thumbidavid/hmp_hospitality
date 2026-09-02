<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    buyerTypes: Array
})

// Field configuration matching the exact 2D structure of useResourceCRUD
const buyerTypeFormFields = [
    [
        { name: 'name', label: 'Buyer Type Name', component: 'TextInput', props: { placeholder: 'e.g. Travel Advisor' } }
    ],
    [
        { name: 'slug', label: 'Slug (Auto-generated if blank)', component: 'TextInput', props: { placeholder: 'e.g. travel-advisor' } }
    ]
]

const crud = useResourceCRUD({
    resourceName: 'Buyer Type',
    entityName: 'buyer_type',
    routeNames: {
        store: 'app.admin.buyer-types.store',
        update: 'app.admin.buyer-types.update',
        destroy: 'app.admin.buyer-types.destroy',
    },
    initialFormState: () => ({
        id: null,
        name: '',
        slug: ''
    }),
    fieldsConfig: buyerTypeFormFields,
})

// Inline modal open trigger
const editBuyerType = (item) => {
    crud.openEditModal(item)
}

const columns = ref([
    { key: 'name', label: 'Buyer Type Name' },
    { key: 'slug', label: 'Slug' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="Buyer Type Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Buyer Type Management" />

        <DataTable :data="buyerTypes" :columns="columns" @edit-item="editBuyerType" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="crud.openCreateModal()">Create Buyer Type</PrimaryButton>
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
