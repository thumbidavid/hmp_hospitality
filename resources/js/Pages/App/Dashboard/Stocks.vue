<script setup>
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import { ref } from "vue";
import VueApexCharts from "vue3-apexcharts";

// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from "swiper/vue";

// Import Swiper styles
import "swiper/css";
import "swiper/css/navigation";

// Import required Swiper modules
import { Navigation } from "swiper/modules";

const currentPageTitle = ref("Stocks");

// Refs for navigation elements
const prev = ref(null);
const next = ref(null);

const modules = [Navigation];

// --- Dummy Data and Chart Configurations ---

// 1. Top Stock Cards
const stockCards = ref([
    {
        ticker: "AAPL",
        name: "Apple, Inc",
        price: "$1,232.00",
        change: "+11.01%",
        status: "success",
        image: "/images/brand/brand-07.svg",
    },
    {
        ticker: "PYPL",
        name: "Paypal, Inc",
        price: "$965.00",
        change: "-9.05%",
        status: "error",
        image: "/images/brand/brand-08.svg",
    },
    {
        ticker: "TSLA",
        name: "Tesla, Inc",
        price: "$1,232.00",
        change: "+11.01%",
        status: "success",
        image: "/images/brand/brand-09.svg",
    },
    {
        ticker: "AMZN",
        name: "Amazone.com, Inc",
        price: "$2,567.99",
        change: "+11.01%",
        status: "success",
        image: "/images/brand/brand-10.svg",
    },
]);

