<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import { ref } from "vue";
import VueApexCharts from "vue3-apexcharts";

const currentPageTitle = ref("Logistics");

// --- Dummy Data and Chart Configurations ---

// 1. Top Stat Cards
const overviewCards = ref([
    {
        title: "Total Orders",
        value: "12,384",
        change: "+20%",
        icon: `<svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none"><path d="M14.0003 24.5898V24.5863M14.0003 12.8684V24.5863M9.06478 16.3657V10.6082M18.9341 5.67497C18.9341 5.67497 12.9204 8.68175 9.06706 10.6084M23.5913 8.27989C23.7686 8.55655 23.8679 8.88278 23.8679 9.2241V18.7779C23.8679 19.4407 23.4934 20.0467 22.9005 20.3431L14.7834 24.4015C14.537 24.5248 14.2686 24.5864 14.0003 24.5863M23.5913 8.27989L14.7834 12.6837C14.2908 12.93 13.7109 12.93 13.2182 12.6837L4.41037 8.27989M23.5913 8.27989C23.4243 8.01927 23.1881 7.80264 22.9005 7.65884L14.7834 3.60044C14.2908 3.35411 13.7109 3.35411 13.2182 3.60044L5.10118 7.65884C4.81359 7.80264 4.57737 8.01927 4.41037 8.27989M4.41037 8.27989C4.23309 8.55655 4.13379 8.88278 4.13379 9.2241V18.7779C4.13379 19.4407 4.5083 20.0467 5.10118 20.3431L13.2182 24.4015C13.4644 24.5246 13.7324 24.5862 14.0003 24.5863" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`,
    },
    {
        title: "Orders in Transit",
        value: "728",
        change: "+20%",
        icon: `<svg xmlns="http://www.w3.org/2000/svg" width="29" height="28" viewBox="0 0 29 28" fill="none"><path d="M7.04102 4.66667H15.791C16.7575 4.66667 17.541 5.45017 17.541 6.41667V20.4167M17.541 20.4167V8.33004H20.4036C20.9846 8.33004 21.5277 8.61836 21.8532 9.09958L24.8239 13.4917C24.9171 13.6295 24.9897 13.7792 25.0402 13.9359M17.541 20.4167H17.5495M17.541 20.4167H11.5592M3.54102 20.4167H6.17451M25.1243 20.4167V14.4721C25.1243 14.2891 25.0956 14.1082 25.0402 13.9359M25.9993 20.4167H22.9342M12.8743 20.4167H17.5495M17.541 13.9359H25.0402M5.29102 9.04167H10.541M10.541 13.4167H3.54102M17.5495 20.4167C17.6595 19.026 18.8229 17.9317 20.2418 17.9317C21.6608 17.9317 22.8242 19.026 22.9342 20.4167M17.5495 20.4167C17.5439 20.4879 17.541 20.5599 17.541 20.6325C17.541 22.1241 18.7502 23.3333 20.2418 23.3333C21.7335 23.3333 22.9427 22.1241 22.9427 20.6325C22.9427 20.5599 22.9398 20.4879 22.9342 20.4167M11.5592 20.4167C11.5648 20.4879 11.5677 20.5599 11.5677 20.6325C11.5677 22.1241 10.3585 23.3333 8.86685 23.3333C7.37522 23.3333 6.16602 22.1241 6.16602 20.6325C6.16602 20.5599 6.16888 20.4879 6.17451 20.4167M11.5592 20.4167C11.4492 19.026 10.2858 17.9317 8.86685 17.9317C7.44787 17.9317 6.28447 19.026 6.17451 20.4167" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`,
    },
    {
        title: "Total Orders",
        value: "12,384",
        change: "+20%",
        icon: `<svg class="h-7 w-7" width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.625 9.33333L3 9.33333M4.75 14H3M3.875 18.6667H3M9.90222 22.3117H23.0071C23.9027 22.3117 24.6537 21.6356 24.7475 20.7449L26.129 7.62071C26.2378 6.58744 25.4276 5.6875 24.3887 5.6875H11.2838C10.3882 5.6875 9.63716 6.36364 9.5434 7.25429L8.16184 20.3785C8.05307 21.4118 8.86324 22.3117 9.90222 22.3117ZM16.4622 5.6875H19.5793L18.7043 11.508H15.5872L16.4622 5.6875Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`,
    },
]);

