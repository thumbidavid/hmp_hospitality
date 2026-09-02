<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useSidebar } from '@/composables/useSidebar'
import {
    GridIcon,
    CalenderIcon,
    UserCircleIcon,
    ChatIcon,
    MailIcon,
    DocsIcon,
    PieChartIcon,
    ChevronDownIcon,
    HorizontalDots,
    PageIcon,
    TableIcon,
    ListIcon,
    PlugInIcon,
    UserGroupIcon,
    PlusIcon,
    HomeIcon,
} from '../icons'

const iconMap = {
    GridIcon,
    UserCircleIcon,
    ChatIcon,
    CalenderIcon,
    MailIcon,
    DocsIcon,
    TableIcon,
    PageIcon,
    ListIcon,
    PlugInIcon,
    UserGroupIcon,
    PieChartIcon,
    PlusIcon,
    HomeIcon,
}

const { isExpanded, isMobileOpen, isHovered, toggleMobileSidebar } = useSidebar()
const openSubmenu = ref(null)

const page = usePage()
const user = computed(() => page.props.auth.user)

// Role-based logic modified to match the single-column 'role' ENUM: 'admin' or 'staff'
const hasRole = (role) => user.value?.role === role

/**
 * MENU GROUPS
 * Organizes operations into active phases.
 */
const menuGroups = computed(() => {
    if (!user.value) return []

    const groups = [
        {
            title: 'MAIN MENU',
            items: [
                { name: 'Dashboard', route: 'app.dashboard', icon: 'PieChartIcon' },
                { name: 'RFP Inbox', route: 'app.admin.rfps.index', icon: 'DocsIcon' },
            ],
        },
        {
            title: 'PORTFOLIO',
            items: [
                { name: 'Properties', route: 'app.admin.properties.index', icon: 'HomeIcon' },
                { name: 'Destinations', route: 'app.admin.destinations.index', icon: 'GridIcon' },
            ]
        },
        {
            title: 'MARKETING & COMMUNICATIONS',
            items: [
                { name: 'Blog Posts', route: 'app.admin.blog-posts.index', icon: 'PageIcon' },
                { name: 'Partners', route: 'app.admin.partners.index', icon: 'PlugInIcon' },
                { name: 'Contact Inquiries', route: 'app.admin.contacts.index', icon: 'ChatIcon' },
                { name: 'Newsletter List', route: 'app.admin.subscribers.index', icon: 'MailIcon' },
            ]
        }
    ]

    // Foundational taxonomies grouped into a dropdown to keep the interface orderly
    groups.push({
        title: 'DATA SETTINGS',
        items: [
            {
                name: 'Taxonomies',
                icon: 'ListIcon',
                subItems: [
                    { name: 'Countries', route: 'app.admin.countries.index' },
                    { name: 'Categories', route: 'app.admin.portfolio-categories.index' },
                    { name: 'Best For Tags', route: 'app.admin.settings.index' },
                    { name: 'Amenities', route: 'app.admin.amenities.index' },
                    { name: 'Buyer Types', route: 'app.admin.buyer-types.index' },
                    { name: 'Agency Services', route: 'app.admin.agency-services.index' },
                    { name: 'Blog Categories', route: 'app.admin.blog-categories.index' },
                ]
            }
        ]
    })

    // Staff / User Management (Admin Only)
    if (hasRole('admin')) {
        groups.push({
            title: 'ADMINISTRATION',
            items: [
                { name: 'Staff Directory', route: 'app.admin.users.index', icon: 'UserGroupIcon' },
            ]
        })
    }

    return groups
})

const isActive = (routeName) => route().current(routeName)

const toggleSubmenu = (groupIndex, itemIndex) => {
    const key = `${groupIndex}-${itemIndex}`
    openSubmenu.value = openSubmenu.value === key ? null : key
}

const isSubmenuOpen = (groupIndex, itemIndex) => {
    const key = `${groupIndex}-${itemIndex}`
    const item = menuGroups.value[groupIndex].items[itemIndex]
    return (
        openSubmenu.value === key ||
        (item.subItems && item.subItems.some((sub) => isActive(sub.route)))
    )
}

const startTransition = (el) => {
    el.style.height = 'auto'
    const height = el.scrollHeight
    el.style.height = '0px'
    el.offsetHeight
    el.style.height = height + 'px'
}

const endTransition = (el) => {
    el.style.height = ''
}
</script>

