<x-master-layout>
    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.order_list') }}</h4>
            <h6>{{ __('message.manage_your_orders') }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('order.create') }}" class="btn btn-added">
                <img src="{{ asset('assets/img/icons/plus.svg') }}" alt="img" class="me-1">
                {{ __('message.add_new_order') }}
            </a>
        </div>

        <!-- Active Filters Display -->
        @if(count(array_filter(request()->only(['order_id', 'date_from', 'date_to', 'status', 'customer_id', 'location', 'payment_status', 'search']))) > 0)
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm font-medium text-gray-700">Active filters:</span>

                    @if(request('order_id'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Order ID: {{ request('order_id') }}
                            <a href="{{ route('order.index', array_merge(request()->except('order_id'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('date_from') || request('date_to'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Date:
                            @if(request('date_from') && request('date_to'))
                                {{ request('date_from') }} to {{ request('date_to') }}
                            @elseif(request('date_from'))
                                From {{ request('date_from') }}
                            @elseif(request('date_to'))
                                Until {{ request('date_to') }}
                            @endif
                            <a href="{{ route('order.index', array_merge(request()->except(['date_from', 'date_to']), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('status'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Status: {{ ucfirst(request('status')) }}
                            <a href="{{ route('order.index', array_merge(request()->except('status'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('customer_id'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Customer: {{ \App\Models\User::find(request('customer_id'))->name ?? request('customer_id') }}
                            <a href="{{ route('order.index', array_merge(request()->except('customer_id'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('location'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Location: {{ request('location') }}
                            <a href="{{ route('order.index', array_merge(request()->except('location'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('payment_status'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Payment: {{ ucfirst(request('payment_status')) }}
                            <a href="{{ route('order.index', array_merge(request()->except('payment_status'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </span>
                    @endif

                    @if(request('search'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Search: {{ request('search') }}
                            <a href="{{ route('order.index', array_merge(request()->except('search'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Order ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pickup Location
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Delivery Location
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Payment Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                <a href="{{ route('order.show', $order->id) }}">{{ $order->id }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'shipped' => 'bg-purple-100 text-purple-800',
                                        'delivered' => 'bg-green-100 text-green-800',
                                    ][$order->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->client->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->pickup_point ? json_decode($order->pickup_point)->address : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->delivery_point ? json_decode($order->delivery_point)->address : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @php
                                    $paymentClass = [
                                        'paid' => 'bg-green-100 text-green-800',
                                        'unpaid' => 'bg-red-100 text-red-800',
                                        'partial' => 'bg-yellow-100 text-yellow-800',
                                    ][$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paymentClass }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->total_amount ? getPriceFormat($order->total_amount) : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex space-x-2">
                                    <a href="{{ route('order.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('order.edit', $order->id) }}" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('order.destroy', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this order?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No orders found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if ($orders->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-50 cursor-not-allowed">
                        Previous
                    </span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </a>
                @endif

                @if ($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-50 cursor-not-allowed">
                        Next
                    </span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $orders->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $orders->lastItem() ?? 0 }}</span> of
                        <span class="font-medium">{{ $orders->total() }}</span> results
                    </p>
                </div>
                <div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">Rows per page:</span>
                        <form id="per-page-form" action="{{ route('order.index') }}" method="GET" class="inline">
                            @foreach(request()->except(['per_page', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <select name="per_page" onchange="document.getElementById('per-page-form').submit()"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>

                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                            aria-label="Pagination">
                            @if ($orders->onFirstPage())
                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-50 text-sm font-medium text-gray-400 cursor-not-allowed">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left h-5 w-5"></i>
                                </span>
                            @else
                                <a href="{{ $orders->previousPageUrl() }}"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left h-5 w-5"></i>
                                </a>
                            @endif

                            @if ($orders->hasMorePages())
                                <a href="{{ $orders->nextPageUrl() }}"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right h-5 w-5"></i>
                                </a>
                            @else
                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-50 text-sm font-medium text-gray-400 cursor-not-allowed">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right h-5 w-5"></i>
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Filter Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter Pills
            const filterPills = document.querySelectorAll('.filter-pill');
            const filterPopups = document.getElementById('filter-popups');
            const allPopups = document.querySelectorAll('.filter-popup');

            // Handle filter pill clicks
            filterPills.forEach(pill => {
                pill.addEventListener('click', function() {
                    const filterType = this.getAttribute('data-filter');
                    const popup = document.getElementById(`${filterType}-popup`);

                    // Hide all popups first
                    allPopups.forEach(p => p.classList.add('hidden'));

                    // Toggle the filter popups container
                    if (popup && filterPopups.classList.contains('hidden')) {
                        filterPopups.classList.remove('hidden');
                        popup.classList.remove('hidden');

                        // Highlight the active pill
                        filterPills.forEach(p => {
                            p.classList.remove('bg-blue-100', 'text-blue-800');
                            p.classList.add('bg-gray-100', 'text-gray-800');
                        });

                        this.classList.remove('bg-gray-100', 'text-gray-800');
                        this.classList.add('bg-blue-100', 'text-blue-800');
                    } else {
                        filterPopups.classList.add('hidden');
                        this.classList.remove('bg-blue-100', 'text-blue-800');
                        this.classList.add('bg-gray-100', 'text-gray-800');
                    }
                });
            });

            // Handle cancel buttons
            const cancelButtons = document.querySelectorAll('.cancel-filter');
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    filterPopups.classList.add('hidden');
                    allPopups.forEach(popup => popup.classList.add('hidden'));

                    // Reset all pills to default state
                    filterPills.forEach(pill => {
                        pill.classList.remove('bg-blue-100', 'text-blue-800');
                        pill.classList.add('bg-gray-100', 'text-gray-800');
                    });
                });
            });

            // Order ID Search Functionality
            const orderIdSearch = document.getElementById('order-id-search');
            const orderIdResults = document.getElementById('order-id-results');

            if (orderIdSearch && orderIdResults) {
                const orderIdResultItems = document.querySelectorAll('.order-id-result');

                orderIdSearch.addEventListener('focus', function() {
                    orderIdResults.classList.remove('hidden');
                });

                orderIdSearch.addEventListener('input', function() {
                    const searchValue = this.value.toLowerCase();

                    orderIdResultItems.forEach(item => {
                        if (item.textContent.toLowerCase().includes(searchValue)) {
                            item.classList.remove('hidden');
                        } else {
                            item.classList.add('hidden');
                        }
                    });
                });

                // Handle clicking on a result
                orderIdResultItems.forEach(item => {
                    item.addEventListener('click', function() {
                        orderIdSearch.value = this.textContent;
                        orderIdResults.classList.add('hidden');
                    });
                });

                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!orderIdSearch.contains(e.target) && !orderIdResults.contains(e.target)) {
                        orderIdResults.classList.add('hidden');
                    }
                });
            }

            // Date Range Presets
            const datePresets = document.querySelectorAll('.date-preset');
            const dateFrom = document.getElementById('date-from');
            const dateTo = document.getElementById('date-to');

            if (datePresets.length > 0 && dateFrom && dateTo) {
                datePresets.forEach(preset => {
                    preset.addEventListener('click', function() {
                        const days = parseInt(this.getAttribute('data-days'));
                        const today = new Date();
                        const toDate = today.toISOString().split('T')[0]; // YYYY-MM-DD

                        dateTo.value = toDate;

                        if (days === 0) {
                            // Today
                            dateFrom.value = toDate;
                        } else {
                            // Past days
                            const fromDate = new Date();
                            fromDate.setDate(today.getDate() - days);
                            dateFrom.value = fromDate.toISOString().split('T')[0];
                        }

                        // Highlight the selected preset
                        datePresets.forEach(p => p.classList.remove('bg-blue-100', 'text-blue-800'));
                        this.classList.add('bg-blue-100', 'text-blue-800');
                        this.classList.remove('bg-gray-100', 'text-gray-800');
                    });
                });
            }

            // Customer Search Functionality
            const customerSearch = document.getElementById('customer-search');
            const customerResults = document.getElementById('customer-results');

            if (customerSearch && customerResults) {
                const customerResultItems = document.querySelectorAll('.customer-result');

                customerSearch.addEventListener('focus', function() {
                    customerResults.classList.remove('hidden');
                });

                customerSearch.addEventListener('input', function() {
                    const searchValue = this.value.toLowerCase();

                    customerResultItems.forEach(item => {
                        if (item.textContent.toLowerCase().includes(searchValue)) {
                            item.classList.remove('hidden');
                        } else {
                            item.classList.add('hidden');
                        }
                    });
                });

                // Handle clicking on a result
                customerResultItems.forEach(item => {
                    item.addEventListener('click', function() {
                        customerSearch.value = this.textContent;
                        document.querySelector('input[name="customer_id"]').value = this.getAttribute('data-id');
                        customerResults.classList.add('hidden');
                    });
                });

                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!customerSearch.contains(e.target) && !customerResults.contains(e.target)) {
                        customerResults.classList.add('hidden');
                    }
                });
            }

            // Highlight active filter pills
            @if(request('order_id'))
                document.getElementById('order-id-filter').classList.remove('bg-gray-100', 'text-gray-800');
                document.getElementById('order-id-filter').classList.add('bg-blue-100', 'text-blue-800');
            @endif

            @if(request('date_from') || request('date_to'))
                document.getElementById('date-filter').classList.remove('bg-gray-100', 'text-gray-800');
                document.getElementById('date-filter').classList.add('bg-blue-100', 'text-blue-800');
            @endif

            @if(request('status'))
                document.getElementById('status-filter').classList.remove('bg-gray-100', 'text-gray-800');
                document.getElementById('status-filter').classList.add('bg-blue-100', 'text-blue-800');
            @endif

            @if(request('customer_id'))
                document.getElementById('customer-filter').classList.remove('bg-gray-100', 'text-gray-800');
                document.getElementById('customer-filter').classList.add('bg-blue-100', 'text-blue-800');
            @endif

            @if(request('location'))
                document.getElementById('location-filter').classList.remove('bg-gray-100', 'text-gray-800');
                document.getElementById('location-filter').classList.add('bg-blue-100', 'text-blue-800');
            @endif

            @if(request('payment_status'))
                document.getElementById('payment-filter').classList.remove('bg-gray-100', 'text-gray-800');
                document.getElementById('payment-filter').classList.add('bg-blue-100', 'text-blue-800');
            @endif

            @if(request('search'))
                document.getElementById('more-filter').classList.remove('bg-gray-100', 'text-gray-800');
                document.getElementById('more-filter').classList.add('bg-blue-100', 'text-blue-800');
            @endif
        });
    </script>
</x-master-layout>


