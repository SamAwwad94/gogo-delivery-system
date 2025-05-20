<template>
    <div class="bg-white shadow rounded-md overflow-hidden w-full max-w-full">
        <!-- Header with Title and Actions -->
        <div
            class="p-4 border-b border-gray-200 flex justify-between items-center"
        >
            <h2 class="text-lg font-semibold text-gray-800">Orders</h2>

            <!-- Right side actions -->
            <div class="flex flex-wrap items-center gap-4 justify-end">
                <!-- Search Field -->
                <div class="relative">
                    <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                    >
                        <n-icon size="16"><SearchIcon /></n-icon>
                    </div>
                    <n-input
                        v-model:value="searchValue"
                        placeholder="Search orders..."
                        size="small"
                        class="w-[200px]"
                        clearable
                    />
                </div>

                <!-- Create Order Button -->
                <n-button
                    type="primary"
                    color="#f97316"
                    @click="createOrder"
                    size="small"
                    class="w-[160px]"
                >
                    <template #icon>
                        <n-icon><Add /></n-icon>
                    </template>
                    Create Order
                </n-button>

                <!-- Export Button -->
                <n-button @click="exportOrders" size="small" class="w-[160px]">
                    <template #icon>
                        <n-icon><DataTable /></n-icon>
                    </template>
                    Export
                </n-button>

                <!-- Toggle Filters Button -->
                <n-button
                    @click="showFilters = !showFilters"
                    size="small"
                    class="w-[160px]"
                    :type="showFilters ? 'primary' : 'default'"
                >
                    <template #icon>
                        <n-icon><Filter /></n-icon>
                    </template>
                    {{ showFilters ? "Hide Filters" : "Show Filters" }}
                </n-button>
            </div>
        </div>

        <!-- Enhanced Filter Section -->
        <div v-if="showFilters" class="p-4 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Order ID Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1"
                        >Order ID</label
                    >
                    <n-input
                        v-model:value="filters.orderId"
                        placeholder="Search by ID"
                        size="small"
                        clearable
                    />
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1"
                        >Status</label
                    >
                    <n-select
                        v-model:value="filters.status"
                        :options="statusOptions"
                        placeholder="All Statuses"
                        size="small"
                        clearable
                        multiple
                    />
                </div>

                <!-- Customer Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1"
                        >Customer</label
                    >
                    <n-input
                        v-model:value="filters.customer"
                        placeholder="Search by customer"
                        size="small"
                        clearable
                    />
                </div>

                <!-- Phone Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1"
                        >Phone</label
                    >
                    <n-input
                        v-model:value="filters.phone"
                        placeholder="Search by phone"
                        size="small"
                        clearable
                    />
                </div>

                <!-- Location Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1"
                        >Location</label
                    >
                    <n-select
                        v-model:value="filters.location"
                        :options="locationOptions"
                        placeholder="All Locations"
                        size="small"
                        clearable
                        multiple
                    />
                </div>

                <!-- Payment Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1"
                        >Payment Status</label
                    >
                    <n-select
                        v-model:value="filters.paymentStatus"
                        :options="paymentOptions"
                        placeholder="All Payment Statuses"
                        size="small"
                        clearable
                        multiple
                    />
                </div>

                <!-- Date Range Filter -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1"
                        >Date Range</label
                    >
                    <n-date-picker
                        v-model:value="filters.dateRange"
                        type="daterange"
                        placeholder="Select date range"
                        size="small"
                        clearable
                        style="width: 100%"
                    />
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex justify-end mt-4 gap-2">
                <n-button size="small" @click="resetFilters">
                    <template #icon>
                        <n-icon><Close /></n-icon>
                    </template>
                    Reset Filters
                </n-button>
                <n-button type="primary" size="small" @click="applyFilters">
                    <template #icon>
                        <n-icon><Filter /></n-icon>
                    </template>
                    Apply Filters
                </n-button>
            </div>
        </div>

        <!-- Active Filters Display -->
        <div
            v-if="hasActiveFilters"
            class="p-3 bg-blue-50 border-b border-blue-200"
        >
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-medium text-blue-700"
                    >Active Filters:</span
                >

                <n-tag
                    v-if="filters.orderId"
                    size="small"
                    closable
                    @close="filters.orderId = ''"
                >
                    ID: {{ filters.orderId }}
                </n-tag>

                <n-tag
                    v-for="status in filters.status"
                    :key="status"
                    size="small"
                    closable
                    @close="removeFilter('status', status)"
                >
                    Status: {{ getStatusText(status) }}
                </n-tag>

                <n-tag
                    v-if="filters.customer"
                    size="small"
                    closable
                    @close="filters.customer = ''"
                >
                    Customer: {{ filters.customer }}
                </n-tag>

                <n-tag
                    v-if="filters.phone"
                    size="small"
                    closable
                    @close="filters.phone = ''"
                >
                    Phone: {{ filters.phone }}
                </n-tag>

                <n-tag
                    v-for="location in filters.location"
                    :key="location"
                    size="small"
                    closable
                    @close="removeFilter('location', location)"
                >
                    Location: {{ location }}
                </n-tag>

                <n-tag
                    v-for="payment in filters.paymentStatus"
                    :key="payment"
                    size="small"
                    closable
                    @close="removeFilter('paymentStatus', payment)"
                >
                    Payment: {{ getPaymentStatusText(payment) }}
                </n-tag>

                <n-tag
                    v-if="filters.dateRange && filters.dateRange.length === 2"
                    size="small"
                    closable
                    @close="filters.dateRange = null"
                >
                    Date: {{ formatDateRange(filters.dateRange) }}
                </n-tag>

                <n-button size="tiny" text @click="resetFilters">
                    Clear All
                </n-button>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div
            v-if="selectedRows.length > 0"
            class="p-3 bg-white border-b border-gray-200"
        >
            <div class="flex flex-wrap items-center gap-4 justify-between">
                <div class="text-sm text-gray-700">
                    <span class="font-medium">{{ selectedRows.length }}</span>
                    selected
                </div>
                <n-button size="small" class="w-[160px]" @click="shipOrders">
                    <template #icon>
                        <n-icon><Delivery /></n-icon>
                    </template>
                    Ship Order
                </n-button>
                <n-button
                    size="small"
                    class="w-[160px]"
                    @click="unassignOrders"
                >
                    <template #icon>
                        <n-icon><UserMultiple /></n-icon>
                    </template>
                    Unassign
                </n-button>
                <n-button
                    size="small"
                    class="w-[160px]"
                    @click="generateLabels"
                >
                    <template #icon>
                        <n-icon><Document /></n-icon>
                    </template>
                    Generate Labels
                </n-button>
                <n-button size="small" class="w-[160px]" @click="cancelOrders">
                    <template #icon>
                        <n-icon><Close /></n-icon>
                    </template>
                    Cancel
                </n-button>
                <n-button size="small" class="w-[160px]" @click="exportOrders">
                    <template #icon>
                        <n-icon><DataTable /></n-icon>
                    </template>
                    Export CSV
                </n-button>
            </div>
        </div>

        <!-- Data Table -->
        <EasyDataTable
            v-model:server-options="serverOptions"
            :headers="headers"
            :items="filteredOrders"
            :loading="loading"
            :pagination-options="{
                enabled: true,
                position: 'bottom',
            }"
            :rows-per-page="10"
            :rows-items="[10, 20, 50, 100]"
            show-index
            show-select
            @selection-change="onSelectionChange"
            @click-row="onRowClick"
            @sort-change="onSortChange"
            buttons-pagination
            table-class-name="customize-table"
        >
            <!-- Status Column Template -->
            <template #item-status="{ status }">
                <span :class="getStatusClass(status)">
                    {{ getStatusText(status) }}
                </span>
            </template>

            <!-- Payment Status Column Template -->
            <template #item-paymentStatus="{ paymentStatus }">
                <span :class="getPaymentStatusClass(paymentStatus)">
                    {{ getPaymentStatusText(paymentStatus) }}
                </span>
            </template>

            <!-- Actions Column Template -->
            <template #item-actions="{ id }">
                <div class="flex items-center space-x-1">
                    <n-button
                        @click.stop="viewOrder(id)"
                        quaternary
                        circle
                        size="small"
                        title="View Order Details"
                        class="text-blue-500"
                    >
                        <template #icon>
                            <n-icon><View /></n-icon>
                        </template>
                    </n-button>
                    <n-button
                        @click.stop="editOrder(id)"
                        quaternary
                        circle
                        size="small"
                        title="Edit Order"
                        class="text-indigo-500"
                    >
                        <template #icon>
                            <n-icon><Edit /></n-icon>
                        </template>
                    </n-button>
                    <n-button
                        @click.stop="printOrder(id)"
                        quaternary
                        circle
                        size="small"
                        title="Print Order"
                        class="text-teal-500"
                    >
                        <template #icon>
                            <n-icon><Printer /></n-icon>
                        </template>
                    </n-button>
                    <n-button
                        @click.stop="showOnMap(id)"
                        quaternary
                        circle
                        size="small"
                        title="Show on Map"
                        class="text-purple-500"
                    >
                        <template #icon>
                            <n-icon><Location /></n-icon>
                        </template>
                    </n-button>
                </div>
            </template>

            <!-- Empty State Template -->
            <template #empty-message>
                <div class="py-8 text-center">
                    <DocumentIcon class="mx-auto h-12 w-12 text-gray-300" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">
                        No orders found
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Try adjusting your search or filter to find what you're
                        looking for.
                    </p>
                </div>
            </template>
        </EasyDataTable>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import EasyDataTable from "vue3-easy-data-table";
