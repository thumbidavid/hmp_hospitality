// resources/js/admin.js
import './bootstrap'

// 🔹 Add the template’s CSS imports
// import '../css/main.css'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'jsvectormap/dist/jsvectormap.css'
import 'flatpickr/dist/flatpickr.css'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import { createPinia } from 'pinia'

// --- PRIME VUE IMPORTS ---
import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura'
import 'primeicons/primeicons.css'
import ToastService from 'primevue/toastservice'

// 🔹 Import VueApexCharts (plugin from template)
import VueApexCharts from 'vue3-apexcharts'

// --- FIX: Create the Pinia instance here ---
const pinia = createPinia()

const appName = import.meta.env.VITE_APP_NAME || 'Laravel App'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/App/**/*.vue', { eager: true })
        const path = `./Pages/App/${name}.vue`
        const page = pages[path]

        if (!page) {
            console.error(`[Inertia Resolver] FAILED to find component at: ${path}`)
        }

        return page
    },
    setup({ el, App, props, plugin }) {
        // 🔹 Add .use(VueApexCharts) here
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue, {
                ripple: true,
                theme: {
                    preset: Aura,
                    options: {
                        // You can optionally configure dark mode here
                        darkModeSelector: '.dark-mode',
                        colorScheme: 'light', // or 'dark'
                        primaryColor: 'gray',
                    },
                },
            })
            .use(ToastService)
            .use(ZiggyVue)
            .use(pinia)
            .use(VueApexCharts) // register plugin globally
            .mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})
