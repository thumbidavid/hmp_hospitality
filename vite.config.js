import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        tailwindcss(), // This handles both app.css and marketing.css
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/marketing.css',
                'resources/js/marketing.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: { base: null, includeAbsolute: false },
            },
        }),
    ],
    optimizeDeps: {
        include: [
            'vue3-apexcharts',
            'apexcharts',
            'primevue/config',
            'swiper',
            'flatpickr',
            'jsvectormap',
        ],
    },
})
