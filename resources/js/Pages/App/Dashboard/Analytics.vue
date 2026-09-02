<script setup>
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import { ref } from "vue";
import VueApexCharts from "vue3-apexcharts";
import CustomerDemographics from "@/Components/Admin/dashboard/CustomerDemographics.vue";

const currentPageTitle = ref("Analytics");

// --- Dummy Data and Chart Configurations ---

// 1. Top Stat Cards
const statCards = ref([
    {
        title: "Unique Visitors",
        value: "24.7K",
        change: "+20%",
        status: "success",
    },
    {
        title: "Total Pageviews",
        value: "55.9K",
        change: "+4%",
        status: "success",
    },
    { title: "Bounce Rate", value: "54%", change: "-1.59%", status: "error" },
    {
        title: "Visit Duration",
        value: "2m 56s",
        change: "+7%",
        status: "success",
    },
]);

// 2. Main Analytics Chart (chartFour)
const mainAnalyticsChart = ref({
    series: [
        {
            name: "Sales",
            data: [
                168, 385, 201, 298, 187, 195, 291, 110, 115, 123, 221, 288, 187,
                195, 291, 110, 115, 123, 221, 288, 168, 385, 201, 298, 187, 195,
                291, 110, 115, 123,
            ],
        },
    ],
    chartOptions: {
        chart: { type: "bar", height: 350, toolbar: { show: false } },
        plotOptions: {
            bar: {
                borderRadius: 5,
                columnWidth: "40%",
                dataLabels: { position: "top" },
            },
        },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 4, colors: ["transparent"] },
        xaxis: {
            categories: [
                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18,
                19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
            ],
            labels: {
                style: { colors: "#373d3f", fontFamily: "Outfit, sans-serif" },
            },
        },
        yaxis: {
            labels: {
                formatter: (val) => val,
                style: { colors: "#373d3f", fontFamily: "Outfit, sans-serif" },
            },
        },
        grid: { borderColor: "#e0e0e0" },
        colors: ["#465fff"],
    },
});

// 3. Active Users Chart (chartFive)
const activeUsersChart = ref({
    series: [
        {
            name: "Active Users",
            data: [
                22, 28, 25, 32, 28, 40, 35, 48, 42, 58, 52, 69, 65, 79, 72, 85,
                80, 95,
            ],
        },
    ],
    chartOptions: {
        chart: { type: "area", sparkline: { enabled: true } },
        stroke: { curve: "smooth", width: 2 },
        colors: ["#465fff"],
        fill: {
            type: "gradient",
            gradient: { opacityFrom: 0.55, opacityTo: 0 },
        },
        tooltip: { enabled: false },
    },
});

// 4. Acquisition Channels Chart (chartSix)
const acquisitionChart = ref({
    series: [
        { name: "Direct", data: [44, 55, 41, 67, 22, 43, 21, 49] },
        { name: "Referral", data: [13, 23, 20, 8, 13, 27, 33, 12] },
        { name: "Organic Search", data: [11, 17, 15, 15, 21, 14, 15, 13] },
        { name: "Social", data: [21, 7, 25, 13, 22, 8, 13, 20] },
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
            fontFamily: "Outfit",
        },
        grid: { borderColor: "#e0e0e0" },
    },
});

// 5. Sessions by Device Chart (chartSeven)
const deviceSessionsChart = ref({
    series: [45, 65, 25],
    chartOptions: {
        chart: { type: "donut", height: 286 },
        labels: ["Desktop", "Mobile", "Tablet"],
        colors: ["#3641f5", "#7592ff", "#dde9ff"],
        legend: { show: true, position: "bottom", fontFamily: "Outfit" },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: "75%",
                    labels: {
                        show: false,
                    },
                },
            },
        },
    },
});

// 6. Top Lists Data
const topChannels = ref([
    { source: "Google", visitors: "4.7K" },
    { source: "Facebook", visitors: "3.4K" },
    { source: "Threads", visitors: "2.9K" },
    { source: "Google", visitors: "1.5K" }, // Assuming duplicate for example
]);
const topPages = ref([
    { url: "tailadmin.com", pageviews: "4.7K" },
    { url: "preview.tailadmin.com", pageviews: "3.4K" },
    { url: "docs.tailadmin.com", pageviews: "2.9K" },
    { url: "tailadmin.com/components", pageviews: "1.5K" },
]);

