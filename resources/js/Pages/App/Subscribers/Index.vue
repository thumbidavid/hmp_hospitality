<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    subscribers: Array
})

// Configuration schema mapping for the inline popup modal form
const subscriberFormFields = [
    [
        { name: 'email', label: 'Subscriber Email Address', component: 'TextInput', props: { disabled: true } }
    ],
    [
        { name: 'is_active', label: 'Subscription Status', component: 'Checkbox', props: { label: 'Active Newsletter Subscription' } }
    ]
]

const crud = useResourceCRUD({
    resourceName: 'Subscriber',
    entityName: 'newsletter_subscriber',
    routeNames: {
        update: 'app.admin.subscribers.update',
        destroy: 'app.admin.subscribers.destroy',
    },
    initialFormState: () => ({
        id: null,
        email: '',
        is_active: true,
    }),
    fieldsConfig: subscriberFormFields,
})

// Triggers the modal to view the subscriber and toggle status manually
const viewSubscriber = (item) => {
    crud.openEditModal(item)
}

const columns = ref([
    { key: 'email', label: 'Subscriber Email' },
    { key: 'is_active', label: 'Subscription Status' },
    { key: 'subscribed_at', label: 'Subscribed At' },
    { key: 'unsubscribed_at', label: 'Unsubscribed At' },
    { key: 'action', label: 'Action' },
])

// Format timestamps into a readable string
const formatTime = (timeString) => {
    if (!timeString) return '-'
    const date = new Date(timeString)
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<template>

    <Head title="Newsletter Subscriber Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Newsletter Recipients" />

        <DataTable :data="subscribers" :columns="columns" @edit-item="viewSubscriber" @delete-item="crud.deleteItem">
            <!-- Informational header replacing create button -->
            <template #header-actions>
                <div class="text-xs text-gray-400 font-semibold italic">
                    Public footer signups write directly to this index.
                </div>
            </template>

            <!-- Subscriber Email -->
            <template #cell-email="{ item }">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    {{ item.email }}
                </span>
            </template>

            <!-- Subscription Status Badge -->
            <template #cell-is_active="{ item }">
                <span v-if="item.is_active"
                    class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-800 ring-1 ring-inset ring-green-600/20 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/20">
                    Active Subscriber
                </span>
                <span v-else
                    class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-800 ring-1 ring-inset ring-red-600/20 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20">
                    Unsubscribed
                </span>
            </template>

            <!-- Subscription Date -->
            <template #cell-subscribed_at="{ item }">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatTime(item.subscribed_at) }}
                </span>
            </template>

            <!-- Unsubscription Date -->
            <template #cell-unsubscribed_at="{ item }">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatTime(item.unsubscribed_at) }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>