import {
    useMessage,
    NButton,
    NIcon,
    NInput,
    NSelect,
    NDatePicker,
    NPopover,
} from "naive-ui";
import dayjs from "dayjs";
import { DocumentIcon, XCircleIcon } from "@heroicons/vue/24/outline";
import ChevronDownIcon from "./icons/ChevronDownFixed.vue";

// Import Carbon icons
import {
    Add,
    ArrowsVertical,
    Filter,
    OverflowMenuVertical,
    View,
    Edit,
    Printer,
    Location,
    Delivery,
    UserMultiple,
    Document,
    Close,
    DataTable,
    Search as SearchIcon,
} from "@vicons/carbon";

const message = useMessage();
const loading = ref(false);
const searchValue = ref("");
const selectedRows = ref([]);
const showFilters = ref(false);

// Advanced filter state
const filters = ref({
    orderId: "",
    status: [],
    customer: "",
    phone: "",
    location: [],
    paymentStatus: [],
    dateRange: null,
});

// Server options for pagination and sorting
const serverOptions = ref({
    page: 1,
    rowsPerPage: 10,
    sortBy: "date",
    sortType: "desc",
});

// Filter options
const statusOptions = [
    { label: "Pending", value: "pending" },
    { label: "Processing", value: "processing" },
    { label: "Completed", value: "completed" },
    { label: "Cancelled", value: "cancelled" },
];