// 7. Recent Orders Table
const recentOrders = ref([
    {
        product: "TailGrids",
        category: "UI Kit",
        country: "USA",
        countryImg: "/images/country/country-01.svg",
        cr: "Dashboard",
        value: "$12,499",
    },
    {
        product: "GrayGrids",
        category: "Templates",
        country: "UK",
        countryImg: "/images/country/country-03.svg",
        cr: "Dashboard",
        value: "$5,498",
    },
    {
        product: "Uideck",
        category: "Templates",
        country: "Canada",
        countryImg: "/images/country/country-04.svg",
        cr: "Dashboard",
        value: "$4,521",
    },
    {
        product: "FormBold",
        category: "SaaS",
        country: "Australia",
        countryImg: "/images/country/country-05.svg",
        cr: "Dashboard",
        value: "$13,843",
    },
    {
        product: "NextAdmin",
        category: "Dashboard",
        country: "Germany",
        countryImg: "/images/country/country-06.svg",
        cr: "Dashboard",
        value: "$7,523",
    },
    {
        product: "Form Builder",
        category: "SaaS",
        country: "France",
        countryImg: "/images/country/country-07.svg",
        cr: "Dashboard",
        value: "$1,377",
    },
    {
        product: "AyroUI",
        category: "UI Kit",
        country: "Japan",
        countryImg: "/images/country/country-08.svg",
        cr: "Dashboard",
        value: "$599,00",
    },
]);
</script>

