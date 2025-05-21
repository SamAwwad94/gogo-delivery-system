<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/logo-loader.css') }}">
    <link rel="stylesheet" href="{{ asset('css/new-orders-shadcn.css') }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="flex justify-between items-center">
                            <h4 class="card-title">New Orders ShadCN</h4>
                            <div class="flex items-center gap-2">
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
                    </div>
                    <div class="card-body">
                        <div id="simple-vue-app">
                            <!-- Filter Pills -->
                            <div class="flex flex-wrap gap-4 items-center mb-4">
                                <div v-for="filter in filters" :key="filter.name" class="filter-pill-container relative w-[160px]">
                                    <button
                                        class="filter-pill flex items-center justify-between px-3 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors"
                                        :class="{ 'active': filter.active }"
                                        @click="toggleFilter(filter)"
                                    >
                                        <span class="text-sm font-medium" v-text="filter.label"></span>
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m6 9 6 6 6-6" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="shadcn-table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="order in orders" :key="order.id">
                                            <td v-text="order.id"></td>
                                            <td v-text="order.customer"></td>
                                            <td v-text="order.date"></td>
                                            <td>
                                                <span :class="['status-badge', order.status]" v-text="order.status.charAt(0).toUpperCase() + order.status.slice(1)">
                                                </span>
                                            </td>
                                            <td>$<span v-text="order.amount.toFixed(2)"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        { name: 'payment', label: 'Payment', active: false },
                        { name: 'dateRange', label: 'Date Range', active: false },
                        { name: 'search', label: 'Search', active: false }
                    ]);

                    const orders = ref([
                        { id: 'ORD-001', customer: 'Hassan Nasrallah', date: '2023-05-15', status: 'pending', amount: 45.99 },
                        { id: 'ORD-002', customer: 'Zeinab Khalil', date: '2023-05-14', status: 'processing', amount: 78.50 },
                        { id: 'ORD-003', customer: 'Ali Haidar', date: '2023-05-13', status: 'completed', amount: 125.75 },
                        { id: 'ORD-004', customer: 'Sara Khoury', date: '2023-05-12', status: 'cancelled', amount: 67.25 }
                    ]);

                    function toggleFilter(filter) {
                        filter.active = !filter.active;
                    }

                    return {
                        filters,
                        orders,
                        toggleFilter
                    };
                }
            }).mount('#simple-vue-app');
        });
    </script>

    <style>
        .filter-pill {
            width: 160px;
        }
        .filter-pill.active {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-badge.pending {
            background-color: #fff7ed;
            color: #c2410c;
        }
        .status-badge.processing {
            background-color: #f0f9ff;
            color: #0369a1;
        }
        .status-badge.completed {
            background-color: #f0fdf4;
            color: #16a34a;
        }
        .status-badge.cancelled {
            background-color: #fef2f2;
            color: #dc2626;
        }
    </style>
</x-master-layout>
