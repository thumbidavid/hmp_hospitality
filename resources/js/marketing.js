import './bootstrap'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import { createPinia } from 'pinia'
import { VueQueryPlugin } from '@tanstack/vue-query' // Import Vue Query

const appName = import.meta.env.VITE_APP_NAME || 'HMP Hospitality'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/Marketing/${name}.vue`,
            import.meta.glob('./Pages/Marketing/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        const pinia = createPinia()

        return app
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .use(VueQueryPlugin) // Register Vue Query
            .mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})