<template>

    <Head title="Analytics" />

    <AuthenticatedLayout>
        <PageBreadcrumb :pageTitle="currentPageTitle" />
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <!-- Top Stat Cards -->
            <div class="col-span-12">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 xl:grid-cols-4">
                    <div v-for="(card, index) in statCards" :key="index"
                        class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                            {{ card.title }}
                        </p>
                        <div class="flex items-end justify-between mt-3">
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                    {{ card.value }}
                                </h4>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="flex items-center gap-1 rounded-full px-2 py-0.5 text-theme-xs font-medium"
                                    :class="card.status === 'success'
                                        ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500'
                                        : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500'
                                        ">{{ card.change }}</span>
                                <span class="text-gray-500 text-theme-xs dark:text-gray-400">Vs last month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Analytics Chart -->
            <div class="col-span-12">
                <div
                    class="rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
                    <div class="flex flex-wrap items-start justify-between gap-5">
                        <div>
                            <h3 class="mb-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                                Analytics
                            </h3>
                            <span class="block text-gray-500 text-theme-sm dark:text-gray-400">Visitor analytics of last
                                30 days</span>
                        </div>
                        <div class="flex items-center gap-0.5 rounded-lg bg-gray-100 p-0.5 dark:bg-gray-900">
                            <button
                                class="px-3 py-2 font-medium rounded-md text-theme-sm shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800">
                                12 months
                            </button>
                            <button
                                class="px-3 py-2 font-medium rounded-md text-theme-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                30 days
                            </button>
                            <button
                                class="px-3 py-2 font-medium rounded-md text-theme-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                7 days
                            </button>
                            <button
                                class="px-3 py-2 font-medium rounded-md text-theme-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                24 hours
                            </button>
                        </div>
                    </div>
                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <div class="-ml-5 min-w-[1300px] xl:min-w-full pl-2">
                            <VueApexCharts type="bar" :height="mainAnalyticsChart.chartOptions.chart.height
                                " :options="mainAnalyticsChart.chartOptions" :series="mainAnalyticsChart.series" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendor Cards -->
            <div class="col-span-12 xl:col-span-7">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Vendor Card 1 -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-0 dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
                        <!-- Top: Vendor Image -->
                        <div class="h-60 w-full bg-gray-100 dark:bg-gray-800">
                            <img src="https://plus.unsplash.com/premium_photo-1686981883168-2a087795cc48?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170"
                                alt="Vendor Image" class="h-full w-full object-cover" />
                        </div>

                        <!-- Bottom: Vendor Info -->
                        <div class="p-5 md:p-6">
                            <div class="flex items-start justify-between">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    Sunshine Events Co.
                                </h3>
                                <button class="text-gray-500 dark:text-gray-400">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="my-6">
                                <div
                                    class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
                                    <span class="text-gray-400 text-theme-xs">Category</span>
                                    <span class="text-right text-gray-400 text-theme-xs">Rating</span>
                                </div>
                                <div
                                    class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                    <span class="text-gray-500 text-theme-sm dark:text-gray-400">Event Planning</span>
                                    <span class="text-right text-gray-500 text-theme-sm dark:text-gray-400">4.8 ★</span>
                                </div>
                                <div
                                    class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                    <span class="text-gray-500 text-theme-sm dark:text-gray-400">Location</span>
                                    <span
                                        class="text-right text-gray-500 text-theme-sm dark:text-gray-400">Nairobi</span>
                                </div>
                            </div>

                            <a href="#"
                                class="flex justify-center gap-2 rounded-lg border border-gray-300 bg-white p-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">View
                                Vendor Profile
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                                        fill=""></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Vendor Card 2 -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-0 dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
                        <!-- Top: Vendor Image -->
                        <div class="h-60 w-full bg-gray-100 dark:bg-gray-800">
                            <img src="https://plus.unsplash.com/premium_photo-1686981883168-2a087795cc48?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170"
                                alt="Vendor Image" class="h-full w-full object-cover" />
                        </div>

                        <!-- Bottom: Vendor Info -->
                        <div class="p-5 md:p-6">
                            <div class="flex items-start justify-between">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    Elite Catering Services
                                </h3>
                                <button class="text-gray-500 dark:text-gray-400">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="my-6">
                                <div
                                    class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
                                    <span class="text-gray-400 text-theme-xs">Category</span>
                                    <span class="text-right text-gray-400 text-theme-xs">Rating</span>
                                </div>
                                <div
                                    class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                    <span class="text-gray-500 text-theme-sm dark:text-gray-400">Catering</span>
                                    <span class="text-right text-gray-500 text-theme-sm dark:text-gray-400">4.6 ★</span>
                                </div>
                                <div
                                    class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                    <span class="text-gray-500 text-theme-sm dark:text-gray-400">Location</span>
                                    <span
                                        class="text-right text-gray-500 text-theme-sm dark:text-gray-400">Mombasa</span>
                                </div>
                            </div>

                            <a href="#"
                                class="flex justify-center gap-2 rounded-lg border border-gray-300 bg-white p-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">View
                                Vendor Profile
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                                        fill=""></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Channels & Pages -->
            <div class="col-span-12 xl:col-span-7">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                        <div class="flex items-start justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Top Channels
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
                        <div class="my-6">
                            <div
                                class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
                                <span class="text-gray-400 text-theme-xs">Source</span><span
                                    class="text-right text-gray-400 text-theme-xs">Visitors</span>
                            </div>
                            <div v-for="(channel, index) in topChannels" :key="index"
                                class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                <span class="text-gray-500 text-theme-sm dark:text-gray-400">{{ channel.source
                                }}</span><span class="text-right text-gray-500 text-theme-sm dark:text-gray-400">{{
                                        channel.visitors }}</span>
                            </div>
                        </div>
                        <a href="#"
                            class="flex justify-center gap-2 rounded-lg border border-gray-300 bg-white p-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">Channels
                            Report
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                                    fill=""></path>
                            </svg></a>
                    </div>
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                        <div class="flex items-start justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Top Pages
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
                        <div class="my-6">
                            <div
                                class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
                                <span class="text-gray-400 text-theme-xs">Source</span><span
                                    class="text-right text-gray-400 text-theme-xs">Pageview</span>
                            </div>
                            <div v-for="(page, index) in topPages" :key="index"
                                class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                                <span class="text-gray-500 text-theme-sm dark:text-gray-400">{{ page.url }}</span><span
                                    class="text-right text-gray-500 text-theme-sm dark:text-gray-400">{{ page.pageviews
                                    }}</span>
                            </div>
                        </div>
                        <a href="#"
                            class="flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white p-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">Channels
                            Report
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                                    fill=""></path>
                            </svg></a>
                    </div>
                </div>
            </div>

            <!-- Active Users and Device Sessions -->
            <div class="col-span-12 xl:col-span-5">
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <div class="flex items-start justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Active Users
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
                    <div class="mt-6 flex items-end gap-1.5">
                        <div class="flex items-center gap-2.5 relative">
                            <span class="relative inline-block w-5 h-5">
                                <span
                                    class="absolute w-2 h-2 transform -translate-x-1/2 -translate-y-1/2 rounded-full top-1/2 left-1/2 bg-error-500"><span
                                        class="absolute inline-flex w-4 h-4 rounded-full opacity-75 bg-error-400 animate-ping -top-1 -left-1"></span></span>
                            </span>
                            <span class="font-semibold text-gray-800 text-title-sm dark:text-white/90">364</span>
                        </div>
                        <span class="block mb-1 text-gray-500 text-theme-sm dark:text-gray-400">Live visitors</span>
                    </div>
                    <div class="my-5 min-h-[155px] rounded-xl bg-gray-50 dark:bg-gray-900">
                        <div class="-ml-[22px] -mr-2.5 h-full">
                            <VueApexCharts type="area" height="140" :options="activeUsersChart.chartOptions"
                                :series="activeUsersChart.series" />
                        </div>
                    </div>
                    <div class="flex items-center justify-center gap-6">
                        <div>
                            <p class="text-lg font-semibold text-center text-gray-800 dark:text-white/90">
                                224
                            </p>
                            <p class="mt-0.5 text-center text-theme-xs text-gray-500 dark:text-gray-400">
                                Avg, Daily
                            </p>
                        </div>
                        <div class="w-px bg-gray-200 h-11 dark:bg-gray-800"></div>
                        <div>
                            <p class="text-lg font-semibold text-center text-gray-800 dark:text-white/90">
                                1.4K
                            </p>
                            <p class="mt-0.5 text-center text-theme-xs text-gray-500 dark:text-gray-400">
                                Avg, Weekly
                            </p>
                        </div>
                        <div class="w-px bg-gray-200 h-11 dark:bg-gray-800"></div>
                        <div>
                            <p class="text-lg font-semibold text-center text-gray-800 dark:text-white/90">
                                22.1K
                            </p>
                            <p class="mt-0.5 text-center text-theme-xs text-gray-500 dark:text-gray-400">
                                Avg, Monthly
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acquisition Channels -->
            <div class="col-span-12 xl:col-span-7">
                <div
                    class="rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Acquisition Channels
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
                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <div class="-ml-5 min-w-[700px] xl:min-w-full pl-2">
                            <VueApexCharts type="bar" :height="acquisitionChart.chartOptions.chart.height
                                " :options="acquisitionChart.chartOptions" :series="acquisitionChart.series" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sessions By Device -->
            <div class="col-span-12 xl:col-span-5">
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
                    <div class="flex items-start justify-between mb-9">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Sessions By Device
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
                    <div class="flex justify-center mx-auto">
                        <VueApexCharts type="donut" height="286" :options="deviceSessionsChart.chartOptions"
                            :series="deviceSessionsChart.series" />
                    </div>
                </div>
            </div>

            <!-- Demographics -->
            <div class="col-span-12 xl:col-span-5">
                <CustomerDemographics />
            </div>

            <!-- Recent Orders Table -->
            <div class="col-span-12 xl:col-span-7">
                <div
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex flex-col gap-4 px-6 mb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Recent Orders
                            </h3>
                        </div>
                        <div class="flex items-center gap-3">
                            <button
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                <svg class="stroke-current fill-white dark:fill-gray-800" width="20" height="20"
                                    viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.29004 5.90393H17.7067" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path d="M17.7075 14.0961H2.29085" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M12.0826 3.33331C13.5024 3.33331 14.6534 4.48431 14.6534 5.90414C14.6534 7.32398 13.5024 8.47498 12.0826 8.47498C10.6627 8.47498 9.51172 7.32398 9.51172 5.90415C9.51172 4.48432 10.6627 3.33331 12.0826 3.33331Z"
                                        fill="" stroke-width="1.5"></path>
                                    <path
                                        d="M7.91745 11.525C6.49762 11.525 5.34662 12.676 5.34662 14.0959C5.34661 15.5157 6.49762 16.6667 7.91745 16.6667C9.33728 16.6667 10.4883 15.5157 10.4883 14.0959C10.4883 12.676 9.33728 11.525 7.91745 11.525Z"
                                        fill="" stroke-width="1.5"></path>
                                </svg>
                                Filter
                            </button>
                            <button
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                See all
                            </button>
                        </div>
                    </div>
                    <div class="max-w-full overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <th class="px-6 py-3">
                                        <p class="font-medium text-left text-gray-500 text-theme-xs dark:text-gray-400">
                                            Products
                                        </p>
                                    </th>
                                    <th class="px-6 py-3">
                                        <p class="font-medium text-left text-gray-500 text-theme-xs dark:text-gray-400">
                                            Category
                                        </p>
                                    </th>
                                    <th class="px-6 py-3">
                                        <p class="font-medium text-left text-gray-500 text-theme-xs dark:text-gray-400">
                                            Country
                                        </p>
                                    </th>
                                    <th class="px-6 py-3">
                                        <p class="font-medium text-left text-gray-500 text-theme-xs dark:text-gray-400">
                                            CR
                                        </p>
                                    </th>
                                    <th class="px-6 py-3">
                                        <p class="font-medium text-left text-gray-500 text-theme-xs dark:text-gray-400">
                                            Value
                                        </p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr v-for="(order, index) in recentOrders" :key="index">
                                    <td class="px-6 py-3.5">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ order.product }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ order.category }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="w-5 h-5 overflow-hidden rounded-full">
                                            <img :src="order.countryImg" :alt="order.country" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ order.cr }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <p class="text-theme-sm text-success-600">
                                            {{ order.value }}
                                        </p>
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