const paymentOptions = [
    { label: "Paid", value: "paid" },
    { label: "Unpaid", value: "unpaid" },
    { label: "Partial", value: "partial" },
    { label: "Refunded", value: "refunded" },
];

const locationOptions = [
    { label: "Beirut", value: "Beirut" },
    { label: "Mount Lebanon", value: "Mount Lebanon" },
    { label: "North Lebanon", value: "North Lebanon" },
    { label: "South Lebanon", value: "South Lebanon" },
    { label: "Bekaa Valley", value: "Bekaa Valley" },
];

// Check if any filters are active
const hasActiveFilters = computed(() => {
    return (
        filters.value.orderId !== "" ||
        filters.value.status.length > 0 ||
        filters.value.customer !== "" ||
        filters.value.phone !== "" ||
        filters.value.location.length > 0 ||
        filters.value.paymentStatus.length > 0 ||
        (filters.value.dateRange && filters.value.dateRange.length === 2)
    );
});

// Table headers
const headers = [
    { text: "Order ID", value: "id", sortable: true, width: 120 },
    { text: "Date", value: "date", sortable: true, width: 150 },
    { text: "Customer", value: "customer", sortable: true, width: 180 },
    { text: "Phone", value: "phone", width: 140 },
    { text: "Pickup Location", value: "pickupLocation" },
    { text: "Delivery Location", value: "deliveryLocation" },
    { text: "Status", value: "status", sortable: true, width: 120 },
    { text: "Payment", value: "paymentStatus", sortable: true, width: 140 },
    { text: "Amount", value: "amount", sortable: true, width: 100 },
    { text: "Actions", value: "actions", width: 150 },
];

