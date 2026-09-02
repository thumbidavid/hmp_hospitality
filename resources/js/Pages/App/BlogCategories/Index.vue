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
const blogCategoryFormFields = [
    [
        { name: 'name', label: 'Blog Category Name', component: 'TextInput', props: { placeholder: 'e.g. Property Stories' } }
    ],
    [
        { name: 'slug', label: 'Slug (Auto-generated if blank)', component: 'TextInput', props: { placeholder: 'e.g. property-stories' } }
    ]
]

const crud = useResourceCRUD({
    resourceName: 'Blog Category',
    entityName: 'blog_category',
    routeNames: {
        store: 'app.admin.blog-categories.store',
        update: 'app.admin.blog-categories.update',
        destroy: 'app.admin.blog-categories.destroy',
    },
    initialFormState: () => ({
        id: null,
        name: '',
        slug: ''
    }),
    fieldsConfig: blogCategoryFormFields,
})

// Inline modal open trigger
const editBlogCategory = (item) => {
    crud.openEditModal(item)
}

const columns = ref([
    { key: 'name', label: 'Blog Category Name' },
    { key: 'slug', label: 'Slug' },
    { key: 'action', label: 'Action' },
])
</script>

<template>

    <Head title="Blog Category Management" />
    <AuthenticatedLayout>
        <PageBreadcrumb pageTitle="Blog Category Management" />

        <DataTable :data="categories" :columns="columns" @edit-item="editBlogCategory" @delete-item="crud.deleteItem">
            <template #header-actions>
                <PrimaryButton @click="crud.openCreateModal()">Create Category</PrimaryButton>
            </template>

            <template #cell-name="{ item }">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ item.name }}
                </span>
            </template>

            <template #cell-slug="{ item }">
                <span class="font-mono text-xs text-gray-500 dark:text-gray-400">
                    {{ item.slug }}
                </span>
            </template>
        </DataTable>
    </AuthenticatedLayout>
</template>
