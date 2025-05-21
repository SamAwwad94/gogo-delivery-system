<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shadcn-filter.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.modern_orders') }}</h4>
            <h6>{{ __('message.manage_your_orders') }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('order.create') }}" class="btn btn-added">
                <img src="{{ asset('assets/img/icons/plus.svg') }}" alt="img" class="me-1">
                {{ __('message.add_new_order') }}
            </a>
            <a href="{{ route('admin.orders-modern.export') }}" class="btn btn-added ms-2">
                <img src="{{ asset('assets/img/icons/download.svg') }}" alt="img" class="me-1">
                {{ __('message.export_csv') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Filter Pills -->
            <div class="filter-pills-container">
                <div class="flex space-x-2 flex-nowrap">
                    <button id="order-id-filter" type="button" class="filter-pill" data-filter="order_id">
                        <i class="fas fa-hashtag"></i> Order ID
                    </button>
                    <button id="date-filter" type="button" class="filter-pill" data-filter="date">
                        <i class="fas fa-calendar-alt"></i> Date
                    </button>
                    <button id="status-filter" type="button" class="filter-pill" data-filter="status">
                        <i class="fas fa-tag"></i> Status
                    </button>
                    <button id="customer-filter" type="button" class="filter-pill" data-filter="customer">
                        <i class="fas fa-user"></i> Customer
                    </button>
                    <button id="phone-filter" type="button" class="filter-pill" data-filter="phone">
                        <i class="fas fa-phone"></i> Phone
                    </button>
                    <button id="pickup-location-filter" type="button" class="filter-pill" data-filter="pickup_location">
                        <i class="fas fa-map-marker-alt"></i> Pickup Location
                    </button>
                    <button id="delivery-location-filter" type="button" class="filter-pill"
                        data-filter="delivery_location">
                        <i class="fas fa-map-marker-alt"></i> Delivery Location
                    </button>
                    <button id="payment-status-filter" type="button" class="filter-pill" data-filter="payment_status">
                        <i class="fas fa-credit-card"></i> Payment Status
                    </button>
                </div>
            </div>

            <!-- Filter Popups -->
            <div id="filter-popups" class="relative">
                <!-- Order ID Filter Popup -->
                <div id="order_id-popup"
                    class="filter-popup hidden absolute z-10 mt-2 w-72 rounded-md bg-white shadow-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-medium mb-2">Filter by Order ID</h3>
                    <input type="text" id="order_id-input"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter Order ID">
                    <div class="mt-3 flex justify-end space-x-2">
                        <button type="button"
                            class="filter-apply-btn px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700"
                            data-filter="order_id">Apply</button>
                        <button type="button"
                            class="filter-clear-btn px-3 py-1.5 border border-gray-300 text-gray-700 text-xs rounded-md hover:bg-gray-50"
                            data-filter="order_id">Clear</button>
                    </div>
                </div>

                <!-- Date Filter Popup -->
                <div id="date-popup"
                    class="filter-popup hidden absolute z-10 mt-2 w-72 rounded-md bg-white shadow-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-medium mb-2">Filter by Date Range</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Start Date</label>
                            <input type="text" id="date-start-input"
                                class="flatpickr w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Select start date">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">End Date</label>
                            <input type="text" id="date-end-input"
                                class="flatpickr w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Select end date">
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end space-x-2">
                        <button type="button"
                            class="filter-apply-btn px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700"
                            data-filter="date">Apply</button>
                        <button type="button"
                            class="filter-clear-btn px-3 py-1.5 border border-gray-300 text-gray-700 text-xs rounded-md hover:bg-gray-50"
                            data-filter="date">Clear</button>
                    </div>
                </div>

                <!-- Status Filter Popup -->
                <div id="status-popup"
                    class="filter-popup hidden absolute z-10 mt-2 w-72 rounded-md bg-white shadow-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-medium mb-2">Filter by Status</h3>
                    <select id="status-input"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="assigned">Assigned</option>
                        <option value="in_transit">In Transit</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <div class="mt-3 flex justify-end space-x-2">
                        <button type="button"
                            class="filter-apply-btn px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700"
                            data-filter="status">Apply</button>
                        <button type="button"
                            class="filter-clear-btn px-3 py-1.5 border border-gray-300 text-gray-700 text-xs rounded-md hover:bg-gray-50"
                            data-filter="status">Clear</button>
                    </div>
                </div>

                <!-- Customer Filter Popup -->
                <div id="customer-popup"
                    class="filter-popup hidden absolute z-10 mt-2 w-72 rounded-md bg-white shadow-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-medium mb-2">Filter by Customer</h3>
                    <select id="customer-input"
                        class="select2 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Customers</option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <div class="mt-3 flex justify-end space-x-2">
                        <button type="button"
                            class="filter-apply-btn px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700"
                            data-filter="customer">Apply</button>
                        <button type="button"
                            class="filter-clear-btn px-3 py-1.5 border border-gray-300 text-gray-700 text-xs rounded-md hover:bg-gray-50"
                            data-filter="customer">Clear</button>
                    </div>
                </div>

                <!-- Payment Status Filter Popup -->
                <div id="payment_status-popup"
                    class="filter-popup hidden absolute z-10 mt-2 w-72 rounded-md bg-white shadow-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-medium mb-2">Filter by Payment Status</h3>
                    <select id="payment_status-input"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Payment Statuses</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="partial">Partial</option>
                    </select>
                    <div class="mt-3 flex justify-end space-x-2">
                        <button type="button"
                            class="filter-apply-btn px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700"
                            data-filter="payment_status">Apply</button>
                        <button type="button"
                            class="filter-clear-btn px-3 py-1.5 border border-gray-300 text-gray-700 text-xs rounded-md hover:bg-gray-50"
                            data-filter="payment_status">Clear</button>
                    </div>
                </div>
            </div>

            <!-- Active Filters -->
            <div id="active-filters" class="mb-4 flex flex-wrap gap-2">
                <!-- Active filters will be displayed here -->
            </div>

            <!-- Orders Table -->
            <div class="shadcn-table-container" id="orders-table">
                <div class="loading-overlay hidden">
                    <!-- Skeleton Loader -->
                    <div class="skeleton-loader">
                        <div class="skeleton-header">
                            <div class="skeleton-row">
                                <div class="skeleton-cell skeleton-header-cell"></div>
                                <div class="skeleton-cell skeleton-header-cell"></div>
                                <div class="skeleton-cell skeleton-header-cell"></div>
                                <div class="skeleton-cell skeleton-header-cell"></div>
                                <div class="skeleton-cell skeleton-header-cell"></div>
                                <div class="skeleton-cell skeleton-header-cell"></div>
                                <div class="skeleton-cell skeleton-header-cell"></div>
                            </div>
                        </div>
                        <div class="skeleton-body">
                            <div class="skeleton-row">
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell skeleton-badge"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                            </div>
                            <div class="skeleton-row">
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell skeleton-badge"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                            </div>
                            <div class="skeleton-row">
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell skeleton-badge"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                            </div>
                            <div class="skeleton-row">
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell skeleton-badge"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                            </div>
                            <div class="skeleton-row">
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell skeleton-badge"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                                <div class="skeleton-cell"></div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                <td data-label="Order ID">{{ $order->id }}</td>
                                <td data-label="Date">{{ $order->date }}</td>
                                <td data-label="Status">
                                    <span
                                        class="badge {{ $order->status == 'completed' ? 'bg-success' : ($order->status == 'cancelled' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td data-label="Customer">{{ $order->client->name ?? 'N/A' }}</td>
                                <td data-label="Phone">{{ $order->client->contact_number ?? 'N/A' }}</td>
                                <td data-label="Pickup Location">
                                    {{ $order->pickup_point ? json_decode($order->pickup_point)->address : 'N/A' }}</td>
                                <td data-label="Delivery Location">
                                    {{ $order->delivery_point ? json_decode($order->delivery_point)->address : 'N/A' }}</td>
                                <td data-label="Payment Status">
                                    <span
                                        class="badge {{ $order->payment && $order->payment->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $order->payment ? ucfirst($order->payment->payment_status) : 'N/A' }}
                                    </span>
                                </td>
                                <td data-label="Amount">{{ $order->total_amount }}</td>
                                <td data-label="Actions">
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
            </div>

            <!-- Pagination -->
            <div class="pagination-box mt-4">
                {{ $orders->links() ?? '' }}
            </div>
        </div>
    </div>

    <!-- Additional Styles -->
    <style>
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 100;
            border-radius: 0.5rem;
        }

        .loading-overlay.hidden {
            display: none;
        }

        /* Skeleton Loader */
        .skeleton-loader {
            width: 100%;
            padding: 1rem;
        }

        .skeleton-header,
        .skeleton-body {
            width: 100%;
        }

        .skeleton-row {
            display: flex;
            margin-bottom: 0.75rem;
            width: 100%;
        }

        .skeleton-cell {
            height: 1.5rem;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 0.25rem;
            margin-right: 1rem;
            flex: 1;
        }

        .dark .skeleton-cell {
            background: linear-gradient(90deg, hsl(var(--muted)) 25%, hsl(var(--accent)) 50%, hsl(var(--muted)) 75%);
        }

        .skeleton-header-cell {
            height: 2rem;
            background-color: #e5e7eb;
        }

        .dark .skeleton-header-cell {
            background-color: hsl(var(--accent));
        }

        .skeleton-badge {
            width: 5rem;
            border-radius: 9999px;
            flex: 0 0 auto;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        /* Legacy spinner (keeping for compatibility) */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4f46e5;
            animation: spin 1s linear infinite;
            display: none;
            /* Hide by default in favor of skeleton */
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Filter Functionality -->
    <script>
        // Show loading animation when page loads
        window.addEventListener('load', function () {
            const loadingOverlay = document.querySelector('.loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Show loading animation initially
            const loadingOverlay = document.querySelector('.loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.classList.remove('hidden');
            }

            // Initialize Flatpickr for date inputs
            flatpickr(".flatpickr", {
                dateFormat: "Y-m-d",
                allowInput: true
            });

            // Initialize Select2 for dropdown inputs
            $('.select2').select2({
                width: '100%',
                dropdownParent: $('#filter-popups')
            });

            // Filter Pills
            const filterPills = document.querySelectorAll('.filter-pill');
            const filterPopups = document.getElementById('filter-popups');
            const allPopups = document.querySelectorAll('.filter-popup');

            // Handle filter pill clicks
            filterPills.forEach(pill => {
                pill.addEventListener('click', function () {
                    const filterType = this.getAttribute('data-filter');
                    const popup = document.getElementById(`${filterType}-popup`);

                    // Hide all popups first
                    allPopups.forEach(p => p.classList.add('hidden'));

                    // Show the selected popup
                    if (popup) {
                        popup.classList.remove('hidden');
                    }
                });
            });

            // Close popups when clicking outside
            document.addEventListener('click', function (event) {
                if (!event.target.closest('.filter-pill') && !event.target.closest('.filter-popup')) {
                    allPopups.forEach(popup => popup.classList.add('hidden'));
                }
            });

            // Apply filter buttons
            const applyButtons = document.querySelectorAll('.filter-apply-btn');
            applyButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const filterType = this.getAttribute('data-filter');
                    applyFilter(filterType);
                });
            });

            // Clear filter buttons
            const clearButtons = document.querySelectorAll('.filter-clear-btn');
            clearButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const filterType = this.getAttribute('data-filter');
                    clearFilter(filterType);
                });
            });

            // Apply filter function
            function applyFilter(filterType) {
                // Get filter value
                let filterValue;

                if (filterType === 'date') {
                    const startDate = document.getElementById('date-start-input').value;
                    const endDate = document.getElementById('date-end-input').value;
                    filterValue = `${startDate}|${endDate}`;
                } else {
                    filterValue = document.getElementById(`${filterType}-input`).value;
                }

                // Add to active filters
                if (filterValue && filterValue !== '|') {
                    addActiveFilter(filterType, filterValue);
                }

                // Hide popup
                document.getElementById(`${filterType}-popup`).classList.add('hidden');

                // Refresh page with filter
                applyFiltersToURL();
            }

            // Clear filter function
            function clearFilter(filterType) {
                // Clear input value
                if (filterType === 'date') {
                    document.getElementById('date-start-input').value = '';
                    document.getElementById('date-end-input').value = '';
                } else {
                    const input = document.getElementById(`${filterType}-input`);
                    input.value = '';
                    if (input.classList.contains('select2')) {
                        $(input).val('').trigger('change');
                    }
                }

                // Remove from active filters
                removeActiveFilter(filterType);

                // Hide popup
                document.getElementById(`${filterType}-popup`).classList.add('hidden');

                // Refresh page without filter
                applyFiltersToURL();
            }

            // Add active filter
            function addActiveFilter(filterType, filterValue) {
                // Remove existing filter of same type
                removeActiveFilter(filterType);

                // Create filter badge
                const activeFilters = document.getElementById('active-filters');
                const filterBadge = document.createElement('div');
                filterBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800';
                filterBadge.setAttribute('data-filter-type', filterType);

                // Format display value based on filter type
                let displayValue = filterValue;
                if (filterType === 'date' && filterValue.includes('|')) {
                    const [start, end] = filterValue.split('|');
                    displayValue = `${start} to ${end}`;
                } else if (filterType === 'status' || filterType === 'payment_status') {
                    const select = document.getElementById(`${filterType}-input`);
                    const option = select.options[select.selectedIndex];
                    displayValue = option.textContent;
                } else if (filterType === 'customer') {
                    const select = document.getElementById(`${filterType}-input`);
                    const option = select.options[select.selectedIndex];
                    displayValue = option.textContent;
                }

                // Set filter badge content
                filterBadge.innerHTML = `
                    <span class="mr-1">${getFilterLabel(filterType)}:</span>
                    <span>${displayValue}</span>
                    <button type="button" class="ml-1 inline-flex items-center p-0.5 rounded-full text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none" data-filter-type="${filterType}">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                `;

                // Add to active filters
                activeFilters.appendChild(filterBadge);

                // Add click event to remove button
                const removeButton = filterBadge.querySelector('button');
                removeButton.addEventListener('click', function () {
                    const filterType = this.getAttribute('data-filter-type');
                    clearFilter(filterType);
                });
            }

            // Remove active filter
            function removeActiveFilter(filterType) {
                const activeFilters = document.getElementById('active-filters');
                const existingFilter = activeFilters.querySelector(`[data-filter-type="${filterType}"]`);
                if (existingFilter) {
                    activeFilters.removeChild(existingFilter);
                }
            }

            // Get filter label
            function getFilterLabel(filterType) {
                const labels = {
                    'order_id': 'Order ID',
                    'date': 'Date',
                    'status': 'Status',
                    'customer': 'Customer',
                    'phone': 'Phone',
                    'pickup_location': 'Pickup',
                    'delivery_location': 'Delivery',
                    'payment_status': 'Payment'
                };
                return labels[filterType] || filterType;
            }

            // Apply filters to URL
            function applyFiltersToURL() {
                // Show loading animation
                const loadingOverlay = document.querySelector('.loading-overlay');
                if (loadingOverlay) {
                    loadingOverlay.classList.remove('hidden');
                }

                const activeFilters = document.getElementById('active-filters');
                const filterBadges = activeFilters.querySelectorAll('[data-filter-type]');

                const params = new URLSearchParams(window.location.search);

                // Clear existing filter params
                ['order_id', 'date_start', 'date_end', 'status', 'customer', 'phone', 'pickup_location', 'delivery_location', 'payment_status'].forEach(param => {
                    params.delete(param);
                });

                // Add active filters to params
                filterBadges.forEach(badge => {
                    const filterType = badge.getAttribute('data-filter-type');
                    let filterValue;

                    if (filterType === 'date') {
                        const [start, end] = document.getElementById(`date-start-input`).value + '|' + document.getElementById(`date-end-input`).value;
                        params.set('date_start', start);
                        params.set('date_end', end);
                    } else {
                        filterValue = document.getElementById(`${filterType}-input`).value;
                        params.set(filterType, filterValue);
                    }
                });

                // Add a small delay to show the loading animation
                setTimeout(() => {
                    // Redirect with filters
                    window.location.href = `${window.location.pathname}?${params.toString()}`;
                }, 300);
            }

            // Initialize active filters from URL params
            function initializeFiltersFromURL() {
                const params = new URLSearchParams(window.location.search);

                // Check for order_id filter
                if (params.has('order_id')) {
                    document.getElementById('order_id-input').value = params.get('order_id');
                    addActiveFilter('order_id', params.get('order_id'));
                }

                // Check for date filter
                if (params.has('date_start') || params.has('date_end')) {
                    const startDate = params.get('date_start') || '';
                    const endDate = params.get('date_end') || '';
                    document.getElementById('date-start-input').value = startDate;
                    document.getElementById('date-end-input').value = endDate;
                    addActiveFilter('date', `${startDate}|${endDate}`);
                }

                // Check for status filter
                if (params.has('status')) {
                    document.getElementById('status-input').value = params.get('status');
                    addActiveFilter('status', params.get('status'));
                }

                // Check for customer filter
                if (params.has('customer')) {
                    const customerId = params.get('customer');
                    const customerSelect = document.getElementById('customer-input');
                    customerSelect.value = customerId;
                    $(customerSelect).trigger('change');
                    addActiveFilter('customer', customerId);
                }

                // Check for payment_status filter
                if (params.has('payment_status')) {
                    document.getElementById('payment_status-input').value = params.get('payment_status');
                    addActiveFilter('payment_status', params.get('payment_status'));
                }
            }

            // Initialize filters from URL
            initializeFiltersFromURL();
        });
    </script>
</x-master-layout>