// Lebanese orders with realistic data (same as before)
const orders = ref([
    {
        id: "ORD-001",
        date: "2023-05-15",
        customer: "Hassan Nasrallah",
        phone: "+961 3 123 456",
        pickupLocation: "Hamra St, Beirut",
        deliveryLocation: "Bliss St, Beirut",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 45.99,
    },
    {
        id: "ORD-002",
        date: "2023-05-14",
        customer: "Zeinab Khalil",
        phone: "+961 70 987 654",
        pickupLocation: "Verdun, Beirut",
        deliveryLocation: "Ashrafieh, Beirut",
        status: "processing",
        paymentStatus: "paid",
        amount: 78.5,
    },
    {
        id: "ORD-003",
        date: "2023-05-13",
        customer: "Ali Haidar",
        phone: "+961 71 456 789",
        pickupLocation: "Jounieh, Mount Lebanon",
        deliveryLocation: "Byblos, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 125.75,
    },
    {
        id: "ORD-004",
        date: "2023-05-12",
        customer: "Sara Khoury",
        phone: "+961 76 789 012",
        pickupLocation: "Tripoli, North Lebanon",
        deliveryLocation: "Batroun, North Lebanon",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 67.25,
    },
    {
        id: "ORD-005",
        date: "2023-05-11",
        customer: "Mohammad Ayyoub",
        phone: "+961 3 234 567",
        pickupLocation: "Sidon, South Lebanon",
        deliveryLocation: "Tyre, South Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 92.0,
    },
    {
        id: "ORD-006",
        date: "2023-05-10",
        customer: "Layla Abboud",
        phone: "+961 78 345 678",
        pickupLocation: "Zahle, Bekaa Valley",
        deliveryLocation: "Baalbek, Bekaa Valley",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 54.25,
    },
    {
        id: "ORD-007",
        date: "2023-05-09",
        customer: "Karim Mansour",
        phone: "+961 79 567 890",
        pickupLocation: "Nabatieh, South Lebanon",
        deliveryLocation: "Marjayoun, South Lebanon",
        status: "processing",
        paymentStatus: "partial",
        amount: 135.5,
    },
    {
        id: "ORD-008",
        date: "2023-05-08",
        customer: "Nour Saleh",
        phone: "+961 81 678 901",
        pickupLocation: "Aley, Mount Lebanon",
        deliveryLocation: "Bhamdoun, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 89.99,
    },
    {
        id: "ORD-009",
        date: "2023-05-07",
        customer: "Fadi Karam",
        phone: "+961 3 789 012",
        pickupLocation: "Beit Mery, Mount Lebanon",
        deliveryLocation: "Broummana, Mount Lebanon",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 42.75,
    },
    {
        id: "ORD-010",
        date: "2023-05-06",
        customer: "Rima Saad",
        phone: "+961 70 890 123",
        pickupLocation: "Jbeil, Mount Lebanon",
        deliveryLocation: "Amchit, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 112.25,
    },
    {
        id: "ORD-011",
        date: "2023-05-05",
        customer: "Walid Tawfiq",
        phone: "+961 71 901 234",
        pickupLocation: "Raouche, Beirut",
        deliveryLocation: "Ramlet El Baida, Beirut",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 67.5,
    },
    {
        id: "ORD-012",
        date: "2023-05-04",
        customer: "Yasmine Hariri",
        phone: "+961 76 012 345",
        pickupLocation: "Gemmayzeh, Beirut",
        deliveryLocation: "Mar Mikhael, Beirut",
        status: "processing",
        paymentStatus: "partial",
        amount: 95.25,
    },
    {
        id: "ORD-013",
        date: "2023-05-03",
        customer: "Bilal Farhat",
        phone: "+961 3 123 456",
        pickupLocation: "Antelias, Mount Lebanon",
        deliveryLocation: "Dbayeh, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 145.0,
    },
    {
        id: "ORD-014",
        date: "2023-05-02",
        customer: "Hiba Nassar",
        phone: "+961 78 234 567",
        pickupLocation: "Saida, South Lebanon",
        deliveryLocation: "Jezzine, South Lebanon",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 55.75,
    },
    {
        id: "ORD-015",
        date: "2023-05-01",
        customer: "Omar Jaber",
        phone: "+961 79 345 678",
        pickupLocation: "Chouf, Mount Lebanon",
        deliveryLocation: "Baakline, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 78.5,
    },
    {
        id: "ORD-016",
        date: "2023-04-30",
        customer: "Lina Khalil",
        phone: "+961 81 456 789",
        pickupLocation: "Achrafieh, Beirut",
        deliveryLocation: "Gemmayze, Beirut",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 63.25,
    },
    {
        id: "ORD-017",
        date: "2023-04-29",
        customer: "Sami Haddad",
        phone: "+961 3 567 890",
        pickupLocation: "Hamra, Beirut",
        deliveryLocation: "Ras Beirut, Beirut",
        status: "processing",
        paymentStatus: "partial",
        amount: 105.75,
    },
    {
        id: "ORD-018",
        date: "2023-04-28",
        customer: "Dalia Moussa",
        phone: "+961 70 678 901",
        pickupLocation: "Baabda, Mount Lebanon",
        deliveryLocation: "Hadath, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 92.5,
    },
    {
        id: "ORD-019",
        date: "2023-04-27",
        customer: "Rami Aoun",
        phone: "+961 71 789 012",
        pickupLocation: "Byblos, Mount Lebanon",
        deliveryLocation: "Amchit, Mount Lebanon",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 48.75,
    },
    {
        id: "ORD-020",
        date: "2023-04-26",
        customer: "Zeina Khoury",
        phone: "+961 76 890 123",
        pickupLocation: "Jounieh, Mount Lebanon",
        deliveryLocation: "Kaslik, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 115.25,
    },
    {
        id: "ORD-021",
        date: "2023-04-25",
        customer: "Marwan Saab",
        phone: "+961 3 901 234",
        pickupLocation: "Tripoli, North Lebanon",
        deliveryLocation: "Mina, North Lebanon",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 72.5,
    },
    {
        id: "ORD-022",
        date: "2023-04-24",
        customer: "Nadia Rizk",
        phone: "+961 78 012 345",
        pickupLocation: "Zahle, Bekaa Valley",
        deliveryLocation: "Chtaura, Bekaa Valley",
        status: "processing",
        paymentStatus: "partial",
        amount: 98.75,
    },
    {
        id: "ORD-023",
        date: "2023-04-23",
        customer: "Jad Salloum",
        phone: "+961 79 123 456",
        pickupLocation: "Sidon, South Lebanon",
        deliveryLocation: "Ghazieh, South Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 135.0,
    },
    {
        id: "ORD-024",
        date: "2023-04-22",
        customer: "Maya Abou Jaoude",
        phone: "+961 81 234 567",
        pickupLocation: "Verdun, Beirut",
        deliveryLocation: "Ain El Mreisseh, Beirut",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 59.25,
    },
    {
        id: "ORD-025",
        date: "2023-04-21",
        customer: "Elie Chamoun",
        phone: "+961 3 345 678",
        pickupLocation: "Jbeil, Mount Lebanon",
        deliveryLocation: "Fidar, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 82.5,
    },
    {
        id: "ORD-026",
        date: "2023-04-20",
        customer: "Rana Makarem",
        phone: "+961 70 456 789",
        pickupLocation: "Nabatieh, South Lebanon",
        deliveryLocation: "Kfar Roummane, South Lebanon",
        status: "pending",
        paymentStatus: "unpaid",
        amount: 68.25,
    },
    {
        id: "ORD-027",
        date: "2023-04-19",
        customer: "Tarek Zein",
        phone: "+961 71 567 890",
        pickupLocation: "Aley, Mount Lebanon",
        deliveryLocation: "Souk El Gharb, Mount Lebanon",
        status: "processing",
        paymentStatus: "partial",
        amount: 110.75,
    },
    {
        id: "ORD-028",
        date: "2023-04-18",
        customer: "Carla Sfeir",
        phone: "+961 76 678 901",
        pickupLocation: "Achrafieh, Beirut",
        deliveryLocation: "Badaro, Beirut",
        status: "completed",
        paymentStatus: "paid",
        amount: 95.5,
    },
    {
        id: "ORD-029",
        date: "2023-04-17",
        customer: "Hadi Nasrallah",
        phone: "+961 3 789 012",
        pickupLocation: "Hamra, Beirut",
        deliveryLocation: "Clemenceau, Beirut",
        status: "cancelled",
        paymentStatus: "refunded",
        amount: 52.75,
    },
    {
        id: "ORD-030",
        date: "2023-04-16",
        customer: "Lamia Fakhry",
        phone: "+961 78 890 123",
        pickupLocation: "Jounieh, Mount Lebanon",
        deliveryLocation: "Zouk Mosbeh, Mount Lebanon",
        status: "completed",
        paymentStatus: "paid",
        amount: 118.25,
    },
]);

