<x-master-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Orders</h4>
                    </div>
                    <div class="card-body">
                        <!-- Tailwind Filter Section -->
                        <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                            <div
                                class="flex items-center justify-between bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <div class="flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-700">Filter Orders</h3>
                                    <span id="filter-count"
                                        class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-blue-500 rounded-full">0</span>
                                </div>
                                <button id="toggle-filter" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 transform transition-transform duration-200" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div id="filter-content" class="px-4 py-4">
                                <form id="filter-form" class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <!-- Order Status Filter -->
                                        <div class="space-y-2">
                                            <label for="status" class="block text-sm font-medium text-gray-700">Order
                                                Status</label>
                                            <div class="relative">
                                                <select id="status" name="status"
                                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                                    <option value="">All Order Statuses</option>
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
                                                <div
                                                    class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                                    <svg class="h-4 w-4 text-gray-400"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Date Range Filter -->
                                        <div class="space-y-2">
                                            <label for="date_range" class="block text-sm font-medium text-gray-700">Date
                                                Range</label>
                                            <div class="relative">
                                                <select id="date_range" name="date_range"
                                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                                    <option value="">All Time</option>
                                                    <option value="today">Today</option>
                                                    <option value="yesterday">Yesterday</option>
                                                    <option value="this_week">This Week</option>
                                                    <option value="last_week">Last Week</option>
                                                    <option value="this_month">This Month</option>
                                                    <option value="last_month">Last Month</option>
                                                </select>
                                                <div
                                                    class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                                    <svg class="h-4 w-4 text-gray-400"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer Search -->
                                        <div class="space-y-2">
                                            <label for="customer_search"
                                                class="block text-sm font-medium text-gray-700">Customer</label>
                                            <div class="relative">
                                                <input type="text" id="customer_search" name="customer_search"
                                                    placeholder="Search by customer name"
                                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                <div
                                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Order ID Search -->
                                        <div class="space-y-2">
                                            <label for="order_id" class="block text-sm font-medium text-gray-700">Order
                                                ID</label>
                                            <div class="relative">
                                                <input type="text" id="order_id" name="order_id"
                                                    placeholder="Enter order ID"
                                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                <div
                                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-3">
                                        <button type="button" id="reset-filter"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="mr-2 -ml-1 h-5 w-5 text-gray-400"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Reset
                                        </button>
                                        <button type="submit" id="apply-filter"
                                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Apply Filters
                                        </button>
                                    </div>
                                </form>
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
                                                <span class="badge bg-{{ $order->status_color }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td data-column="payment">
                                                <span class="badge bg-{{ $order->payment_status_color }}">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            </td>
                                            <td data-column="delivery">
                                                <span class="badge bg-{{ $order->delivery_status_color }}">
                                                    {{ ucfirst($order->delivery_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('order-view.show', $order->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('order.edit', $order->id) }}"
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
                // Initialize filter toggle
                const toggleFilterBtn = document.getElementById('toggle-filter');
                const filterContent = document.getElementById('filter-content');
                const filterForm = document.getElementById('filter-form');
                const filterCount = document.getElementById('filter-count');
                const resetFilterBtn = document.getElementById('reset-filter');

                // Toggle filter content visibility
                toggleFilterBtn.addEventListener('click', function () {
                    const isExpanded = toggleFilterBtn.getAttribute('aria-expanded') === 'true';
                    toggleFilterBtn.setAttribute('aria-expanded', !isExpanded);
                    toggleFilterBtn.querySelector('svg').classList.toggle('rotate-180');

                    if (isExpanded) {
                        filterContent.style.maxHeight = '0';
                        filterContent.style.overflow = 'hidden';
                        setTimeout(() => {
                            filterContent.style.display = 'none';
                        }, 300);
                    } else {
                        filterContent.style.display = 'block';
                        setTimeout(() => {
                            filterContent.style.maxHeight = filterContent.scrollHeight + 'px';
                            filterContent.style.overflow = 'visible';
                        }, 10);
                    }
                });

                // Initialize filter count
                function updateFilterCount() {
                    const activeFilters = Array.from(filterForm.elements)
                        .filter(el => el.tagName === 'SELECT' || el.tagName === 'INPUT')
                        .filter(el => el.value && el.value !== '').length;

                    filterCount.textContent = activeFilters;
                    filterCount.style.display = activeFilters > 0 ? 'inline-flex' : 'none';
                }

                // Add event listeners to form elements
                Array.from(filterForm.elements).forEach(el => {
                    if (el.tagName === 'SELECT' || el.tagName === 'INPUT') {
                        el.addEventListener('change', updateFilterCount);
                    }
                });

                // Reset filter
                resetFilterBtn.addEventListener('click', function () {
                    filterForm.reset();
                    updateFilterCount();
                });

                // Apply filter
                filterForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Build query string
                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams();

                    for (const [key, value] of formData.entries()) {
                        if (value) {
                            params.append(key, value);
                        }
                    }

                    // Redirect with query parameters
                    window.location.href = `{{ route('shadcn.orders') }}?${params.toString()}`;
                });

                // Initialize with URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                for (const [key, value] of urlParams.entries()) {
                    const el = filterForm.elements[key];
                    if (el) {
                        el.value = value;
                    }
                }

                // Update filter count on load
                updateFilterCount();

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
                                form.action = `{{ route('order.index') }}/${orderId}`;
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

                // Add Tailwind styles to table
                const table = document.querySelector('.shadcn-table');
                if (table) {
                    table.classList.add('min-w-full', 'divide-y', 'divide-gray-200');

                    const thead = table.querySelector('thead');
                    if (thead) {
                        thead.classList.add('bg-gray-50');

                        const headerCells = thead.querySelectorAll('th');
                        headerCells.forEach(cell => {
                            cell.classList.add('px-6', 'py-3', 'text-left', 'text-xs', 'font-medium', 'text-gray-500', 'uppercase', 'tracking-wider');
                        });
                    }

                    const tbody = table.querySelector('tbody');
                    if (tbody) {
                        tbody.classList.add('bg-white', 'divide-y', 'divide-gray-200');

                        const rows = tbody.querySelectorAll('tr');
                        rows.forEach(row => {
                            row.classList.add('hover:bg-gray-50');

                            const cells = row.querySelectorAll('td');
                            cells.forEach(cell => {
                                cell.classList.add('px-6', 'py-4', 'whitespace-nowrap', 'text-sm', 'text-gray-500');
                            });
                        });
                    }
                }
            });
        </script>
    @endpush
</x-master-layout>