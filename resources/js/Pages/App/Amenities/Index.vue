<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    amenities: Array
})

// Field configuration matching the exact 2D structure of useResourceCRUD
const amenityFormFields = [
    [
        { name: 'name', label: 'Amenity Name', component: 'TextInput', props: { placeholder: 'e.g. Swimming Pool' } }
    ],
    [
        { name: 'slug', label: 'Slug (Auto-generated if blank)', component: 'TextInput', props: { placeholder: 'e.g. swimming-pool' } }
    ]
]

const crud = useResourceCRUD({
    resourceName: 'Amenity',
    entityName: 'amenity',
    routeNames: {
        store: 'app.admin.amenities.store',
        update: 'app.admin.amenities.update',
        destroy: 'app.admin.amenities.destroy',
    },
    initialFormState: () => ({
        id: null,
        name: '',
        slug: ''
    }),
    fieldsConfig: amenityFormFields,
})

// Inline modal open trigger
const editAmenity = (item) => {
    crud.openEditModal(item)
}

const columns = ref([
    { key: 'name', label: 'Amenity Name' },
    { key: 'slug', label: 'Slug' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="Amenity Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Amenity Management" />

        <DataTable :data="amenities" :columns="columns" @edit-item="editAmenity" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="crud.openCreateModal()">Create Amenity</PrimaryButton>
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
