<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Orders ShadCN Simple</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto p-4">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-4">New Orders ShadCN Simple</h1>

            <!-- Vue App Container -->
            <div id="app">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-4">Orders Table</h2>

                    <!-- Search and Filters -->
                    <div class="mb-4 flex flex-wrap gap-2">
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Search orders..."
                            class="px-3 py-2 border border-gray-300 rounded-md"
                        >

                        <select
                            v-model="statusFilter"
                            class="px-3 py-2 border border-gray-300 rounded-md"
                        >
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Table -->
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border-b">Order ID</th>
                                <th class="px-4 py-2 border-b">Date</th>
                                <th class="px-4 py-2 border-b">Customer</th>
                                <th class="px-4 py-2 border-b">Status</th>
                                <th class="px-4 py-2 border-b">Amount</th>
                                <th class="px-4 py-2 border-b">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-gray-50">
                                <td class="px-4 py-2 border-b">@{{ order.id }}</td>
                                <td class="px-4 py-2 border-b">@{{ order.date }}</td>
                                <td class="px-4 py-2 border-b">@{{ order.customer }}</td>
                                <td class="px-4 py-2 border-b">
                                    <span
                                        :class="{
                                            'px-2 py-1 rounded-full text-xs font-medium': true,
                                            'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                            'bg-blue-100 text-blue-800': order.status === 'processing',
                                            'bg-green-100 text-green-800': order.status === 'completed',
                                            'bg-red-100 text-red-800': order.status === 'cancelled'
                                        }"
                                    >
                                        @{{ order.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border-b">$@{{ order.amount.toFixed(2) }}</td>
                                <td class="px-4 py-2 border-b">
                                    <button
                                        @click="viewOrder(order.id)"
                                        class="px-2 py-1 bg-blue-500 text-white rounded-md text-xs mr-1"
                                    >
                                        View
                                    </button>
                                    <button
                                        @click="editOrder(order.id)"
                                        class="px-2 py-1 bg-green-500 text-white rounded-md text-xs"
                                    >
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    searchQuery: '',
                    statusFilter: '',
                    orders: [
                        {
                            id: 'ORD-001',
                            date: '2023-05-15',
                            customer: 'Hassan Nasrallah',
                            status: 'pending',
                            amount: 45.99
                        },
                        {
                            id: 'ORD-002',
                            date: '2023-05-14',
                            customer: 'Zeinab Khalil',
                            status: 'processing',
                            amount: 78.50
                        },
                        {
                            id: 'ORD-003',
                            date: '2023-05-13',
                            customer: 'Ali Haidar',
                            status: 'completed',
                            amount: 125.75
                        },
                        {
                            id: 'ORD-004',
                            date: '2023-05-12',
                            customer: 'Sara Khoury',
                            status: 'cancelled',
                            amount: 67.25
                        }
                    ]
                };
            },
            computed: {
                filteredOrders() {
                    let result = this.orders;

                    // Apply status filter
                    if (this.statusFilter) {
                        result = result.filter(order => order.status === this.statusFilter);
                    }

                    // Apply search filter
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        result = result.filter(order =>
                            order.id.toLowerCase().includes(query) ||
                            order.customer.toLowerCase().includes(query)
                        );
                    }

                    return result;
                }
            },
            methods: {
                viewOrder(id) {
                    alert(`Viewing order ${id}`);
                },
                editOrder(id) {
                    alert(`Editing order ${id}`);
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
