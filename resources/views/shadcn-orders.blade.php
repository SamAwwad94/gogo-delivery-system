<x-master-layout :assets="$assets ?? []">
    <link rel="stylesheet" href="{{ asset('css/shadcn-filter.css') }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Orders</h4>
                    </div>
                    <div class="card-body">
                        <!-- ShadCN Filter -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="flex flex-wrap items-center justify-between mb-4">
                                    <h4 class="text-xl font-bold">New Orders ShadCN</h4>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus mr-1"></i> New Order
                                        </a>
                                        <button class="btn btn-outline-secondary" id="refresh-button">
                                            <i class="fas fa-sync-alt mr-1"></i> Refresh
                                        </button>
                                    </div>
                                </div>

                                <!-- Horizontal Filter Pills -->
                                <div class="filter-pills-container">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <!-- Order ID Filter -->
                                        <div class="filter-pill-container relative w-[180px]">
                                            <button
                                                class="filter-pill flex items-center justify-between px-3 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <span class="text-sm font-medium">Order ID</span>
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m6 9 6 6 6-6" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div
                                                class="filter-dropdown absolute left-0 top-full mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 p-2 z-50 hidden">
                                                <input type="text"
                                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                                    placeholder="Search by ID" name="order_id" id="order_id">
                                            </div>
                                        </div>

                                        <!-- Status Filter -->
                                        <div class="filter-pill-container relative w-[180px]">
                                            <button
                                                class="filter-pill flex items-center justify-between px-3 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <span class="text-sm font-medium">Status</span>
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m6 9 6 6 6-6" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div
                                                class="filter-dropdown absolute left-0 top-full mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 p-2 z-50 hidden">
                                                <select
                                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                                    name="status" id="status">
                                                    <option value="">All Statuses</option>
                                                    <option value="pending">Pending</option>
                                                    <option value="processing">Processing</option>
                                                    <option value="shipped">Shipped</option>
                                                    <option value="delivered">Delivered</option>
                                                    <option value="cancelled">Cancelled</option>
                                                    <option value="returned">Returned</option>
                                                    <option value="refunded">Refunded</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Customer Filter -->
                                        <div class="filter-pill-container relative w-[180px]">
                                            <button
                                                class="filter-pill flex items-center justify-between px-3 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <span class="text-sm font-medium">Customer</span>
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m6 9 6 6 6-6" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div
                                                class="filter-dropdown absolute left-0 top-full mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 p-2 z-50 hidden">
                                                <input type="text"
                                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                                    placeholder="Search customer" name="customer" id="customer">
                                            </div>
                                        </div>

                                        <!-- Phone Filter -->
                                        <div class="filter-pill-container relative w-[180px]">
                                            <button
                                                class="filter-pill flex items-center justify-between px-3 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <span class="text-sm font-medium">Phone</span>
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m6 9 6 6 6-6" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div
                                                class="filter-dropdown absolute left-0 top-full mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 p-2 z-50 hidden">
                                                <input type="text"
                                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                                    placeholder="Search phone" name="phone" id="phone">
                                            </div>
                                        </div>

                                        <!-- Location Filter -->
                                        <div class="filter-pill-container relative w-[180px]">
                                            <button
                                                class="filter-pill flex items-center justify-between px-3 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <span class="text-sm font-medium">Location</span>
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m6 9 6 6 6-6" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div
                                                class="filter-dropdown absolute left-0 top-full mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 p-2 z-50 hidden">
                                                <input type="text"
                                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                                    placeholder="Search location" name="location" id="location">
                                            </div>
                                        </div>

                                        <!-- Payment Status Filter -->
                                        <div class="filter-pill-container relative w-[180px]">
                                            <button
                                                class="filter-pill flex items-center justify-between px-3 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <span class="text-sm font-medium">Payment</span>
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m6 9 6 6 6-6" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div
                                                class="filter-dropdown absolute left-0 top-full mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 p-2 z-50 hidden">
                                                <select
                                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                                    name="payment_status" id="payment_status">
                                                    <option value="">All Payment Statuses</option>
                                                    <option value="paid">Paid</option>
                                                    <option value="unpaid">Unpaid</option>
                                                    <option value="partial">Partially Paid</option>
                                                    <option value="refunded">Refunded</option>
                                                    <option value="failed">Failed</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Date Range Filter -->
                                        <div class="filter-pill-container relative w-[180px]">
                                            <button
                                                class="filter-pill flex items-center justify-between px-3 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <span class="text-sm font-medium">Date Range</span>
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m6 9 6 6 6-6" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div
                                                class="filter-dropdown absolute left-0 top-full mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 p-2 z-50 hidden">
                                                <select
                                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                                    name="date_range" id="date_range">
                                                    <option value="">All Time</option>
                                                    <option value="today">Today</option>
                                                    <option value="yesterday">Yesterday</option>
                                                    <option value="this_week">This Week</option>
                                                    <option value="last_week">Last Week</option>
                                                    <option value="this_month">This Month</option>
                                                    <option value="last_month">Last Month</option>
                                                    <option value="custom">Custom Range</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Search -->
                                        <div class="filter-pill-container relative w-[180px]">
                                            <button
                                                class="filter-pill flex items-center justify-between px-3 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <span class="text-sm font-medium">Search</span>
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="11" cy="11" r="8"></circle>
                                                        <path d="m21 21-4.3-4.3"></path>
                                                    </svg>
                                                </span>
                                            </button>
                                            <div
                                                class="filter-dropdown absolute left-0 top-full mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 p-2 z-50 hidden">
                                                <input type="text"
                                                    class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm"
                                                    placeholder="Search all..." name="search" id="search">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ShadCN Table -->
                        <div class="table-responsive">
                            <table class="shadcn-table">
                                <thead>
                                    <tr>
                                        <th data-sortable="true" data-column="id">Order ID</th>
                                        <th data-sortable="true" data-column="customer">Customer</th>
                                        <th data-sortable="true" data-column="date">Date</th>
                                        <th data-sortable="true" data-column="total">Total</th>
                                        <th data-sortable="true" data-column="status">Status</th>
                                        <th data-sortable="true" data-column="payment">Payment</th>
                                        <th data-sortable="true" data-column="delivery">Delivery</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr data-selectable="true">
                                            <td data-column="id">#{{ $order->id }}</td>
                                            <td data-column="customer">{{ $order->customer_name }}</td>
                                            <td data-column="date">{{ $order->created_at->format('M d, Y') }}</td>
                                            <td data-column="total">
                                                @if($order->currency == 'USD')
                                                    ${{ number_format($order->total, 2) }}
                                                @else
                                                    {{ number_format($order->total) }} LBP
                                                @endif
                                            </td>
                                            <td data-column="status">
                                                <span class="status-badge {{ strtolower($order->status) }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td data-column="payment">
                                                <span class="payment-badge {{ strtolower($order->payment_status) }}">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            </td>
                                            <td data-column="delivery">
                                                <span class="status-badge {{ strtolower($order->delivery_status) }}">
                                                    {{ ucfirst($order->delivery_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('orders.show', $order->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('orders.edit', $order->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-order"
                                                        data-id="{{ $order->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of
                                {{ $orders->total() }} entries
                            </div>
                            <div>
                                {{ $orders->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/shadcn-bundle.min.js') }}"></script>
        <script src="{{ asset('js/shadcn-components.min.js') }}"></script>
        <script src="{{ asset('js/shadcn-filter-configs.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize filter pills
                initFilterPills();

                // Initialize refresh button
                const refreshButton = document.getElementById('refresh-button');
                if (refreshButton) {
                    refreshButton.addEventListener('click', function () {
                        window.location.href = window.location.pathname;
                    });
                }

                // Initialize delete buttons
                const deleteButtons = document.querySelectorAll('.delete-order');
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const orderId = this.getAttribute('data-id');

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Submit delete form
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = `/orders/${orderId}`;
                                form.innerHTML = `
                                                            @csrf
                                                            @method('DELETE')
                                                        `;
                                document.body.appendChild(form);
                                form.submit();
                            }
                        });
                    });
                });

                // Initialize filter pills functionality
                function initFilterPills() {
                    const filterPills = document.querySelectorAll('.filter-pill');

                    // Toggle dropdown on pill click
                    filterPills.forEach(pill => {
                        pill.addEventListener('click', function (e) {
                            e.preventDefault();

                            // Get the dropdown
                            const dropdown = this.closest('.filter-pill-container').querySelector('.filter-dropdown');

                            // Close all other dropdowns
                            document.querySelectorAll('.filter-dropdown').forEach(el => {
                                if (el !== dropdown) {
                                    el.classList.add('hidden');
                                }
                            });

                            // Toggle this dropdown
                            dropdown.classList.toggle('hidden');

                            // Focus on input if it exists
                            const input = dropdown.querySelector('input, select');
                            if (input) {
                                setTimeout(() => {
                                    input.focus();
                                }, 100);
                            }
                        });
                    });

                    // Close dropdowns when clicking outside
                    document.addEventListener('click', function (e) {
                        if (!e.target.closest('.filter-pill-container')) {
                            document.querySelectorAll('.filter-dropdown').forEach(dropdown => {
                                dropdown.classList.add('hidden');
                            });
                        }
                    });

                    // Handle filter changes
                    document.querySelectorAll('.filter-dropdown input, .filter-dropdown select').forEach(input => {
                        input.addEventListener('change', function () {
                            const pillContainer = this.closest('.filter-pill-container');
                            const pill = pillContainer.querySelector('.filter-pill');
                            const pillLabel = pill.querySelector('span:first-child');

                            // Update pill label to show selected value
                            if (this.value) {
                                let displayValue = this.value;

                                // For select elements, show the selected option text
                                if (this.tagName === 'SELECT' && this.selectedOptions[0]) {
                                    displayValue = this.selectedOptions[0].textContent;
                                }

                                pillLabel.innerHTML = `${pillLabel.textContent.split(':')[0]}: <span class="font-bold">${displayValue}</span>`;
                                pill.classList.add('active');
                            } else {
                                pillLabel.textContent = pillLabel.textContent.split(':')[0];
                                pill.classList.remove('active');
                            }

                            // Auto-apply filter
                            applyFilters();

                            // Hide dropdown
                            pillContainer.querySelector('.filter-dropdown').classList.add('hidden');
                        });

                        // For text inputs, add keyup event with Enter key handling
                        if (input.type === 'text') {
                            input.addEventListener('keyup', function (e) {
                                const pillContainer = this.closest('.filter-pill-container');
                                const pill = pillContainer.querySelector('.filter-pill');
                                const pillLabel = pill.querySelector('span:first-child');

                                // Update pill label
                                if (this.value) {
                                    pillLabel.innerHTML = `${pillLabel.textContent.split(':')[0]}: <span class="font-bold">${this.value}</span>`;
                                    pill.classList.add('active');
                                } else {
                                    pillLabel.textContent = pillLabel.textContent.split(':')[0];
                                    pill.classList.remove('active');
                                }

                                // Apply on Enter key
                                if (e.key === 'Enter') {
                                    applyFilters();
                                    pillContainer.querySelector('.filter-dropdown').classList.add('hidden');
                                }
                            });
                        }
                    });

                    // Apply filters function
                    function applyFilters() {
                        // Collect all filter values
                        const filterValues = {};
                        document.querySelectorAll('.filter-dropdown input, .filter-dropdown select').forEach(input => {
                            if (input.value) {
                                filterValues[input.name] = input.value;
                            }
                        });

                        // Build query string
                        const queryParams = [];
                        for (const key in filterValues) {
                            queryParams.push(`${key}=${encodeURIComponent(filterValues[key])}`);
                        }

                        // Redirect with filters
                        if (queryParams.length > 0) {
                            window.location.href = `${window.location.pathname}?${queryParams.join('&')}`;
                        } else {
                            window.location.href = window.location.pathname;
                        }
                    }
                }
            });
        </script>
    @endpush
</x-master-layout>