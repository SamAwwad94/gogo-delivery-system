<x-master-layout :assets="$assets ?? []">
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shadcn-filter.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shadcn-responsive.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-header">
        <div class="page-title">
            <h4>{{ $pageTitle ?? __('message.order_list') }}</h4>
            <h6>{{ __('message.manage_your_orders') }}</h6>
        </div>
        <div class="page-btn">
            {!! $button ?? '' !!}
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- View Toggle -->
            <div class="flex justify-end mb-4">
                <div class="inline-flex items-center gap-2">
                    <a href="{{ route('order.export.csv') }}" id="export-csv"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium bg-green-600 text-white hover:bg-green-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" x2="12" y1="15" y2="3"></line>
                        </svg>
                        Export CSV
                    </a>
                    <a href="{{ route('order.export.pdf') }}" id="export-pdf"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium bg-red-600 text-white hover:bg-red-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        Export PDF
                    </a>
                    <a href="{{ route('order.map') }}" id="view-map"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                            <line x1="8" y1="2" x2="8" y2="18"></line>
                            <line x1="16" y1="6" x2="16" y2="22"></line>
                        </svg>
                        View Map
                    </a>
                </div>
            </div>

            <!-- Order Type Pills -->
            <div class="flex flex-wrap gap-2 mb-4 order-type-pills">
                <a href="{{ route('order.index') }}"
                   class="inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-medium {{ !request('order_type') ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80' }}">
                    All Orders
                </a>
                <a href="{{ route('order.index', ['order_type' => 'pending']) }}"
                   class="inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-medium {{ request('order_type') == 'pending' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80' }}">
                    Pending
                </a>
                <a href="{{ route('order.index', ['order_type' => 'schedule']) }}"
                   class="inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-medium {{ request('order_type') == 'schedule' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80' }}">
                    Scheduled
                </a>
                <a href="{{ route('order.index', ['order_type' => 'inprogress']) }}"
                   class="inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-medium {{ request('order_type') == 'inprogress' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80' }}">
                    In Progress
                </a>
                <a href="{{ route('order.index', ['order_type' => 'completed']) }}"
                   class="inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-medium {{ request('order_type') == 'completed' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80' }}">
                    Completed
                </a>
                <a href="{{ route('order.index', ['order_type' => 'cancelled']) }}"
                   class="inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-medium {{ request('order_type') == 'cancelled' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80' }}">
                    Cancelled
                </a>
                <a href="{{ route('order.index', ['order_type' => 'today']) }}"
                   class="inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-medium {{ request('order_type') == 'today' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80' }}">
                    Today
                </a>
                <a href="{{ route('order.index', ['order_type' => 'shipped_order']) }}"
                   class="inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-medium {{ request('order_type') == 'shipped_order' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80' }}">
                    Shipped
                </a>
            </div>

            <!-- Filter Form -->
            <form action="{{ route('order.index') }}" method="GET" id="order-filter-form">
                <input type="hidden" name="order_type" value="{{ request('order_type') }}">
                <div class="shadcn-filter-container mb-4">
                    <div class="shadcn-filter-header flex items-center justify-between p-3 bg-muted rounded-t-lg border-b border-border">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"></path>
                            </svg>
                            <h3 class="text-base font-medium">Filters</h3>
                        </div>
                        <div class="flex items-center">
                            <a href="{{ route('order.index', ['order_type' => request('order_type')]) }}" class="shadcn-button shadcn-button-ghost text-sm mr-2">Clear</a>
                            <button type="submit" class="shadcn-button shadcn-button-primary text-sm">Apply</button>
                        </div>
                    </div>
                    <div class="shadcn-filter-body p-3 bg-background rounded-b-lg border border-t-0 border-border">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                            <!-- Order ID Filter -->
                            <div class="shadcn-filter-item">
                                <label class="text-sm font-medium mb-1 block" for="order_id">Order ID</label>
                                <input type="text" id="order_id" name="order_id"
                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                    placeholder="Search by order ID" value="{{ request('order_id') }}">
                            </div>

                            <!-- Status Filter -->
                            <div class="shadcn-filter-item">
                                <label class="text-sm font-medium mb-1 block" for="status">Status</label>
                                <select id="status" name="status"
                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm">
                                    <option value="">All Statuses</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="create" {{ request('status') == 'create' ? 'selected' : '' }}>Created</option>
                                    <option value="courier_assigned" {{ request('status') == 'courier_assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="courier_accepted" {{ request('status') == 'courier_accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="courier_arrived" {{ request('status') == 'courier_arrived' ? 'selected' : '' }}>Arrived</option>
                                    <option value="courier_picked_up" {{ request('status') == 'courier_picked_up' ? 'selected' : '' }}>Picked Up</option>
                                    <option value="courier_departed" {{ request('status') == 'courier_departed' ? 'selected' : '' }}>Departed</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="reschedule" {{ request('status') == 'reschedule' ? 'selected' : '' }}>Rescheduled</option>
                                </select>
                            </div>

                            <!-- Customer Filter -->
                            <div class="shadcn-filter-item">
                                <label class="text-sm font-medium mb-1 block" for="client_id">Customer</label>
                                <select id="client_id" name="client_id"
                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm">
                                    <option value="">All Customers</option>
                                    @foreach(\App\Models\User::where('user_type', 'client')->get() as $client)
                                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Phone Filter -->
                            <div class="shadcn-filter-item">
                                <label class="text-sm font-medium mb-1 block" for="phone">Phone</label>
                                <input type="text" id="phone" name="phone"
                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                    placeholder="Search by phone" value="{{ request('phone') }}">
                            </div>

                            <!-- Pickup Location Filter -->
                            <div class="shadcn-filter-item">
                                <label class="text-sm font-medium mb-1 block" for="pickup_location">Pickup Location</label>
                                <input type="text" id="pickup_location" name="pickup_location"
                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                    placeholder="Search by location" value="{{ request('pickup_location') }}">
                            </div>

                            <!-- Delivery Location Filter -->
                            <div class="shadcn-filter-item">
                                <label class="text-sm font-medium mb-1 block" for="delivery_location">Delivery Location</label>
                                <input type="text" id="delivery_location" name="delivery_location"
                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                    placeholder="Search by location" value="{{ request('delivery_location') }}">
                            </div>

                            <!-- Payment Status Filter -->
                            <div class="shadcn-filter-item">
                                <label class="text-sm font-medium mb-1 block" for="payment_status">Payment Status</label>
                                <select id="payment_status" name="payment_status"
                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm">
                                    <option value="">All Payment Statuses</option>
                                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                </select>
                            </div>

                            <!-- Date Range Filter -->
                            <div class="shadcn-filter-item">
                                <label class="text-sm font-medium mb-1 block" for="from_date">From Date</label>
                                <input type="date" id="from_date" name="from_date"
                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                    value="{{ request('from_date') }}">
                            </div>

                            <div class="shadcn-filter-item">
                                <label class="text-sm font-medium mb-1 block" for="to_date">To Date</label>
                                <input type="date" id="to_date" name="to_date"
                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                    value="{{ request('to_date') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Orders Table -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse responsive-table" role="table" aria-label="Orders Table">
                    <caption class="sr-only">List of orders with their details and actions</caption>
                    <thead>
                        <tr class="bg-muted border-b border-border">
                            <th class="h-10 w-10 px-2 text-center align-middle font-medium text-muted-foreground" scope="col">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-primary focus:ring-primary" aria-label="Select all orders">
                            </th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Order ID</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Date</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Status</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Customer</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Phone</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Pickup Location</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Delivery Location</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground" scope="col">Payment Status</th>
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
                                            'in_progress', 'courier_assigned', 'courier_accepted', 'courier_arrived', 'courier_picked_up', 'courier_departed' => 'bg-blue-100 text-blue-800 ring-blue-200',
                                            'cancelled' => 'bg-red-100 text-red-800 ring-red-200',
                                            'reschedule' => 'bg-purple-100 text-purple-800 ring-purple-200',
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
                                            'reschedule' => 'Rescheduled',
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
                                            $pickupPoint = is_string($order->pickup_point) ? json_decode($order->pickup_point) : $order->pickup_point;
                                            $pickupLocation = $pickupPoint->address ?? 'N/A';
                                        }
                                    @endphp
                                    {{ Str::limit($pickupLocation, 30) }}
                                </td>
                                <td class="p-4 align-middle" data-label="Delivery Location">
                                    @php
                                        $deliveryLocation = 'N/A';
                                        if ($order->delivery_point) {
                                            $deliveryPoint = is_string($order->delivery_point) ? json_decode($order->delivery_point) : $order->delivery_point;
                                            $deliveryLocation = $deliveryPoint->address ?? 'N/A';
                                        }
                                    @endphp
                                    {{ Str::limit($deliveryLocation, 30) }}
                                </td>
                                <td class="p-4 align-middle" data-label="Payment Status" data-editable="true" data-field="payment_status">
                                    @php
                                        $paymentClass = match($order->payment_status) {
                                            'paid' => 'bg-green-100 text-green-800 ring-green-200',
                                            'unpaid' => 'bg-red-100 text-red-800 ring-red-200',
                                            'partial' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
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
                                <td colspan="10" class="p-4 text-center text-gray-500">No orders found</td>
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
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="{{ asset('js/unified-orders-table.js') }}"></script>
</x-master-layout>
