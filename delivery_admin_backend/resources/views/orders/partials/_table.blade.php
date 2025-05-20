<!-- Bulk Actions -->
<div class="bulk-actions mb-4" role="toolbar" aria-label="Bulk actions toolbar">
    <button id="bulk-delete" class="bulk-action bulk-action-delete" disabled aria-label="Delete selected orders" title="Delete selected orders">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2" aria-hidden="true"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
        Delete (<span class="count" aria-live="polite">0</span>)
    </button>
    <button id="bulk-print" class="bulk-action bulk-action-print" disabled aria-label="Print selected orders" title="Print selected orders">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2" aria-hidden="true"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Print (<span class="count" aria-live="polite">0</span>)
    </button>
</div>

<div class="overflow-x-auto">
    <table class="w-full border-collapse responsive-table" role="table" aria-label="Orders Table">
        <caption class="sr-only">List of orders with their details and actions</caption>
        <thead>
            <tr class="bg-muted border-b border-border">
                <th class="h-10 w-10 px-2 text-center align-middle font-medium text-muted-foreground" scope="col">
                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-primary focus:ring-primary" aria-label="Select all orders">
                </th>
                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" data-sortable="true" data-column="id" data-direction="{{ request('sort') == 'id' ? request('direction', 'asc') : '' }}" scope="col" aria-sort="{{ request('sort') == 'id' ? (request('direction', 'asc') == 'asc' ? 'ascending' : 'descending') : 'none' }}">
                    <button class="w-full text-left flex items-center" aria-label="Sort by Order ID">
                        Order ID
                        <span class="sort-icon ml-1" aria-hidden="true">
                            @if(request('sort') == 'id' && request('direction', 'asc') == 'asc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            @elseif(request('sort') == 'id' && request('direction') == 'desc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-30"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
                            @endif
                        </span>
                    </button>
                </th>
                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" data-sortable="true" data-column="created_at" data-direction="{{ request('sort') == 'created_at' ? request('direction', 'asc') : '' }}" scope="col" aria-sort="{{ request('sort') == 'created_at' ? (request('direction', 'asc') == 'asc' ? 'ascending' : 'descending') : 'none' }}">
                    <button class="w-full text-left flex items-center" aria-label="Sort by Date">
                        Date
                        <span class="sort-icon ml-1" aria-hidden="true">
                            @if(request('sort') == 'created_at' && request('direction', 'asc') == 'asc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            @elseif(request('sort') == 'created_at' && request('direction') == 'desc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-30"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
                            @endif
                        </span>
                    </button>
                </th>
                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" data-sortable="true" data-column="status" data-direction="{{ request('sort') == 'status' ? request('direction', 'asc') : '' }}" scope="col" aria-sort="{{ request('sort') == 'status' ? (request('direction', 'asc') == 'asc' ? 'ascending' : 'descending') : 'none' }}">
                    <button class="w-full text-left flex items-center" aria-label="Sort by Status">
                        Status
                        <span class="sort-icon ml-1" aria-hidden="true">
                            @if(request('sort') == 'status' && request('direction', 'asc') == 'asc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            @elseif(request('sort') == 'status' && request('direction') == 'desc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-30"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
                            @endif
                        </span>
                    </button>
                </th>
                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" data-sortable="true" data-column="client_id" data-direction="{{ request('sort') == 'client_id' ? request('direction', 'asc') : '' }}" scope="col" aria-sort="{{ request('sort') == 'client_id' ? (request('direction', 'asc') == 'asc' ? 'ascending' : 'descending') : 'none' }}">
                    <button class="w-full text-left flex items-center" aria-label="Sort by Customer">
                        Customer
                        <span class="sort-icon ml-1" aria-hidden="true">
                            @if(request('sort') == 'client_id' && request('direction', 'asc') == 'asc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            @elseif(request('sort') == 'client_id' && request('direction') == 'desc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-30"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
                            @endif
                        </span>
                    </button>
                </th>
                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Phone</th>
                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Pickup Location</th>
                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Delivery Location</th>
                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" data-sortable="true" data-column="payment_status" data-direction="{{ request('sort') == 'payment_status' ? request('direction', 'asc') : '' }}" scope="col" aria-sort="{{ request('sort') == 'payment_status' ? (request('direction', 'asc') == 'asc' ? 'ascending' : 'descending') : 'none' }}">
                    <button class="w-full text-left flex items-center" aria-label="Sort by Payment Status">
                        Payment Status
                        <span class="sort-icon ml-1" aria-hidden="true">
                            @if(request('sort') == 'payment_status' && request('direction', 'asc') == 'asc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            @elseif(request('sort') == 'payment_status' && request('direction') == 'desc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-30"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
                            @endif
                        </span>
                    </button>
                </th>
                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders ?? [] as $order)
                <tr class="border-b border-border hover:bg-muted/50" data-order-id="{{ $order->id }}" data-order-status="{{ $order->status }}">
                    <td class="p-2 text-center align-middle">
                        <input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-primary focus:ring-primary" value="{{ $order->id }}" aria-label="Select order {{ $order->id }}">
                    </td>
                    <td class="p-4 align-middle" data-label="Order ID" data-editable="true" data-field="id">{{ $order->id }}</td>
                    <td class="p-4 align-middle" data-label="Date">{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                    <td class="p-4 align-middle" data-label="Status" data-editable="true" data-field="status">
                        @php
                            $statusClass = match($order->status) {
                                'delivered', 'completed' => 'bg-green-100 text-green-800 ring-green-200',
                                'pending', 'create' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                                'in_progress' => 'bg-blue-100 text-blue-800 ring-blue-200',
                                'cancelled' => 'bg-red-100 text-red-800 ring-red-200',
                                'courier_assigned' => 'bg-purple-100 text-purple-800 ring-purple-200',
                                'courier_accepted' => 'bg-indigo-100 text-indigo-800 ring-indigo-200',
                                'courier_picked_up' => 'bg-pink-100 text-pink-800 ring-pink-200',
                                'courier_departed' => 'bg-orange-100 text-orange-800 ring-orange-200',
                                default => 'bg-gray-100 text-gray-800 ring-gray-200'
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
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClass }}" title="Double-click to edit status" aria-label="Order status: {{ $statusLabel }}. Double-click to edit.">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="p-4 align-middle" data-label="Customer">{{ $order->client->name ?? 'N/A' }}</td>
                    <td class="p-4 align-middle" data-label="Phone" data-editable="true" data-field="phone" title="Double-click to edit phone number" aria-label="Phone number: {{ $order->phone ?? 'N/A' }}. Double-click to edit.">{{ $order->phone ?? 'N/A' }}</td>
                    <td class="p-4 align-middle" data-label="Pickup Location">
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
                    <td class="p-4 align-middle" data-label="Delivery Location">
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
                    <td class="p-4 align-middle" data-label="Payment Status" data-editable="true" data-field="payment_status">
                        @php
                            $paymentClass = match($order->payment_status) {
                                'paid' => 'bg-green-100 text-green-800 ring-green-200',
                                'unpaid' => 'bg-red-100 text-red-800 ring-red-200',
                                'partial' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                                'refunded' => 'bg-purple-100 text-purple-800 ring-purple-200',
                                default => 'bg-gray-100 text-gray-800 ring-gray-200'
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $paymentClass }}" title="Double-click to edit payment status" aria-label="Payment status: {{ ucfirst($order->payment_status) }}. Double-click to edit.">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="p-4 align-middle" data-label="Actions">
                        <div class="flex space-x-2" role="group" aria-label="Order actions">
                            <a href="{{ route('order.show', $order->id) }}" class="text-blue-600 hover:text-blue-900 p-1 rounded-md hover:bg-blue-50 transition-colors" title="View order details" aria-label="View order {{ $order->id }}">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                                <span class="sr-only">View</span>
                            </a>
                            <a href="{{ route('order.edit', $order->id) }}" class="text-green-600 hover:text-green-900 p-1 rounded-md hover:bg-green-50 transition-colors" title="Edit order" aria-label="Edit order {{ $order->id }}">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                                <span class="sr-only">Edit</span>
                            </a>
                            <form action="{{ route('order.destroy', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-50 transition-colors" title="Delete order" aria-label="Delete order {{ $order->id }}">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                    <span class="sr-only">Delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="p-4 text-center text-gray-500">No orders found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="flex items-center justify-between mt-4" aria-label="Pagination navigation">
    <div class="text-sm text-muted-foreground" aria-live="polite">
        @if($orders ?? null)
            Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() ?? 0 }} entries
        @else
            Showing 0 to 0 of 0 entries
        @endif
    </div>
    <nav role="navigation" aria-label="Pagination Navigation">
        @if($orders ?? null)
            {{ $orders->appends(request()->query())->links('pagination.tailwind') }}
        @endif
    </nav>
</div>
