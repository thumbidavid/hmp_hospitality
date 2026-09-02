<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    partners: Array
})

// Configuration schema mapping for the inline popup modal form
const partnerFormFields = [
    [
        { name: 'name', label: 'Partner/Company Name', component: 'TextInput', props: { placeholder: 'e.g. Sabre GDS' } },
        {
            name: 'partner_type',
            label: 'Partner Type',
            component: 'SelectInput',
            props: {
                placeholder: 'Select Affiliation',
                options: [
                    { value: 'gds', label: 'GDS / Distribution Systems' },
                    { value: 'dmc', label: 'DMC / Destination Management' },
                    { value: 'tourism_board', label: 'Tourism Board / DMO' },
                    { value: 'association', label: 'Tourism Association' },
                    { value: 'technology', label: 'Technology Partner' },
                    { value: 'distribution', label: 'Distribution Channels' },
                    { value: 'media', label: 'Media / PR' },
                    { value: 'other', label: 'Other Affiliation' }
                ]
            }
        }
    ],
    [
        { name: 'website_url', label: 'Partner Website URL', component: 'TextInput', props: { placeholder: 'https://example.com' } },
        { name: 'sort_order', label: 'Sorting Index', component: 'NumberInput', props: { placeholder: '0' } }
    ],
    [
        { name: 'logo', label: 'Partner Logo', component: 'FileUploader', props: { options: { maxFiles: 1 } } },
        { name: 'is_active', label: 'Visibility Option', component: 'Checkbox', props: { label: 'Visible on Public Portal' } }
    ],
    [
        { name: 'description', label: 'Partner Summary Description', component: 'TextArea', props: { placeholder: 'Optional background summary description...', rows: 3 } }
    ]
]

const crud = useResourceCRUD({
    resourceName: 'Partner',
    entityName: 'partner',
    routeNames: {
        store: 'app.admin.partners.store',
        update: 'app.admin.partners.update',
        destroy: 'app.admin.partners.destroy',
    },
    initialFormState: () => ({
        id: null,
        name: '',
        partner_type: '',
        logo: null,
        website_url: '',
        description: '',
        is_active: true,
        sort_order: 0
    }),
    fieldsConfig: partnerFormFields,
})

// Inline modal open trigger
const editPartner = (item) => {
    crud.openEditModal(item)
}

const columns = ref([
    { key: 'logo_url', label: 'Logo' },
    { key: 'name', label: 'Company / Alliance Name' },
    { key: 'partner_type', label: 'Affiliation Type' },
    { key: 'website_url', label: 'Website' },
    { key: 'is_active', label: 'Status' },
    { key: 'action', label: 'Action' },
])

// Helper mapping to translate system DB keys to clean UI badges
const typeMap = {
    gds: 'GDS / Distribution',
    dmc: 'DMC / Destination Management',
    tourism_board: 'Tourism Board / DMO',
    association: 'Tourism Association',
    technology: 'Technology Partner',
    distribution: 'Distribution Channel',
    media: 'Media / PR Partner',
    other: 'Other Affiliation'
}
</script>

<template>

    <Head title="Partner Directory Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Represented Partners" />

        <DataTable :data="partners" :columns="columns" @edit-item="editPartner" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="crud.openCreateModal()">Register Partner</PrimaryButton>
            </template>

            <!-- Logo display cell -->
            <template #cell-logo_url="{ item }">
                <div
                    class="h-10 w-16 overflow-hidden rounded-lg bg-gray-50 border border-gray-150 p-1 flex items-center justify-center dark:bg-gray-950 dark:border-gray-800">
                    <img v-if="item.logo_url" :src="item.logo_url" class="max-h-full max-w-full object-contain" />
                    <span v-else class="material-symbols-outlined text-gray-400 text-sm">broken_image</span>
                </div>
            </template>

            <!-- Name display cell -->
            <template #cell-name="{ item }">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    {{ item.name }}
                </span>
            </template>

            <!-- Formatted affiliation type cell -->
            <template #cell-partner_type="{ item }">
                <span
                    class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                    {{ typeMap[item.partner_type] || 'Other Affiliation' }}
                </span>
            </template>

            <!-- Website link display cell -->
            <template #cell-website_url="{ item }">
                <a v-if="item.website_url" :href="item.website_url" target="_blank"
                    class="text-brand-500 hover:underline inline-flex items-center gap-1 text-xs font-semibold">
                    Open Site
                    <span class="material-symbols-outlined text-xs">open_in_new</span>
                </a>
                <span v-else class="text-xs text-gray-400">None</span>
            </template>

            <!-- Visibility status indicator -->
            <template #cell-is_active="{ item }">
                <span :class="item.is_active
                    ? 'text-green-600 dark:text-green-400 font-semibold text-xs'
                    : 'text-gray-400 font-semibold text-xs'">
                    {{ item.is_active ? 'Active' : 'Hidden' }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>