// 2. Delivery Statistics Chart
const deliveryStatsChart = ref({
    series: [
        {
            name: "Shipment",
            data: [80, 60, 70, 40, 65, 45, 48, 55, 58, 50, 67, 75],
        },
        {
            name: "Delivery",
            data: [90, 50, 65, 30, 78, 68, 55, 90, 50, 65, 75, 95],
        },
    ],
    chartOptions: {
        chart: { type: "bar", height: 256, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 2, columnWidth: "40%" } },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 4, colors: ["transparent"] },
        xaxis: {
            categories: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ],
            labels: {
                style: { colors: "#373d3f", fontFamily: "Outfit, sans-serif" },
            },
        },
        yaxis: {
            labels: {
                formatter: (val) => `${val}%`,
                style: { colors: "#344054", fontFamily: "Outfit, sans-serif" },
            },
        },
        grid: { borderColor: "#f2f4f7" },
        colors: ["#c2d6ff", "#465fff"],
        legend: { show: false },
    },
});

// 3. Shipped Quantities Chart
const shippedQuantitiesChart = ref({
    series: [
        {
            name: "New Sales",
            data: [
                15, 0, 8, 12, 10, 15, 11, 14, 12, 11, 13, 11, 15, 18, 16, 19,
            ],
        },
    ],
    chartOptions: {
        chart: { type: "area", sparkline: { enabled: true } },
        stroke: { curve: "smooth", width: 1 },
        colors: ["#12b76a"],
        fill: {
            type: "gradient",
            gradient: { opacityFrom: 0.55, opacityTo: 0 },
        },
        tooltip: { enabled: false },
    },
});

// 4. Delivery Activities Table
const deliveryActivities = ref([
    {
        id: "#324112",
        category: "Furniture",
        company: "HomeLine",
        arrivalTime: "10 Apr 2028 2:15 pm",
        route: "Berlin–Milan",
        price: "$1,250.00",
        status: "Delivered",
    },
    {
        id: "#332800",
        category: "Clothing",
        company: "StylePro",
        arrivalTime: "21 May 2028 9:00 am",
        route: "Paris–Rome",
        price: "$340.75",
        status: "Canceled",
    },
    {
        id: "#3328100",
        category: "Books",
        company: "EduSource",
        arrivalTime: "02 Jun 2028 11:45 am",
        route: "New York–Chicago",
        price: "$128.40",
        status: "In-Transit",
    },
    {
        id: "#3328200",
        category: "Automotive",
        company: "AutoParts Co.",
        arrivalTime: "18 Mar 2028 4:00 pm",
        route: "Tokyo–Osaka",
        price: "$2,150.89",
        status: "Delivered",
    },
    {
        id: "#3328300",
        category: "Beauty",
        company: "GlamShine",
        arrivalTime: "28 Jun 2028 5:45 pm",
        route: "Dubai–Doha",
        price: "$323.75",
        status: "Canceled",
    },
]);
</script>

