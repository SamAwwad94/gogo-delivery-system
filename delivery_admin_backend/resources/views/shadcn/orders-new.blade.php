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
                            <i class="fas fa-plus mr-2"></i> New
                        </button>
                        <button type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-question-circle mr-2"></i> Learn
                        </button>
                        <a href="{{ route('shadcn.orders') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-arrow-left mr-2"></i> Original UI
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
                                Welcome to Demo Mode! Sample Orders & Routes Generated! Have a look around!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Filter Pills -->
                <div class="bg-white shadow rounded-md mb-6 overflow-x-auto">
                    <div class="p-4 flex space-x-2 flex-nowrap">
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-hashtag mr-1.5 text-gray-500"></i> Code
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-layer-group mr-1.5 text-gray-500"></i> Stages
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-tag mr-1.5 text-gray-500"></i> Status
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-route mr-1.5 text-gray-500"></i> Route/Name
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-concierge-bell mr-1.5 text-gray-500"></i> Services
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-credit-card mr-1.5 text-gray-500"></i> Payment/Status
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-calendar-alt mr-1.5 text-gray-500"></i> Created/At
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-clock mr-1.5 text-gray-500"></i> Status/Updated/At
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-map-marker-alt mr-1.5 text-gray-500"></i> pickup/zones
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-map-pin mr-1.5 text-gray-500"></i> delivery/zones
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-truck mr-1.5 text-gray-500"></i> Delivery Method
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-phone mr-1.5 text-gray-500"></i> Pickup/Phone
                        </button>
                        <button
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <i class="fas fa-ellipsis-h mr-1.5 text-gray-500"></i> More
                        </button>
                    </div>
                </div>

                <div id="table-view" class="bg-white shadow rounded-md overflow-hidden">
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Code
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stage
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Route
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Created At
                                        <i class="fas fa-sort ml-1"></i>
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Note
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status Updated At
                                        <i class="fas fa-sort ml-1"></i>
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Zone
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Delivery Last Attempt
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Delivery Attempts Count
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pickup Failed Reason
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Reference
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                            <a href="{{ route('order-view.show', $order->id) }}">{{ $order->id }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->stage ?? 'Pickup' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->status == 'Lost')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Lost
                                                </span>
                                            @elseif($order->status == 'ReadyForDelivery')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Ready For Delivery
                                                </span>
                                            @elseif($order->status == 'ReadyForPickup')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Ready For Pickup
                                                </span>
                                            @elseif($order->status == 'PickedUp')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    Picked Up
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ $order->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->route ?? 'Mar Roukoz' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('d M H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ---
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->updated_at->format('d M H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            Lebanon
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ---
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            0
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ---
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->reference ?? '---' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a href="#"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                            <a href="#"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">15</span> of
                                    <span class="font-medium">15</span> results
                                </p>
                            </div>
                            <div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-700">Rows per page:</span>
                                    <select
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option>10</option>
                                        <option selected>30</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>

                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                        aria-label="Pagination">
                                        <a href="#"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <i class="fas fa-chevron-left h-5 w-5"></i>
                                        </a>
                                        <a href="#"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <i class="fas fa-chevron-right h-5 w-5"></i>
                                        </a>
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
                            <p class="text-gray-500">Map view will be displayed here</p>
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
                const filterPills = document.querySelectorAll('.bg-gray-100.text-gray-800');
                filterPills.forEach(pill => {
                    pill.addEventListener('click', function () {
                        pill.classList.toggle('bg-blue-100');
                        pill.classList.toggle('text-blue-800');
                        pill.classList.toggle('bg-gray-100');
                        pill.classList.toggle('text-gray-800');
                    });
                });
            });
        </script>
    @endpush
</x-master-layout>