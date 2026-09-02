<script setup>
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import PageBreadcrumb from '@/Components/Admin/common/PageBreadcrumb.vue'
import { ref, computed } from "vue";
import VueApexCharts from "vue3-apexcharts";

const props = defineProps({
    metrics: {
        type: Object,
        default: () => ({
            total_active_events: 0,
            total_news_coverage: 0,
            total_venues: 0,
            ticket_amount_sold: 0,
            pending_sales: 0,
            total_tickets_distributed: 0,
            global_check_in_ratio: 0,
            popular_ticket_tiers: [],
            most_read_articles: [],
            user_role_distribution: [],
            recent_orders: [],
            scoped_event_data: null
        })
    },
    ticketed_events: {
        type: Array,
        default: () => []
    }
});

const currentPageTitle = ref("Dashboard Overview");
const selectedEventFilter = ref("");

// Helper to filter dashboard statistics by event
const filterDashboardByEvent = () => {
    router.reload({
        data: { event_id: selectedEventFilter.value },
        only: ['metrics'],
        preserveState: true
    });
};

// Formatting currencies cleanly
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-KE', { style: 'currency', currency: 'KES' }).format(amount);
};

// 1. Dynamic Overview Stats Cards
const overviewStats = computed(() => [
    {
        title: "Ticket Amount Sold (Revenue)",
        value: formatCurrency(props.metrics.ticket_amount_sold),
        change: "Active Sales",
        status: "success",
    },
    {
        title: "Active Events",
        value: String(props.metrics.total_active_events),
        change: "On Directory",
        status: "success",
    },
    {
        title: "Total News Coverage",
        value: String(props.metrics.total_news_coverage),
        change: "Published Updates",
        status: "success",
    },
    {
        title: "Total Tickets Distributed",
        value: String(props.metrics.total_tickets_distributed),
        change: "Distributed Passes",
        status: "success",
    },
]);

// 2. Churn Rate (Pending) & User Growth (Check-In Ratio) Cards
const smallInfoCards = computed(() => [
    {
        title: "Pending Sales (Outstanding)",
        subtitle: "Awaiting Manual Verification",
        value: formatCurrency(props.metrics.pending_sales),
        change: "Pending Orders",
        changeText: "awaiting admin action",
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
                    name: "Pending Sales",
                    data: [15, 25, 35, 40, 38, 30, 28],
                },
            ],
            tooltip: { enabled: false },
        },
    },
    {
        title: "Check-In Conversion Ratio",
        subtitle: "Present Attendees at Gates",
        value: `${props.metrics.global_check_in_ratio}%`,
        change: "Presence Rate",
        changeText: "of total tickets distributed",
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
                { name: "Arrivals", data: [12, 18, 22, 25, 30, 35, 40] },
            ],
            tooltip: { enabled: false },
        },
    },
]);

