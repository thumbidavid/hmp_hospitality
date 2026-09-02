<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({ users: Array, roles: Array })

const userFormFields = [
    [{ name: 'first_name', label: 'First Name', component: 'TextInput' },
    { name: 'last_name', label: 'Last Name', component: 'TextInput' }],
    [{ name: 'email', label: 'Email', component: 'TextInput' }],
    [{ name: 'roles', label: 'Roles', component: 'SelectInput', props: { options: props.roles.map(r => ({ value: r.name, label: r.name })), placeholder: 'Select a role' } }]
]

const crud = useResourceCRUD({
    resourceName: 'User',
    entityName: 'user',
    routeNames: {
        update: 'app.admin.users.update',
        destroy: 'app.admin.users.destroy',
    },
    initialFormState: () => ({ id: null, first_name: '', last_name: '', email: '', roles: [] }),
    fieldsConfig: userFormFields,
})

// Transform user roles for the form
const openEdit = (user) => {
    crud.openEditModal({
        ...user,
        roles: user.roles.length > 0 ? user.roles[0].name : ''
    })
}

const columns = ref([
    { key: 'first_name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'roles', label: 'Roles' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="User Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="User Management" />
        <DataTable :data="users" :columns="columns" @edit-item="openEdit" @delete-item="crud.deleteItem">
            <template #cell-roles="{ item }">
                <span v-for="role in item.roles" :key="role.id"
                    class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs mr-1">
                    {{ role.name }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>
