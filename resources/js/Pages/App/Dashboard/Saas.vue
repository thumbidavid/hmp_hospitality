<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import { ref } from "vue";
import VueApexCharts from "vue3-apexcharts";

const currentPageTitle = ref("SaaS");

// --- Dummy Data and Chart Configurations ---

// 1. Overview Stats Cards
const overviewStats = ref([
    {
        title: "Total Revenue",
        value: "$200,45.87",
        change: "+2.5%",
        status: "success",
    },
    {
        title: "Active Users",
        value: "9,528",
        change: "+ 9.5%",
        status: "success",
    },
    {
        title: "Customer Lifetime Value",
        value: "$849.54",
        change: "-1.6%",
        status: "error",
    },
    {
        title: "Customer Acquisition Cost",
        value: "9,528",
        change: "+3.5%",
        status: "success",
    },
]);

// 2. Churn Rate & User Growth Cards
const smallInfoCards = ref([
    {
        title: "Churn Rate",
        subtitle: "Downgrade to Free plan",
        value: "4.26%",
        change: "0.31%",
        changeText: "than last Week",
        changeColor: "text-red-500",
        chartOptions: {
            chart: { type: "area", sparkline: { enabled: true } },
            stroke: { curve: "smooth", width: 2 },
            colors: ["#ef4444"],
            fill: {
                type: "gradient",
                gradient: { opacityFrom: 0.6, opacityTo: 0.1 },
            },
            series: [
                {
                    name: "Churn Rate",
                    data: [20, 30, 15, 25, 35, 40, 38, 30, 28],
                },
            ],
            tooltip: { enabled: false },
        },
    },
    {
        title: "User Growth",
        subtitle: "New signups website + mobile",
        value: "3,768",
        change: "+3.85%",
        changeText: "than last Week",
        changeColor: "text-green-600",
        chartOptions: {
            chart: { type: "area", sparkline: { enabled: true } },
            stroke: { curve: "smooth", width: 2 },
            colors: ["#10b981"],
            fill: {
                type: "gradient",
                gradient: { opacityFrom: 0.6, opacityTo: 0.1 },
            },
            series: [
                { name: "User Growth", data: [35, 28, 20, 22, 25, 18, 12, 10] },
            ],
            tooltip: { enabled: false },
        },
    },
]);

// 3. Conversion Funnel Chart
const funnelChart = ref({
    series: [
        { name: "Ad Impressions", data: [44, 55, 41, 67, 22, 43, 21, 49] },
        { name: "Website Session", data: [13, 23, 20, 8, 13, 27, 33, 12] },
        { name: "App Download", data: [11, 17, 15, 15, 21, 14, 15, 13] },
        { name: "New Users", data: [21, 7, 25, 13, 22, 8, 13, 20] },
    ],
    chartOptions: {
        chart: {
            type: "bar",
            height: 315,
            stacked: true,
            toolbar: { show: false },
        },
        plotOptions: {
            bar: { horizontal: true, barHeight: "60%", borderRadius: 5 },
        },
        colors: ["#2a31d8", "#465fff", "#7592ff", "#c2d6ff"],
        dataLabels: { enabled: false },
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
            ],
            labels: {
                style: { colors: "#373d3f", fontFamily: "Outfit, sans-serif" },
            },
        },
        yaxis: {
            labels: {
                style: { colors: "#373d3f", fontFamily: "Outfit, sans-serif" },
            },
        },
        legend: {
            position: "top",
            horizontalAlign: "left",
            fontFamily: "Outfit, sans-serif",
        },
        grid: { borderColor: "#e0e0e0", strokeDashArray: 0 },
    },
});

