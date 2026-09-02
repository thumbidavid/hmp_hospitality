<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import debounce from 'lodash/debounce'

const searchInput = ref(null)
const query = ref('')
const results = ref([])
const isDropdownOpen = ref(false)

// --- KEYBOARD SHORTCUT (⌘ K) ---
const handleKeyDown = (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault()
        searchInput.value.focus()
    }
}

onMounted(() => window.addEventListener('keydown', handleKeyDown))
onUnmounted(() => window.removeEventListener('keydown', handleKeyDown))

// --- SEARCH LOGIC ---
const performSearch = debounce(async (val) => {
    if (val.length < 2) {
        results.value = []
        return
    }
    const response = await axios.get(route('admin.global-search', { q: val }))
    results.value = response.data
    isDropdownOpen.value = true
}, 300)

watch(query, (newVal) => performSearch(newVal))

const navigateTo = (url) => {
    isDropdownOpen.value = false
    query.value = ''
    router.visit(url)
}
</script>

<template>
    <div class="group relative hidden lg:block">
        <form @submit.prevent>
            <div class="relative">
                <!-- SEARCH ICON -->
                <button type="button" class="absolute top-1/2 left-4 -translate-y-1/2">
                    <svg
                        class="fill-gray-500 dark:fill-gray-400"
                        width="20"
                        height="20"
                        viewBox="0 0 20 20"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                            fill=""
                        />
                    </svg>
                </button>

                <!-- INPUT -->
                <input
                    ref="searchInput"
                    v-model="query"
                    type="text"
                    placeholder="Search or type command..."
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pr-14 pl-12 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden xl:w-[430px] dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30"
                    @focus="query.length > 1 ? (isDropdownOpen = true) : null"
                />

                <!-- COMMAND BUTTON -->
                <button
                    type="button"
                    class="absolute top-1/2 right-2.5 inline-flex -translate-y-1/2 items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50 px-[7px] py-[4.5px] text-xs -tracking-[0.2px] text-gray-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400"
                >
                    <span> ⌘ </span>
                    <span> K </span>
                </button>
            </div>
        </form>

        <!-- RESULTS DROPDOWN -->
        <div
            v-if="isDropdownOpen && results.length > 0"
            v-click-outside="() => (isDropdownOpen = false)"
            class="absolute top-full left-0 z-50 mt-2 w-full overflow-hidden rounded-2xl border bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900"
        >
            <div class="max-h-[400px] overflow-y-auto p-2">
                <div
                    v-for="(result, index) in results"
                    :key="index"
                    @click="navigateTo(result.url)"
                    class="flex cursor-pointer items-center justify-between rounded-xl border-b p-3 transition-all last:border-0 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/5"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="bg-brand-50 dark:bg-brand-500/10 text-brand-600 flex h-8 w-8 items-center justify-center rounded-lg text-[10px] font-black uppercase"
                        >
                            {{ result.type[0] }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800 dark:text-white">
                                {{ result.label }}
                            </p>
                            <p class="font-mono text-[10px] text-gray-400">{{ result.sub }}</p>
                        </div>
                    </div>

                    <span class="text-[10px] font-bold tracking-widest text-gray-300 uppercase">{{
                        result.type
                    }}</span>
                </div>
            </div>

            <div class="border-t bg-gray-50 p-2 text-center dark:border-gray-800 dark:bg-white/5">
                <p class="text-[9px] font-bold tracking-tighter text-gray-400 uppercase">
                    Press ESC to close
                </p>
            </div>
        </div>
    </div>
</template>