// 2. Portfolio Performance Chart
const portfolioChart = ref({
    series: [
        {
            name: "Portfolio Performance",
            data: [
                31.34, 31.8, 31.4, 31.5, 31.2, 31.6, 31.3, 31.9, 31.6, 31.7,
                31.4, 31.8, 32.2, 31.9, 32.4, 32.0, 31.9, 32.5, 32.3, 31.8,
                32.7, 32.3, 32.8, 32.5, 32.1, 32.4, 31.5, 31.7, 31.3, 32.6,
                32.2, 32.9, 32.4, 32.1, 31.4, 32.7, 32.5, 32.9, 31.9, 31.2,
                32.2, 31.9, 32.5, 31.8, 32.4, 32.8, 31.4, 32.6, 32.9, 31.8,
                31.3, 32.3, 31.9, 32.5, 32.8, 31.9, 31.2, 32.4, 32.9, 31.5,
                31.8, 32.8, 31.9, 32.5, 32.2, 31.8, 32.9, 31.5, 31.9, 32.2,
                31.4, 32.7, 32.2, 31.9, 32.4, 32.1, 31.8, 32.2, 31.9, 32.5,
                32.8, 31.4, 32.7, 32.9, 31.8, 31.3, 32.4, 31.9, 32.5, 32.2,
                31.8, 32.7, 32.3, 32.8, 32.1, 31.4, 32.7, 32.5, 31.9, 32.2,
                31.4,
            ],
        },
    ],
    chartOptions: {
        chart: { type: "area", height: 335, toolbar: { show: false } },
        dataLabels: { enabled: false },
        stroke: { curve: "smooth", width: 1 },
        xaxis: {
            type: "datetime",
            categories: [
                "2025-05-19T00:00:00.000Z",
                "2025-05-20T00:00:00.000Z",
                "2025-05-21T00:00:00.000Z",
                "2025-05-22T00:00:00.000Z",
                "2025-05-23T00:00:00.000Z",
                "2025-05-24T00:00:00.000Z",
                "2025-05-25T00:00:00.000Z",
                "2025-05-26T00:00:00.000Z",
                "2025-05-27T00:00:00.000Z",
                "2025-05-28T00:00:00.000Z",
                "2025-05-29T00:00:00.000Z",
                "2025-05-30T00:00:00.000Z",
                "2025-06-01T00:00:00.000Z",
                "2025-06-02T00:00:00.000Z",
                "2025-06-03T00:00:00.000Z",
                "2025-06-04T00:00:00.000Z",
                "2025-06-05T00:00:00.000Z",
                "2025-06-06T00:00:00.000Z",
                "2025-06-07T00:00:00.000Z",
                "2025-06-08T00:00:00.000Z",
                "2025-06-09T00:00:00.000Z",
                "2025-06-10T00:00:00.000Z",
                "2025-06-11T00:00:00.000Z",
                "2025-06-12T00:00:00.000Z",
                "2025-06-13T00:00:00.000Z",
                "2025-06-14T00:00:00.000Z",
                "2025-06-15T00:00:00.000Z",
                "2025-06-16T00:00:00.000Z",
                "2025-06-17T00:00:00.000Z",
                "2025-06-18T00:00:00.000Z",
                "2025-06-19T00:00:00.000Z",
                "2025-06-20T00:00:00.000Z",
                "2025-06-21T00:00:00.000Z",
                "2025-06-22T00:00:00.000Z",
                "2025-06-23T00:00:00.000Z",
                "2025-06-24T00:00:00.000Z",
                "2025-06-25T00:00:00.000Z",
                "2025-06-26T00:00:00.000Z",
                "2025-06-27T00:00:00.000Z",
                "2025-06-28T00:00:00.000Z",
                "2025-06-29T00:00:00.000Z",
                "2025-06-30T00:00:00.000Z",
                "2025-07-01T00:00:00.000Z",
                "2025-07-02T00:00:00.000Z",
                "2025-07-03T00:00:00.000Z",
                "2025-07-04T00:00:00.000Z",
                "2025-07-05T00:00:00.000Z",
                "2025-07-06T00:00:00.000Z",
                "2025-07-07T00:00:00.000Z",
                "2025-07-08T00:00:00.000Z",
                "2025-07-09T00:00:00.000Z",
                "2025-07-10T00:00:00.000Z",
                "2025-07-11T00:00:00.000Z",
                "2025-07-12T00:00:00.000Z",
                "2025-07-13T00:00:00.000Z",
                "2025-07-14T00:00:00.000Z",
                "2025-07-15T00:00:00.000Z",
                "2025-07-16T00:00:00.000Z",
                "2025-07-17T00:00:00.000Z",
                "2025-07-18T00:00:00.000Z",
                "2025-07-19T00:00:00.000Z",
                "2025-07-20T00:00:00.000Z",
                "2025-07-21T00:00:00.000Z",
                "2025-07-22T00:00:00.000Z",
                "2025-07-23T00:00:00.000Z",
                "2025-07-24T00:00:00.000Z",
                "2025-07-25T00:00:00.000Z",
                "2025-07-26T00:00:00.000Z",
                "2025-07-27T00:00:00.000Z",
                "2025-07-28T00:00:00.000Z",
                "2025-07-29T00:00:00.000Z",
                "2025-07-30T00:00:00.000Z",
                "2025-07-31T00:00:00.000Z",
                "2025-08-01T00:00:00.000Z",
                "2025-08-02T00:00:00.000Z",
                "2025-08-03T00:00:00.000Z",
                "2025-08-04T00:00:00.000Z",
                "2025-08-05T00:00:00.000Z",
                "2025-08-06T00:00:00.000Z",
                "2025-08-07T00:00:00.000Z",
                "2025-08-08T00:00:00.000Z",
                "2025-08-09T00:00:00.000Z",
                "2025-08-10T00:00:00.000Z",
                "2025-08-11T00:00:00.000Z",
                "2025-08-12T00:00:00.000Z",
                "2025-08-13T00:00:00.000Z",
                "2025-08-14T00:00:00.000Z",
                "2025-08-15T00:00:00.000Z",
                "2025-08-16T00:00:00.000Z",
                "2025-08-17T00:00:00.000Z",
                "2025-08-18T00:00:00.000Z",
                "2025-08-19T00:00:00.000Z",
                "2025-08-20T00:00:00.000Z",
            ],
            labels: {
                style: { colors: "#373d3f", fontFamily: "Outfit, sans-serif" },
            },
        },
        yaxis: {
            labels: {
                formatter: (val) => val.toFixed(2),
                style: { colors: "#373d3f", fontFamily: "Outfit, sans-serif" },
            },
        },
        grid: { borderColor: "#e0e0e0" },
        colors: ["#465fff"],
        fill: {
            type: "gradient",
            gradient: { opacityFrom: 0.55, opacityTo: 0 },
        },
    },
});

