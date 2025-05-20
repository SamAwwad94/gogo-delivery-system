<x-master-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Add Tailwind CSS -->
                <style>
                    @import url('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
                </style>
                <!-- Header with Table/Map Toggle -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ __('message.list_form_title', ['form' => __('message.order')]) }}
                        </h1>
                    </div>

                    <div class="flex items-center space-x-2">
                        @if(isset($button) && $button)
                            {!! $button !!}
                        @endif
                        <a href="{{ route('order.create') }}"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-2"></i> {{ __('message.add_form_title', ['form' => __('message.order')]) }}
                        </a>
                        <a href="{{ route('order.index', ['classic' => 1]) }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-table mr-2"></i> {{ __('message.classic_view') }}
                        </a>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white shadow rounded-md mb-6 overflow-hidden">
                    <div class="p-4">
                        <form id="filter-form" action="{{ route('order.index') }}" method="GET">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Order ID Filter -->
                                <div>
                                    <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-hashtag mr-1 text-gray-500"></i> {{ __('message.order_id') }}
                                    </label>
                                    <input type="text" name="order_id" id="order_id" value="{{ request('order_id') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        placeholder="{{ __('message.enter_order_id') }}">
                                </div>

                                <!-- Customer Filter -->
                                <div>
                                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-user mr-1 text-gray-500"></i> {{ __('message.customer') }}
                                    </label>
                                    <select name="customer_id" id="customer_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">{{ __('message.all_customers') }}</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Date Range Filter -->
                                <div>
                                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-calendar-alt mr-1 text-gray-500"></i> {{ __('message.date_range') }}
                                    </label>
                                    <select name="date_range" id="date_range"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">{{ __('message.all_dates') }}</option>
                                        @foreach($dateRanges as $key => $value)
                                            <option value="{{ $key }}" {{ request('date_range') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Custom Date Range -->
                                <div id="custom-date-range" class="{{ request('date_range') == 'custom' ? '' : 'hidden' }} grid grid-cols-2 gap-2">
                                    <div>
                                        <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ __('message.from_date') }}
                                        </label>
                                        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ __('message.to_date') }}
                                        </label>
                                        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                </div>

                                <!-- Order Status Filter -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-tag mr-1 text-gray-500"></i> {{ __('message.status') }}
                                    </label>
                                    <select name="status" id="status"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">{{ __('message.all_statuses') }}</option>
                                        @foreach($orderStatuses as $key => $value)
                                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Payment Status Filter -->
                                <div>
                                    <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-credit-card mr-1 text-gray-500"></i> {{ __('message.payment_status') }}
                                    </label>
                                    <select name="payment_status" id="payment_status"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">{{ __('message.all_payment_statuses') }}</option>
                                        @foreach($paymentStatuses as $key => $value)
                                            <option value="{{ $key }}" {{ request('payment_status') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Delivery Status Filter -->
                                <div>
                                    <label for="delivery_status" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-truck mr-1 text-gray-500"></i> {{ __('message.delivery_status') }}
                                    </label>
                                    <select name="delivery_status" id="delivery_status"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">{{ __('message.all_delivery_statuses') }}</option>
                                        @foreach($deliveryStatuses as $key => $value)
                                            <option value="{{ $key }}" {{ request('delivery_status') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- City Filter -->
                                <div>
                                    <label for="city_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-map-marker-alt mr-1 text-gray-500"></i> {{ __('message.city') }}
                                    </label>
                                    <select name="city_id" id="city_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">{{ __('message.all_cities') }}</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Country Filter -->
                                <div>
                                    <label for="country_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-globe mr-1 text-gray-500"></i> {{ __('message.country') }}
                                    </label>
                                    <select name="country_id" id="country_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">{{ __('message.all_countries') }}</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Per Page Filter -->
                                <div>
                                    <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-list-ol mr-1 text-gray-500"></i> {{ __('message.per_page') }}
                                    </label>
                                    <select name="per_page" id="per_page"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Filter Actions -->
                            <div class="mt-4 flex justify-end space-x-3">
                                <a href="{{ route('order.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-redo mr-2"></i> {{ __('message.reset') }}
                                </a>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-filter mr-2"></i> {{ __('message.apply_filters') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Active Filters -->
                    @if(count(array_filter(request()->only(['order_id', 'customer_id', 'date_range', 'from_date', 'to_date', 'status', 'payment_status', 'delivery_status', 'city_id', 'country_id']))) > 0)
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                            <div class="flex flex-wrap gap-2">
                                <span class="text-sm font-medium text-gray-700">{{ __('message.active_filters') }}:</span>

                                @if(request('order_id'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('message.order_id') }}: {{ request('order_id') }}
                                        <a href="{{ route('order.index', array_merge(request()->except('order_id'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('customer_id'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('message.customer') }}: {{ $customers->where('id', request('customer_id'))->first()->display_name ?? request('customer_id') }}
                                        <a href="{{ route('order.index', array_merge(request()->except('customer_id'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('date_range'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('message.date_range') }}: {{ $dateRanges[request('date_range')] ?? request('date_range') }}
                                        <a href="{{ route('order.index', array_merge(request()->except(['date_range', 'from_date', 'to_date']), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('status'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('message.status') }}: {{ $orderStatuses[request('status')] ?? request('status') }}
                                        <a href="{{ route('order.index', array_merge(request()->except('status'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('payment_status'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('message.payment_status') }}: {{ $paymentStatuses[request('payment_status')] ?? request('payment_status') }}
                                        <a href="{{ route('order.index', array_merge(request()->except('payment_status'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('delivery_status'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('message.delivery_status') }}: {{ $deliveryStatuses[request('delivery_status')] ?? request('delivery_status') }}
                                        <a href="{{ route('order.index', array_merge(request()->except('delivery_status'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('city_id'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('message.city') }}: {{ $cities->where('id', request('city_id'))->first()->name ?? request('city_id') }}
                                        <a href="{{ route('order.index', array_merge(request()->except('city_id'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('country_id'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('message.country') }}: {{ $countries->where('id', request('country_id'))->first()->name ?? request('country_id') }}
                                        <a href="{{ route('order.index', array_merge(request()->except('country_id'), ['page' => 1])) }}" class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div id="table-view" class="bg-white shadow rounded-md overflow-hidden">
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('message.order_id') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('message.customer') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('message.date') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('message.total') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('message.status') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('message.payment') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('message.delivery') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('message.actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                            <a href="{{ route('order-view.show', $order->id) }}">#{{ $order->id }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->customer_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($order->currency == 'USD')
                                                ${{ number_format($order->total_amount, 2) }}
                                            @else
                                                {{ number_format($order->total_amount) }} {{ $order->currency }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClass = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'processing' => 'bg-blue-100 text-blue-800',
                                                    'shipped' => 'bg-indigo-100 text-indigo-800',
                                                    'delivered' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                    'returned' => 'bg-purple-100 text-purple-800',
                                                    'refunded' => 'bg-gray-100 text-gray-800',
                                                    'create' => 'bg-blue-100 text-blue-800',
                                                    'draft' => 'bg-gray-100 text-gray-800',
                                                ][$order->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $paymentStatusClass = [
                                                    'paid' => 'bg-green-100 text-green-800',
                                                    'unpaid' => 'bg-red-100 text-red-800',
                                                    'partial' => 'bg-yellow-100 text-yellow-800',
                                                    'refunded' => 'bg-purple-100 text-purple-800',
                                                    'failed' => 'bg-gray-100 text-gray-800',
                                                ][$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paymentStatusClass }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $deliveryStatusClass = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'assigned' => 'bg-blue-100 text-blue-800',
                                                    'in_transit' => 'bg-indigo-100 text-indigo-800',
                                                    'delivered' => 'bg-green-100 text-green-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                    'returned' => 'bg-purple-100 text-purple-800',
                                                ][$order->delivery_status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $deliveryStatusClass }}">
                                                {{ ucfirst($order->delivery_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('order-view.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($order->status == 'draft' && $auth_user->can('order-edit'))
                                                    <a href="{{ route('order.edit', $order->id) }}" class="text-green-600 hover:text-green-900">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if($auth_user->can('order-delete'))
                                                    <button type="button" class="text-red-600 hover:text-red-900 delete-order" data-id="{{ $order->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($orders->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-50 cursor-not-allowed">
                                    {{ __('message.previous') }}
                                </span>
                            @else
                                <a href="{{ $orders->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    {{ __('message.previous') }}
                                </a>
                            @endif

                            @if($orders->hasMorePages())
                                <a href="{{ $orders->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    {{ __('message.next') }}
                                </a>
                            @else
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-50 cursor-not-allowed">
                                    {{ __('message.next') }}
                                </span>
                            @endif
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    {{ __('message.showing') }} <span class="font-medium">{{ $orders->firstItem() }}</span> {{ __('message.to') }} <span class="font-medium">{{ $orders->lastItem() }}</span> {{ __('message.of') }} <span class="font-medium">{{ $orders->total() }}</span> {{ __('message.results') }}
                                </p>
                            </div>
                            <div>
                                {{ $orders->links('pagination::tailwind') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Date Range Filter
            const dateRangeSelect = document.getElementById('date_range');
            const customDateRange = document.getElementById('custom-date-range');

            // Show/hide custom date range fields based on selection
            dateRangeSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customDateRange.classList.remove('hidden');
                } else {
                    customDateRange.classList.add('hidden');
                }
            });

            // Delete Order Confirmation
            const deleteButtons = document.querySelectorAll('.delete-order');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-id');

                    if (confirm("{{ __('message.delete_confirm') }}")) {
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

            // Auto-submit on per_page change
            const perPageSelect = document.getElementById('per_page');
            perPageSelect.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        });
    </script>
    @endpush
</x-master-layout>
