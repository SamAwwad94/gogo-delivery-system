<template>
    <div class="bg-white shadow rounded-md overflow-hidden w-full max-w-full">
        <div class="overflow-x-auto">
            <!-- Custom Table Implementation -->
            <table class="min-w-full divide-y divide-gray-200">
                <!-- Column Widths -->
                <colgroup>
                    <col style="width: 120px; min-width: 120px" />
                    <col style="width: 130px; min-width: 130px" />
                    <col style="width: 180px; min-width: 180px" />
                    <col style="width: 150px; min-width: 150px" />
                    <col style="width: 220px; min-width: 220px" />
                    <col style="width: 220px; min-width: 220px" />
                    <col style="width: 130px; min-width: 130px" />
                    <col style="width: 130px; min-width: 130px" />
                    <col style="width: 120px; min-width: 120px" />
                    <col style="width: 150px; min-width: 150px" />
                </colgroup>

                <!-- Table Header -->
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            scope="col"
                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Order ID
                        </th>
                        <th
                            scope="col"
                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Date
                        </th>
                        <th
                            scope="col"
                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Customer
                        </th>
                        <th
                            scope="col"
                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Phone
                        </th>
                        <th
                            scope="col"
                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Pickup Location
                        </th>
                        <th
                            scope="col"
                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Delivery Location
                        </th>
                        <th
                            scope="col"
                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Status
                        </th>
                        <th
                            scope="col"
                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Payment
                        </th>
                        <th
                            scope="col"
                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Amount
                        </th>
                        <th
                            scope="col"
                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="loading" class="animate-pulse">
                        <td colspan="10" class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center">
                                <div
                                    class="w-8 h-8 border-4 border-blue-500 rounded-full border-t-transparent animate-spin"
                                ></div>
                                <span class="ml-2">Loading...</span>
                            </div>
                        </td>
                    </tr>
                    <tr v-else-if="!orders.length" class="hover:bg-gray-50">
                        <td
                            colspan="10"
                            class="px-6 py-4 whitespace-nowrap text-center text-gray-500"
                        >
                            No orders found
                        </td>
                    </tr>
                    <tr
                        v-for="order in displayedOrders"
                        :key="order.id"
                        class="hover:bg-gray-50 transition-colors border-b border-gray-100"
                    >
                        <!-- Order ID -->
                        <td
                            class="px-4 py-2.5 whitespace-nowrap text-xs font-medium text-gray-900"
                        >
                            {{ order.id }}
                        </td>

                        <!-- Date -->
                        <td
                            class="px-4 py-2.5 whitespace-nowrap text-xs text-gray-500"
                        >
                            {{ formatDate(order.date) }}
                        </td>

                        <!-- Customer -->
                        <td
                            class="px-4 py-2.5 whitespace-nowrap text-xs text-gray-500"
                        >
                            {{ order.customer }}
                        </td>

                        <!-- Phone -->
                        <td
                            class="px-4 py-2.5 whitespace-nowrap text-xs text-gray-500"
                        >
                            {{ order.phone }}
                        </td>

                        <!-- Pickup Location -->
                        <td
                            class="px-4 py-2.5 text-xs text-gray-500 truncate max-w-xs"
                            :title="order.pickupLocation"
                        >
                            {{ order.pickupLocation }}
                        </td>

                        <!-- Delivery Location -->
                        <td
                            class="px-4 py-2.5 text-xs text-gray-500 truncate max-w-xs"
                            :title="order.deliveryLocation"
                        >
                            {{ order.deliveryLocation }}
                        </td>

                        <!-- Status -->
                        <td
                            class="px-4 py-2.5 whitespace-nowrap text-xs text-gray-500"
                        >
                            <span :class="getStatusClass(order.status)">
                                {{ getStatusText(order.status) }}
                            </span>
                        </td>

                        <!-- Payment Status -->
                        <td
                            class="px-4 py-2.5 whitespace-nowrap text-xs text-gray-500"
                        >
                            <span
                                :class="
                                    getPaymentStatusClass(order.paymentStatus)
                                "
                            >
                                {{ getPaymentStatusText(order.paymentStatus) }}
                            </span>
                        </td>

                        <!-- Amount -->
                        <td
                            class="px-4 py-2.5 whitespace-nowrap text-xs font-medium text-gray-900"
                        >
                            ${{ order.amount.toFixed(2) }}
                        </td>

                        <!-- Actions -->
                        <td
                            class="px-4 py-2.5 whitespace-nowrap text-xs text-gray-500"
                        >
                            <div class="flex items-center space-x-2">
                                <!-- View Button -->
                                <n-button
                                    @click="viewOrder(order)"
                                    quaternary
                                    circle
                                    size="small"
                                    title="View Order Details"
                                    class="text-blue-500"
                                >
                                    <template #icon>
                                        <n-icon><EyeIcon /></n-icon>
                                    </template>
                                </n-button>

                                <!-- Edit Button -->
                                <n-button
                                    @click="editOrder(order)"
                                    quaternary
                                    circle
                                    size="small"
                                    title="Edit Order"
                                    class="text-indigo-500"
                                >
                                    <template #icon>
                                        <n-icon><EditIcon /></n-icon>
                                    </template>
                                </n-button>

                                <!-- Print Button -->
                                <n-button
                                    @click="printOrder(order)"
                                    quaternary
                                    circle
                                    size="small"
                                    title="Print Order"
                                    class="text-teal-500"
                                >
                                    <template #icon>
                                        <n-icon><PrinterIcon /></n-icon>
                                    </template>
                                </n-button>

                                <!-- Map Button -->
                                <n-button
                                    @click="showOnMap(order)"
                                    quaternary
                                    circle
                                    size="small"
                                    title="Show on Map"
                                    class="text-purple-500"
                                >
                                    <template #icon>
                                        <n-icon><MapIcon /></n-icon>
                                    </template>
                                </n-button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Custom Pagination -->
            <div class="px-4 py-3 bg-white border-t border-gray-200">
                <div
                    class="flex flex-col sm:flex-row items-center justify-between gap-4"
                >
                    <div class="text-xs text-gray-500 flex items-center">
                        <span
                            >Showing
                            <span class="font-medium text-gray-700">{{
                                paginationStart
                            }}</span>
                            to
                            <span class="font-medium text-gray-700">{{
                                paginationEnd
                            }}</span>
                            of
                            <span class="font-medium text-gray-700">{{
                                orders.length
                            }}</span>
                            orders</span
                        >
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center">
                            <label
                                for="page-size"
                                class="text-xs text-gray-500 mr-2"
                                >Show:</label
                            >
                            <select
                                id="page-size"
                                v-model="pageSize"
                                class="border border-gray-200 rounded text-xs py-1 px-2 bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option
                                    v-for="size in [10, 20, 50, 100]"
                                    :key="size"
                                    :value="size"
                                >
                                    {{ size }}
                                </option>
                            </select>
                        </div>
                        <div class="flex items-center gap-1">
                            <n-button
                                @click="currentPage = 1"
                                :disabled="currentPage === 1"
                                size="tiny"
                                quaternary
                                title="First Page"
                            >
                                <template #icon>
                                    <n-icon><PageFirst /></n-icon>
                                </template>
                            </n-button>
                            <n-button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                size="tiny"
                                quaternary
                                title="Previous Page"
                            >
                                <template #icon>
                                    <n-icon><ChevronLeft /></n-icon>
                                </template>
                            </n-button>
                            <span class="px-2 py-1 text-xs text-gray-500">
                                <span class="font-medium text-gray-700">{{
                                    currentPage
                                }}</span>
                                / {{ totalPages }}
                            </span>
                            <n-button
                                @click="currentPage++"
                                :disabled="currentPage === totalPages"
                                size="tiny"
                                quaternary
                                title="Next Page"
                            >
                                <template #icon>
                                    <n-icon><ChevronRight /></n-icon>
                                </template>
                            </n-button>
                            <n-button
                                @click="currentPage = totalPages"
                                :disabled="currentPage === totalPages"
                                size="tiny"
                                quaternary
                                title="Last Page"
                            >
                                <template #icon>
                                    <n-icon><PageLast /></n-icon>
                                </template>
                            </n-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { h, ref, reactive, computed } from "vue";