// Filtered orders based on search and filters
const filteredOrders = computed(() => {
    let result = [...orders.value];

    // Apply global search filter
    if (searchValue.value) {
        const searchLower = searchValue.value.toLowerCase();
        result = result.filter(
            (order) =>
                order.id.toLowerCase().includes(searchLower) ||
                order.customer.toLowerCase().includes(searchLower) ||
                order.phone.toLowerCase().includes(searchLower) ||
                order.pickupLocation.toLowerCase().includes(searchLower) ||
                order.deliveryLocation.toLowerCase().includes(searchLower)
        );
    }

    // Apply Order ID filter
    if (filters.value.orderId) {
        const idLower = filters.value.orderId.toLowerCase();
        result = result.filter((order) =>
            order.id.toLowerCase().includes(idLower)
        );
    }

    // Apply Status filter (multiple selection)
    if (filters.value.status.length > 0) {
        result = result.filter((order) =>
            filters.value.status.includes(order.status)
        );
    }

    // Apply Customer filter
    if (filters.value.customer) {
        const customerLower = filters.value.customer.toLowerCase();
        result = result.filter((order) =>
            order.customer.toLowerCase().includes(customerLower)
        );
    }

    // Apply Phone filter
    if (filters.value.phone) {
        const phoneLower = filters.value.phone.toLowerCase();
        result = result.filter((order) =>
            order.phone.toLowerCase().includes(phoneLower)
        );
    }

    // Apply Location filter (multiple selection)
    if (filters.value.location.length > 0) {
        result = result.filter((order) => {
            // Check if any of the selected locations match either pickup or delivery location
            return filters.value.location.some((location) => {
                const locationLower = location.toLowerCase();
                return (
                    order.pickupLocation
                        .toLowerCase()
                        .includes(locationLower) ||
                    order.deliveryLocation.toLowerCase().includes(locationLower)
                );
            });
        });
    }

    // Apply Payment Status filter (multiple selection)
    if (filters.value.paymentStatus.length > 0) {
        result = result.filter((order) =>
            filters.value.paymentStatus.includes(order.paymentStatus)
        );
    }

    // Apply Date Range filter
    if (filters.value.dateRange && filters.value.dateRange.length === 2) {
        const startDate = dayjs(filters.value.dateRange[0]).startOf("day");
        const endDate = dayjs(filters.value.dateRange[1]).endOf("day");

        result = result.filter((order) => {
            const orderDate = dayjs(order.date);
            return orderDate.isAfter(startDate) && orderDate.isBefore(endDate);
        });
    }

    // Apply sorting
    const { sortBy, sortType } = serverOptions.value;
    if (sortBy) {
        result.sort((a, b) => {
            let comparison = 0;

            if (sortBy === "date") {
                comparison = dayjs(a[sortBy]).unix() - dayjs(b[sortBy]).unix();
            } else if (sortBy === "amount") {
                comparison = a[sortBy] - b[sortBy];
            } else {
                comparison = String(a[sortBy]).localeCompare(String(b[sortBy]));
            }

            return sortType === "desc" ? -comparison : comparison;
        });
    }

    return result;
});

