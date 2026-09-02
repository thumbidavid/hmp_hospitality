<script setup>
import { usePage } from '@inertiajs/vue3'
import AppHeader from './AppHeader.vue'
// Import the single unified sidebar
import AppSidebar from './AppSidebar.vue'
import Backdrop from '@/Components/Admin/layout/Backdrop.vue'
import ThemeProvider from '@/Components/Admin/layout/ThemeProvider.vue'
import { useSidebarProvider } from '@/composables/useSidebar'
import { computed } from 'vue'
import { useModalStore } from '@/Stores/modalStore'
import Modal from '@/Components/Admin/Modal.vue'
import Toast from 'primevue/toast'

const props = defineProps({
    contentOnly: {
        type: Boolean,
        default: false,
    },
})

const modalStore = useModalStore()
const sidebarContext = useSidebarProvider()
const isExpanded = sidebarContext.isExpanded
const isHovered = sidebarContext.isHovered

// Layout margin logic based on sidebar state
const layoutClasses = computed(() => {
    const isBig = isExpanded.value || isHovered.value
    return isBig ? 'lg:ml-[250px]' : 'lg:ml-[90px]'
})
</script>

<template>
    <ThemeProvider>
        <Toast position="bottom-right" />

        <div class="flex min-h-screen bg-gray-50 dark:bg-gray-900">
            <!-- Use the single AppSidebar component directly -->
            <AppSidebar v-if="!contentOnly" />

            <Backdrop />

            <div class="flex-1 transition-all duration-300 ease-in-out" :class="[contentOnly ? '' : layoutClasses]">
                <AppHeader v-if="!contentOnly" />

                <div class="mx-auto max-w-full dark:bg-gray-900" :class="{ 'p-4 md:p-6': !contentOnly }">
                    <slot v-if="!contentOnly" name="header" />
                    <slot></slot>
                </div>
            </div>
        </div>

        <!-- Modal Host -->
        <Modal v-if="modalStore.isOpen" @close="modalStore.close()" :max-width="modalStore.props.width">
            <template #body>
                <component :is="modalStore.component" v-bind="modalStore.props" />
            </template>
        </Modal>
    </ThemeProvider>
</template>
