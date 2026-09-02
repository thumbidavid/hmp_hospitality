<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import DataTable from '@/Components/Admin/DataTable.vue'
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue'
import { useResourceCRUD } from '@/composables/useResourceCRUD.js'

const props = defineProps({
    categories: Array
})

// Field configuration matching the exact 2D structure of useResourceCRUD
const categoryFormFields = [
    [
        { name: 'name', label: 'Category Name', component: 'TextInput', props: { placeholder: 'e.g. Hotels, Resorts & Lodges' } },
        { name: 'singular_label', label: 'Singular Label', component: 'TextInput', props: { placeholder: 'e.g. Property' } }
    ],
    [
        { name: 'slug', label: 'Slug (Auto-generated if blank)', component: 'TextInput', props: { placeholder: 'e.g. hotels-resorts-lodges' } },
        { name: 'sort_order', label: 'Sort Order', component: 'TextInput', props: { type: 'number', placeholder: '0' } }
    ]
]

const crud = useResourceCRUD({
    resourceName: 'Category',
    entityName: 'portfolio_category',
    routeNames: {
        store: 'app.admin.portfolio-categories.store',
        update: 'app.admin.portfolio-categories.update',
        destroy: 'app.admin.portfolio-categories.destroy',
    },
    initialFormState: () => ({
        id: null,
        name: '',
        slug: '',
        singular_label: '',
        sort_order: 0
    }),
    fieldsConfig: categoryFormFields,
})

// Inline modal open trigger
const editCategory = (item) => {
    crud.openEditModal(item)
}

const columns = ref([
    { key: 'name', label: 'Category Name' },
    { key: 'singular_label', label: 'Singular Label' },
    { key: 'slug', label: 'Slug' },
    { key: 'sort_order', label: 'Sort Order' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="Portfolio Category Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Portfolio Category Management" />

        <DataTable :data="categories" :columns="columns" @edit-item="editCategory" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="crud.openCreateModal()">Create Category</PrimaryButton>
            </template>

            <template #cell-singular_label="{ item }">
                <span
                    class="inline-flex items-center rounded-md bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                    {{ item.singular_label }}
                </span>
            </template>

            <template #cell-slug="{ item }">
                <span class="font-mono text-xs text-gray-500 dark:text-gray-400">
                    {{ item.slug }}
                </span>
            </template>

            <template #cell-sort_order="{ item }">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    {{ item.sort_order }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>