// 4. Recent Invoices Table
const recentInvoices = ref([
    {
        serialNo: "#DF429",
        closeDate: "April 28, 2016",
        user: "Jenny Wilson",
        amount: "$473.85",
        status: "Complete",
    },
    {
        serialNo: "#HTY274",
        closeDate: "October 30, 2017",
        user: "Wade Warren",
        amount: "$293.01",
        status: "Complete",
    },
    {
        serialNo: "#LKE600",
        closeDate: "May 29, 2017",
        user: "Darlene Robertson",
        amount: "$782.01",
        status: "Pending",
    },
    {
        serialNo: "#HRP447",
        closeDate: "May 20, 2015",
        user: "Arlene McCoy",
        amount: "$202.87",
        status: "Cancelled",
    },
    {
        serialNo: "#WRH647",
        closeDate: "March 13, 2014",
        user: "Bessie Cooper",
        amount: "$490.51",
        status: "Complete",
    },
]);

// 5. Product Performance Chart
const salesChart = ref({
    series: [{ name: "Sales", data: [168, 385, 201, 298, 187, 195, 160] }],
    chartOptions: {
        chart: { type: "bar", height: 200, sparkline: { enabled: true } },
        plotOptions: { bar: { borderRadius: 5, columnWidth: "40%" } },
        colors: ["#465fff"],
        xaxis: {
            categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        },
        tooltip: { enabled: true, x: { show: false } },
    },
});