// 3. Trending Stocks
const trendingStocks = ref([
    {
        ticker: "TSLA",
        name: "Tesla, Inc",
        price: "$192.53",
        change: "+1.01%",
        status: "success",
        image: "/images/brand/brand-09.svg",
    },
    {
        ticker: "AAPL",
        name: "Apple, Inc",
        price: "$192.53",
        change: "+3.59%",
        status: "success",
        image: "/images/brand/brand-07.svg",
    },
    {
        ticker: "SPOT",
        name: "Spotify, Inc",
        price: "$192.53",
        change: "+1.01%",
        status: "success",
        image: "/images/brand/brand-11.svg",
    },
    {
        ticker: "PYPL",
        name: "Paypal, Inc",
        price: "$192.53",
        change: "+1.01%",
        status: "success",
        image: "/images/brand/brand-08.svg",
    },
    {
        ticker: "AMZN",
        name: "Amazon, Inc",
        price: "$192.53",
        change: "+1.01%",
        status: "success",
        image: "/images/brand/brand-10.svg",
    },
]);

// 4. Dividend Chart
const dividendChart = ref({
    series: [{ name: "Sales", data: [168, 385, 201, 298, 187, 195] }],
    chartOptions: {
        chart: { type: "bar", height: 200, sparkline: { enabled: true } },
        plotOptions: { bar: { borderRadius: 5, columnWidth: "45%" } },
        colors: ["#465fff"],
        xaxis: { categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"] },
        tooltip: { enabled: true, x: { show: false } },
    },
});

// 5. Watchlist
const watchlist = ref([
    {
        ticker: "AAPL",
        name: "Apple, Inc",
        price: "$4,008.65",
        change: "+11.01%",
        status: "success",
        image: "/images/brand/brand-07.svg",
    },
    {
        ticker: "SPOT",
        name: "Spotify.com",
        price: "$11,689.00",
        change: "+9.48%",
        status: "success",
        image: "/images/brand/brand-11.svg",
    },
    {
        ticker: "ABNB",
        name: "Airbnb, Inc",
        price: "$32,227.00",
        change: "-0.29%",
        status: "error",
        image: "/images/brand/brand-12.svg",
    },
    {
        ticker: "ENVT",
        name: "Envato, Inc",
        price: "$13,895.00",
        change: "+3.79%",
        status: "success",
        image: "/images/brand/brand-13.svg",
    },
    {
        ticker: "AAPL",
        name: "Apple, Inc",
        price: "$4,008.65",
        change: "+11.01%",
        status: "success",
        image: "/images/brand/brand-07.svg",
    },
    {
        ticker: "SPOT",
        name: "Spotify.com",
        price: "$11,689.00",
        change: "+9.48%",
        status: "success",
        image: "/images/brand/brand-11.svg",
    },
    {
        ticker: "QIWI",
        name: "qiwi.com, Inc",
        price: "$4,008.65",
        change: "+4.52%",
        status: "success",
        image: "/images/brand/brand-14.svg",
    },
]);

// 6. Latest Transactions Table
const transactions = ref([
    {
        name: "Bought PYPL",
        image: "/images/brand/brand-08.svg",
        date: "Nov 23, 01:00 PM",
        price: "$2,567.88",
        category: "Finance",
        status: "Success",
    },
    {
        name: "Bought AAPL",
        image: "/images/brand/brand-07.svg",
        date: "Nov 22, 09:00 PM",
        price: "$3,987.45",
        category: "Technology",
        status: "Pending",
    },
    {
        name: "Sell KKST",
        image: "/images/brand/brand-15.svg",
        date: "Oct 12, 03:54 PM",
        price: "$6,754.99",
        category: "Finance",
        status: "Success",
    },
    {
        name: "Bought FB",
        image: "/images/brand/brand-02.svg",
        date: "Sep 09, 02:00 AM",
        price: "$1,445.41",
        category: "Social media",
        status: "Success",
    },
    {
        name: "Sell AMZN",
        image: "/images/brand/brand-10.svg",
        date: "Feb 35, 08:00 PM",
        price: "$5,698.55",
        category: "E-commerce",
        status: "Failed",
    },
]);
</script>

<template>

    <Head title="Stocks" />

    <AuthenticatedLayout>
        <PageBreadcrumb :pageTitle="currentPageTitle" />

        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <!-- Stock Cards -->
            <div class="col-span-12">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 xl:grid-cols-4">
                    <div v-for="(stock, index) in stockCards" :key="index"
                        class="rounded-2xl border border-gray-200 bg-white px-6 pb-5 pt-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10">
                                <img :src="stock.image" :alt="stock.name" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                                    {{ stock.ticker }}
                                </h3>
                                <span class="block text-gray-500 text-theme-xs dark:text-gray-400">{{ stock.name
                                }}</span>
                            </div>
                        </div>
                        <div class="flex items-end justify-between">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    {{ stock.price }}
                                </h4>
                            </div>
                            <span class="flex items-center gap-1 rounded-full py-0.5 pl-2 pr-2.5 text-sm font-medium"
                                :class="stock.status === 'success'
                                    ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500'
                                    : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500'
                                    ">
                                <svg v-if="stock.status === 'success'" class="fill-current" width="13" height="12"
                                    viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.06462 1.62393C6.20193 1.47072 6.40135 1.37432 6.62329 1.37432C6.81631 1.37415 7.00845 1.44731 7.15505 1.5938L10.1551 4.5918C10.4481 4.88459 10.4483 5.35946 10.1555 5.65246C9.86273 5.94546 9.38785 5.94562 9.09486 5.65283L7.37329 3.93247V10.125C7.37329 10.5392 7.03751 10.875 6.62329 10.875C6.20908 10.875 5.87329 10.5392 5.87329 10.125L5.87329 3.93578L4.15516 5.65281C3.86218 5.94561 3.3873 5.94546 3.0945 5.65248C2.8017 5.35949 2.80185 4.88462 3.09484 4.59182L6.06462 1.62393Z">
                                    </path>
                                </svg>
                                <svg v-else class="fill-current" width="12" height="12" viewBox="0 0 12 12" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753V1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875L5.12329 8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z">
                                    </path>
                                </svg>
                                {{ stock.change }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portfolio Performance -->
            <div class="col-span-12 space-y-6 xl:col-span-8">
                <div
                    class="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-5">
                    <div class="flex flex-col gap-5 mb-6 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Portfolio Performance
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Here is your performance stats of each month
                            </p>
                        </div>
                        <div
                            class="inline-flex w-fit items-center gap-0.5 rounded-lg bg-gray-100 p-0.5 dark:bg-gray-900">
                            <button
                                class="rounded-md px-3 py-2 text-sm font-medium transition-colors shadow-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800">
                                Monthly
                            </button>
                            <button
                                class="rounded-md px-3 py-2 text-sm font-medium transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Quarterly
                            </button>
                            <button
                                class="rounded-md px-3 py-2 text-sm font-medium transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Annually
                            </button>
                        </div>
                    </div>
                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <div class="-ml-4 min-w-[900px] xl:min-w-full pl-2">
                            <VueApexCharts type="area" :height="portfolioChart.chartOptions.chart.height
                                " :options="portfolioChart.chartOptions" :series="portfolioChart.series" />
                        </div>
                    </div>
                </div>

                <!-- Trending Stocks -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Trending Stocks
                        </h3>
                        <!-- Swiper Navigation Buttons -->
                        <div class="relative flex items-center gap-1.5">
                            <div ref="prev" class="swiper-button-prev">
                                <svg class="stroke-current" width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.1667 4L6 8.16667L10.1667 12.3333" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <div ref="next" class="swiper-button-next">
                                <svg class="stroke-current" width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.83333 12.6667L10 8.50002L5.83333 4.33335" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Swiper Component -->
                    <Swiper :slides-per-view="'auto'" :space-between="20" :modules="modules" :navigation="{
                        prevEl: prev,
                        nextEl: next,
                    }" class="stocksSlider">
                        <SwiperSlide v-for="(stock, index) in trendingStocks" :key="index" class="!w-[395px]">
                            <div class="rounded-2xl bg-gray-100 p-5 dark:bg-white/[0.03]">
                                <div
                                    class="flex items-center justify-between pb-5 mb-5 border-b border-gray-200 dark:border-gray-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10">
                                            <img :src="stock.image" :alt="stock.name" />
                                        </div>
                                        <div>
                                            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                                                {{ stock.ticker }}
                                            </h3>
                                            <span class="block text-gray-500 text-theme-xs dark:text-gray-400">{{
                                                stock.name }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h4
                                            class="mb-1 font-medium text-right text-gray-700 text-theme-sm dark:text-gray-400">
                                            {{ stock.price }}
                                        </h4>
                                        <span class="flex items-center justify-end gap-1 font-medium text-theme-xs"
                                            :class="stock.status === 'success'
                                                ? 'text-success-600 dark:text-success-500'
                                                : 'text-error-600 dark:text-error-500'
                                                ">
                                            <svg v-if="
                                                stock.status === 'success'
                                            " class="fill-current" width="12" height="12" viewBox="0 0 12 12"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M5.56462 1.62394C5.70193 1.47073 5.90135 1.37433 6.12329 1.37433C6.31631 1.37416 6.50845 1.44732 6.65505 1.59381L9.65514 4.59181C9.94814 4.8846 9.94831 5.35947 9.65552 5.65247C9.36273 5.94546 8.88785 5.94563 8.59486 5.65284L6.87329 3.93248V10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125L5.37329 3.93579L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65249C2.3017 5.3595 2.30185 4.88463 2.59484 4.59183L5.56462 1.62394Z">
                                                </path>
                                            </svg>
                                            <svg v-else class="fill-current" width="12" height="12" viewBox="0 0 12 12"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753V1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875L5.12329 8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z">
                                                </path>
                                            </svg>
                                            {{ stock.change }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button
                                        class="flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white p-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                        Short Stock
                                    </button>
                                    <button
                                        class="flex items-center justify-center w-full p-3 font-medium text-white rounded-lg bg-brand-500 text-theme-sm shadow-theme-xs hover:bg-brand-600">
                                        Buy Stock
                                    </button>
                                </div>
                            </div>
                        </SwiperSlide>
                    </Swiper>
                </div>
            </div>

            <!-- Dividend and Watchlist -->
            <div class="col-span-12 space-y-6 xl:col-span-4">
                <div
                    class="rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Dividend
                        </h3>
                        <button class="text-gray-500 dark:text-gray-400">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fillRule="evenodd" clipRule="evenodd"
                                    d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                    fill="currentColor"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="chartFifteen" class="-ml-5">
                        <VueApexCharts type="bar" :height="dividendChart.chartOptions.chart.height"
                            :options="dividendChart.chartOptions" :series="dividendChart.series" />
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            My Watchlist
                        </h3>
                        <button class="text-gray-500 dark:text-gray-400">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fillRule="evenodd" clipRule="evenodd"
                                    d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                    fill="currentColor"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex h-[372px] flex-col">
                        <div class="flex flex-col h-auto pr-3 overflow-y-auto custom-scrollbar">
                            <div v-for="(item, index) in watchlist" :key="index"
                                class="flex items-center justify-between pt-4 pb-4 border-b border-gray-200 first:pt-0 last:border-b-0 last:pb-0 dark:border-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10">
                                        <img :src="item.image" :alt="item.name" />
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                                            {{ item.ticker }}
                                        </h3>
                                        <span class="block text-gray-500 text-theme-xs dark:text-gray-400">{{ item.name
                                        }}</span>
                                    </div>
                                </div>
                                <div>
                                    <h4
                                        class="mb-1 font-medium text-right text-gray-700 text-theme-sm dark:text-gray-400">
                                        {{ item.price }}
                                    </h4>
                                    <span class="flex items-center justify-end gap-1 font-medium text-theme-xs" :class="item.status === 'success'
                                        ? 'text-success-600 dark:text-success-500'
                                        : 'text-error-600 dark:text-error-500'
                                        ">
                                        <svg v-if="item.status === 'success'" class="fill-current" width="12"
                                            height="12" viewBox="0 0 12 12" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M5.56462 1.62394C5.70193 1.47073 5.90135 1.37433 6.12329 1.37433C6.31631 1.37416 6.50845 1.44732 6.65505 1.59381L9.65514 4.59181C9.94814 4.8846 9.94831 5.35947 9.65552 5.65247C9.36273 5.94546 8.88785 5.94563 8.59486 5.65284L6.87329 3.93248V10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125L5.37329 3.93579L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65249C2.3017 5.3595 2.30185 4.88463 2.59484 4.59183L5.56462 1.62394Z">
                                            </path>
                                        </svg>
                                        <svg v-else class="fill-current" width="12" height="12" viewBox="0 0 12 12"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753V1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875L5.12329 8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z">
                                            </path>
                                        </svg>
                                        {{ item.change }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Transactions Table -->
            <div class="col-span-12">
                <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Latest Transactions
                            </h3>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <form>
                                <div class="relative">
                                    <span class="absolute -translate-y-1/2 pointer-events-none left-4 top-1/2"><svg
                                            class="fill-gray-500 dark:fill-gray-400" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
                                                fill=""></path>
                                        </svg></span><input type="text" placeholder="Search..."
                                        class="dark:bg-dark-900 h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[300px]" />
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="w-full px-5 overflow-x-auto sm:px-6">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-gray-100 border-y dark:border-gray-800">
                                    <th class="py-3 font-normal text-left">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Name
                                        </p>
                                    </th>
                                    <th class="py-3 font-normal text-left">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Date
                                        </p>
                                    </th>
                                    <th class="py-3 font-normal text-left">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Price
                                        </p>
                                    </th>
                                    <th class="py-3 font-normal text-left">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Category
                                        </p>
                                    </th>
                                    <th class="py-3 font-normal text-left">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            Status
                                        </p>
                                    </th>
                                    <th class="py-3 font-normal text-left">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400"></p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr v-for="(trans, index) in transactions" :key="index">
                                    <td class="py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8">
                                                <img :src="trans.image" :alt="trans.name" />
                                            </div>
                                            <div>
                                                <span
                                                    class="block font-medium text-gray-700 text-theme-sm dark:text-gray-400">{{
                                                        trans.name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <p class="text-gray-700 text-theme-sm dark:text-gray-400">
                                            {{ trans.date }}
                                        </p>
                                    </td>
                                    <td class="py-4">
                                        <p class="text-gray-700 text-theme-sm dark:text-gray-400">
                                            {{ trans.price }}
                                        </p>
                                    </td>
                                    <td class="py-4">
                                        <p class="text-gray-700 text-theme-sm dark:text-gray-400">
                                            {{ trans.category }}
                                        </p>
                                    </td>
                                    <td class="py-4">
                                        <p class="rounded-full px-2 py-0.5 text-theme-xs font-medium" :class="{
                                            'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500':
                                                trans.status === 'Success',
                                            'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400':
                                                trans.status === 'Pending',
                                            'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500':
                                                trans.status === 'Failed',
                                        }">
                                            {{ trans.status }}
                                        </p>
                                    </td>
                                    <td class="py-4">
                                        <div class="flex items-center justify-center">
                                            <button class="text-gray-500 dark:text-gray-400">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M5.99902 10.245C6.96552 10.245 7.74902 11.0285 7.74902 11.995V12.005C7.74902 12.9715 6.96552 13.755 5.99902 13.755C5.03253 13.755 4.24902 12.9715 4.24902 12.005V11.995C4.24902 11.0285 5.03253 10.245 5.99902 10.245ZM17.999 10.245C18.9655 10.245 19.749 11.0285 19.749 11.995V12.005C19.749 12.9715 18.9655 13.755 17.999 13.755C17.0325 13.755 16.249 12.9715 16.249 12.005V11.995C16.249 11.0285 17.0325 10.245 17.999 10.245ZM13.749 11.995C13.749 11.0285 12.9655 10.245 11.999 10.245C11.0325 10.245 10.249 11.0285 10.249 11.995V12.005C10.249 12.9715 11.0325 13.755 11.999 13.755C12.9655 13.755 13.749 12.9715 13.749 12.005V11.995Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Custom styles for Swiper navigation to match the original design */
.swiper-button-prev,
.swiper-button-next {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 1px solid #e5e7eb;
    /* border-gray-200 */
    color: #6b7280;
    /* text-gray-500 */
    background-color: white;
    transition: all 0.2s ease-in-out;
}

.swiper-button-prev:hover,
.swiper-button-next:hover {
    background-color: #f3f4f6;
    /* hover:bg-gray-100 */
    color: #374151;
    /* hover:text-gray-700 */
}

.dark .swiper-button-prev,
.dark .swiper-button-next {
    border-color: #374151;
    /* dark:border-gray-700 */
    background-color: #1f2937;
    /* dark:bg-gray-800 */
    color: #9ca3af;
    /* dark:text-gray-400 */
}

.dark .swiper-button-prev:hover,
.dark .swiper-button-next:hover {
    background-color: #374151;
    /* dark:hover:bg-gray-700 */
    color: #f9fafb;
    /* dark:hover:text-gray-100 */
}

.swiper-button-prev::after,
.swiper-button-next::after {
    content: none;
    /* Remove default Swiper arrows */
}

.swiper-button-disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Ensure slides have a fixed width and don't shrink */
.swiper-slide {
    flex-shrink: 0;
}
</style>