<template>

    <Head title="Logistics" />

    <AuthenticatedLayout>
        <PageBreadcrumb :pageTitle="currentPageTitle" />

        <div class="space-y-6">
            <!-- Top Stats Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="(card, index) in overviewCards" :key="index"
                    class="flex items-center gap-5 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/3">
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-xl bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white/90"
                        v-html="card.icon"></div>
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
                            {{ card.value }}
                        </h3>
                        <p class="flex items-center gap-3 text-gray-500 dark:text-gray-400">
                            {{ card.title }}
                            <span
                                class="bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500 inline-flex items-center justify-center gap-1 rounded-full px-2.5 py-0.5 text-sm font-medium">{{
                                    card.change }}</span>
                        </p>
                    </div>
                </article>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <!-- Delivery Statistics Card -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
                        <div class="flex items-center justify-between gap-5">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    Delivery Statistics
                                </h3>
                                <p class="dark:text-gray-400 text-sm text-gray-500">
                                    Total number of deliveries 70.5K
                                </p>
                            </div>
                            <div class="relative z-20 bg-transparent">
                                <select
                                    class="dark:bg-dark-900 shadow-theme-xs h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                                <span
                                    class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-5 pt-5">
                            <div class="flex items-center gap-1.5">
                                <div class="bg-brand-200 h-2.5 w-2.5 rounded-full"></div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Shipment
                                </p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="bg-brand-500 h-2.5 w-2.5 rounded-full"></div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Delivery
                                </p>
                            </div>
                        </div>
                        <div class="w-full pt-5">
                            <VueApexCharts type="bar" :height="deliveryStatsChart.chartOptions.chart.height
                                " :options="deliveryStatsChart.chartOptions" :series="deliveryStatsChart.series" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div
                            class="flex flex-col justify-between space-y-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Total revenue earned
                                    </p>
                                    <h3 class="text-3xl font-medium text-gray-800 dark:text-white/90">
                                        $23,445,700
                                    </h3>
                                </div>
                                <button class="text-gray-500 dark:text-gray-400">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fillRule="evenodd" clipRule="evenodd"
                                            d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Shipped quantities
                                    </p>
                                    <h3 class="text-3xl font-medium text-gray-800 dark:text-white/90">
                                        9,258
                                    </h3>
                                </div>
                                <div class="h-[60px] w-full max-w-[150px]">
                                    <VueApexCharts type="area" height="70" :options="shippedQuantitiesChart.chartOptions
                                        " :series="shippedQuantitiesChart.series" />
                                </div>
                            </div>
                        </div>
                        <div
                            class="space-y5 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                        Delivery Vehicles
                                    </h3>
                                    <p class="dark:text-gray-40 text-sm text-gray-500">
                                        Vehicles operating on the road
                                    </p>
                                </div>
                                <button class="text-gray-500 dark:text-gray-400">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fillRule="evenodd" clipRule="evenodd"
                                            d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="relative mt-5 flex justify-between">
                                <div>
                                    <h3 class="mb-1 text-3xl font-medium text-gray-800 dark:text-white/90">
                                        29
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <span class="text-success-600 font-medium">+3.85%</span>
                                        than last Week
                                    </p>
                                    <div class="mt-5 flex items-center gap-2">
                                        <div
                                            class="ring-success-500 flex h-6 w-6 items-center justify-center rounded-full ring-2 ring-inset">
                                            <div class="bg-success-500 h-2.5 w-2.5 rounded-full"></div>
                                        </div>
                                        <div>
                                            <span class="text-success-500 text-sm font-medium">On-route</span>
                                        </div>
                                    </div>
                                </div>
                                <img class="absolute -right-6 -bottom-2" src="/images/logistics/truck.png"
                                    alt="Truck" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-1">
                    <div class="space-y-2 rounded-xl border bg-gray-100 p-2 dark:border-gray-800 dark:bg-white/3">
                        <div class="rounded-xl bg-white p-4 dark:bg-gray-900">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                        Tracking Delivery
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Last viewed delivery history
                                    </p>
                                </div>
                                <button class="text-gray-500 dark:text-gray-400">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fillRule="evenodd" clipRule="evenodd"
                                            d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-5">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3650.5145053176284!2d90.42105717591272!3d23.800296778636472!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c7e9f37a5a3d%3A0x41d7d1d02e1ed0e4!2sPimjo!5e0!3m2!1sen!2sbd!4v1751871078440!5m2!1sen!2sbd"
                                    width="303" height="180" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                    class="!w-full rounded-xl border border-gray-200 grayscale dark:border-gray-800"></iframe>
                            </div>
                        </div>
                        <div class="rounded-xl bg-white p-4 dark:bg-gray-900">
                            <div class="flex items-end justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Tracking ID
                                    </p>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                        #28745-72809bjk
                                    </h3>
                                </div>
                                <span
                                    class="bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500 inline-flex items-center justify-center gap-1 rounded-full px-2.5 py-0.5 text-sm font-medium">In
                                    Transit</span>
                            </div>
                            <div class="mt-5">
                                <!-- Timeline -->
                                <div class="relative pb-5 pl-11">
                                    <div
                                        class="text-brand-500 bg-brand-50 dark:ring-brand-500/15 ring-brand-50 dark:bg-brand-950 absolute top-0 left-0 z-20 flex h-10 w-10 items-center justify-center rounded-full border-2 border-white ring-2 dark:border-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none">
                                            <path
                                                d="M15.1039 13.3343L13.5141 14.924L12.6039 14.0137M9.99967 3.33414H6.56405C6.11247 3.33414 5.69599 3.5777 5.47459 3.97128L3.49355 7.49292C3.44274 7.58326 3.40357 7.67918 3.37664 7.77839M9.99967 3.33414H13.4353C13.8869 3.33414 14.3034 3.5777 14.5248 3.97128L16.5058 7.49292C16.5566 7.58326 16.5958 7.67918 16.6227 7.77839M9.99967 3.33414V7.77839M9.99967 7.77839L16.6227 7.77839M9.99967 7.77839L3.37664 7.77839M16.6227 7.77839C16.6516 7.88467 16.6663 7.99474 16.6663 8.10578V8.43098M3.37664 7.77839C3.3478 7.88467 3.33301 7.99474 3.33301 8.10578V15.4168C3.33301 16.1071 3.89265 16.6668 4.58301 16.6668H8.02525M17.708 14.1292C17.708 16.2578 15.9824 17.9833 13.8538 17.9833C11.7252 17.9833 9.99967 16.2578 9.99967 14.1292C9.99967 12.0006 11.7252 10.275 13.8538 10.275C15.9824 10.275 17.708 12.0006 17.708 14.1292Z"
                                                stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                                                stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2 flex items-end justify-between">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                12 Apr 2028
                                            </p>
                                            <h4 class="font-medium text-gray-800 dark:text-white/90">
                                                Picked up
                                            </h4>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">12:54</span>
                                    </div>
                                    <div
                                        class="border-brand-500 absolute top-10 left-5 h-full w-px border border-dashed">
                                    </div>
                                </div>
                                <div class="relative pb-5 pl-11">
                                    <div
                                        class="text-brand-500 bg-brand-50 dark:ring-brand-500/15 ring-brand-50 dark:bg-brand-950 absolute top-0 left-0 z-20 flex h-10 w-10 items-center justify-center rounded-full border-2 border-white ring-2 dark:border-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none">
                                            <path
                                                d="M4.79199 3.33334H11.042C11.7323 3.33334 12.292 3.89299 12.292 4.58334V14.5833M12.292 14.5833V5.95003H14.3367C14.7517 5.95003 15.1396 6.15598 15.3721 6.49971L17.4941 9.63691C17.5607 9.73538 17.6125 9.84227 17.6485 9.95419M12.292 14.5833H12.2981M12.292 14.5833H8.01926M2.29199 14.5833H4.17306M17.7087 14.5833V10.3372C17.7087 10.2065 17.6882 10.0773 17.6485 9.95419M18.3337 14.5833H16.1443M8.95866 14.5833H12.2981M12.292 9.95419H17.6485M3.54199 6.45834H7.29199M7.29199 9.58334H2.29199M12.2981 14.5833C12.3766 13.59 13.2076 12.8083 14.2212 12.8083C15.2347 12.8083 16.0657 13.59 16.1443 14.5833M12.2981 14.5833C12.294 14.6342 12.292 14.6856 12.292 14.7375C12.292 15.803 13.1557 16.6667 14.2212 16.6667C15.2866 16.6667 16.1503 15.803 16.1503 14.7375C16.1503 14.6856 16.1483 14.6342 16.1443 14.5833M8.01926 14.5833C8.02328 14.6342 8.02533 14.6856 8.02533 14.7375C8.02533 15.803 7.16161 16.6667 6.09616 16.6667C5.03071 16.6667 4.16699 15.803 4.16699 14.7375C4.16699 14.6856 4.16904 14.6342 4.17306 14.5833M8.01926 14.5833C7.94071 13.59 7.10972 12.8083 6.09616 12.8083C5.0826 12.8083 4.25161 13.59 4.17306 14.5833"
                                                stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                                                stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2 flex justify-between">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                12 Apr 2028
                                            </p>
                                            <h4 class="font-medium text-gray-800 dark:text-white/90">
                                                In Transit
                                            </h4>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">12:58</span>
                                    </div>
                                    <div
                                        class="absolute top-10 left-5 z-1 h-full w-px border border-dashed border-gray-200 dark:border-gray-800">
                                    </div>
                                </div>
                                <div class="relative pl-11">
                                    <div
                                        class="dark:ring-brand-500/15 ring-brand-50 absolute top-0 left-0 z-20 flex h-10 w-10 items-center justify-center rounded-full border-2 border-white bg-gray-100 text-gray-700 ring-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none">
                                            <path
                                                d="M3.54199 6.66667L1.66699 6.66667M2.91699 10H1.66699M2.29199 13.3333H1.66699M6.59715 15.9369H15.9578C16.5975 15.9369 17.1339 15.454 17.2009 14.8178L18.1877 5.44336C18.2654 4.70531 17.6867 4.0625 16.9446 4.0625H7.58398C6.94429 4.0625 6.40782 4.54546 6.34085 5.18164L5.35402 14.5561C5.27633 15.2941 5.85502 15.9369 6.59715 15.9369ZM11.2829 4.0625H13.5093L12.8843 8.22H10.6579L11.2829 4.0625Z"
                                                stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                                                stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2 flex justify-between">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                13 Apr 2028
                                            </p>
                                            <h4 class="font-medium text-gray-800 dark:text-white/90">
                                                Delivered
                                            </h4>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">--:--</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between rounded-xl bg-white p-2 dark:bg-gray-900">
                            <div class="flex items-center gap-3">
                                <img src="/images/user/user-01.jpg" class="h-12 w-12 rounded-full" alt="Courier" />
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Courier
                                    </p>
                                    <h3 class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        Devid walthen
                                    </h3>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border-[0.5px] border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                        fill="none">
                                        <path
                                            d="M7.99967 15.3714C12.0267 15.3714 15.2913 12.1068 15.2913 8.07975C15.2913 4.05268 12.0267 0.788086 7.99967 0.788086C3.9726 0.788086 0.708008 4.05268 0.708008 8.07975C0.708008 10.0933 1.52416 11.9162 2.84369 13.2357L0.708008 15.3714H7.99967Z"
                                            stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        <path
                                            d="M4.35449 8.08057H4.36283M8.00033 8.08057H8.00866M11.6462 8.08057H11.6545"
                                            stroke="#344054" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                                <button
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border-[0.5px] border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                                        fill="none">
                                        <path
                                            d="M10.1201 15.5908C11.5913 16.4194 13.1728 16.9798 14.7931 17.2718C15.2176 17.3484 15.6482 17.1989 15.9532 16.8939L16.8434 16.0038C17.598 15.2491 17.3666 13.973 16.395 13.5314L13.4227 12.1803C12.8264 11.9093 11.9403 12.0405 11.6197 12.6657L10.1201 15.5908ZM10.1201 15.5908C8.95781 14.9362 7.86434 14.1142 6.87489 13.1248C5.88545 12.1353 5.06343 11.0419 4.40883 9.87958M4.40883 9.87958C3.58025 8.40839 3.01991 6.82693 2.7278 5.2066C2.65127 4.78204 2.80072 4.3515 3.10578 4.04646L3.99594 3.15635C4.7506 2.40169 6.0267 2.6331 6.46833 3.60468L7.81939 6.57702C8.09041 7.17326 7.95921 8.05942 7.33394 8.37998L4.40883 9.87958Z"
                                            stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Activities Table Card -->
            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div
                    class="flex flex-col gap-4 border-b border-gray-200 px-4 py-4 sm:px-5 lg:flex-row lg:items-center lg:justify-between dark:border-gray-800">
                    <div class="flex-shrink-0">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Delivery Activities
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Track your recent shipping activities
                        </p>
                    </div>
                    <div class="flex flex-col gap-4 sm:flex-row lg:items-center">
                        <div
                            class="inline-flex h-11 w-full flex-1 gap-0.5 overflow-x-auto rounded-lg bg-gray-100 p-0.5 sm:w-auto lg:min-w-fit dark:bg-gray-900">
                            <button
                                class="h-10 flex-1 rounded-md px-2 py-2 text-xs font-medium sm:px-3 sm:text-sm lg:flex-initial shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800">
                                All
                            </button>
                            <button
                                class="h-10 flex-1 rounded-md px-2 py-2 text-xs font-medium sm:px-3 sm:text-sm lg:flex-initial text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Delivered
                            </button>
                            <button
                                class="h-10 flex-1 rounded-md px-2 py-2 text-xs font-medium sm:px-3 sm:text-sm lg:flex-initial text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                In-Transit
                            </button>
                            <button
                                class="h-10 flex-1 rounded-md px-2 py-2 text-xs font-medium sm:px-3 sm:text-sm lg:flex-initial text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Pending
                            </button>
                            <button
                                class="h-10 flex-1 rounded-md px-2 py-2 text-xs font-medium sm:px-3 sm:text-sm lg:flex-initial text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Processing
                            </button>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row flex-1sm:items-center lg:gap-4">
                            <div class="relative">
                                <button
                                    class="shadow-theme-xs flex h-11 w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 sm:w-auto sm:min-w-[100px] dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                                    type="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                                        fill="none">
                                        <path
                                            d="M14.6537 5.90414C14.6537 4.48433 13.5027 3.33331 12.0829 3.33331C10.6631 3.33331 9.51206 4.48433 9.51204 5.90415M14.6537 5.90414C14.6537 7.32398 13.5027 8.47498 12.0829 8.47498C10.663 8.47498 9.51204 7.32398 9.51204 5.90415M14.6537 5.90414L17.7087 5.90411M9.51204 5.90415L2.29199 5.90411M5.34694 14.0958C5.34694 12.676 6.49794 11.525 7.91777 11.525C9.33761 11.525 10.4886 12.676 10.4886 14.0958M5.34694 14.0958C5.34694 15.5156 6.49794 16.6666 7.91778 16.6666C9.33761 16.6666 10.4886 15.5156 10.4886 14.0958M5.34694 14.0958L2.29199 14.0958M10.4886 14.0958L17.7087 14.0958"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                    Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="custom-scrollbar overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="border-b border-gray-200 dark:divide-gray-800 dark:border-gray-800">
                                <th class="p-4">
                                    <div class="flex w-full cursor-pointer items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <label
                                                class="flex cursor-pointer items-center text-sm font-medium text-gray-700 select-none dark:text-gray-400"><span
                                                    class="relative"><input type="checkbox" class="sr-only" /><span
                                                        class="bg-transparent border-gray-300 dark:border-gray-700 flex h-4 w-4 items-center justify-center rounded-sm border-[1.25px]"><span
                                                            class="opacity-0"><svg width="12" height="12"
                                                                viewBox="0 0 12 12" fill="none">
                                                                <path d="M10 3L4.5 8.5L2 6" stroke="white"
                                                                    stroke-width="1.6666" stroke-linecap="round"
                                                                    stroke-linejoin="round"></path>
                                                            </svg></span></span></span></label>
                                            <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">
                                                Order ID
                                            </p>
                                        </div>
                                    </div>
                                </th>
                                <th
                                    class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <p class="text-theme-xs font-medium">
                                            Category
                                        </p>
                                        <span class="flex flex-col gap-0.5"><svg class="text-gray-300" width="8"
                                                height="5">
                                                <path
                                                    d="M4.40962 0.585167C4.21057 0.300808 3.78943 0.300807 3.59038 0.585166L1.05071 4.21327C0.81874 4.54466 1.05582 5 1.46033 5H6.53967C6.94418 5 7.18126 4.54466 6.94929 4.21327L4.40962 0.585167Z"
                                                    fill="currentColor"></path>
                                            </svg><svg class="text-gray-300" width="8" height="5">
                                                <path
                                                    d="M4.40962 4.41483C4.21057 4.69919 3.78943 4.69919 3.59038 4.41483L1.05071 0.786732C0.81874 0.455343 1.05582 0 1.46033 0H6.53967C6.94418 0 7.18126 0.455342 6.94929 0.786731L4.40962 4.41483Z"
                                                    fill="currentColor"></path>
                                            </svg></span>
                                    </div>
                                </th>
                                <th
                                    class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <p class="text-theme-xs font-medium">
                                            Company
                                        </p>
                                        <span class="flex flex-col gap-0.5"><svg class="text-gray-300" width="8"
                                                height="5">
                                                <path
                                                    d="M4.40962 0.585167C4.21057 0.300808 3.78943 0.300807 3.59038 0.585166L1.05071 4.21327C0.81874 4.54466 1.05582 5 1.46033 5H6.53967C6.94418 5 7.18126 4.54466 6.94929 4.21327L4.40962 0.585167Z"
                                                    fill="currentColor"></path>
                                            </svg><svg class="text-gray-300" width="8" height="5">
                                                <path
                                                    d="M4.40962 4.41483C4.21057 4.69919 3.78943 4.69919 3.59038 4.41483L1.05071 0.786732C0.81874 0.455343 1.05582 0 1.46033 0H6.53967C6.94418 0 7.18126 0.455342 6.94929 0.786731L4.40962 4.41483Z"
                                                    fill="currentColor"></path>
                                            </svg></span>
                                    </div>
                                </th>
                                <th
                                    class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <p class="text-theme-xs font-medium">
                                            Arrival Time
                                        </p>
                                        <span class="flex flex-col gap-0.5"><svg class="text-gray-300" width="8"
                                                height="5">
                                                <path
                                                    d="M4.40962 0.585167C4.21057 0.300808 3.78943 0.300807 3.59038 0.585166L1.05071 4.21327C0.81874 4.54466 1.05582 5 1.46033 5H6.53967C6.94418 5 7.18126 4.54466 6.94929 4.21327L4.40962 0.585167Z"
                                                    fill="currentColor"></path>
                                            </svg><svg class="text-gray-300" width="8" height="5">
                                                <path
                                                    d="M4.40962 4.41483C4.21057 4.69919 3.78943 4.69919 3.59038 4.41483L1.05071 0.786732C0.81874 0.455343 1.05582 0 1.46033 0H6.53967C6.94418 0 7.18126 0.455342 6.94929 0.786731L4.40962 4.41483Z"
                                                    fill="currentColor"></path>
                                            </svg></span>
                                    </div>
                                </th>
                                <th class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                    Route
                                </th>
                                <th class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                    Price
                                </th>
                                <th class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-x divide-y divide-gray-200 dark:divide-gray-800">
                            <tr v-for="activity in deliveryActivities" :key="activity.id"
                                class="transition hover:bg-gray-50 dark:hover:bg-gray-900">
                                <td class="p-4 whitespace-nowrap">
                                    <div class="group flex items-center gap-3">
                                        <label
                                            class="flex cursor-pointer items-center text-sm font-medium text-gray-700 select-none dark:text-gray-400"><span
                                                class="relative"><input type="checkbox" class="sr-only" /><span
                                                    class="bg-transparent border-gray-300 dark:border-gray-700 flex h-4 w-4 items-center justify-center rounded-sm border-[1.25px]"><span
                                                        class="opacity-0"><svg width="12" height="12"
                                                            viewBox="0 0 12 12" fill="none">
                                                            <path d="M10 3L4.5 8.5L2 6" stroke="white"
                                                                stroke-width="1.6666" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </svg></span></span></span></label>
                                        <span class="text-theme-xs font-medium text-gray-700 dark:text-gray-400">{{
                                            activity.id }}</span>
                                    </div>
                                </td>
                                <td class="p-4 text-sm font-normal whitespace-nowrap text-gray-800 dark:text-white/90">
                                    {{ activity.category }}
                                </td>
                                <td class="p-4 text-sm font-normal whitespace-nowrap text-gray-700 dark:text-white/90">
                                    {{ activity.company }}
                                </td>
                                <td class="p-4 text-sm font-normal whitespace-nowrap text-gray-700 dark:text-white/90">
                                    {{ activity.arrivalTime }}
                                </td>
                                <td class="p-4 text-sm font-normal whitespace-nowrap text-gray-700 dark:text-white/90">
                                    {{ activity.route }}
                                </td>
                                <td class="p-4 text-sm font-normal whitespace-nowrap text-gray-700 dark:text-white/90">
                                    {{ activity.price }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <span class="text-theme-xs rounded-full px-2 py-0.5 font-medium" :class="{
                                        'bg-success-50 dark:bg-success-500/15 text-success-700 dark:text-success-500':
                                            activity.status === 'Delivered',
                                        'bg-red-50 dark:bg-red-500/15 text-red-600 dark:text-red-500':
                                            activity.status === 'Canceled',
                                        'bg-warning-50 dark:bg-warning-500/15 text-warning-600 dark:text-warning-400':
                                            activity.status ===
                                            'In-Transit',
                                    }">{{ activity.status }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    class="flex items-center flex-col sm:flex-row justify-between border-t border-gray-200 px-5 py-4 dark:border-gray-800">
                    <div class="pb-3 sm:pb-0">
                        <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">Showing
                            <span class="text-gray-800 dark:text-white/90">1</span>
                            to
                            <span class="text-gray-800 dark:text-white/90">5</span>
                            of
                            <span class="text-gray-800 dark:text-white/90">7</span></span>
                    </div>
                    <div
                        class="flex items-center bg-gray-50 dark:bg-white/[0.03] dark:sm:bg-transparent sm:bg-transparent p-4 sm:p-0 w-full sm:w-auto rounded-lg justify-between gap-2 sm:justify-normal">
                        <button disabled
                            class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 hover:text-gray-800 disabled:opacity-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                            <span><svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M2.58203 9.99868C2.58174 10.1909 2.6549 10.3833 2.80152 10.53L7.79818 15.5301C8.09097 15.8231 8.56584 15.8233 8.85883 15.5305C9.15183 15.2377 9.152 14.7629 8.85921 14.4699L5.13911 10.7472L16.6665 10.7472C17.0807 10.7472 17.4165 10.4114 17.4165 9.99715C17.4165 9.58294 17.0807 9.24715 16.6665 9.24715L5.14456 9.24715L8.85919 5.53016C9.15199 5.23717 9.15184 4.7623 8.85885 4.4695C8.56587 4.1767 8.09099 4.17685 7.79819 4.46984L2.84069 9.43049C2.68224 9.568 2.58203 9.77087 2.58203 9.99715C2.58203 9.99766 2.58203 9.99817 2.58203 9.99868Z"
                                        fill=""></path>
                                </svg></span>
                        </button>
                        <span class="block text-sm font-medium text-gray-700 sm:hidden dark:text-gray-400">Page 1 of
                            2</span>
                        <ul class="hidden sm:flex items-center gap-0.5">
                            <li>
                                <a href="#"
                                    class="bg-brand-500 text-white flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium">1</a>
                            </li>
                            <li>
                                <a href="#"
                                    class="text-gray-700 dark:text-gray-400 flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium">2</a>
                            </li>
                        </ul>
                        <button
                            class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 hover:text-gray-800 disabled:opacity-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                            <span><svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M17.4165 9.9986C17.4168 10.1909 17.3437 10.3832 17.197 10.53L12.2004 15.5301C11.9076 15.8231 11.4327 15.8233 11.1397 15.5305C10.8467 15.2377 10.8465 14.7629 11.1393 14.4699L14.8594 10.7472L3.33203 10.7472C2.91782 10.7472 2.58203 10.4114 2.58203 9.99715C2.58203 9.58294 2.91782 9.24715 3.33203 9.24715L14.854 9.24715L11.1393 5.53016C10.8465 5.23717 10.8467 4.7623 11.1397 4.4695C11.4327 4.1767 11.9075 4.17685 12.2003 4.46984L17.1578 9.43049C17.3163 9.568 17.4165 9.77087 17.4165 9.99715C17.4165 9.99763 17.4165 9.99812 17.4165 9.9986Z"
                                        fill=""></path>
                                </svg></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
