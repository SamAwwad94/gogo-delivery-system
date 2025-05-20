<table class="shadcn-table w-full">
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
            <th>Amount</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders ?? [] as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->date }}</td>
                <td>
                    <span class="badge {{ $order->status == 'completed' ? 'bg-success' : ($order->status == 'cancelled' ? 'bg-danger' : 'bg-warning') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>{{ $order->client->name ?? 'N/A' }}</td>
                <td>{{ $order->client->contact_number ?? 'N/A' }}</td>
                <td>{{ $order->pickup_point ? json_decode($order->pickup_point)->address : 'N/A' }}</td>
                <td>{{ $order->delivery_point ? json_decode($order->delivery_point)->address : 'N/A' }}</td>
                <td>
                    <span class="badge {{ $order->payment && $order->payment->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                        {{ $order->payment ? ucfirst($order->payment->payment_status) : 'N/A' }}
                    </span>
                </td>
                <td>{{ $order->total_amount }}</td>
                <td>
                    <div class="flex space-x-1">
                        <a href="{{ route('order.show', $order->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('order.edit', $order->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Pagination -->
<div class="pagination-box mt-4">
    {{ $orders->links() ?? '' }}
</div>
