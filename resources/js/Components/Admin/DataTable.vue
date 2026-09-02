<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import Papa from 'papaparse'
import * as XLSX from 'xlsx'

// --- PROPS AND EMITS ---
const props = defineProps({
    data: { type: Array, required: true },
    columns: { type: Array, required: true },
    title: { type: String, default: 'Data Records' },
    subTitle: { type: String, default: '' },
    emptyMessage: { type: String, default: 'No records found.' },
})
const emit = defineEmits(['edit-item', 'delete-item'])

// --- CORE STATE ---
const searchQuery = ref('')
const itemsPerPage = ref(5)
const currentPage = ref(1)
const selectedRows = ref([])
const sortKey = ref('')
const sortOrder = ref('asc')
const reactiveColumns = ref(props.columns.map((col) => ({ ...col, visible: true })))

// --- DROPDOWN STATE ---
const columnsDropdownOpen = ref(false)
const exportDropdownOpen = ref(false)
const columnsDropdown = ref(null)
const exportDropdown = ref(null)

// --- COMPUTED PROPERTIES ---
const filteredData = computed(() => {
    if (!searchQuery.value) return props.data
    return props.data.filter((item) =>
        Object.values(item).some((val) =>
            String(val).toLowerCase().includes(searchQuery.value.toLowerCase())
        )
    )
})

const sortedData = computed(() => {
    if (!sortKey.value) return filteredData.value
    return [...filteredData.value].sort((a, b) => {
        const aValue = a[sortKey.value]
        const bValue = b[sortKey.value]
        if (aValue < bValue) return sortOrder.value === 'asc' ? -1 : 1
        if (aValue > bValue) return sortOrder.value === 'asc' ? 1 : -1
        return 0
    })
})

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage.value
    return sortedData.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value))

watch(filteredData, () => {
    currentPage.value = 1
})

watch(totalPages, (value) => {
    if (currentPage.value > value) {
        currentPage.value = Math.max(value, 1)
    }
})

// --- METHODS ---
const sortBy = (key) => {
    if (!key || key === 'action') return
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortKey.value = key
        sortOrder.value = 'asc'
    }
}

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++
}
const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--
}
const goToPage = (page) => {
    currentPage.value = page
}

const onRowClick = (item) => emit('edit-item', item)
const editItem = (item) => emit('edit-item', item)
const deleteItem = (item) => emit('delete-item', item)

const selectAllRows = (event) => {
    selectedRows.value = event.target.checked ? paginatedData.value.map((item) => item.id) : []
}

// --- EXPORT LOGIC ---
const getExportData = () => {
    const visibleCols = reactiveColumns.value.filter((c) => c.visible && c.key !== 'action')
    const headers = visibleCols.map((c) => c.label)
    const keys = visibleCols.map((c) => c.key)
    const data = sortedData.value.map((item) => {
        let row = {}
        keys.forEach((key) => {
            row[key] = item[key]
        })
        return row
    })
    return { headers, data }
}

