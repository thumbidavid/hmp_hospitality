<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    rfps: Array
})

// Initialize useResourceCRUD for deletes and standard event tracking
const crud = useResourceCRUD({
    resourceName: 'RFP Submission',
    entityName: 'rfp',
    routeNames: {
        update: 'app.admin.rfps.update',
        destroy: 'app.admin.rfps.destroy',
    },
    initialFormState: () => ({}),
    fieldsConfig: [],
})

// View Details redirects to the dedicated pipeline page
const viewRfp = (item) => {
    window.location.href = route('app.admin.rfps.show', item.id)
}

const columns = ref([
    { key: 'reference_number', label: 'Reference' },
    { key: 'buyer', label: 'Buyer' },
    { key: 'requirement_type', label: 'Inquiry Profile' },
    { key: 'programme_name', label: 'Programme' },
    { key: 'budget_amount', label: 'Budget' },
    { key: 'status', label: 'Pipeline Status' },
    { key: 'created_at', label: 'Submitted At' },
    { key: 'action', label: 'Action' },
])

const formatTime = (timeString) => {
    const date = new Date(timeString)
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}

const formatCurrency = (amount, currency) => {
    if (!amount) return 'TBD'
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency || 'USD' }).format(amount)
}

const typeMap = {
    business_travel: 'Business Travel',
    group_accommodation: 'Group Accommodation',
    conference_or_meeting: 'Conference & Meeting',
    incentive_programme: 'Incentive Programme',
    association_event: 'Association Event',
    government_ngo_programme: 'Gov / NGO Programme',
    leisure_group: 'Leisure Group',
    long_stay_accommodation: 'Long-Stay',
    venue_only_event: 'Venue-Only Event',
    destination_enquiry: 'Destination Inquiry'
}
</script>

<template>

    <Head title="RFP Lead Center" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="RFP Lead Pipeline" />

        <DataTable :data="rfps" :columns="columns" @edit-item="viewRfp" @delete-item="crud.deleteItem">
            <template #header-actions>
                <div class="text-xs text-gray-400 font-semibold italic">
                    All client-shortlisted leads funnel into this manager.
                </div>
            </template>

            <!-- Custom Ref display -->
            <template #cell-reference_number="{ item }">
                <span class="font-mono text-xs font-bold text-brand-500">
                    {{ item.reference_number }}
                </span>
            </template>

            <!-- Custom Buyer display -->
            <template #cell-buyer="{ item }">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ item.full_name }}
                    </span>
                    <span v-if="item.company_name" class="text-xs text-gray-400">
                        {{ item.company_name }}
                    </span>
                </div>
            </template>

            <!-- Custom Requirement Type display -->
            <template #cell-requirement_type="{ item }">
                <span class="text-xs text-gray-600 dark:text-gray-400">
                    {{ typeMap[item.requirement_type] || item.requirement_type }}
                </span>
            </template>

            <!-- Custom Programme Name display -->
            <template #cell-programme_name="{ item }">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ item.programme_name || 'General Brief' }}
                </span>
            </template>

            <!-- Custom Budget display -->
            <template #cell-budget_amount="{ item }">
                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                    {{ formatCurrency(item.budget_amount, item.currency) }}
                </span>
            </template>

            <!-- Custom Pipeline Status Badges -->
            <template #cell-status="{ item }">
                <span v-if="item.status === 'new'"
                    class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-800 ring-1 ring-inset ring-red-600/20 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20">
                    New Lead
                </span>
                <span v-else-if="item.status === 'contacted'"
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-800 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">
                    Contacted
                </span>
                <span v-else-if="item.status === 'quoted'"
                    class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20">
                    Quoted
                </span>
                <span v-else-if="item.status === 'won'"
                    class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-800 ring-1 ring-inset ring-green-600/20 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/20">
                    Won
                </span>
                <span v-else
                    class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                    Lost
                </span>
            </template>

            <!-- Custom Date display -->
            <template #cell-created_at="{ item }">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatTime(item.created_at) }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>
