<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    properties: Array
})

// Initialize useResourceCRUD for deletes and standard event tracking
const crud = useResourceCRUD({
    resourceName: 'Property',
    entityName: 'property',
    routeNames: {
        store: 'app.admin.properties.store',
        update: 'app.admin.properties.update',
        destroy: 'app.admin.properties.destroy',
    },
    initialFormState: () => ({}),
    fieldsConfig: [],
})

const createProperty = () => {
    window.location.href = route('app.admin.properties.create')
}

const editProperty = (item) => {
    window.location.href = route('app.admin.properties.edit', item.id)
}

const columns = ref([
    { key: 'featured_image_url', label: 'Image' },
    { key: 'name', label: 'Property / Venue Name' },
    { key: 'category', label: 'Category' },
    { key: 'destination', label: 'Destination' },
    { key: 'rooms_capacity', label: 'Rooms / Capacity' },
    { key: 'is_featured', label: 'Featured' },
    { key: 'is_active', label: 'Status' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="Property Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Property Management" />

        <DataTable :data="properties" :columns="columns" @edit-item="editProperty" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="createProperty">Create Property</PrimaryButton>
            </template>

            <!-- Property Thumbnail Cover Slot -->
            <template #cell-featured_image_url="{ item }">
                <div
                    class="h-12 w-12 overflow-hidden rounded-lg bg-gray-100 border border-gray-200 dark:border-gray-700">
                    <img v-if="item.featured_image_url" :src="item.featured_image_url"
                        class="h-full w-full object-cover" />
                    <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                        <span class="material-symbols-outlined text-xl">image</span>
                    </div>
                </div>
            </template>

            <!-- Custom Category Name Cell -->
            <template #cell-category="{ item }">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    {{ item.portfolio_category ? item.portfolio_category.name : 'N/A' }}
                </span>
            </template>

            <!-- Custom Destination Name Cell -->
            <template #cell-destination="{ item }">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ item.destination ? item.destination.name : 'N/A' }} ({{ item.country ? item.country.name : '' }})
                </span>
            </template>

            <!-- Capacity Metrics Combined Display Cell -->
            <template #cell-rooms_capacity="{ item }">
                <div class="flex flex-col text-xs text-gray-500 dark:text-gray-400">
                    <span v-if="item.number_of_rooms">{{ item.number_of_rooms }} Rooms</span>
                    <span v-if="item.max_event_capacity">{{ item.max_event_capacity }} Pax Cap</span>
                    <span v-if="!item.number_of_rooms && !item.max_event_capacity">-</span>
                </div>
            </template>

            <!-- Featured Status Indicator -->
            <template #cell-is_featured="{ item }">
                <span v-if="item.is_featured"
                    class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20">
                    Featured
                </span>
                <span v-else class="text-xs text-gray-400">Standard</span>
            </template>

            <!-- Active Visibility Status Indicator -->
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
