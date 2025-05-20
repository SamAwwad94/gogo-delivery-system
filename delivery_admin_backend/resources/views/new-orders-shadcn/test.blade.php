<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Orders ShadCN Test</title>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/logo-loader.css') }}">
    <link rel="stylesheet" href="{{ asset('css/new-orders-shadcn.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fix-chevron.css') }}">
    <!-- Include Naive UI styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/naive-ui/2.34.4/index.css" />
</head>

<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">New Orders ShadCN Test Page</h1>

        <!-- Vue App Container -->
        <div id="new-orders-shadcn-test-app">
            <div class="bg-white shadow rounded-md p-4 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Orders Management</h2>
                        <p class="text-gray-600">Manage your orders with modern components</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Search Field -->
                        <div class="relative">
                            <n-input v-model:value="searchValue" placeholder="Filter orders" size="small" class="w-64"
                                clearable>
                                <template #prefix>
                                    <n-icon><search-icon /></n-icon>
                                </template>
                            </n-input>
                        </div>

                        <!-- Create Order Button -->
                        <n-button type="primary" color="#f97316" @click="createOrder">
                            <template #icon>
                                <n-icon><add-icon /></n-icon>
                            </template>
                            Create Order
                        </n-button>

                        <!-- Sort Button -->
                        <n-button @click="toggleSort">
                            <template #icon>
                                <n-icon><arrows-vertical-icon /></n-icon>
                            </template>
                            Sort
                        </n-button>

                        <!-- Filter Button -->
                        <n-button @click="toggleFilter">
                            <template #icon>
                                <n-icon><filter-icon /></n-icon>
                            </template>
                            Filter
                        </n-button>

                        <!-- More Options -->
                        <n-button circle @click="showMoreOptions">
                            <template #icon>
                                <n-icon><overflow-menu-vertical-icon /></n-icon>
                            </template>
                        </n-button>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div v-if="selectedRows.length > 0" class="p-3 bg-white border-b border-gray-200 mb-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="text-sm text-gray-700">
                            <span class="font-medium">{{ selectedRows.length }}</span>
                            selected
                        </div>
                        <n-button size="small" @click="shipOrders">
                            <template #icon>
                                <n-icon><delivery-icon /></n-icon>
                            </template>
                            Ship Order
                        </n-button>
                        <n-button size="small" @click="unassignOrders">
                            <template #icon>
                                <n-icon><user-multiple-icon /></n-icon>
                            </template>
                            Unassign
                        </n-button>
                        <n-button size="small" @click="generateLabels">
                            <template #icon>
                                <n-icon><document-icon /></n-icon>
                            </template>
                            Generate Labels
                        </n-button>
                        <n-button size="small" @click="cancelOrders">
                            <template #icon>
                                <n-icon><close-icon /></n-icon>
                            </template>
                            Cancel
                        </n-button>
                        <n-button size="small" @click="exportOrders">
                            <template #icon>
                                <n-icon><data-table-icon /></n-icon>
                            </template>
                            Export CSV
                        </n-button>
                    </div>
                </div>

                <!-- Data Table -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <n-checkbox v-model:checked="selectAll" @update:checked="toggleSelectAll" />
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order ID</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payment</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="order in orders" :key="order.id" @click="selectRow(order)"
                            class="hover:bg-gray-50 cursor-pointer">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <n-checkbox v-model:checked="order.selected" @click.stop />
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ order.id }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.date }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.customer }}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span :class="getStatusClass(order.status)">
                                    {{ getStatusText(order.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span :class="getPaymentStatusClass(order.paymentStatus)">
                                    {{ getPaymentStatusText(order.paymentStatus) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${{ order.amount.toFixed(2) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-1">
                                    <n-button @click.stop="viewOrder(order.id)" quaternary circle size="small"
                                        title="View Order Details" class="text-blue-500">
                                        <template #icon>
                                            <n-icon><view-icon /></n-icon>
                                        </template>
                                    </n-button>
                                    <n-button @click.stop="editOrder(order.id)" quaternary circle size="small"
                                        title="Edit Order" class="text-indigo-500">
                                        <template #icon>
                                            <n-icon><edit-icon /></n-icon>
                                        </template>
                                    </n-button>
                                    <n-button @click.stop="printOrder(order.id)" quaternary circle size="small"
                                        title="Print Order" class="text-teal-500">
                                        <template #icon>
                                            <n-icon><printer-icon /></n-icon>
                                        </template>
                                    </n-button>
                                    <n-button @click.stop="showOnMap(order.id)" quaternary circle size="small"
                                        title="Show on Map" class="text-purple-500">
                                        <template #icon>
                                            <n-icon><location-icon /></n-icon>
                                        </template>
                                    </n-button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <n-button size="small" :disabled="currentPage === 1" @click="currentPage--">Previous</n-button>
                        <n-button size="small" :disabled="currentPage === totalPages"
                            @click="currentPage++">Next</n-button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span>
                                to
                                <span
                                    class="font-medium">{{ Math.min(currentPage * perPage, orders.length) }}</span>
                                of
                                <span class="font-medium">{{ orders.length }}</span>
                                results
                            </p>
                        </div>
                        <div class="flex items-center gap-1">
                            <n-button @click="currentPage = 1" :disabled="currentPage === 1" size="tiny" quaternary
                                title="First Page">
                                <template #icon>
                                    <n-icon><page-first-icon /></n-icon>
                                </template>
                            </n-button>
                            <n-button @click="currentPage--" :disabled="currentPage === 1" size="tiny" quaternary
                                title="Previous Page">
                                <template #icon>
                                    <n-icon><chevron-left-icon /></n-icon>
                                </template>
                            </n-button>
                            <span class="px-2 py-1 text-xs text-gray-500">
                                <span class="font-medium text-gray-700">{{ currentPage }}</span>
                                / {{ totalPages }}
                            </span>
                            <n-button @click="currentPage++" :disabled="currentPage === totalPages" size="tiny" quaternary
                                title="Next Page">
                                <template #icon>
                                    <n-icon><chevron-right-icon /></n-icon>
                                </template>
                            </n-button>
                            <n-button @click="currentPage = totalPages" :disabled="currentPage === totalPages" size="tiny"
                                quaternary title="Last Page">
                                <template #icon>
                                    <n-icon><page-last-icon /></n-icon>
                                </template>
                            </n-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Vue 3 and Naive UI -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/naive-ui"></script>
    <script src="https://unpkg.com/@vicons/carbon"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const { createApp, ref, computed } = Vue;
            const { NButton, NIcon, NInput, NCheckbox } = naive;

            // Import icons from @vicons/carbon
            const SearchIcon = window.CarbonIcon.Search;
            const AddIcon = window.CarbonIcon.Add;
            const ArrowsVerticalIcon = window.CarbonIcon.ArrowsVertical;
            const FilterIcon = window.CarbonIcon.Filter;
            const OverflowMenuVerticalIcon = window.CarbonIcon.OverflowMenuVertical;
            const ViewIcon = window.CarbonIcon.View;
            const EditIcon = window.CarbonIcon.Edit;
            const PrinterIcon = window.CarbonIcon.Printer;
            const LocationIcon = window.CarbonIcon.Location;
            const DeliveryIcon = window.CarbonIcon.Delivery;
            const UserMultipleIcon = window.CarbonIcon.UserMultiple;
            const DocumentIcon = window.CarbonIcon.Document;
            const CloseIcon = window.CarbonIcon.Close;
            const DataTableIcon = window.CarbonIcon.DataTable;
            const ChevronLeftIcon = window.CarbonIcon.ChevronLeft;
            const ChevronRightIcon = window.CarbonIcon.ChevronRight;
            const PageFirstIcon = window.CarbonIcon.PageFirst;
            const PageLastIcon = window.CarbonIcon.PageLast;

            createApp({
                components: {
                    NButton,
                    NIcon,
                    NInput,
                    NCheckbox,
                    SearchIcon,
                    AddIcon,
                    ArrowsVerticalIcon,
                    FilterIcon,
                    OverflowMenuVerticalIcon,
                    ViewIcon,
                    EditIcon,
                    PrinterIcon,
                    LocationIcon,
                    DeliveryIcon,
                    UserMultipleIcon,
                    DocumentIcon,
                    CloseIcon,
                    DataTableIcon,
                    ChevronLeftIcon,
                    ChevronRightIcon,
                    PageFirstIcon,
                    PageLastIcon
                },
                setup() {
                    const searchValue = ref('');
                    const currentPage = ref(1);
                    const perPage = ref(10);
                    const selectAll = ref(false);

                    const orders = ref([
                        {
                            id: 'ORD-001',
                            date: '2023-05-15',
                            customer: 'Hassan Nasrallah',
                            status: 'pending',
                            paymentStatus: 'unpaid',
                            amount: 45.99,
                            selected: false
                        },
                        {
                            id: 'ORD-002',
                            date: '2023-05-14',
                            customer: 'Zeinab Khalil',
                            status: 'processing',
                            paymentStatus: 'paid',
                            amount: 78.50,
                            selected: false
                        },
                        {
                            id: 'ORD-003',
                            date: '2023-05-13',
                            customer: 'Ali Haidar',
                            status: 'completed',
                            paymentStatus: 'paid',
                            amount: 125.75,
                            selected: false
                        },
                        {
                            id: 'ORD-004',
                            date: '2023-05-12',
                            customer: 'Sara Khoury',
                            status: 'cancelled',
                            paymentStatus: 'refunded',
                            amount: 67.25,
                            selected: false
                        },
                        {
                            id: 'ORD-005',
                            date: '2023-05-11',
                            customer: 'Mohammed Diab',
                            status: 'pending',
                            paymentStatus: 'unpaid',
                            amount: 92.30,
                            selected: false
                        },
                        {
                            id: 'ORD-006',
                            date: '2023-05-10',
                            customer: 'Fatima Hariri',
                            status: 'processing',
                            paymentStatus: 'partial',
                            amount: 145.80,
                            selected: false
                        }
                    ]);

                    const totalPages = computed(() => Math.ceil(orders.value.length / perPage.value));

                    const selectedRows = computed(() => {
                        return orders.value.filter(order => order.selected);
                    });

                    function toggleSelectAll() {
                        orders.value.forEach(order => {
                            order.selected = selectAll.value;
                        });
                    }

                    function selectRow(order) {
                        order.selected = !order.selected;
                        updateSelectAll();
                    }

                    function updateSelectAll() {
                        selectAll.value = orders.value.length > 0 && orders.value.every(order => order.selected);
                    }

                    function createOrder() {
                        alert('Creating new order');
                    }

                    function toggleSort() {
                        alert('Toggling sort options');
                    }

                    function toggleFilter() {
                        alert('Toggling filter options');
                    }

                    function showMoreOptions() {
                        alert('Showing more options');
                    }

                    function viewOrder(id) {
                        alert(`Viewing order ${id}`);
                    }

                    function editOrder(id) {
                        alert(`Editing order ${id}`);
                    }

                    function printOrder(id) {
                        alert(`Printing order ${id}`);
                    }

                    function showOnMap(id) {
                        alert(`Showing order ${id} on map`);
                    }

                    function shipOrders() {
                        alert(`Shipping ${selectedRows.value.length} orders`);
                    }

                    function unassignOrders() {
                        alert(`Unassigning ${selectedRows.value.length} orders`);
                    }

                    function generateLabels() {
                        alert(`Generating labels for ${selectedRows.value.length} orders`);
                    }

                    function cancelOrders() {
                        alert(`Cancelling ${selectedRows.value.length} orders`);
                    }

                    function exportOrders() {
                        alert(`Exporting ${selectedRows.value.length} orders to CSV`);
                    }

                    function getStatusClass(status) {
                        const classes = 'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ';
                        switch (status) {
                            case 'pending':
                                return classes + 'bg-yellow-100 text-yellow-800';
                            case 'processing':
                                return classes + 'bg-blue-100 text-blue-800';
                            case 'completed':
                                return classes + 'bg-green-100 text-green-800';
                            case 'cancelled':
                                return classes + 'bg-red-100 text-red-800';
                            default:
                                return classes + 'bg-gray-100 text-gray-800';
                        }
                    }

                    function getStatusText(status) {
                        return status.charAt(0).toUpperCase() + status.slice(1);
                    }

                    function getPaymentStatusClass(status) {
                        const classes = 'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ';
                        switch (status) {
                            case 'paid':
                                return classes + 'bg-green-100 text-green-800';
                            case 'unpaid':
                                return classes + 'bg-red-100 text-red-800';
                            case 'partial':
                                return classes + 'bg-yellow-100 text-yellow-800';
                            case 'refunded':
                                return classes + 'bg-purple-100 text-purple-800';
                            default:
                                return classes + 'bg-gray-100 text-gray-800';
                        }
                    }

                    function getPaymentStatusText(status) {
                        return status.charAt(0).toUpperCase() + status.slice(1);
                    }

                    return {
                        searchValue,
                        orders,
                        currentPage,
                        perPage,
                        totalPages,
                        selectAll,
                        selectedRows,
                        toggleSelectAll,
                        selectRow,
                        createOrder,
                        toggleSort,
                        toggleFilter,
                        showMoreOptions,
                        viewOrder,
                        editOrder,
                        printOrder,
                        showOnMap,
                        shipOrders,
                        unassignOrders,
                        generateLabels,
                        cancelOrders,
                        exportOrders,
                        getStatusClass,
                        getStatusText,
                        getPaymentStatusClass,
                        getPaymentStatusText
                    };
                }
            }).mount('#new-orders-shadcn-test-app');
        });
    </script>

    <style>
        /* Custom styles for n-button overrides */
        .text-blue-500 .n-icon {
            color: #3b82f6;
        }

        .text-indigo-500 .n-icon {
            color: #4f46e5;
        }

        .text-teal-500 .n-icon {
            color: #14b8a6;
        }

        .text-purple-500 .n-icon {
            color: #a855f7;
        }

        .text-blue-500:hover .n-icon {
            color: #2563eb;
        }

        .text-indigo-500:hover .n-icon {
            color: #4338ca;
        }

        .text-teal-500:hover .n-icon {
            color: #0d9488;
        }

        .text-purple-500:hover .n-icon {
            color: #9333ea;
        }

        /* Custom pagination styles */
        .n-button.n-button--quaternary.n-button--disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</body>

</html>