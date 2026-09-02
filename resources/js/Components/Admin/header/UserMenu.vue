<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { UserCircleIcon, ChevronDownIcon, LogoutIcon, SettingsIcon, InfoCircleIcon } from '@/icons'
import { ref, onMounted, onUnmounted, computed } from 'vue'

const dropdownOpen = ref(false)
const dropdownRef = ref(null)

const page = usePage()

// 1. Contextual Data from Shared Props
const user = computed(() => page.props.auth.user)
const org = computed(() => page.props.auth.organization)
const role = computed(() => page.props.auth.role)

const userName = computed(() => user.value?.name || 'User')
const userEmail = computed(() => user.value?.email || 'user@example.com')
const userImage = computed(() => user.value?.profile_photo_url || '/images/admin/user/owner.jpg')

/**
 * 2. DYNAMIC MENU ITEMS
 * Based on the routes defined in your web.php
 */
const menuItems = computed(() => {
    if (!org.value) return []

    // CORPORATE ROUTES
    if (org.value.type === 'corporate') {
        return [
            { href: route('corporate.profile.edit'), icon: UserCircleIcon, text: 'Edit profile' },
            { href: route('corporate.dashboard'), icon: SettingsIcon, text: 'Account settings' },
            { href: route('corporate.dashboard'), icon: InfoCircleIcon, text: 'Support' },
        ]
    }

    // SUPPLIER ROUTES
    if (org.value.type === 'supplier') {
        return [
            // Note: Since supplier profile doesn't exist yet, we link to dashboard
            { href: route('supplier.dashboard'), icon: UserCircleIcon, text: 'Edit profile' },
            { href: route('supplier.dashboard'), icon: SettingsIcon, text: 'Account settings' },
            { href: route('supplier.dashboard'), icon: InfoCircleIcon, text: 'Support' },
        ]
    }

    // PLATFORM (SYSTEM ADMIN) ROUTES
    if (role.value === 'system_admin') {
        return [
            { href: route('platform.dashboard'), icon: UserCircleIcon, text: 'Admin Profile' },
            { href: route('platform.dashboard'), icon: SettingsIcon, text: 'System Settings' },
            { href: route('platform.dashboard'), icon: InfoCircleIcon, text: 'Help Desk' },
        ]
    }

    return []
})

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value
}

const closeDropdown = () => {
    dropdownOpen.value = false
}

const signOut = () => {
    // Standard Breeze/SaaS Logout
    router.post(route('logout'))
    closeDropdown()
}

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        closeDropdown()
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <!-- Dropdown Trigger Button -->
        <button
            class="group flex items-center text-gray-700 focus:outline-none dark:text-gray-400"
            @click.prevent="toggleDropdown"
        >
            <!-- Profile Image -->
            <span
                class="mr-3 h-11 w-11 overflow-hidden rounded-full border border-gray-200 dark:border-gray-800"
            >
                <img :src="userImage" :alt="userName" class="h-full w-full object-cover" />
            </span>

            <!-- User Name (Visible on wider screens) -->
            <span
                class="text-theme-sm mr-1 hidden font-medium text-gray-800 lg:block dark:text-white/90"
            >
                {{ userName }}
            </span>

            <!-- Dropdown Arrow -->
            <ChevronDownIcon
                :class="[
                    'h-5 w-5 fill-current text-gray-700 transition-transform duration-200 dark:text-gray-400',
                    { 'rotate-180': dropdownOpen },
                ]"
            />
        </button>

        <!-- Dropdown Menu -->
        <div
            v-if="dropdownOpen"
            class="shadow-theme-lg absolute top-full right-0 z-50 mt-4 flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-800"
        >
            <!-- User Info Section -->
            <div class="border-b border-gray-200 pb-3 dark:border-gray-700">
                <span class="text-theme-sm block font-medium text-gray-700 dark:text-white/90">
                    {{ userName }}
                </span>
                <span class="text-theme-xs mt-0.5 block text-gray-500 dark:text-gray-400">
                    {{ userEmail }}
                </span>
            </div>

            <!-- Menu Items -->
            <ul class="flex flex-col gap-1 border-b border-gray-200 pt-4 pb-3 dark:border-gray-700">
                <li v-for="item in menuItems" :key="item.href">
                    <Link
                        :href="item.href"
                        @click="closeDropdown"
                        class="group text-theme-sm flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
                    >
                        <component
                            :is="item.icon"
                            class="h-5 w-5 fill-current text-gray-500 transition-colors group-hover:text-gray-700 dark:group-hover:text-gray-300"
                        />
                        {{ item.text }}
                    </Link>
                </li>
            </ul>

            <!-- Sign Out Link -->
            <button
                @click="signOut"
                class="group text-theme-sm mt-3 flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/10"
            >
                <LogoutIcon class="h-5 w-5 fill-current text-red-500 transition-colors" />
                Log out
            </button>
        </div>
    </div>
</template>
