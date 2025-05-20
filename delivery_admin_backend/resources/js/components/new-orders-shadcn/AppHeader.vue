<template>
    <div class="bg-white shadow">
        <div class="w-full px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        New Orders ShadCN
                    </h1>
                    <p class="text-gray-600">
                        Manage your orders with modern components
                    </p>
                </div>
                <!-- Action buttons removed as requested -->
            </div>

            <!-- Modern Filter Pills Section -->
            <div
                class="mt-4 flex flex-wrap gap-4 items-center justify-between mb-4"
            >
                <!-- Order ID Filter Pill -->
                <n-popover
                    trigger="click"
                    placement="bottom"
                    :show-arrow="false"
                >
                    <template #trigger>
                        <div
                            class="filter-pill w-[160px]"
                            :class="{ 'filter-pill-active': filters.orderId }"
                        >
                            <span>Order ID</span>
                            <n-icon size="12" class="ml-1"
                                ><ChevronDown
                            /></n-icon>
                        </div>
                    </template>
                    <div class="p-3 w-64">
                        <n-input
                            v-model:value="filters.orderId"
                            placeholder="Search order ID..."
                            size="small"
                        />
                        <div class="mt-2 flex justify-end">
                            <n-button
                                size="small"
                                @click="applyFilter('orderId')"
                                type="primary"
                                >Apply</n-button
                            >
                        </div>
                    </div>
                </n-popover>

                <!-- Status Filter Pill -->
                <n-popover
                    trigger="click"
                    placement="bottom"
                    :show-arrow="false"
                >
                    <template #trigger>
                        <div
                            class="filter-pill w-[160px]"
                            :class="{ 'filter-pill-active': filters.status }"
                        >
                            <span>Status</span>
                            <n-icon size="12" class="ml-1"
                                ><ChevronDown
                            /></n-icon>
                        </div>
                    </template>
                    <div class="p-3 w-64">
                        <n-select
                            v-model:value="filters.status"
                            placeholder="Select status"
                            :options="statusOptions"
                            filterable
                            size="small"
                        />
                        <div class="mt-2 flex justify-between">
                            <n-button
                                size="small"
                                @click="clearFilter('status')"
                                quaternary
                                >Clear</n-button
                            >
                            <n-button
                                size="small"
                                @click="applyFilter('status')"
                                type="primary"
                                >Apply</n-button
                            >
                        </div>
                    </div>
                </n-popover>

                <!-- Customer Filter Pill -->
                <n-popover
                    trigger="click"
                    placement="bottom"
                    :show-arrow="false"
                >
                    <template #trigger>
                        <div
                            class="filter-pill w-[160px]"
                            :class="{ 'filter-pill-active': filters.customer }"
                        >
                            <span>Customer</span>
                            <n-icon size="12" class="ml-1"
                                ><ChevronDown
                            /></n-icon>
                        </div>
                    </template>
                    <div class="p-3 w-64">
                        <n-input
                            v-model:value="filters.customer"
                            placeholder="Search customer..."
                            size="small"
                        />
                        <div class="mt-2 flex justify-end">
                            <n-button
                                size="small"
                                @click="applyFilter('customer')"
                                type="primary"
                                >Apply</n-button
                            >
                        </div>
                    </div>
                </n-popover>

                <!-- Phone Filter Pill -->
                <n-popover
                    trigger="click"
                    placement="bottom"
                    :show-arrow="false"
                >
                    <template #trigger>
                        <div
                            class="filter-pill w-[160px]"
                            :class="{ 'filter-pill-active': filters.phone }"
                        >
                            <span>Phone</span>
                            <n-icon size="12" class="ml-1"
                                ><ChevronDown
                            /></n-icon>
                        </div>
                    </template>
                    <div class="p-3 w-64">
                        <n-input
                            v-model:value="filters.phone"
                            placeholder="Search phone number..."
                            size="small"
                        />
                        <div class="mt-2 flex justify-end">
                            <n-button
                                size="small"
                                @click="applyFilter('phone')"
                                type="primary"
                                >Apply</n-button
                            >
                        </div>
                    </div>
                </n-popover>

                <!-- Location Filter Pill -->
                <n-popover
                    trigger="click"
                    placement="bottom"
                    :show-arrow="false"
                >
                    <template #trigger>
                        <div
                            class="filter-pill w-[160px]"
                            :class="{ 'filter-pill-active': filters.location }"
                        >
                            <span>Location</span>
                            <n-icon size="12" class="ml-1"
                                ><ChevronDown
                            /></n-icon>
                        </div>
                    </template>
                    <div class="p-3 w-64">
                        <n-input
                            v-model:value="filters.location"
                            placeholder="Search location..."
                            size="small"
                        />
                        <div class="mt-2 flex justify-end">
                            <n-button
                                size="small"
                                @click="applyFilter('location')"
                                type="primary"
                                >Apply</n-button
                            >
                        </div>
                    </div>
                </n-popover>

                <!-- Payment Status Filter Pill -->
                <n-popover
                    trigger="click"
                    placement="bottom"
                    :show-arrow="false"
                >
                    <template #trigger>
                        <div
                            class="filter-pill w-[160px]"
                            :class="{
                                'filter-pill-active': filters.paymentStatus,
                            }"
                        >
                            <span>Payment</span>
                            <n-icon size="12" class="ml-1"
                                ><ChevronDown
                            /></n-icon>
                        </div>
                    </template>
                    <div class="p-3 w-64">
                        <n-select
                            v-model:value="filters.paymentStatus"
                            placeholder="Select payment status"
                            :options="paymentStatusOptions"
                            filterable
                            size="small"
                        />
                        <div class="mt-2 flex justify-between">
                            <n-button
                                size="small"
                                @click="clearFilter('paymentStatus')"
                                quaternary
                                >Clear</n-button
                            >
                            <n-button
                                size="small"
                                @click="applyFilter('paymentStatus')"
                                type="primary"
                                >Apply</n-button
                            >
                        </div>
                    </div>
                </n-popover>

                <!-- Date Range Filter Pill -->
                <n-popover
                    trigger="click"
                    placement="bottom"
                    :show-arrow="false"
                >
                    <template #trigger>
                        <div
                            class="filter-pill w-[160px]"
                            :class="{ 'filter-pill-active': filters.dateRange }"
                        >
                            <span>Date Range</span>
                            <n-icon size="12" class="ml-1"
                                ><ChevronDown
                            /></n-icon>
                        </div>
                    </template>
                    <div class="p-3 w-80">
                        <n-date-picker
                            v-model:value="filters.dateRange"
                            type="daterange"
                            clearable
                            style="width: 100%"
                            size="small"
                        />
                        <div class="mt-2 flex justify-between">
                            <n-button
                                size="small"
                                @click="clearFilter('dateRange')"
                                quaternary
                                >Clear</n-button
                            >
                            <n-button
                                size="small"
                                @click="applyFilter('dateRange')"
                                type="primary"
                                >Apply</n-button
                            >
                        </div>
                    </div>
                </n-popover>

                <!-- Search Filter Pill -->
                <n-popover
                    trigger="click"
                    placement="bottom"
                    :show-arrow="false"
                >
                    <template #trigger>
                        <div
                            class="filter-pill w-[160px]"
                            :class="{ 'filter-pill-active': filters.search }"
                        >
                            <span>Search</span>
                            <n-icon size="12" class="ml-1"
                                ><SearchIcon
                            /></n-icon>
                        </div>
                    </template>
                    <div class="p-3 w-64">
                        <n-input
                            v-model:value="filters.search"
                            placeholder="Search all fields..."
                            size="small"
                        />
                        <div class="mt-2 flex justify-end">
                            <n-button
                                size="small"
                                @click="applyFilter('search')"
                                type="primary"
                                >Apply</n-button
                            >
                        </div>
                    </div>
                </n-popover>

                <!-- Reset All Filters Button -->
                <n-button
                    v-if="hasActiveFilters"
                    @click="resetFilters"
                    size="small"
                    class="ml-2"
                    type="error"
                    text
                >
                    <template #icon>
                        <n-icon><Close /></n-icon>
                    </template>
                    Clear All
                </n-button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import {
    NButton,
    NIcon,
    NInput,
    NInputGroup,
    NSelect,
    NDatePicker,
    NPopover,
} from "naive-ui";
import { Add as PlusIcon } from "@vicons/carbon";
import { Renew as RefreshIcon } from "@vicons/carbon";
import { Search as SearchIcon } from "@vicons/carbon";
import { ChevronDown } from "@vicons/carbon";
import { Close } from "@vicons/carbon";

