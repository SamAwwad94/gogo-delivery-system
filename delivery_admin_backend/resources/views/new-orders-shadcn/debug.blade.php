<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vue Debug Page</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .filter-pill {
            background-color: #f0f0f0;
            border-radius: 20px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 160px;
            cursor: pointer;
        }
        .filter-pill:hover {
            background-color: #e0e0e0;
        }
        .filter-pill-active {
            background-color: #e6f7ff;
            color: #1890ff;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .table th {
            background-color: #f9f9f9;
            font-weight: 600;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-pending {
            background-color: #fffbe6;
            color: #faad14;
        }
        .status-processing {
            background-color: #e6f7ff;
            color: #1890ff;
        }
        .status-completed {
            background-color: #f6ffed;
            color: #52c41a;
        }
        .status-cancelled {
            background-color: #fff1f0;
            color: #f5222d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vue Debug Page</h1>
        
        <div id="app">
            <div class="card">
                <h2>Filter Pills</h2>
                <div class="filter-row">
                    @verbatim
                    <div
                        v-for="filter in filters"
                        :key="filter.name"
                        :class="['filter-pill', { 'filter-pill-active': filter.active }]"
                        @click="toggleFilter(filter)"
                    >
                        <span>{{ filter.label }}</span>
                        <span>▼</span>
                    </div>
                    @endverbatim
                </div>
                
                <h2>Orders Table</h2>
                <table class="table">
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
                            <td>{{ order.id }}</td>
                            <td>{{ order.customer }}</td>
                            <td>{{ order.date }}</td>
                            <td>
                                <span :class="['status-badge', 'status-' + order.status]">
                                    {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                                </span>
                            </td>
                            <td>${{ order.amount.toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const { createApp, ref } = Vue;
        
        createApp({
            setup() {
                const filters = ref([
                    { name: 'orderId', label: 'Order ID', active: false },
                    { name: 'status', label: 'Status', active: false },
                    { name: 'customer', label: 'Customer', active: false },
                    { name: 'date', label: 'Date', active: false },
                    { name: 'amount', label: 'Amount', active: false }
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
        }).mount('#app');
    </script>
</body>
</html>
