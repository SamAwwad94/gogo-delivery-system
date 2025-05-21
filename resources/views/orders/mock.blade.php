<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shadcn-filter.css') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.order_list') }}</h4>
            <h6>{{ __('message.manage_your_orders') }}</h6>
        </div>
        <div class="page-btn">
            {!! $button !!}
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- View Toggle -->
            <div class="flex justify-end mb-4">
                <div class="inline-flex items-center rounded-md border border-input bg-background p-1 text-muted-foreground">
                    <button class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-sm bg-primary text-primary-foreground shadow-sm" data-state="active">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><line x1="3" x2="21" y1="9" y2="9"></line><line x1="3" x2="21" y1="15" y2="15"></line><line x1="9" x2="9" y1="9" y2="21"></line><line x1="15" x2="15" y1="9" y2="21"></line></svg>
                        Table
                    </button>
                    <a href="{{ route('order.map') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon><line x1="9" x2="9" y1="3" y2="18"></line><line x1="15" x2="15" y1="6" y2="21"></line></svg>
                        Map
                    </a>
                </div>
            </div>

            <!-- Filter Pills -->
            <div class="shadcn-filter-container mb-4">
                <div class="shadcn-filter-header flex items-center justify-between p-3 bg-muted rounded-t-lg border-b border-border">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"></path></svg>
                        <h3 class="text-base font-medium">Filters</h3>
                    </div>
                    <div class="flex items-center">
                        <button class="shadcn-button shadcn-button-ghost text-sm mr-2">Clear</button>
                        <button class="shadcn-button shadcn-button-primary text-sm">Apply</button>
                    </div>
                </div>
                <div class="shadcn-filter-body p-3 bg-background rounded-b-lg border border-t-0 border-border">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                        <!-- Order ID Filter -->
                        <div class="shadcn-filter-item">
                            <label class="text-sm font-medium mb-1 block">Order ID</label>
                            <input type="text" class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm" placeholder="Search by order ID">
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="shadcn-filter-item">
                            <label class="text-sm font-medium mb-1 block">Status</label>
                            <select class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm">
                                <option value="">All Statuses</option>
                                <option value="draft">Draft</option>
                                <option value="create">Created</option>
                                <option value="courier_assigned">Assigned</option>
                                <option value="courier_accepted">Accepted</option>
                                <option value="courier_arrived">Arrived</option>
                                <option value="courier_picked_up">Picked Up</option>
                                <option value="courier_departed">Departed</option>
                                <option value="completed">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        
                        <!-- Customer Filter -->
                        <div class="shadcn-filter-item">
                            <label class="text-sm font-medium mb-1 block">Customer</label>
                            <select class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm">
                                <option value="">All Customers</option>
                                <option value="1">John Doe</option>
                                <option value="2">Jane Smith</option>
                                <option value="3">Robert Johnson</option>
                                <option value="4">Sarah Williams</option>
                                <option value="5">Michael Brown</option>
                            </select>
                        </div>
                        
                        <!-- Phone Filter -->
                        <div class="shadcn-filter-item">
                            <label class="text-sm font-medium mb-1 block">Phone</label>
                            <input type="text" class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm" placeholder="Search by phone">
                        </div>
                        
                        <!-- Pickup Location Filter -->
                        <div class="shadcn-filter-item">
                            <label class="text-sm font-medium mb-1 block">Pickup Location</label>
                            <input type="text" class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm" placeholder="Search by location">
                        </div>
                        
                        <!-- Delivery Location Filter -->
                        <div class="shadcn-filter-item">
                            <label class="text-sm font-medium mb-1 block">Delivery Location</label>
                            <input type="text" class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm" placeholder="Search by location">
                        </div>
                        
                        <!-- Payment Status Filter -->
                        <div class="shadcn-filter-item">
                            <label class="text-sm font-medium mb-1 block">Payment Status</label>
                            <select class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm">
                                <option value="">All Payment Statuses</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial</option>
                            </select>
                        </div>
                        
                        <!-- Date Range Filter -->
                        <div class="shadcn-filter-item">
                            <label class="text-sm font-medium mb-1 block">Date Range</label>
                            <div class="flex space-x-2">
                                <input type="date" class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm" placeholder="From">
                                <input type="date" class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm" placeholder="To">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-muted border-b border-border">
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Order ID</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Date</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Customer</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Phone</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Pickup Location</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Delivery Location</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Payment Status</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mockOrders as $order)
                            <tr class="border-b border-border hover:bg-muted/50">
                                <td class="p-4 align-middle">{{ $order['id'] }}</td>
                                <td class="p-4 align-middle">{{ $order['date'] }}</td>
                                <td class="p-4 align-middle">
                                    @php
                                        $statusClass = match($order['status']) {
                                            'delivered', 'completed' => 'bg-green-100 text-green-800 ring-green-200',
                                            'pending' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                                            'in_progress' => 'bg-blue-100 text-blue-800 ring-blue-200',
                                            'cancelled' => 'bg-red-100 text-red-800 ring-red-200',
                                            'courier_assigned' => 'bg-purple-100 text-purple-800 ring-purple-200',
                                            'courier_accepted' => 'bg-indigo-100 text-indigo-800 ring-indigo-200',
                                            'courier_picked_up' => 'bg-pink-100 text-pink-800 ring-pink-200',
                                            'courier_departed' => 'bg-orange-100 text-orange-800 ring-orange-200',
                                            default => 'bg-gray-100 text-gray-800 ring-gray-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                                        {{ $order['status_label'] }}
                                    </span>
                                </td>
                                <td class="p-4 align-middle">{{ $order['customer'] }}</td>
                                <td class="p-4 align-middle">{{ $order['phone'] }}</td>
                                <td class="p-4 align-middle">{{ $order['pickup_location'] }}</td>
                                <td class="p-4 align-middle">{{ $order['delivery_location'] }}</td>
                                <td class="p-4 align-middle">
                                    @php
                                        $paymentClass = match($order['payment_status']) {
                                            'paid' => 'bg-green-100 text-green-800 ring-green-200',
                                            'unpaid' => 'bg-red-100 text-red-800 ring-red-200',
                                            'partial' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                                            'refunded' => 'bg-purple-100 text-purple-800 ring-purple-200',
                                            default => 'bg-gray-100 text-gray-800 ring-gray-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $paymentClass }}">
                                        {{ $order['payment_status_label'] }}
                                    </span>
                                </td>
                                <td class="p-4 align-middle">
                                    <div class="flex space-x-2">
                                        <a href="#" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between mt-4">
                <div class="text-sm text-muted-foreground">
                    Showing 1 to 10 of 10 entries
                </div>
                <div class="flex items-center space-x-1">
                    <button class="relative inline-flex h-9 min-w-[36px] items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 text-muted-foreground">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <button class="relative inline-flex h-9 min-w-[36px] items-center justify-center rounded-md bg-primary text-primary-foreground shadow hover:bg-primary/90 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50">
                        1
                    </button>
                    <button class="relative inline-flex h-9 min-w-[36px] items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 text-muted-foreground">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>
