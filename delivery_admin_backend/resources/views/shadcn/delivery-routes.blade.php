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
                        <!-- Table/Map Toggle -->
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <button type="button" id="table-view-btn"
                                class="px-4 py-2 text-sm font-medium text-blue-700 bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 active">
                                Table
                            </button>
                            <button type="button" id="map-view-btn"
                                class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-r-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700">
                                Map
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-file-import mr-2"></i> Import
                        </button>
                        <button type="button"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-2"></i> New Route
                        </button>
                        <button type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-question-circle mr-2"></i> Help
                        </button>
                        <a href="{{ route('order.index') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-arrow-left mr-2"></i> Orders
                        </a>
                    </div>
                </div>

                <!-- Demo Mode Banner -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Welcome to Delivery Routes! Manage your delivery routes and track drivers efficiently.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Filter Pills -->
                <div class="bg-white shadow rounded-md mb-6 overflow-x-auto">
                    <div class="p-4 flex space-x-2 flex-nowrap">
                        <button id="route-id-filter" type="button"
                            class="filter-pill inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200"
                            data-filter="route_id">
                            <i class="fas fa-hashtag mr-1.5 text-gray-500"></i> Route ID
                        </button>
                        <button id="date-filter" type="button"
                            class="filter-pill inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200"
                            data-filter="date">
                            <i class="fas fa-calendar-alt mr-1.5 text-gray-500"></i> Date
                        </button>
                        <button id="status-filter" type="button"
                            class="filter-pill inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200"
                            data-filter="status">
                            <i class="fas fa-tag mr-1.5 text-gray-500"></i> Status
                        </button>
                        <button id="driver-filter" type="button"
                            class="filter-pill inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200"
                            data-filter="driver_id">
                            <i class="fas fa-user mr-1.5 text-gray-500"></i> Driver
                        </button>
                        <button id="zone-filter" type="button"
                            class="filter-pill inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200"
                            data-filter="zone">
                            <i class="fas fa-map-marker-alt mr-1.5 text-gray-500"></i> Zone
                        </button>
                        <button id="vehicle-filter" type="button"
                            class="filter-pill inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200"
                            data-filter="vehicle">
                            <i class="fas fa-truck mr-1.5 text-gray-500"></i> Vehicle
                        </button>
                        <button id="orders-count-filter" type="button"
                            class="filter-pill inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200"
                            data-filter="orders_count">
                            <i class="fas fa-box mr-1.5 text-gray-500"></i> Orders Count
                        </button>
                        <button id="more-filter" type="button"
                            class="filter-pill inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200"
                            data-filter="more">
                            <i class="fas fa-ellipsis-h mr-1.5 text-gray-500"></i> More
                        </button>
                    </div>

                    <!-- Hidden Filter Popups -->
                    <div id="filter-popups" class="hidden">
                        <!-- Route ID Filter Popup -->
                        <div id="route_id-popup" class="filter-popup hidden p-4 border-t border-gray-200">
                            <form id="route_id-form" action="{{ route('delivery-routes.index') }}" method="GET">
                                <div class="space-y-4">
                                    <div class="relative">
                                        <input type="text" id="route-id-search" name="route_id"
                                            value="{{ request('route_id') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm pl-10"
                                            placeholder="Search by Route ID">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                    </div>

                                    <div id="route-id-results"
                                        class="max-h-40 overflow-y-auto bg-white rounded-md border border-gray-200 hidden">
                                        <!-- Results populated from database -->
                                        @foreach($routeIds as $id)
                                            <div class="p-2 hover:bg-gray-100 cursor-pointer route-id-result">{{ $id }}</div>
                                        @endforeach
                                    </div>

                                    <div class="flex justify-end space-x-2">
                                        <button type="button"
                                            class="cancel-filter px-3 py-2 bg-gray-200 text-gray-700 text-xs rounded-md hover:bg-gray-300">Cancel</button>
                                        <button type="submit"
                                            class="px-3 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">Apply</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Date Filter Popup -->
                        <div id="date-popup" class="filter-popup hidden p-4 border-t border-gray-200">
                            <form id="date-form" action="{{ route('delivery-routes.index') }}" method="GET">
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="date-from"
                                                class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                            <div class="relative">
                                                <input type="date" id="date-from" name="date_from"
                                                    value="{{ request('date_from') }}"
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm pl-10">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="date-to" class="block text-sm font-medium text-gray-700 mb-1">To
                                                Date</label>
                                            <div class="relative">
                                                <input type="date" id="date-to" name="date_to"
                                                    value="{{ request('date_to') }}"
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm pl-10">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <button type="button"
                                            class="date-preset px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded hover:bg-gray-200"
                                            data-days="0">Today</button>
                                        <button type="button"
                                            class="date-preset px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded hover:bg-gray-200"
                                            data-days="1">Yesterday</button>
                                        <button type="button"
                                            class="date-preset px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded hover:bg-gray-200"
                                            data-days="7">Last 7 days</button>
                                        <button type="button"
                                            class="date-preset px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded hover:bg-gray-200"
                                            data-days="30">Last 30 days</button>
                                        <button type="button"
                                            class="date-preset px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded hover:bg-gray-200"
                                            data-days="90">Last 90 days</button>
                                    </div>

                                    <div class="flex justify-end space-x-2">
                                        <button type="button"
                                            class="cancel-filter px-3 py-2 bg-gray-200 text-gray-700 text-xs rounded-md hover:bg-gray-300">Cancel</button>
                                        <button type="submit"
                                            class="px-3 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">Apply</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Status Filter Popup -->
                        <div id="status-popup" class="filter-popup hidden p-4 border-t border-gray-200">
                            <form id="status-form" action="{{ route('delivery-routes.index') }}" method="GET">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Status</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            @foreach($statuses as $status)
                                                <div class="relative flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input id="status-{{ $status }}" name="status" type="radio" value="{{ $status }}"
                                                            {{ request('status') == $status ? 'checked' : '' }}
                                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label for="status-{{ $status }}" class="font-medium text-gray-700">{{ ucfirst($status) }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="relative flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="status-all" name="status" type="radio" value=""
                                                        {{ request('status') == '' ? 'checked' : '' }}
                                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="status-all" class="font-medium text-gray-700">All Statuses</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-2">
                                        <button type="button" class="cancel-filter px-3 py-2 bg-gray-200 text-gray-700 text-xs rounded-md hover:bg-gray-300">Cancel</button>
                                        <button type="submit" class="px-3 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">Apply</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Driver Filter Popup -->
                        <div id="driver_id-popup" class="filter-popup hidden p-4 border-t border-gray-200">
                            <form id="driver_id-form" action="{{ route('delivery-routes.index') }}" method="GET">
                                <div class="flex items-center space-x-2">
                                    <select name="driver_id"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">All Drivers</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                        class="px-3 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">Apply</button>
                                    <button type="button"
                                        class="cancel-filter px-3 py-2 bg-gray-200 text-gray-700 text-xs rounded-md hover:bg-gray-300">Cancel</button>
                                </div>
                            </form>
                        </div>

                        <!-- Zone Filter Popup -->
                        <div id="zone-popup" class="filter-popup hidden p-4 border-t border-gray-200">
                            <form id="zone-form" action="{{ route('delivery-routes.index') }}" method="GET">
                                <div class="flex items-center space-x-2">
                                    <select name="zone"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">All Zones</option>
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>
                                                {{ $zone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                        class="px-3 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">Apply</button>
                                    <button type="button"
                                        class="cancel-filter px-3 py-2 bg-gray-200 text-gray-700 text-xs rounded-md hover:bg-gray-300">Cancel</button>
                                </div>
                            </form>
                        </div>

                        <!-- Vehicle Filter Popup -->
                        <div id="vehicle-popup" class="filter-popup hidden p-4 border-t border-gray-200">
                            <form id="vehicle-form" action="{{ route('delivery-routes.index') }}" method="GET">
                                <div class="flex items-center space-x-2">
                                    <select name="vehicle"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">All Vehicles</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ request('vehicle') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                        class="px-3 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">Apply</button>
                                    <button type="button"
                                        class="cancel-filter px-3 py-2 bg-gray-200 text-gray-700 text-xs rounded-md hover:bg-gray-300">Cancel</button>
                                </div>
                            </form>
                        </div>

                        <!-- Orders Count Filter Popup -->
                        <div id="orders_count-popup" class="filter-popup hidden p-4 border-t border-gray-200">
                            <form id="orders_count-form" action="{{ route('delivery-routes.index') }}" method="GET">
                                <div class="flex items-center space-x-2">
                                    <input type="number" name="orders_count" value="{{ request('orders_count') }}"
                                        min="0"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        placeholder="Enter number of orders">
                                    <button type="submit"
                                        class="px-3 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">Apply</button>
                                    <button type="button"
                                        class="cancel-filter px-3 py-2 bg-gray-200 text-gray-700 text-xs rounded-md hover:bg-gray-300">Cancel</button>
                                </div>
                            </form>
                        </div>

                        <!-- More Filter Popup -->
                        <div id="more-popup" class="filter-popup hidden p-4 border-t border-gray-200">
                            <form id="more-form" action="{{ route('delivery-routes.index') }}" method="GET">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            placeholder="Search routes...">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                                        <select name="per_page"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10
                                            </option>
                                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25
                                            </option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50
                                            </option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-end space-x-2">
                                    <button type="submit"
                                        class="px-3 py-2 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">Apply</button>
                                    <button type="button"
                                        class="cancel-filter px-3 py-2 bg-gray-200 text-gray-700 text-xs rounded-md hover:bg-gray-300">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Active Filters Display -->
                    @if(count(array_filter(request()->only(['route_id', 'date_from', 'date_to', 'status', 'driver_id', 'zone', 'vehicle', 'orders_count', 'search']))) > 0)
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                            <div class="flex flex-wrap gap-2">
                                <span class="text-sm font-medium text-gray-700">Active filters:</span>

                                @if(request('route_id'))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Route ID: {{ request('route_id') }}
                                        <a href="{{ route('delivery-routes.index', array_merge(request()->except('route_id'), ['page' => 1])) }}"
                                            class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('date_from') || request('date_to'))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Date:
                                        @if(request('date_from') && request('date_to'))
                                            {{ request('date_from') }} to {{ request('date_to') }}
                                        @elseif(request('date_from'))
                                            From {{ request('date_from') }}
                                        @elseif(request('date_to'))
                                            Until {{ request('date_to') }}
                                        @endif
                                        <a href="{{ route('delivery-routes.index', array_merge(request()->except(['date_from', 'date_to']), ['page' => 1])) }}"
                                            class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('status'))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Status: {{ ucfirst(request('status')) }}
                                        <a href="{{ route('delivery-routes.index', array_merge(request()->except('status'), ['page' => 1])) }}"
                                            class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('driver_id'))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Driver:
                                        @php
                                            $driverName = request('driver_id');
                                            foreach ($drivers as $driver) {
                                                if ($driver->id == request('driver_id')) {
                                                    $driverName = $driver->display_name;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        {{ $driverName }}
                                        <a href="{{ route('delivery-routes.index', array_merge(request()->except('driver_id'), ['page' => 1])) }}"
                                            class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('zone'))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Zone: {{ request('zone') }}
                                        <a href="{{ route('delivery-routes.index', array_merge(request()->except('zone'), ['page' => 1])) }}"
                                            class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('vehicle'))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Vehicle:
                                        @php
                                            $vehicleName = request('vehicle');
                                            foreach ($vehicles as $vehicle) {
                                                if ($vehicle->id == request('vehicle')) {
                                                    $vehicleName = $vehicle->title;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        {{ $vehicleName }}
                                        <a href="{{ route('delivery-routes.index', array_merge(request()->except('vehicle'), ['page' => 1])) }}"
                                            class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('orders_count'))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Orders Count: {{ request('orders_count') }}
                                        <a href="{{ route('delivery-routes.index', array_merge(request()->except('orders_count'), ['page' => 1])) }}"
                                            class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                @if(request('search'))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Search: {{ request('search') }}
                                        <a href="{{ route('delivery-routes.index', array_merge(request()->except('search'), ['page' => 1])) }}"
                                            class="ml-1 text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </span>
                                @endif

                                <a href="{{ route('delivery-routes.index') }}"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Clear All Filters
                                    <i class="fas fa-times-circle ml-1"></i>
                                </a>
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
                                        Route ID
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Driver
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vehicle
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Zone
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Orders
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Completed
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Start Time
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        End Time
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($delivery_routes as $route)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                            <a href="{{ route('delivery-routes.show', $route->id) }}">{{ $route->id }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $route->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClass = [
                                                    'active' => 'bg-green-100 text-green-800',
                                                    'inactive' => 'bg-red-100 text-red-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'completed' => 'bg-blue-100 text-blue-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                ][$route->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ ucfirst($route->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $route->deliveryman->display_name ?? 'Unassigned' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($route->deliveryman && isset($route->deliveryman->vehicle))
                                                {{ $route->deliveryman->vehicle->title ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $route->start_location ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $route->orders_count ?? 0 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $route->completed_orders_count ?? 0 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $route->start_time ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $route->end_time ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('delivery-routes.show', $route->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('delivery-routes.edit', $route->id) }}" class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('delivery-routes.destroy', $route->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this route?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No delivery routes found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if ($delivery_routes->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-50 cursor-not-allowed">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $delivery_routes->previousPageUrl() }}"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            @endif

                            @if ($delivery_routes->hasMorePages())
                                <a href="{{ $delivery_routes->nextPageUrl() }}"
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
                                    Showing <span class="font-medium">{{ $delivery_routes->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $delivery_routes->lastItem() ?? 0 }}</span> of
                                    <span class="font-medium">{{ $delivery_routes->total() }}</span> results
                                </p>
                            </div>
                            <div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-700">Rows per page:</span>
                                    <form id="per-page-form" action="{{ route('delivery-routes.index') }}" method="GET" class="inline">
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
                                        @if ($delivery_routes->onFirstPage())
                                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-50 text-sm font-medium text-gray-400 cursor-not-allowed">
                                                <span class="sr-only">Previous</span>
                                                <i class="fas fa-chevron-left h-5 w-5"></i>
                                            </span>
                                        @else
                                            <a href="{{ $delivery_routes->previousPageUrl() }}"
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                <span class="sr-only">Previous</span>
                                                <i class="fas fa-chevron-left h-5 w-5"></i>
                                            </a>
                                        @endif

                                        @if ($delivery_routes->hasMorePages())
                                            <a href="{{ $delivery_routes->nextPageUrl() }}"
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

                <!-- Map View (Hidden by default) -->
                <div id="map-view" class="bg-white shadow rounded-md overflow-hidden hidden">
                    <div class="p-4 h-96 flex items-center justify-center bg-gray-100">
                        <div class="text-center">
                            <i class="fas fa-map-marked-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500">Delivery routes map will be displayed here</p>
                            <p class="text-sm text-gray-400 mt-2">Google Maps integration required</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Table/Map View Toggle
                const tableViewBtn = document.getElementById('table-view-btn');
                const mapViewBtn = document.getElementById('map-view-btn');
                const tableView = document.getElementById('table-view');
                const mapView = document.getElementById('map-view');

                tableViewBtn.addEventListener('click', function () {
                    tableViewBtn.classList.add('text-blue-700', 'active');
                    tableViewBtn.classList.remove('text-gray-900');
                    mapViewBtn.classList.add('text-gray-900');
                    mapViewBtn.classList.remove('text-blue-700', 'active');

                    tableView.classList.remove('hidden');
                    mapView.classList.add('hidden');
                });

                mapViewBtn.addEventListener('click', function () {
                    mapViewBtn.classList.add('text-blue-700', 'active');
                    mapViewBtn.classList.remove('text-gray-900');
                    tableViewBtn.classList.add('text-gray-900');
                    tableViewBtn.classList.remove('text-blue-700', 'active');

                    mapView.classList.remove('hidden');
                    tableView.classList.add('hidden');
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
                    button.addEventListener('click', function () {
                        filterPopups.classList.add('hidden');
                        allPopups.forEach(popup => popup.classList.add('hidden'));

                        // Reset all pills to default state
                        filterPills.forEach(pill => {
                            pill.classList.remove('bg-blue-100', 'text-blue-800');
                            pill.classList.add('bg-gray-100', 'text-gray-800');
                        });
                    });
                });

                // Route ID Search Functionality
                const routeIdSearch = document.getElementById('route-id-search');
                const routeIdResults = document.getElementById('route-id-results');

                if (routeIdSearch && routeIdResults) {
                    const routeIdResultItems = document.querySelectorAll('.route-id-result');

                    routeIdSearch.addEventListener('focus', function() {
                        routeIdResults.classList.remove('hidden');
                    });

                    routeIdSearch.addEventListener('input', function() {
                        const searchValue = this.value.toLowerCase();

                        // In a real implementation, you would fetch results from the server
                        // For now, we'll just filter the example results
                        routeIdResultItems.forEach(item => {
                            if (item.textContent.toLowerCase().includes(searchValue)) {
                                item.classList.remove('hidden');
                            } else {
                                item.classList.add('hidden');
                            }
                        });
                    });

                    // Handle clicking on a result
                    routeIdResultItems.forEach(item => {
                        item.addEventListener('click', function() {
                            routeIdSearch.value = this.textContent;
                            routeIdResults.classList.add('hidden');
                        });
                    });

                    // Hide results when clicking outside
                    document.addEventListener('click', function(e) {
                        if (!routeIdSearch.contains(e.target) && !routeIdResults.contains(e.target)) {
                            routeIdResults.classList.add('hidden');
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

                // Highlight active filter pills
                @if(request('route_id'))
                    document.getElementById('route-id-filter').classList.remove('bg-gray-100', 'text-gray-800');
                    document.getElementById('route-id-filter').classList.add('bg-blue-100', 'text-blue-800');
                @endif

                @if(request('date_from') || request('date_to'))
                    document.getElementById('date-filter').classList.remove('bg-gray-100', 'text-gray-800');
                    document.getElementById('date-filter').classList.add('bg-blue-100', 'text-blue-800');
                @endif

                @if(request('status'))
                    document.getElementById('status-filter').classList.remove('bg-gray-100', 'text-gray-800');
                    document.getElementById('status-filter').classList.add('bg-blue-100', 'text-blue-800');
                @endif

                @if(request('driver_id'))
                    document.getElementById('driver-filter').classList.remove('bg-gray-100', 'text-gray-800');
                    document.getElementById('driver-filter').classList.add('bg-blue-100', 'text-blue-800');
                @endif

                @if(request('zone'))
                    document.getElementById('zone-filter').classList.remove('bg-gray-100', 'text-gray-800');
                    document.getElementById('zone-filter').classList.add('bg-blue-100', 'text-blue-800');
                @endif

                @if(request('vehicle'))
                    document.getElementById('vehicle-filter').classList.remove('bg-gray-100', 'text-gray-800');
                    document.getElementById('vehicle-filter').classList.add('bg-blue-100', 'text-blue-800');
                @endif

                @if(request('orders_count'))
                    document.getElementById('orders-count-filter').classList.remove('bg-gray-100', 'text-gray-800');
                    document.getElementById('orders-count-filter').classList.add('bg-blue-100', 'text-blue-800');
                @endif

                @if(request('search') || request('per_page'))
                    document.getElementById('more-filter').classList.remove('bg-gray-100', 'text-gray-800');
                    document.getElementById('more-filter').classList.add('bg-blue-100', 'text-blue-800');
                @endif
            });
        </script>
    @endpush
</x-master-layout>