// Helper functions for formatting and styling
const formatDate = (dateString) => {
    return dayjs(dateString).format("MMM D, YYYY HH:mm");
};

const formatDateRange = (range) => {
    if (!range || range.length !== 2) return "";
    return `${dayjs(range[0]).format("MMM D")} - ${dayjs(range[1]).format(
        "MMM D, YYYY"
    )}`;
};

const formatCurrency = (amount) => {
    return amount.toFixed(2);
};

const getStatusText = (status) => {
    const statusMap = {
        pending: "Pending",
        processing: "Processing",
        completed: "Completed",
        cancelled: "Cancelled",
    };
    return statusMap[status] || status;
};

const getStatusTagType = (status) => {
    const typeMap = {
        pending: "warning",
        processing: "info",
        completed: "success",
        cancelled: "error",
    };
    return typeMap[status] || "default";
};

const getStatusClass = (status) => {
    const statusMap = {
        pending:
            "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800",
        processing:
            "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800",
        completed:
            "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800",
        cancelled:
            "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800",
    };
    return (
        statusMap[status] ||
        "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
    );
};

const getPaymentStatusText = (status) => {
    const statusMap = {
        paid: "Paid",
        unpaid: "Unpaid",
        partial: "Partial",
        refunded: "Refunded",
    };
    return statusMap[status] || status;
};

