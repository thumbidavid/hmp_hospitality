<script setup>
import { ref, computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    users: Array,
    roles: Array
})

const page = usePage()
const currentUser = computed(() => page.props.auth.user)

// FIXED: Defined as a clean, static 2D array to avoid circular dependency runtime errors
const userFormFields = [
    [
        { name: 'name', label: 'Full Name', component: 'TextInput', props: { placeholder: 'e.g. David Thumbi' } },
        { name: 'email', label: 'Email Address', component: 'TextInput', props: { placeholder: 'e.g. name@example.com', type: 'email' } }
    ],
    [
        {
            name: 'password',
            label: 'Password',
            component: 'TextInput',
            props: {
                type: 'password',
                placeholder: 'Password (leave blank to keep current)' // Descriptively covers both states safely
            }
        },
        {
            name: 'roles',
            label: 'System Access Role',
            component: 'SelectInput',
            props: {
                placeholder: 'Select System Access',
                options: props.roles.map(r => ({ value: r.name, label: r.name.charAt(0).toUpperCase() + r.name.slice(1) }))
            }
        }
    ],
    [
        { name: 'avatar', label: 'Profile Avatar', component: 'FileUploader', props: { options: { maxFiles: 1 } } },
        { name: 'is_active', label: 'Account Status', component: 'Checkbox', props: { label: 'Active Staff Member' } }
    ]
]

const crud = useResourceCRUD({
    resourceName: 'User',
    entityName: 'user',
    routeNames: {
        store: 'app.admin.users.store',
        update: 'app.admin.users.update',
        destroy: 'app.admin.users.destroy',
    },
    initialFormState: () => ({
        id: null,
        name: '',
        email: '',
        password: '',
        roles: '',
        avatar: null,
        is_active: true
    }),
    fieldsConfig: userFormFields, // Safely passed
})

// Prepare the user model for editing by converting Spatie relation arrays to form values
const editUser = (item) => {
    const itemCopy = JSON.parse(JSON.stringify(item))

    // Convert Spatie roles relation array e.g., [{ name: 'admin' }] to a simple string
    itemCopy.roles = item.roles && item.roles.length > 0 ? item.roles[0].name : ''

    // Clear temporary avatar upload key so uploader starts fresh
    itemCopy.avatar = null

    crud.openEditModal(itemCopy)
}

const columns = ref([
    { key: 'avatar_url', label: 'Avatar' },
    { key: 'name', label: 'Staff Member Name' },
    { key: 'email', label: 'Email Address' },
    { key: 'role', label: 'Access Level' },
    { key: 'is_active', label: 'Status' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="Staff Directory Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Staff Directory" />

        <DataTable :data="users" :columns="columns" @edit-item="editUser" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="crud.openCreateModal()">Register Staff</PrimaryButton>
            </template>

            <!-- Custom Avatar Cell -->
            <template #cell-avatar_url="{ item }">
                <div
                    class="h-10 w-10 overflow-hidden rounded-full border border-gray-200 bg-gray-100 dark:border-gray-700">
                    <img v-if="item.avatar_url" :src="item.avatar_url" class="h-full w-full object-cover" />
                    <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                        <span class="material-symbols-outlined text-lg">person</span>
                    </div>
                </div>
            </template>

            <!-- Custom Name Cell with logged-in user indicator -->
            <template #cell-name="{ item }">
                <div class="flex items-center gap-1.5">
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ item.name }}
                    </span>
                    <span v-if="item.id === currentUser.id"
                        class="inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-theme-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/10">
                        You
                    </span>
                </div>
            </template>

            <!-- Custom Role Badge -->
            <template #cell-role="{ item }">
                <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-medium capitalize" :class="{
                    'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20':
                        item.roles && item.roles.length > 0 && item.roles[0].name === 'admin',
                    'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/10 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20':
                        item.roles && item.roles.length > 0 && item.roles[0].name === 'staff',
                }">
                    {{ item.roles && item.roles.length > 0 ? item.roles[0].name : 'Staff' }}
                </span>
            </template>

            <!-- Active Visibility Toggle -->
            <template #cell-is_active="{ item }">
                <span :class="item.is_active
                    ? 'text-green-600 dark:text-green-400 font-semibold text-xs'
                    : 'text-gray-400 font-semibold text-xs'">
                    {{ item.is_active ? 'Active' : 'Suspended' }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>