// 3. User Role Distribution Funnel Chart (Dynamic)
const funnelChart = computed(() => {
    const roles = props.metrics.user_role_distribution.map(r => r.role_name);
    const counts = props.metrics.user_role_distribution.map(r => r.user_count);

    return {
        series: [
            { name: "Users", data: counts }
        ],
        chartOptions: {
            chart: {
                type: "bar",
                height: 315,
                stacked: true,
                toolbar: { show: false },
            },
            plotOptions: {
                bar: { horizontal: true, barHeight: "50%", borderRadius: 5 },
            },
            colors: ["#465fff"],
            dataLabels: { enabled: true },
            xaxis: {
                categories: roles,
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
    };
});

// 4. Most Read Articles Performance Chart (Dynamic)
const salesChart = computed(() => {
    const titles = props.metrics.most_read_articles.map(a => a.title.substring(0, 10) + '...');
    const views = props.metrics.most_read_articles.map(a => a.view_count);

    return {
        series: [{ name: "Views", data: views }],
        chartOptions: {
            chart: { type: "bar", height: 200, sparkline: { enabled: true } },
            plotOptions: { bar: { borderRadius: 5, columnWidth: "40%" } },
            colors: ["#465fff"],
            xaxis: {
                categories: titles,
            },
            tooltip: { enabled: true, x: { show: true } },
        },
    };
});

// 5. System Highlights Feed (Replaces static activities)
const activities = computed(() => [
    {
        userImage: "/images/user/user-01.jpg",
        userName: "System Monitor",
        action: "scanned total venues successfully",
        details: `${props.metrics.total_venues} venues currently mapped`,
        timestamp: "Active",
        eventType: "Database",
        eventIcon: true,
    },
    {
        userImage: "/images/user/user-03.jpg",
        userName: "Ticketing Engine",
        action: "recorded successful orders",
        details: `${props.metrics.total_tickets_distributed} tickets issued`,
        timestamp: "Active",
    }
]);
</script>

<template>

    <Head title="System Dashboard" />

    <AuthenticatedLayout>
        <PageBreadcrumb :pageTitle="currentPageTitle" />

        <div class="space-y-5 sm:space-y-6">

            <!-- Event Scoping Filter Header -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">Filter Dashboard by Event</h3>
                        <p class="text-xs text-gray-400 mt-1">Scope the ticketing conversion and gate arrival metrics
                            dynamically.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <select v-model="selectedEventFilter" @change="filterDashboardByEvent"
                            class="rounded-xl border border-gray-200 text-sm bg-white dark:bg-gray-900 dark:border-gray-800 dark:text-white px-4 py-2">
                            <option value="">Global (All Events)</option>
                            <option v-for="event in ticketed_events" :key="event.id" :value="event.id">
                                {{ event.title }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

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
                                    class="flex items-center gap-1 rounded-full py-0.5 pr-2.5 pl-2 text-sm font-medium bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">{{
                                        stat.change }}</span>
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

                        <!-- User Role Distribution Funnel Card -->
                        <div
                            class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                            <div class="mb-6 flex justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                        User Distribution by Role
                                    </h3>
                                </div>
                            </div>
                            <div class="overflow-x-auto custom-scrollbar pl-2">
                                <div class="-ml-5 min-w-[700px] xl:min-w-full">
                                    <VueApexCharts type="bar" :height="funnelChart.chartOptions.chart.height"
                                        :options="funnelChart.chartOptions" :series="funnelChart.series" />
                                </div>
                            </div>
                        </div>

                        <!-- Recent Invoices Table (Dynamic Orders) -->
                        <div
                            class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                            <div class="px-6 py-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    Recent Ticket Orders
                                </h3>
                            </div>
                            <div class="custom-scrollbar overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-900">
                                            <th
                                                class="px-6 py-4 text-left text-sm font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                Order Ref
                                            </th>
                                            <th
                                                class="px-6 py-4 text-left text-sm font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                Order Date
                                            </th>
                                            <th
                                                class="px-6 py-4 text-left text-sm font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                Attendee Email
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
                                        <tr v-for="invoice in metrics.recent_orders" :key="invoice.reference">
                                            <td
                                                class="px-6 py-4 text-left text-sm whitespace-nowrap text-gray-700 dark:text-gray-400 font-bold">
                                                {{ invoice.reference }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-left text-sm whitespace-nowrap text-gray-700 dark:text-gray-400">
                                                {{ invoice.close_date }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-left text-sm whitespace-nowrap text-gray-700 dark:text-gray-400">
                                                {{ invoice.user }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-left text-sm whitespace-nowrap text-gray-700 dark:text-gray-400 font-bold">
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
                    <!-- Article View Performance Card -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="mb-6 flex justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    News Performance
                                </h3>
                            </div>
                        </div>
                        <div class="flex w-full items-center gap-0.5 rounded-lg bg-gray-100 p-0.5 dark:bg-gray-900">
                            <button
                                class="text-sm w-full rounded-md px-3 py-2 font-medium bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm">
                                Article Reads
                            </button>
                        </div>
                        <div class="mt-4">
                            <div class="space-y-4">
                                <div
                                    class="grid grid-cols-2 justify-between gap-10 divide-x divide-gray-100 rounded-xl border border-gray-100 bg-white py-4 dark:divide-gray-800 dark:border-gray-800 dark:bg-gray-800/[0.03]">
                                    <div class="px-5">
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">Total Venues</span>
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
                                                {{ metrics.total_venues }}
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="px-5">
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">Total
                                            Articles</span>
                                        <div class="mt-1 flex items-center gap-2">
                                            <h4 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                                                {{ metrics.total_news_coverage }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-gray-100 px-5 py-4 dark:border-gray-800">
                                    <div class="mb-3 flex items-start justify-between">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Trending
                                                Reads</span>
                                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90 mt-1">
                                                Top Editorial Articles
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <div class="-ml-3">
                                            <VueApexCharts type="bar" :height="salesChart.chartOptions.chart.height"
                                                :options="salesChart.chartOptions" :series="salesChart.series" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activities Card (System Logs) -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="mb-6 flex justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                    System Alerts
                                </h3>
                            </div>
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