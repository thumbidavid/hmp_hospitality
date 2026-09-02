<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    countries: Array
})

// Field configuration matching the exact 2D structure of useResourceCRUD
const countryFormFields = [
    [
        { name: 'name', label: 'Country Name', component: 'TextInput', props: { placeholder: 'e.g. Kenya' } }
    ],
    [
        { name: 'iso2_code', label: 'ISO 2-Letter Code', component: 'TextInput', props: { placeholder: 'e.g. KE', maxlength: 2 } },
        { name: 'region', label: 'Region', component: 'TextInput', props: { placeholder: 'e.g. East Africa' } }
    ]
]

const crud = useResourceCRUD({
    resourceName: 'Country',
    entityName: 'country',
    routeNames: {
        store: 'app.admin.countries.store',
        update: 'app.admin.countries.update',
        destroy: 'app.admin.countries.destroy',
    },
    initialFormState: () => ({
        id: null,
        name: '',
        iso2_code: '',
        region: ''
    }),
    fieldsConfig: countryFormFields,
})

// For simple taxonomies, we open the edit modal inline on the index page
const editCountry = (item) => {
    crud.openEditModal(item)
}

const columns = ref([
    { key: 'name', label: 'Country Name' },
    { key: 'iso2_code', label: 'ISO2 Code' },
    { key: 'region', label: 'Region' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="Country Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Country Management" />

        <DataTable :data="countries" :columns="columns" @edit-item="editCountry" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="crud.openCreateModal()">Create Country</PrimaryButton>
            </template>

            <template #cell-iso2_code="{ item }">
                <span class="font-mono text-sm uppercase font-semibold text-gray-700 dark:text-gray-300">
                    {{ item.iso2_code || 'N/A' }}
                </span>
            </template>

            <template #cell-region="{ item }">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ item.region || 'N/A' }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>