const getPaymentTagType = (status) => {
    const typeMap = {
        paid: "success",
        unpaid: "error",
        partial: "warning",
        refunded: "info",
    };
    return typeMap[status] || "default";
};

const getPaymentStatusClass = (status) => {
    const statusMap = {
        paid: "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800",
        unpaid: "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800",
        partial:
            "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800",
        refunded:
            "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800",
    };
    return (
        statusMap[status] ||
        "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
    );
};

// Row class name for hover effect
const rowClassName = () => {
    return "hover:bg-gray-50 transition-colors duration-150";
};

// Event handlers
const onSelectionChange = (items) => {
    selectedRows.value = items;
};

const onRowClick = (item) => {
    viewOrder(item.id);
};

const onSortChange = (sortOptions) => {
    serverOptions.value = {
        ...serverOptions.value,
        sortBy: sortOptions.sortBy,
        sortType: sortOptions.sortType,
    };
};

// Action methods
const viewOrder = (id) => {
    message.info(`Viewing order ${id}`);
    // Navigate to order details or open modal
};

const editOrder = (id) => {
    message.info(`Editing order ${id}`);
    // Navigate to edit page or open modal
};

const printOrder = (id) => {
    message.info(`Printing order ${id}`);
    // Implementation for printing order
    window.print();
};

const showOnMap = (id) => {
    message.info(`Showing order ${id} on map`);
    // Implementation for showing order on map
};

// Header action methods
const createOrder = () => {
    message.info("Creating new order");
    // Implementation for creating a new order
};

const toggleSort = () => {
    message.info("Toggling sort options");
    // Implementation for toggling sort options
};

const toggleFilter = () => {
    message.info("Toggling filter options");
    // Implementation for toggling filter options
};

const showMoreOptions = () => {
    message.info("Showing more options");
    // Implementation for showing more options
};

// Tab and filter methods
const setActiveTab = (tab) => {
    activeTab.value = tab;

    // Apply filters based on tab
    switch (tab) {
        case "all":
            statusFilter.value = "";
            break;
        case "express-dallas":
            // Filter for Dallas Express orders
            break;
        case "express-auto":
            // Filter for Auto-pilot orders
            break;
        case "express-austin":
            // Filter for Austin Express orders
            break;
        case "new":
            statusFilter.value = "pending";
            break;
        case "today":
            dateFilter.value = "today";
            break;
    }
};

// Filter methods
const applyFilters = () => {
    message.success("Filters applied successfully");
    // Reset to first page when applying filters
    serverOptions.value.page = 1;
};

const resetFilters = () => {
    filters.value = {
        orderId: "",
        status: [],
        customer: "",
        phone: "",
        location: [],
        paymentStatus: [],
        dateRange: null,
    };
    message.info("All filters have been reset");
    // Reset to first page when clearing filters
    serverOptions.value.page = 1;
};

const removeFilter = (filterType, value) => {
    if (Array.isArray(filters.value[filterType])) {
        filters.value[filterType] = filters.value[filterType].filter(
            (item) => item !== value
        );
    } else {
        filters.value[filterType] = "";
    }
};

// Bulk action methods
const shipOrders = () => {
    message.info(`Shipping ${selectedRows.value.length} orders`);
    // Implementation for shipping orders
};

const unassignOrders = () => {
    message.info(`Unassigning ${selectedRows.value.length} orders`);
    // Implementation for unassigning orders
};

const generateLabels = () => {
    message.info(`Generating labels for ${selectedRows.value.length} orders`);
    // Implementation for generating labels
};

const cancelOrders = () => {
    message.info(`Cancelling ${selectedRows.value.length} orders`);
    // Implementation for cancelling orders
};

