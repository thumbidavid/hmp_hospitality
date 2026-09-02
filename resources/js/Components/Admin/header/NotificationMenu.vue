<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage()

// 1. Initialize Audio with pre-loading
const notificationSound = new Audio('/assets/sounds/notification.mp3')
notificationSound.load() // Pre-fetch the file

const dropdownOpen = ref(false)
const dropdownRef = ref(null)

// 1. Reactive Notifications from Global Props
const notifications = computed(() => page.props.auth.notifications?.latest || [])
const unreadCount = computed(() => page.props.auth.notifications?.unread_count || 0)
const playNotificationSound = () => {
    // Stop any existing playback and reset
    notificationSound.pause()
    notificationSound.currentTime = 0

    // 🚀 Attempt to play and catch errors
    notificationSound
        .play()
        .then(() => {
            console.log('🔊 Audio played successfully.')
        })
        .catch((error) => {
            // This will tell us if the browser blocked it
            console.warn('🔇 Audio blocked by browser policy. Interaction required.', error)
        })
}
const unseenCount = computed(() => page.props.auth.notifications?.unseen_count || 0)

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value

    // When opening, mark as 'seen' in the backend
    if (dropdownOpen.value && unseenCount.value > 0) {
        markAllAsSeen()
    }
}

const markAllAsSeen = async () => {
    try {
        await axios.post(route('notifications.mark-seen'))
        // Optional: Update local state immediately if not using a full reload
    } catch (e) {
        console.error('Failed to mark seen', e)
    }
}

const handleItemClick = async (notification) => {
    try {
        // 1. Mark as read in DB
        await axios.post(route('notifications.mark-read', notification.id))

        // 2. Redirect to the target URL
        router.visit(notification.url)

        closeDropdown()
    } catch (e) {
        console.error('Failed to mark read', e)
    }
}

const closeDropdown = () => {
    dropdownOpen.value = false
}

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        closeDropdown()
    }
}

// 🌐 REAL-TIME LISTENER (WebSockets)
onMounted(() => {
    document.addEventListener('click', handleClickOutside)

    if (window.Echo && page.props.auth.user) {
        // 🛡️ Guard against duplicate listeners
        window.Echo.leave(`App.Models.User.${page.props.auth.user.id}`)

        window.Echo.private(`App.Models.User.${page.props.auth.user.id}`).notification(
            (notification) => {
                console.log('🔔 WebSocket Notification Received:', notification)

                // 1. Play sound BEFORE the reload to ensure it triggers
                playNotificationSound()

                // 2. Refresh the UI data
                router.reload({
                    only: ['auth'],
                    onSuccess: () => {
                        console.log('✅ UI updated with new notification count')
                    },
                })
            }
        )
    }
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    if (page.props.auth.user) {
        window.Echo.leave(`App.Models.User.${page.props.auth.user.id}`)
    }
})

const getPriorityClass = (priority) => {
    if (priority === 'high') return 'bg-red-500'
    if (priority === 'medium') return 'bg-blue-500'
    return 'bg-gray-400'
}
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <!-- Trigger Button -->
        <button
            class="relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800"
            @click="toggleDropdown"
        >
            <!-- Ping Animation (Only show if there are unseen notifications) -->
            <span
                v-if="unseenCount > 0"
                class="absolute top-0.5 right-0 z-1 h-2.5 w-2.5 rounded-full border-2 border-white bg-orange-400 dark:border-gray-900"
            >
                <span
                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"
                ></span>
            </span>

            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                    fill=""
                />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div
            v-if="dropdownOpen"
            class="shadow-theme-lg absolute top-full right-0 z-50 mt-4 flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 sm:w-[361px] dark:border-gray-800 dark:bg-gray-800"
        >
            <div
                class="mb-3 flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-700"
            >
                <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Notifications ({{ unreadCount }})
                </h5>
            </div>

            <!-- Notification List -->
            <ul class="custom-scrollbar flex h-auto flex-col overflow-y-auto">
                <li v-for="n in notifications" :key="n.id" @click="handleItemClick(n)">
                    <div
                        :class="[n.read_at ? 'opacity-60' : 'bg-blue-50/30 dark:bg-blue-900/5']"
                        class="flex cursor-pointer gap-3 rounded-lg border-b border-gray-50 p-3 transition-all hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-white/5"
                    >
                        <!-- Indicator Icon based on priority -->
                        <span
                            class="relative block flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-900"
                        >
                            <div
                                :class="getPriorityClass(n.priority)"
                                class="h-2 w-2 rounded-full"
                            ></div>
                        </span>

                        <span class="block overflow-hidden">
                            <span
                                class="block truncate text-sm font-bold text-gray-900 dark:text-white"
                                >{{ n.title }}</span
                            >
                            <span
                                class="mt-0.5 line-clamp-2 block text-xs text-gray-500 dark:text-gray-400"
                                >{{ n.message }}</span
                            >
                            <span
                                class="mt-2 flex items-center gap-2 text-[10px] font-medium tracking-wider text-gray-400 uppercase"
                            >
                                <span>{{ n.type.replace('.', ' ') }}</span>
                                <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                                <span>{{ n.created_at }}</span>
                            </span>
                        </span>
                    </div>
                </li>

                <li v-if="notifications.length === 0" class="py-20 text-center">
                    <p class="text-sm text-gray-400">All caught up! No new alerts.</p>
                </li>
            </ul>

            <button
                class="mt-3 flex justify-center rounded-lg border border-gray-300 p-3 text-sm font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800"
            >
                View All History
            </button>
        </div>
    </div>
</template>
