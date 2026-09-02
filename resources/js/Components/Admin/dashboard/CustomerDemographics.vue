<script setup>
import { ref, onMounted } from "vue";
import jsVectorMap from "jsvectormap";
// The CSS is now imported in resources/js/app.js
import "jsvectormap/dist/maps/world.js";

// --- Step 1: Enrich the data source ---
// This is now the single source of truth for both the list and the map markers.
// I've added 'mapName' for the tooltip and 'coords' for the marker position.
const countries = ref([
    {
        name: "USA",
        mapName: "United States",
        coords: [37.0902, -95.7129],
        customerCount: "2,379 Customers",
        percentage: 79,
        flagSrc: "/images/country/country-01.svg",
    },
    {
        name: "France",
        mapName: "France",
        coords: [46.2276, 2.2137],
        customerCount: "589 Customers",
        percentage: 23,
        flagSrc: "/images/country/country-02.svg",
    },
    {
        name: "UK",
        mapName: "United Kingdom",
        coords: [55.3781, -3.436],
        customerCount: "1,982 Customers",
        percentage: 65,
        flagSrc: "/images/country/country-03.svg", // Assuming this image exists
    },
]);

const mapRef = ref(null);

onMounted(() => {
    if (mapRef.value) {
        // --- Step 2: Dynamically generate markers from the countries data ---
        const mapMarkers = countries.value.map((country) => ({
            name: country.mapName,
            coords: country.coords,
            style: { fill: "#465fff" },
        }));

        const map = new jsVectorMap({
            selector: "#mapOne",
            map: "world",
            zoomOnScroll: true,
            zoomButtons: true,

            regionStyle: {
                initial: { fill: "#D9D9D9" },
                hover: { fillOpacity: 1, fill: "#465fff" },
            },

            markerStyle: {
                initial: {
                    fill: "#465fff",
                    stroke: "#FFF",
                    strokeWidth: 1,
                    strokeOpacity: 0.5,
                },
                hover: {
                    fill: "#465fff",
                    stroke: "#465fff",
                },
            },

            // --- Step 3: Use the dynamically generated markers ---
            markers: mapMarkers,
        });
    }
});
</script>

<template>
    <div
        class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6"
    >
        <!-- Card Header -->
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Customers Demographic
            </h3>
            <div class="relative">
                <button class="text-gray-500 dark:text-gray-400">
                    <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fillRule="evenodd"
                            clipRule="evenodd"
                            d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                            fill="currentColor"
                        ></path>
                    </svg>
                </button>
            </div>
        </div>
        <p class="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">
            Number of customer based on country
        </p>

        <!-- Map Container -->
        <div
            class="px-4 py-6 my-6 overflow-hidden border border-gary-200 rounded-2xl bg-gray-50 dark:border-gray-800 dark:bg-gray-900 sm:px-6"
        >
            <div
                ref="mapRef"
                id="mapOne"
                class="mapOne map-btn -mx-4 -my-6 h-[212px] w-full jvm-container"
                style="background-color: transparent"
            ></div>
        </div>

        <!-- Country List -->
        <div class="space-y-5">
            <div
                v-for="country in countries"
                :key="country.name"
                class="flex items-center justify-between"
            >
                <div class="flex items-center gap-3">
                    <div class="items-center w-full rounded-full max-w-8">
                        <img :src="country.flagSrc" :alt="country.name" />
                    </div>
                    <div>
                        <p
                            class="font-semibold text-gray-800 text-theme-sm dark:text-white/90"
                        >
                            {{ country.name }}
                        </p>
                        <span
                            class="block text-gray-500 text-theme-xs dark:text-gray-400"
                            >{{ country.customerCount }}</span
                        >
                    </div>
                </div>
                <div class="flex w-full max-w-[140px] items-center gap-3">
                    <div
                        class="relative block h-2 w-full max-w-[100px] rounded-sm bg-gray-200 dark:bg-gray-800"
                    >
                        <div
                            class="absolute left-0 top-0 flex h-full items-center justify-center rounded-sm bg-brand-500 text-xs font-medium text-white"
                            :style="{ width: country.percentage + '%' }"
                        ></div>
                    </div>
                    <p
                        class="font-medium text-gray-800 text-theme-sm dark:text-white/90"
                    >
                        {{ country.percentage }}%
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