import { NButton, NPopconfirm, useMessage, NIcon } from "naive-ui";
import { View as EyeIcon } from "@vicons/carbon";
import { Edit as EditIcon } from "@vicons/carbon";
import { TrashCan as TrashIcon } from "@vicons/carbon";
import { Printer as PrinterIcon } from "@vicons/carbon";
import { Map as MapIcon } from "@vicons/carbon";
import { ChevronLeft } from "@vicons/carbon";
import { ChevronRight } from "@vicons/carbon";
import { PageFirst } from "@vicons/carbon";
import { PageLast } from "@vicons/carbon";
import dayjs from "dayjs";

const message = useMessage();
const loading = ref(false);

// Pagination state
const currentPage = ref(1);
const pageSize = ref(20);

// Helper functions for formatting and styling
const formatDate = (dateString) => {
    return dayjs(dateString).format("MMM D, YYYY");
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

// Lebanese orders with realistic data
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

// Computed properties for pagination
const totalPages = computed(() =>
    Math.ceil(orders.value.length / pageSize.value)
);
const paginationStart = computed(
    () => (currentPage.value - 1) * pageSize.value + 1
);
const paginationEnd = computed(() =>
    Math.min(currentPage.value * pageSize.value, orders.value.length)
);
const displayedOrders = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;
    return orders.value.slice(start, end);
});

// Action methods
const viewOrder = (order) => {
    message.info(`Viewing order ${order.id}`);
    // Navigate to order details or open modal
};

const editOrder = (order) => {
    message.info(`Editing order ${order.id}`);
    // Navigate to edit page or open modal
};

const deleteOrder = (order) => {
    message.success(`Order ${order.id} deleted`);
    // Delete order via API
    orders.value = orders.value.filter((o) => o.id !== order.id);
};

// Print order method
const printOrder = (order) => {
    message.info(`Printing order ${order.id}`);
    // Implementation for printing order
    window.print();
};

// Show order on map method
const showOnMap = (order) => {
    message.info(`Showing order ${order.id} on map`);
    // Implementation for showing order on map
    // This would typically open a map view or navigate to a map page
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

/* Custom pagination styles */
:deep(.n-button.n-button--quaternary.n-button--disabled) {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
