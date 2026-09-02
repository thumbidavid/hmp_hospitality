<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    submissions: Array
})

// Configure form fields to show message details as read-only, exposing only the status dropdown
const contactFormFields = [
    [
        { name: 'name', label: 'Sender Name', component: 'TextInput', props: { disabled: true } },
        { name: 'email', label: 'Sender Email', component: 'TextInput', props: { disabled: true } }
    ],
    [
        { name: 'company', label: 'Company / Org', component: 'TextInput', props: { disabled: true } },
        { name: 'phone', label: 'Phone Number', component: 'TextInput', props: { disabled: true } }
    ],
    [
        { name: 'subject', label: 'Inquiry Subject', component: 'TextInput', props: { disabled: true } },
        {
            name: 'status',
            label: 'Lead Status',
            component: 'SelectInput',
            props: {
                placeholder: 'Change Status',
                options: [
                    { value: 'new', label: 'New / Unread' },
                    { value: 'read', label: 'Opened / Read' },
                    { value: 'responded', label: 'Responded / Closed' },
                    { value: 'archived', label: 'Archived' }
                ]
            }
        }
    ],
    [
        { name: 'message', label: 'Full Message Body', component: 'TextArea', props: { disabled: true, rows: 5 } }
    ]
]

const crud = useResourceCRUD({
    resourceName: 'Message',
    entityName: 'contact_submission',
    routeNames: {
        update: 'app.admin.contacts.update',
        destroy: 'app.admin.contacts.destroy',
    },
    initialFormState: () => ({
        id: null,
        name: '',
        email: '',
        phone: '',
        company: '',
        subject: '',
        message: '',
        status: 'new'
    }),
    fieldsConfig: contactFormFields,
})

// Triggers the modal to view the static message content and update its status
const viewMessage = (item) => {
    crud.openEditModal(item)
}

const columns = ref([
    { key: 'name', label: 'Sender' },
    { key: 'email', label: 'Email Address' },
    { key: 'subject', label: 'Subject' },
    { key: 'status', label: 'Status' },
    { key: 'created_at', label: 'Received At' },
    { key: 'action', label: 'Action' },
])

// Format timestamps into a readable relative string
const formatTime = (timeString) => {
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

    <Head title="Contact Inbox Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Inquiry Inbox" />

        <DataTable :data="submissions" :columns="columns" @edit-item="viewMessage" @delete-item="crud.deleteItem">
            <!-- No Create button is rendered as admins do not manually create incoming inquiries -->
            <template #header-actions>
                <div class="text-xs text-gray-400 font-semibold italic">
                    Public submissions write directly to this index.
                </div>
            </template>

            <!-- Sender Name Cell -->
            <template #cell-name="{ item }">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ item.name }}
                    </span>
                    <span v-if="item.company" class="text-xs text-gray-400">
                        {{ item.company }}
                    </span>
                </div>
            </template>

            <!-- Email Display -->
            <template #cell-email="{ item }">
                <span class="text-xs font-mono text-gray-500 dark:text-gray-400">
                    {{ item.email }}
                </span>
            </template>

            <!-- Subject Line -->
            <template #cell-subject="{ item }">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ item.subject || 'General Inquiry' }}
                </span>
            </template>

            <!-- Dynamic Status Badges -->
            <template #cell-status="{ item }">
                <span v-if="item.status === 'new'"
                    class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-800 ring-1 ring-inset ring-red-600/20 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20">
                    New / Unread
                </span>
                <span v-else-if="item.status === 'read'"
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-800 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">
                    Read
                </span>
                <span v-else-if="item.status === 'responded'"
                    class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-800 ring-1 ring-inset ring-green-600/20 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/20">
                    Responded
                </span>
                <span v-else
                    class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                    Archived
                </span>
            </template>

            <!-- Human Readable Received Date -->
            <template #cell-created_at="{ item }">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatTime(item.created_at) }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>