// Enhanced filter state
const filters = ref({
    search: "",
    orderId: "",
    status: null,
    customer: "",
    phone: "",
    location: "",
    paymentStatus: null,
    dateRange: null,
});

// Status options for dropdown
const statusOptions = [
    { label: "Pending", value: "pending" },
    { label: "Processing", value: "processing" },
    { label: "Completed", value: "completed" },
    { label: "Cancelled", value: "cancelled" },
];

// Payment status options
const paymentStatusOptions = [
    { label: "Paid", value: "paid" },
    { label: "Unpaid", value: "unpaid" },
    { label: "Partial", value: "partial" },
    { label: "Refunded", value: "refunded" },
];

// Computed property to check if any filters are active
const hasActiveFilters = computed(() => {
    return (
        filters.value.search ||
        filters.value.orderId ||
        filters.value.status ||
        filters.value.customer ||
        filters.value.phone ||
        filters.value.location ||
        filters.value.paymentStatus ||
        filters.value.dateRange
    );
});

// Methods
const createNewOrder = () => {
    // Navigate to create order page or open modal
    console.log("Create new order");
};

const refreshOrders = () => {
    // Refresh orders data
    console.log("Refresh orders");
};

// Apply a specific filter
const applyFilter = (filterName) => {
    console.log(`Applying ${filterName} filter:`, filters.value[filterName]);
    // Here you would typically call an API or update a store
    // For example: orderStore.setFilters({ [filterName]: filters.value[filterName] });
};

// Clear a specific filter
const clearFilter = (filterName) => {
    filters.value[filterName] = null;
    console.log(`Cleared ${filterName} filter`);
    // Here you would typically call an API or update a store
    // For example: orderStore.setFilters({ [filterName]: null });
};

// Legacy method - kept for compatibility
const applyFilters = () => {
    // Apply all filters to orders
    console.log("Apply all filters", filters.value);
};

const resetFilters = () => {
    // Reset all filters
    filters.value = {
        search: "",
        orderId: "",
        status: null,
        customer: "",
        phone: "",
        location: "",
        paymentStatus: null,
        dateRange: null,
    };
    console.log("All filters reset");
};
</script>

<style scoped>
.filter-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background-color: #f3f4f6;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-pill:hover {
    background-color: #e5e7eb;
}

.filter-pill-active {
    background-color: #dbeafe;
    color: #1d4ed8;
}

.filter-pill-active:hover {
    background-color: #bfdbfe;
}
</style>
