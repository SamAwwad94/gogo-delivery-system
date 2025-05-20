<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/logo-loader.css') }}">
    <link rel="stylesheet" href="{{ asset('css/new-orders-shadcn.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fix-chevron.css') }}">

    <!-- Include Naive UI styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/naive-ui/2.34.4/index.css" />

    <div class="bg-gray-100 min-h-screen">
        <!-- Vue App Container -->
        <div id="new-orders-shadcn-fixed-app">
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
                        <div class="flex space-x-4">
                            <a href="{{ route('order.create') }}" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md text-sm font-medium flex items-center shadow-sm transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                New Order
                            </a>
                            <button class="px-3 py-2 border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 rounded-md text-sm font-medium flex items-center shadow-sm transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Modern Filter Pills Section -->
                    <div class="mt-4 flex flex-wrap gap-4 items-center mb-4">
                        <div v-for="filter in filters" :key="filter.name" class="filter-pill w-[160px]" :class="{ 'filter-pill-active': filter.active }" @click="toggleFilter(filter)">
                            <span v-text="filter.label"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full px-6 py-6">
                <div class="bg-white shadow rounded-md overflow-hidden w-full max-w-full">
                    <!-- Data Table -->
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="order in orders" :key="order.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" v-text="order.id"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" v-text="order.date"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" v-text="order.customer"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusClass(order.status)" v-text="getStatusText(order.status)">
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getPaymentStatusClass(order.paymentStatus)" v-text="getPaymentStatusText(order.paymentStatus)">
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<span v-text="order.amount.toFixed(2)"></span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900">View</button>
                                        <button class="text-green-600 hover:text-green-900">Edit</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Vue 3 -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const { createApp, ref } = Vue;

            createApp({
                setup() {
                    const filters = ref([
                        { name: 'orderId', label: 'Order ID', active: false },
                        { name: 'status', label: 'Status', active: false },
                        { name: 'customer', label: 'Customer', active: false },
                        { name: 'phone', label: 'Phone', active: false },
                        { name: 'location', label: 'Location', active: false },
                        { name: 'paymentStatus', label: 'Payment', active: false },
                        { name: 'dateRange', label: 'Date Range', active: false },
                        { name: 'search', label: 'Search', active: false }
                    ]);

                    const orders = ref([
                        {
                            id: 'ORD-001',
                            date: '2023-05-15',
                            customer: 'Hassan Nasrallah',
                            status: 'pending',
                            paymentStatus: 'unpaid',
                            amount: 45.99
                        },
                        {
                            id: 'ORD-002',
                            date: '2023-05-14',
                            customer: 'Zeinab Khalil',
                            status: 'processing',
                            paymentStatus: 'paid',
                            amount: 78.50
                        },
                        {
                            id: 'ORD-003',
                            date: '2023-05-13',
                            customer: 'Ali Haidar',
                            status: 'completed',
                            paymentStatus: 'paid',
                            amount: 125.75
                        },
                        {
                            id: 'ORD-004',
                            date: '2023-05-12',
                            customer: 'Sara Khoury',
                            status: 'cancelled',
                            paymentStatus: 'refunded',
                            amount: 67.25
                        }
                    ]);

                    function toggleFilter(filter) {
                        filter.active = !filter.active;
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
                        filters,
                        orders,
                        toggleFilter,
                        getStatusClass,
                        getStatusText,
                        getPaymentStatusClass,
                        getPaymentStatusText
                    };
                }
            }).mount('#new-orders-shadcn-fixed-app');
        });
    </script>

    <style>
        .filter-pill {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
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
</x-master-layout>