// 6. Activities Feed
const activities = ref([
    {
        userImage: "/images/user/user-01.jpg",
        userName: "Francisco Grbbs",
        action: "created invoice",
        details: "PQ-4491C",
        timestamp: "Just Now",
        eventType: "New invoice",
        eventIcon: true,
    },
    {
        userImage: "/images/user/user-03.jpg",
        userName: "Courtney Henry",
        action: "created invoice",
        details: "HK-234G",
        timestamp: "15 minutes ago",
    },
    {
        userImage: "/images/user/user-04.jpg",
        userName: "Bessie Cooper",
        action: "created invoice",
        details: "LH-2891C",
        timestamp: "5 months ago",
    },
    {
        userImage: "/images/user/user-05.jpg",
        userName: "Theresa Web",
        action: "created invoice",
        details: "CK-125NH",
        timestamp: "2 weeks ago",
    },
]);
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <PageBreadcrumb :pageTitle="currentPageTitle" />

        <div class="space-y-5 sm:space-y-6">
            <!-- Overview Section -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Overview
                        </h3>
                    </div>
                </div>
                <div
                    class="grid rounded-2xl border border-gray-200 bg-white sm:grid-cols-2 xl:grid-cols-4 dark:border-gray-800 dark:bg-gray-900">
                    <div v-for="(stat, index) in overviewStats" :key="index" class="px-6 py-5" :class="{
                        'border-b sm:border-r xl:border-b-0 dark:border-gray-800':
                            index < 3,
                        'border-b xl:border-r xl:border-b-0 dark:border-gray-800':
                            index === 1,
                        'sm:border-r-0 xl:border-r': index === 2,
                        'border-b-0': index === 3,
                    }">
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ stat.title }}</span>
                        <div class="mt-2 flex items-end gap-3">
                            <h4 class="text-title-xs sm:text-title-sm font-bold text-gray-800 dark:text-white/90">
                                {{ stat.value }}
                            </h4>
                            <div>
                                <span
                                    class="flex items-center gap-1 rounded-full py-0.5 pr-2.5 pl-2 text-sm font-medium"
                                    :class="{
                                        'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500':
                                            stat.status === 'success',
                                        'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500':
                                            stat.status === 'error',
                                    }">{{ stat.change }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="gap-6 space-y-5 sm:space-y-6 xl:grid xl:grid-cols-12 xl:space-y-0">
                <!-- Left Column -->
                <div class="xl:col-span-7 2xl:col-span-8">
                    <div class="space-y-5 sm:space-y-6">
                        <div class="grid sm:gap-6 lg:grid-cols-2">
                            <div v-for="(card, index) in smallInfoCards" :key="index"
                                class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                                <div class="mb-6 flex justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                            {{ card.title }}
                                        </h3>
                                        <p class="text-sm mt-1 text-gray-500 dark:text-gray-400">
                                            {{ card.subtitle }}
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
                                <div class="flex justify-between">
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                                            {{ card.value }}
                                        </h3>
                                        <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">
                                            <span class="mr-1 inline-block" :class="card.changeColor">{{ card.change
                                                }}</span>{{ card.changeText }}
                                        </p>
                                    </div>
                                    <div class="max-w-full">
                                        <VueApexCharts type="area" height="60" width="96" :options="card.chartOptions"
                                            :series="card.chartOptions.series" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Conversion Funnel Card -->
                        <div
                            class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                            <div class="mb-6 flex justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                        Conversion Funnel
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
                            <div class="overflow-x-auto custom-scrollbar pl-2">
                                <div class="-ml-5 min-w-[700px] xl:min-w-full">
                                    <VueApexCharts type="bar" :height="funnelChart.chartOptions.chart
                                            .height
                                        " :options="funnelChart.chartOptions" :series="funnelChart.series" />
                                </div>
                            </div>
                        </div>

                        <!-- Recent Invoices Table -->
                        <div
                            class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                            <div class="px-6 py-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    Recent Invoices
                                </h3>
                            </div>
                            <div class="custom-scrollbar overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-900">
                                            <th
                                                class="px-6 py-4 text-left text-sm font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                Serial No:
                                            </th>
                                            <th
                                                class="px-6 py-4 text-left text-sm font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                Close Date
                                            </th>
                                            <th
                                                class="px-6 py-4 text-left text-sm font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                User
                                            </th>
                                            <th
                                                class="px-6 py-4 text-left text-sm font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                Amount
                                            </th>
                                            <th
                                                class="px-6 py-4 text-left text-sm font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                        <tr v-for="invoice in recentInvoices" :key="invoice.serialNo">
                                            <td
                                                class="px-6 py-4 text-left text-sm whitespace-nowrap text-gray-700 dark:text-gray-400">
                                                {{ invoice.serialNo }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-left text-sm whitespace-nowrap text-gray-700 dark:text-gray-400">
                                                {{ invoice.closeDate }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-left text-sm whitespace-nowrap text-gray-700 dark:text-gray-400">
                                                {{ invoice.user }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-left text-sm whitespace-nowrap text-gray-700 dark:text-gray-400">
                                                {{ invoice.amount }}
                                            </td>
                                            <td class="px-6 py-4 text-left">
                                                <span class="rounded-full px-2 py-0.5 font-medium text-theme-xs" :class="{
                                                    'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500':
                                                        invoice.status ===
                                                        'Complete',
                                                    'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-500':
                                                        invoice.status ===
                                                        'Pending',
                                                    'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500':
                                                        invoice.status ===
                                                        'Cancelled',
                                                }">{{ invoice.status }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-5 sm:space-y-6 xl:col-span-5 2xl:col-span-4">
                    <!-- Product Performance Card -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="mb-6 flex justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    Product Performance
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
                        <div class="flex w-full items-center gap-0.5 rounded-lg bg-gray-100 p-0.5 dark:bg-gray-900">
                            <button
                                class="text-sm w-full rounded-md px-3 py-2 font-medium hover:text-gray-900 dark:hover:text-white shadow-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800">
                                Daily Sales
                            </button>
                            <button
                                class="text-sm w-full rounded-md px-3 py-2 font-medium hover:text-gray-900 dark:hover:text-white text-gray-500 dark:text-gray-400">
                                Online Sales
                            </button>
                            <button
                                class="text-sm w-full rounded-md px-3 py-2 font-medium hover:text-gray-900 dark:hover:text-white text-gray-500 dark:text-gray-400">
                                New Users
                            </button>
                        </div>
                        <div class="mt-4">
                            <div class="space-y-4">
                                <div
                                    class="grid grid-cols-2 justify-between gap-10 divide-x divide-gray-100 rounded-xl border border-gray-100 bg-white py-4 dark:divide-gray-800 dark:border-gray-800 dark:bg-gray-800/[0.03]">
                                    <div class="px-5">
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">Digital
                                            Product</span>
                                        <div class="mt-1 flex items-center gap-2">
                                            <span
                                                class="bg-success-50 dark:bg-success-500/15 text-success-600 inline-flex size-5 items-center justify-center rounded-full"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                    viewBox="0 0 12 12" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M5.56462 1.62411C5.70194 1.47091 5.90136 1.37451 6.12329 1.37451C6.1236 1.37451 6.1239 1.37451 6.12421 1.37451C6.3163 1.37434 6.50845 1.4475 6.65505 1.594L9.65514 4.59199C9.94814 4.88478 9.94831 5.35966 9.65552 5.65265C9.36272 5.94565 8.88785 5.94581 8.59486 5.65302L6.87329 3.93267L6.87329 10.1252C6.87329 10.5394 6.53751 10.8752 6.12329 10.8752C5.70908 10.8752 5.37329 10.5394 5.37329 10.1252L5.37329 3.93597L3.65516 5.65301C3.36218 5.94581 2.8873 5.94566 2.5945 5.65267C2.3017 5.35968 2.30185 4.88481 2.59484 4.59201L5.56462 1.62411Z"
                                                        fill="currentColor"></path>
                                                </svg></span>
                                            <h4 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                                                790
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="px-5">
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">Physical
                                            Product</span>
                                        <div class="mt-1 flex items-center gap-2">
                                            <span
                                                class="bg-error-50 dark:bg-error-500/15 text-error-600 inline-flex size-5 items-center justify-center rounded-full"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                    viewBox="0 0 12 12" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257C5.8736 10.6257 5.8739 10.6257 5.87421 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753L6.62329 1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875L5.12329 8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z"
                                                        fill="currentColor"></path>
                                                </svg></span>
                                            <h4 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                                                572
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-gray-100 px-5 py-4 dark:border-gray-800">
                                    <div class="mb-3 flex items-start justify-between">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Average
                                                Daily Sales</span>
                                            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
                                                $2,950
                                            </h3>
                                        </div>
                                        <div>
                                            <span
                                                class="bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500 flex items-center gap-1 rounded-full py-0.5 pr-2.5 pl-2 text-sm font-medium"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                    viewBox="0 0 12 12" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257C5.8736 10.6257 5.8739 10.6257 5.87421 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753L6.62329 1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875L5.12329 8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                                0.52%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <div class="-ml-3">
                                            <VueApexCharts type="bar" :height="salesChart.chartOptions
                                                    .chart.height
                                                " :options="salesChart.chartOptions
                                                    " :series="salesChart.series" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activities Card -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="mb-6 flex justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    Activities
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
                        <div class="relative">
                            <div class="absolute top-6 bottom-10 left-5 w-px bg-gray-200 dark:bg-gray-800"></div>
                            <div v-for="(activity, index) in activities" :key="index" class="relative flex" :class="{
                                'mb-6': index < activities.length - 1,
                            }">
                                <div class="z-10 flex-shrink-0">
                                    <img :src="activity.userImage" :alt="activity.userName"
                                        class="size-10 rounded-full object-cover ring-4 ring-white dark:ring-gray-800" />
                                </div>
                                <div class="ml-4">
                                    <div v-if="activity.eventIcon" class="mb-1 flex items-center gap-1">
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9 5.0625H14.0625L12.5827 8.35084C12.4506 8.64443 12.4506 8.98057 12.5827 9.27416L14.0625 12.5625H10.125C9.50368 12.5625 9 12.0588 9 11.4375V10.875M3.9375 10.875H9M3.9375 3.375H7.875C8.49632 3.375 9 3.87868 9 4.5V10.875M3.9375 15.9375V2.0625"
                                                stroke="#12B76A" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round"></path>
                                        </svg>
                                        <p class="text-theme-xs text-success-500 font-medium">
                                            {{ activity.eventType }}
                                        </p>
                                    </div>
                                    <div class="flex items-baseline">
                                        <h3 class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
                                            {{ activity.userName }}
                                        </h3>
                                        <span class="text-theme-sm ml-2 font-normal text-gray-500 dark:text-gray-400">{{
                                            activity.action }}</span>
                                    </div>
                                    <p class="text-theme-sm font-normal text-gray-500 dark:text-gray-400">
                                        {{ activity.details }}
                                    </p>
                                    <p class="text-theme-xs mt-1 text-gray-400">
                                        {{ activity.timestamp }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
