<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    destinations: Array
})

// Since this is a complex resource, we do not need the inline form configuration here,
// but we keep useResourceCRUD initialized for global handlers like soft-deletes and flash messages.
const crud = useResourceCRUD({
    resourceName: 'Destination',
    entityName: 'destination',
    routeNames: {
        store: 'app.admin.destinations.store',
        update: 'app.admin.destinations.update',
        destroy: 'app.admin.destinations.destroy',
    },
    initialFormState: () => ({}),
    fieldsConfig: [],
})

// Redirects to the dedicated creation page
const createDestination = () => {
    window.location.href = route('app.admin.destinations.create')
}

// Redirects to the dedicated editing page
const editDestination = (item) => {
    window.location.href = route('app.admin.destinations.edit', item.id)
}

const columns = ref([
    { key: 'hero_image_url', label: 'Image' },
    { key: 'name', label: 'Destination' },
    { key: 'country', label: 'Country' },
    { key: 'website_url', label: 'DMO Website' },
    { key: 'is_featured', label: 'Featured' },
    { key: 'is_active', label: 'Status' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="Destination Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Destination Management" />

        <DataTable :data="destinations" :columns="columns" @edit-item="editDestination" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="createDestination">Create Destination</PrimaryButton>
            </template>

            <!-- Destination Thumbnail Image Slot -->
            <template #cell-hero_image_url="{ item }">
                <div
                    class="h-12 w-12 overflow-hidden rounded-lg bg-gray-100 border border-gray-200 dark:border-gray-700">
                    <img v-if="item.hero_image_url" :src="item.hero_image_url" class="h-full w-full object-cover" />
                    <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                        <span class="material-symbols-outlined text-xl">image</span>
                    </div>
                </div>
            </template>

            <!-- Country Name Slot -->
            <template #cell-country="{ item }">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ item.country ? item.country.name : 'N/A' }}
                </span>
            </template>

            <!-- External Website Link Slot -->
            <template #cell-website_url="{ item }">
                <a v-if="item.website_url" :href="item.website_url" target="_blank"
                    class="text-brand-500 hover:underline inline-flex items-center gap-1 text-xs font-semibold">
                    Visit Site
                    <span class="material-symbols-outlined text-xs">open_in_new</span>
                </a>
                <span v-else class="text-xs text-gray-400">None</span>
            </template>

            <!-- Featured Status Indicator -->
            <template #cell-is_featured="{ item }">
                <span v-if="item.is_featured"
                    class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20">
                    Featured
                </span>
                <span v-else class="text-xs text-gray-400">Standard</span>
            </template>

            <!-- Activation Status Indicator -->
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