const exportToCSV = () => {
    const { data } = getExportData()
    const csv = Papa.unparse(data)
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
    const link = document.createElement('a')
    link.href = URL.createObjectURL(blob)
    link.setAttribute('download', 'data.csv')
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

const exportToExcel = () => {
    const { headers, data } = getExportData()
    const worksheet = XLSX.utils.json_to_sheet(data)
    XLSX.utils.sheet_add_aoa(worksheet, [headers], { origin: 'A1' })
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1')
    XLSX.writeFile(workbook, 'data.xlsx')
}

const printTable = () => window.print()

// --- CLICK OUTSIDE LOGIC ---
const handleClickOutside = (event) => {
    if (columnsDropdown.value && !columnsDropdown.value.contains(event.target)) {
        columnsDropdownOpen.value = false
    }
    if (exportDropdown.value && !exportDropdown.value.contains(event.target)) {
        exportDropdownOpen.value = false
    }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>

<style>
/* --- RESPONSIVE & PRINT STYLES --- */
@media print {
    .print-hidden {
        display: none !important;
    }
}

/* On screens smaller than 768px (Tailwind's 'md' breakpoint), transform the table into cards */
@media (max-width: 767px) {
    .responsive-table thead {
        /* Hide the table header on mobile */
        display: none;
    }

    .responsive-table tbody,
    .responsive-table tr {
        /* Make the table body and rows full-width blocks */
        display: block;
        width: 100%;
    }

    .responsive-table tr {
        /* Style each row as a card */
        border: 1px solid #e2e8f0; /* dark:border-gray-700 */
        border-radius: 0.75rem;
        margin-bottom: 1rem;
        padding: 1rem;
    }
    .dark .responsive-table tr {
        border-color: #374151;
    }

    .responsive-table td {
        /* Stack cells vertically */
        display: block;
        width: 100%;
        text-align: right; /* Align cell content to the right */
        padding-left: 50%; /* Create space for the label on the left */
        position: relative;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        border: none; /* Remove default cell borders */
    }

    .responsive-table td:before {
        /* Add the column label before the cell content */
        content: attr(data-label); /* Use the data-label attribute for the content */
        position: absolute;
        left: 0.75rem;
        width: 45%;
        padding-right: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #111827; /* dark:text-white/90 */
    }
    .dark .responsive-table td:before {
        color: rgba(255, 255, 255, 0.9);
    }

    /* Special handling for the first cell (checkbox) and cells without labels */
    .responsive-table td.no-label {
        padding-left: 0.75rem;
    }
    .responsive-table td.no-label:before {
        content: none;
    }

    /* Special handling for the action buttons */
    .responsive-table td.actions-cell {
        display: flex;
        justify-content: flex-end; /* Align buttons to the right */
        align-items: center;
        padding-top: 0.75rem;
    }

    /* Adjust avatar cell to be the first item in the card */
    .responsive-table td.user-cell {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-top: 0;
        padding-bottom: 1rem;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid #e2e8f0; /* dark:border-gray-800 */
    }
    .dark .responsive-table td.user-cell {
        border-color: #1f2937;
    }

    .responsive-table td.user-cell:before {
        display: none; /* Hide the "User:" label as the avatar is self-explanatory */
    }
}
</style>

<template>
    <div
        class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
    >
        <div class="print-hidden flex items-center justify-between px-6 py-5">
            <div class="flex flex-col gap-2">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    {{ title }}
                </h3>
                <p class="text-xs text-gray-800 dark:text-white/90">{{ subTitle }}</p>
            </div>
            <div>
                <slot name="header-actions"></slot>
            </div>
        </div>

        <div class="border-t border-gray-100 p-4 sm:p-6 dark:border-gray-800">
            <div class="space-y-5">
                <div
                    class="overflow-hidden rounded-xl border border-gray-100 bg-white dark:border-white/[0.05] dark:bg-white/[0.03]"
                >
                    <!-- Toolbar -->
                    <div
                        class="print-hidden flex flex-col gap-4 px-4 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <!-- Entries per page -->
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Show</span>
                            <div class="relative z-20">
                                <select
                                    v-model="itemsPerPage"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-9 w-full appearance-none rounded-lg border border-gray-300 bg-transparent py-2 pr-8 pl-3 text-sm focus:ring-3 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                >
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                </select>
                                <span
                                    class="absolute top-1/2 right-2 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400"
                                    ><svg
                                        class="stroke-current"
                                        width="16"
                                        height="16"
                                        viewBox="0 0 16 16"
                                        fill="none"
                                    >
                                        <path
                                            d="M3.8335 5.9165L8.00016 10.0832L12.1668 5.9165"
                                            stroke="currentColor"
                                            stroke-width="1.2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        ></path></svg
                                ></span>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">entries</span>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <!-- Search -->
                            <div class="relative">
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Search..."
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-none xl:w-[300px] dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                /><button
                                    class="absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 dark:text-gray-400"
                                >
                                    <svg
                                        class="fill-current"
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
                                        ></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Actions -->
                            <div ref="columnsDropdown" class="relative">
                                <button
                                    @click="columnsDropdownOpen = !columnsDropdownOpen"
                                    class="shadow-theme-xs flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-[11px] text-sm font-medium text-gray-700 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                                >
                                    Columns
                                    <svg
                                        class="h-4 w-4 transition-transform duration-200"
                                        :class="{
                                            'rotate-180': columnsDropdownOpen,
                                        }"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        ></path>
                                    </svg></button
                                ><Transition
                                    enter-active-class="transition ease-out duration-200"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95"
                                    ><div
                                        v-if="columnsDropdownOpen"
                                        class="absolute right-0 z-10 mt-2 w-56 rounded-lg bg-white p-2 shadow-md dark:border dark:border-gray-700 dark:bg-gray-800"
                                    >
                                        <div
                                            v-for="col in reactiveColumns"
                                            :key="col.key"
                                            @click="col.visible = !col.visible"
                                            class="flex cursor-pointer items-center justify-between gap-x-3.5 rounded-lg px-3 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                                        >
                                            <span
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                >{{ col.label }}</span
                                            ><svg
                                                v-if="col.visible"
                                                class="h-4 w-4 text-blue-600"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="3"
                                                    d="M5 13l4 4L19 7"
                                                ></path>
                                            </svg>
                                        </div></div
                                ></Transition>
                            </div>
                            <div ref="exportDropdown" class="relative">
                                <button
                                    @click="exportDropdownOpen = !exportDropdownOpen"
                                    class="shadow-theme-xs flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-[11px] text-sm font-medium text-gray-700 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                                >
                                    <svg
                                        class="fill-current"
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M14.707 7.707a1 1 0 00-1.414-1.414L10 9.586 6.707 6.293a1 1 0 00-1.414 1.414L8.586 11H4a1 1 0 000 2h12a1 1 0 000-2h-4.586l3.293-3.293zM16 16a1 1 0 01-1 1H5a1 1 0 010-2h10a1 1 0 011 1z"
                                        ></path>
                                    </svg>
                                    Export</button
                                ><Transition
                                    enter-active-class="transition ease-out duration-200"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95"
                                    ><div
                                        v-if="exportDropdownOpen"
                                        class="absolute right-0 z-10 mt-2 min-w-48 rounded-lg bg-white p-2 shadow-md dark:border dark:border-gray-700 dark:bg-gray-800"
                                    >
                                        <button
                                            @click="exportToCSV"
                                            class="flex w-full items-center gap-x-3.5 rounded-lg px-3 py-2 text-left text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                                        >
                                            CSV</button
                                        ><button
                                            @click="exportToExcel"
                                            class="flex w-full items-center gap-x-3.5 rounded-lg px-3 py-2 text-left text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                                        >
                                            Excel</button
                                        ><button
                                            @click="printTable"
                                            class="flex w-full items-center gap-x-3.5 rounded-lg px-3 py-2 text-left text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                                        >
                                            Print
                                        </button>
                                    </div></Transition
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="max-w-full md:overflow-x-auto">
                        <table class="responsive-table min-w-full">
                            <thead>
                                <tr class="border-t border-gray-100 dark:border-white/[0.05]">
                                    <th
                                        class="print-hidden border-b border-gray-100 px-4 py-3 dark:border-gray-800"
                                    >
                                        <div class="flex items-center gap-3">
                                            <input
                                                type="checkbox"
                                                @change="selectAllRows"
                                                class="h-4 w-4 rounded-sm border-gray-300 bg-transparent dark:border-gray-700"
                                            />
                                        </div>
                                    </th>
                                    <th
                                        v-for="col in reactiveColumns"
                                        :key="col.key"
                                        v-show="col.visible"
                                        class="border-b border-gray-100 px-4 py-3 text-left select-none dark:border-gray-800"
                                        :class="{
                                            'cursor-pointer': col.key !== 'action',
                                            'print-hidden': col.key === 'action',
                                        }"
                                        @click="sortBy(col.key)"
                                    >
                                        <div class="flex items-center gap-1">
                                            <p
                                                class="text-theme-xs font-medium text-gray-700 dark:text-gray-400"
                                            >
                                                {{ col.label }}
                                            </p>
                                            <div v-if="col.key !== 'action'" class="h-4 w-4">
                                                <div v-if="sortKey === col.key">
                                                    <svg
                                                        v-if="sortOrder === 'asc'"
                                                        class="h-4 w-4 text-gray-600 dark:text-gray-300"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M5 15l7-7 7 7"
                                                        ></path></svg
                                                    ><svg
                                                        v-else
                                                        class="h-4 w-4 text-gray-600 dark:text-gray-300"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 9l-7 7-7-7"
                                                        ></path>
                                                    </svg>
                                                </div>
                                                <svg
                                                    v-else
                                                    class="h-4 w-4 text-gray-300 dark:text-gray-600"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        d="M8 9l4-4 4 4m0 6l-4 4-4-4"
                                                        stroke-width="1.5"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    ></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>

                            <!-- LOGIC CHANGE STARTS HERE -->
                            <tbody>
                                <tr
                                    v-for="item in paginatedData"
                                    :key="item.id"
                                    @click="onRowClick(item)"
                                    class="border-t border-gray-100 md:cursor-pointer md:hover:bg-gray-50 dark:border-white/[0.5] md:dark:hover:bg-white/[0.05]"
                                >
                                    <!-- Checkbox cell is static, same as before -->
                                    <td
                                        class="print-hidden no-label border-b border-gray-100 px-4 py-3 dark:border-gray-800"
                                        @click.stop
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="selectedRows"
                                            :value="item.id"
                                            class="h-4 w-4 rounded-sm border-gray-300 bg-transparent dark:border-gray-700"
                                        />
                                    </td>

                                    <!-- Loop through columns to create table cells dynamically -->
                                    <td
                                        v-for="col in reactiveColumns.filter((c) => c.visible)"
                                        :key="col.key"
                                        class="border-b border-gray-100 px-4 py-3 dark:border-gray-800"
                                        :data-label="col.label"
                                    >
                                        <!--
                                            Scoped Slot: Allows parent to define custom cell templates.
                                            The slot name is dynamic, e.g., "cell-user", "cell-status".
                                            It passes the current 'item' (the row data) to the parent.
                                        -->
                                        <slot :name="`cell-${col.key}`" :item="item">
                                            <!--
                                                Fallback Content: If no slot is provided by the parent,
                                                this default content will be rendered.
                                            -->
                                            <div
                                                v-if="col.key === 'action'"
                                                class="flex w-full items-center gap-2 md:w-auto"
                                                @click.stop
                                            >
                                                <button
                                                    @click="editItem(item)"
                                                    class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white/90"
                                                >
                                                    <svg
                                                        class="fill-current"
                                                        width="21"
                                                        height="21"
                                                        viewBox="0 0 21 21"
                                                    >
                                                        <path
                                                            d="M17.0911 3.53206C16.2124 2.65338 14.7878 2.65338 13.9091 3.53206L5.6074 11.8337C5.29899 12.1421 5.08687 12.5335 4.99684 12.9603L4.26177 16.445C4.20943 16.6931 4.286 16.9508 4.46529 17.1301C4.64458 17.3094 4.90232 17.3859 5.15042 17.3336L8.63507 16.5985C9.06184 16.5085 9.45324 16.2964 9.76165 15.988L18.0633 7.68631C18.942 6.80763 18.942 5.38301 18.0633 4.50433L17.0911 3.53206ZM14.9697 4.59272C15.2626 4.29982 15.7375 4.29982 16.0304 4.59272L17.0027 5.56499C17.2956 5.85788 17.2956 6.33276 17.0027 6.62565L16.1043 7.52402L14.0714 5.49109L14.9697 4.59272ZM13.0107 6.55175L6.66806 12.8944C6.56526 12.9972 6.49455 13.1277 6.46454 13.2699L5.96704 15.6283L8.32547 15.1308C8.46772 15.1008 8.59819 15.0301 8.70099 14.9273L15.0436 8.58468L13.0107 6.55175Z"
                                                        ></path>
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="deleteItem(item)"
                                                    class="text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-500"
                                                >
                                                    <svg
                                                        class="fill-current"
                                                        width="21"
                                                        height="21"
                                                        viewBox="0 0 21 21"
                                                    >
                                                        <path
                                                            d="M7.04142 4.29199C7.04142 3.04935 8.04878 2.04199 9.29142 2.04199H11.7081C12.9507 2.04199 13.9581 3.04935 13.9581 4.29199V4.54199H17.166C17.5802 4.54199 17.916 4.87778 17.916 5.29199C17.916 5.70621 17.5802 6.04199 17.166 6.04199H3.8335C3.41928 6.04199 3.0835 5.70621 3.0835 5.29199C3.0835 4.87778 3.41928 4.54199 3.8335 4.54199H7.04142V4.29199ZM8.54142 4.54199H12.4581V4.29199C12.4581 3.87778 12.1223 3.54199 11.7081 3.54199H9.29142C8.87721 3.54199 8.54142 3.87778 8.54142 4.29199V4.54199Z M8.8335 8.50033C9.24771 8.50033 9.5835 8.83611 9.5835 9.25033V14.2503C9.5835 14.6645 9.24771 15.0003 8.8335 15.0003C8.41928 15.0003 8.0835 14.6645 8.0835 14.2503V9.25033C8.0835 8.83611 8.41928 8.50033 8.8335 8.50033ZM12.1668 8.50033C11.7526 8.50033 11.4168 8.83611 11.4168 9.25033V14.2503C11.4168 14.6645 11.7526 15.0003 12.1668 15.0003C12.581 15.0003 12.9168 14.6645 12.9168 14.2503V9.25033C12.9168 8.83611 12.581 8.50033 12.1668 8.50033Z"
                                                        ></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <p
                                                v-else
                                                class="text-theme-sm text-gray-700 dark:text-gray-400"
                                            >
                                                {{ item[col.key] }}
                                            </p>
                                        </slot>
                                    </td>
                                </tr>
                                <tr v-if="paginatedData.length === 0">
                                    <td
                                        :colspan="reactiveColumns.filter((c) => c.visible).length + 1"
                                        class="border-b border-gray-100 px-4 py-10 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400"
                                    >
                                        {{ emptyMessage }}
                                    </td>
                                </tr>
                            </tbody>
                            <!-- LOGIC CHANGE ENDS HERE -->
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        class="print-hidden border-t border-gray-100 py-4 pr-4 pl-[18px] dark:border-white/[0.05]"
                    >
                        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between">
                            <p
                                class="border-b border-gray-100 pb-3 text-center text-sm font-medium text-gray-500 xl:border-b-0 xl:pb-0 xl:text-left dark:border-gray-800 dark:text-gray-400"
                            >
                                Showing
                                {{ (currentPage - 1) * itemsPerPage + 1 }} to
                                {{ Math.min(currentPage * itemsPerPage, filteredData.length) }}
                                of {{ filteredData.length }} entries
                            </p>
                            <div
                                class="flex items-center justify-center gap-0.5 pt-4 xl:justify-end xl:pt-0"
                            >
                                <button
                                    @click="prevPage"
                                    :disabled="currentPage === 1"
                                    class="shadow-theme-xs mr-2.5 flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                                >
                                    <svg
                                        class="fill-current"
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                            fill-rule="evenodd"
                                        ></path>
                                    </svg>
                                </button>
                                <button
                                    v-for="page in totalPages"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :class="
                                        currentPage === page
                                            ? 'text-brand-500 bg-blue-500/[0.08]'
                                            : 'hover:text-brand-500 text-gray-700 hover:bg-blue-500/[0.08] dark:text-gray-400'
                                    "
                                    class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium"
                                >
                                    {{ page }}
                                </button>
                                <button
                                    @click="nextPage"
                                    :disabled="currentPage === totalPages"
                                    class="shadow-theme-xs ml-2.5 flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                                >
                                    <svg
                                        class="fill-current"
                                        width="20"
                                        height="20"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"
                                            fill-rule="evenodd"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
