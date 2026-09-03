<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { UserCircleIcon, ChevronDownIcon, LogoutIcon } from '@/icons'
import { ref, onMounted, onUnmounted, computed } from 'vue'

const dropdownOpen = ref(false)
const dropdownRef = ref(null)

const page = usePage()

// 1. Contextual Data from Shared Props
const user = computed(() => page.props.auth.user)

const userName = computed(() => user.value?.name || 'System User')
const userEmail = computed(() => user.value?.email || 'user@hmphospitality.com')
const userImage = computed(() => user.value?.avatar_url || null)

// 2. Generate Capitalized Initials (e.g. "David Thumbi" -> "DT")
const initials = computed(() => {
    if (!userName.value) return 'U'
    const parts = userName.value.trim().split(' ')
    if (parts.length > 1) {
        return (parts[0][0] + parts[1][0]).toUpperCase()
    }
    return parts[0][0].toUpperCase()
})

// 3. Generate a Stable, Deterministic Background Color based on the user's name
const avatarBgClass = computed(() => {
    const palettes = [
        'bg-red-500 text-white',
        'bg-blue-500 text-white',
        'bg-green-500 text-white',
        'bg-yellow-600 text-white',
        'bg-indigo-500 text-white',
        'bg-purple-500 text-white',
        'bg-pink-500 text-white',
        'bg-teal-500 text-white',
        'bg-amber-600 text-white'
    ]

    if (!userName.value) return palettes[0]

    // Simple hashing algorithm to ensure the same user always gets the same color
    let hash = 0
    for (let i = 0; i < userName.value.length; i++) {
        hash = userName.value.charCodeAt(i) + ((hash << 5) - hash)
    }
    const index = Math.abs(hash) % palettes.length
    return palettes[index]
})

/**
 * 4. DYNAMIC MENU ITEMS
 * Defensive checks to safely link to Laravel Breeze profile editor.
 */
const menuItems = computed(() => {
    const items = []

    try {
        // Safe check in case profile.edit has not been registered in web.php
        items.push({
            href: route('profile.edit'),
            icon: UserCircleIcon,
            text: 'My Profile'
        })
    } catch (e) {
        // Fallback to central dashboard
        items.push({
            href: route('app.dashboard'),
            icon: UserCircleIcon,
            text: 'Dashboard'
        })
    }

    return items
})

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value
}

const closeDropdown = () => {
    dropdownOpen.value = false
}

const signOut = () => {
    router.post(route('logout'), {}, {
        onFinish: () => closeDropdown()
    })
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
        <button class="group flex items-center text-gray-700 focus:outline-none dark:text-gray-400"
            @click.prevent="toggleDropdown">
            <!-- Profile Image / Initials Wrapper -->
            <span
                class="mr-3 flex h-11 w-11 overflow-hidden rounded-full border border-gray-200 dark:border-gray-800 shrink-0">
                <!-- Render image if avatar exists in R2 -->
                <img v-if="userImage" :src="userImage" :alt="userName" class="h-full w-full object-cover" />

                <!-- Render stable initials placeholder if avatar is null -->
                <div v-else
                    :class="[avatarBgClass, 'flex h-full w-full items-center justify-center text-sm font-bold tracking-wider uppercase select-none']">
                    {{ initials }}
                </div>
            </span>

            <!-- User Name (Visible on wider screens) -->
            <span class="text-theme-sm mr-1 hidden font-medium text-gray-800 lg:block dark:text-white/90">
                {{ userName }}
            </span>

            <!-- Dropdown Arrow -->
            <ChevronDownIcon :class="[
                'h-5 w-5 fill-current text-gray-700 transition-transform duration-200 dark:text-gray-400',
                { 'rotate-180': dropdownOpen },
            ]" />
        </button>

        <!-- Dropdown Menu -->
        <div v-if="dropdownOpen"
            class="shadow-theme-lg absolute top-full right-0 z-50 mt-4 flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-800">
            <!-- User Info Section -->
            <div class="border-b border-gray-200 pb-3 dark:border-gray-700">
                <span class="text-theme-sm block font-medium text-gray-700 dark:text-white/90">
                    {{ userName }}
                </span>
                <span class="text-theme-xs mt-0.5 block text-gray-500 dark:text-gray-400 font-mono">
                    {{ userEmail }}
                </span>
            </div>

            <!-- Menu Items -->
            <ul class="flex flex-col gap-1 border-b border-gray-200 pt-4 pb-3 dark:border-gray-700">
                <li v-for="item in menuItems" :key="item.href">
                    <Link :href="item.href" @click="closeDropdown"
                        class="group text-theme-sm flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                        <component :is="item.icon"
                            class="h-5 w-5 fill-current text-gray-500 transition-colors group-hover:text-gray-700 dark:group-hover:text-gray-300" />
                        {{ item.text }}
                    </Link>
                </li>
            </ul>

            <!-- Sign Out Control -->
            <button @click="signOut"
                class="group text-theme-sm mt-3 flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/10 w-full text-left">
                <LogoutIcon class="h-5 w-5 fill-current text-red-500 transition-colors" />
                Log out
            </button>
        </div>
    </div>
</template>