<template>
    <aside :class="[
        'fixed top-0 left-0 z-99999 mt-16 flex h-screen flex-col border-r border-gray-200 bg-white px-5 text-gray-900 transition-all duration-300 ease-in-out lg:mt-0 dark:border-gray-800 dark:bg-gray-900',
        {
            'lg:w-[250px]': isExpanded || isMobileOpen || isHovered,
            'lg:w-[90px]': !isExpanded && !isHovered,
            'w-[250px] translate-x-0': isMobileOpen,
            '-translate-x-full': !isMobileOpen,
            'lg:translate-x-0': true,
        },
    ]" @mouseenter="!isExpanded && (isHovered = true)" @mouseleave="isHovered = false">
        <!-- Logo -->
        <div :class="[
            'hidden py-4 lg:flex',
            !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
        ]">
            <Link :href="route('app.dashboard')" @click="isMobileOpen && toggleMobileSidebar()">
                <img v-if="isExpanded || isHovered || isMobileOpen" src="/logo.png" alt="Logo" width="64" height="33"
                    class="h-10 w-auto" />
                <img v-else src="/favicon.png" alt="Logo" width="32" height="32" class="h-10 w-10 object-contain" />
            </Link>
        </div>

        <!-- Navigation -->
        <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
            <nav class="mt-4 mb-6 lg:mt-0">
                <div class="flex flex-col gap-4">
                    <div v-for="(group, gIdx) in menuGroups" :key="gIdx">
                        <!-- Group Title -->
                        <h2 :class="[
                            'mb-4 flex text-xs leading-[20px] text-gray-400 uppercase',
                            !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
                        ]">
                            <template v-if="isExpanded || isHovered || isMobileOpen">
                                {{ group.title }}
                            </template>
                            <HorizontalDots v-else />
                        </h2>

                        <ul class="flex flex-col gap-2">
                            <li v-for="(item, iIdx) in group.items" :key="item.name">
                                <!-- Submenu -->
                                <div v-if="item.subItems">
                                    <button @click="toggleSubmenu(gIdx, iIdx)" :class="[
                                        'menu-item group flex w-full items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition-colors',
                                        isSubmenuOpen(gIdx, iIdx)
                                            ? 'text-brand-500 bg-gray-100 dark:bg-white/[0.03]'
                                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/[0.03]',
                                        !isExpanded && !isHovered
                                            ? 'lg:justify-center'
                                            : 'lg:justify-start',
                                    ]">
                                        <span class="h-5 w-5">
                                            <component :is="iconMap[item.icon]" class="h-full w-full" />
                                        </span>
                                        <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                                            {{ item.name }}
                                        </span>
                                        <ChevronDownIcon v-if="isExpanded || isHovered || isMobileOpen" :class="[
                                            'ml-auto h-5 w-5 transition-transform duration-200',
                                            { 'text-brand-500 rotate-180': isSubmenuOpen(gIdx, iIdx) },
                                        ]" />
                                    </button>

                                    <transition @enter="startTransition" @after-enter="endTransition"
                                        @before-leave="startTransition" @after-leave="endTransition">
                                        <div
                                            v-show="isSubmenuOpen(gIdx, iIdx) && (isExpanded || isHovered || isMobileOpen)">
                                            <ul class="mt-2 ml-9 space-y-1">
                                                <li v-for="sub in item.subItems" :key="sub.route">
                                                    <Link :href="route(sub.route)" :class="[
                                                        'menu-dropdown-item flex items-center rounded-md px-3 py-1 text-sm',
                                                        isActive(sub.route)
                                                            ? 'text-brand-500 font-medium'
                                                            : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200',
                                                    ]" @click="isMobileOpen && toggleMobileSidebar()">
                                                        {{ sub.name }}
                                                    </Link>
                                                </li>
                                            </ul>
                                        </div>
                                    </transition>
                                </div>

                                <!-- Normal Link -->
                                <Link v-else :href="route(item.route)" :class="[
                                    'menu-item group flex w-full items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition-colors',
                                    isActive(item.route)
                                        ? 'text-brand-500 bg-gray-100 dark:bg-white/[0.03]'
                                        : 'text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/[0.03]',
                                    !isExpanded && !isHovered
                                        ? 'lg:justify-center'
                                        : 'lg:justify-start',
                                ]" @click="isMobileOpen && toggleMobileSidebar()">
                                    <span class="h-5 w-5">
                                        <component :is="iconMap[item.icon]" class="h-full w-full" />
                                    </span>
                                    <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                                        {{ item.name }}
                                    </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </aside>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
