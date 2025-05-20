<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            font-size: 12px;
            margin: 5px 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .status-completed, .status-paid {
            background-color: #d1fae5;
            color: #047857;
        }
        .status-pending, .status-create, .status-partial {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-cancelled, .status-unpaid {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .status-in_progress, .status-courier_assigned, .status-courier_accepted, 
        .status-courier_arrived, .status-courier_picked_up, .status-courier_departed {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 20px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orders Export</h1>
        <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Pickup Location</th>
                <th>Delivery Location</th>
                <th>Payment Status</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                    <td>
                        @php
                            $statusClass = match($order->status) {
                                'delivered', 'completed' => 'status-completed',
                                'pending', 'create' => 'status-pending',
                                'in_progress', 'courier_assigned', 'courier_accepted', 'courier_arrived', 'courier_picked_up', 'courier_departed' => 'status-in_progress',
                                'cancelled' => 'status-cancelled',
                                default => ''
                            };
                            
                            $statusLabel = match($order->status) {
                                'draft' => 'Draft',
                                'create' => 'Created',
                                'courier_assigned' => 'Assigned',
                                'courier_accepted' => 'Accepted',
                                'courier_arrived' => 'Arrived',
                                'courier_picked_up' => 'Picked Up',
                                'courier_departed' => 'Departed',
                                'completed' => 'Delivered',
                                'cancelled' => 'Cancelled',
                                default => ucfirst($order->status)
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td>{{ $order->client->name ?? 'N/A' }}</td>
                    <td>{{ $order->phone ?? 'N/A' }}</td>
                    <td>
                        @php
                            $pickupLocation = 'N/A';
                            if ($order->pickup_point) {
                                $pickupPoint = is_string($order->pickup_point) ? json_decode($order->pickup_point, true) : $order->pickup_point;
                                if (is_array($pickupPoint) && isset($pickupPoint['address'])) {
                                    $pickupLocation = $pickupPoint['address'];
                                }
                            }
                        @endphp
                        {{ $pickupLocation }}
                    </td>
                    <td>
                        @php
                            $deliveryLocation = 'N/A';
                            if ($order->delivery_point) {
                                $deliveryPoint = is_string($order->delivery_point) ? json_decode($order->delivery_point, true) : $order->delivery_point;
                                if (is_array($deliveryPoint) && isset($deliveryPoint['address'])) {
                                    $deliveryLocation = $deliveryPoint['address'];
                                }
                            }
                        @endphp
                        {{ $deliveryLocation }}
                    </td>
                    <td>
                        @php
                            $paymentClass = match($order->payment_status) {
                                'paid' => 'status-paid',
                                'unpaid' => 'status-unpaid',
                                'partial' => 'status-partial',
                                default => ''
                            };
                        @endphp
                        <span class="status-badge {{ $paymentClass }}">{{ ucfirst($order->payment_status) }}</span>
                    </td>
                    <td>{{ $order->total_amount }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">No orders found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>© {{ date('Y') }} Gogo Delivery. All rights reserved.</p>
        <p>Contact: support@gogo.delivery | 00961 03 900 270</p>
    </div>
</body>
</html>