const exportOrders = () => {
    message.info(`Exporting ${selectedRows.value.length} orders to CSV`);
    // Implementation for exporting to CSV
    const headerRow = headers.map((h) => h.text).join(",");
    const rows = selectedRows.value
        .map((row) =>
            Object.values(row)
                .map((val) => (typeof val === "string" ? `"${val}"` : val))
                .join(",")
        )
        .join("\n");

    const csvContent = `data:text/csv;charset=utf-8,${headerRow}\n${rows}`;
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute(
        "download",
        `orders_export_${dayjs().format("YYYY-MM-DD")}.csv`
    );
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

const bulkPrint = () => {
    message.info(`Printing ${selectedRows.value.length} orders`);
    // Implementation for bulk printing
    window.print();
};

const clearSelection = () => {
    selectedRows.value = [];
};
</script>

<style scoped>
/* Custom styles for n-button overrides */
:deep(.n-button.text-blue-500 .n-icon) {
    color: #3b82f6;
}

:deep(.n-button.text-indigo-500 .n-icon) {
    color: #4f46e5;
}

:deep(.n-button.text-teal-500 .n-icon) {
    color: #14b8a6;
}

:deep(.n-button.text-purple-500 .n-icon) {
    color: #a855f7;
}

:deep(.n-button.text-blue-500:hover .n-icon) {
    color: #2563eb;
}

:deep(.n-button.text-indigo-500:hover .n-icon) {
    color: #4338ca;
}

:deep(.n-button.text-teal-500:hover .n-icon) {
    color: #0d9488;
}

:deep(.n-button.text-purple-500:hover .n-icon) {
    color: #9333ea;
}

/* Custom table styles */
:deep(.customize-table) {
    --easy-table-border: 1px solid #f3f4f6;
    --easy-table-row-border: 1px solid #f3f4f6;
    --easy-table-header-font-size: 0.75rem;
    --easy-table-header-height: 40px;
    --easy-table-header-font-color: #6b7280;
    --easy-table-header-background-color: #f9fafb;
    --easy-table-header-item-padding: 0.5rem 1rem;

    --easy-table-body-row-height: 48px;
    --easy-table-body-row-font-size: 0.75rem;
    --easy-table-body-row-font-color: #374151;
    --easy-table-body-row-background-color: #ffffff;
    --easy-table-body-row-hover-font-color: #111827;
    --easy-table-body-row-hover-background-color: #f9fafb;
    --easy-table-body-item-padding: 0.5rem 1rem;

    --easy-table-footer-background-color: #ffffff;
    --easy-table-footer-font-color: #6b7280;
    --easy-table-footer-font-size: 0.75rem;
    --easy-table-footer-padding: 0.75rem 1rem;

    --easy-table-rows-per-page-selector-width: 70px;
    --easy-table-rows-per-page-selector-option-padding: 0.5rem;
    --easy-table-rows-per-page-selector-z-index: 1;

    --easy-table-scrollbar-track-color: #f1f1f1;
    --easy-table-scrollbar-color: #e1e1e1;
    --easy-table-scrollbar-thumb-color: #c1c1c1;
    --easy-table-scrollbar-corner-color: #f1f1f1;

    --easy-table-loading-mask-background-color: #ffffff;
}

:deep(.customize-table .et-row) {
    transition: all 0.1s ease;
}

:deep(.customize-table .et-row:hover) {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

:deep(.customize-table .et-header) {
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

:deep(.customize-table .et-pagination) {
    padding: 1rem;
    border-top: 1px solid #f3f4f6;
}

:deep(.customize-table .et-pagination .buttons-pagination button) {
    border-radius: 0.375rem;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    margin: 0 0.125rem;
    border: 1px solid #e5e7eb;
    background-color: white;
    color: #6b7280;
    transition: all 0.2s ease;
}

:deep(.customize-table
        .et-pagination
        .buttons-pagination
        button:hover:not(:disabled)) {
    background-color: #f3f4f6;
    color: #374151;
    border-color: #d1d5db;
}

:deep(.customize-table .et-pagination .buttons-pagination button:disabled) {
    opacity: 0.5;
    cursor: not-allowed;
}

:deep(.customize-table .et-pagination .buttons-pagination button.active) {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

:deep(.customize-table .et-checkbox) {
    width: 1rem;
    height: 1rem;
    border-radius: 0.25rem;
    border: 1px solid #d1d5db;
    transition: all 0.2s ease;
}

:deep(.customize-table .et-checkbox:checked) {
    background-color: #3b82f6;
    border-color: #3b82f6;
}
</style>
