<x-master-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Add Tailwind CSS -->
                <style>
                    @import url('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
                </style>
                
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ $delivery_route->name }} - Route Map
                        </h1>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('delivery-routes.index') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Routes
                        </a>
                        <a href="{{ route('delivery-routes.edit', $delivery_route->id) }}"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-edit mr-2"></i> Edit Route
                        </a>
                    </div>
                </div>
                
                <!-- Route Info -->
                <div class="bg-white shadow rounded-md mb-6 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Route Name</h3>
                            <p class="mt-1 text-base font-semibold text-gray-900">{{ $delivery_route->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Driver</h3>
                            <p class="mt-1 text-base font-semibold text-gray-900">
                                {{ $delivery_route->deliveryman ? $delivery_route->deliveryman->display_name : 'Not Assigned' }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <p class="mt-1">
                                @if($delivery_route->status == 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created</h3>
                            <p class="mt-1 text-base font-semibold text-gray-900">
                                {{ $delivery_route->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Start Location</h3>
                                <p class="mt-1 text-base text-gray-900">{{ $delivery_route->start_location }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">End Location</h3>
                                <p class="mt-1 text-base text-gray-900">{{ $delivery_route->end_location }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($delivery_route->description)
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-500">Description</h3>
                        <p class="mt-1 text-base text-gray-900">{{ $delivery_route->description }}</p>
                    </div>
                    @endif
                </div>
                
                <!-- Map -->
                <div class="bg-white shadow rounded-md overflow-hidden">
                    <div class="p-4">
                        <h2 class="text-lg font-medium text-gray-900">Route Map</h2>
                        <p class="text-sm text-gray-500 mb-4">Visualize the delivery route with waypoints</p>
                        
                        <div id="map" class="h-96 w-full rounded-md border border-gray-200"></div>
                    </div>
                </div>
                
                <!-- Waypoints List -->
                <div class="bg-white shadow rounded-md overflow-hidden mt-6">
                    <div class="p-4">
                        <h2 class="text-lg font-medium text-gray-900">Waypoints</h2>
                        <p class="text-sm text-gray-500 mb-4">Stops along the delivery route</p>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Order
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Location
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estimated Arrival
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            Start
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $delivery_route->start_location }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Departed
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ now()->format('H:i') }}
                                        </td>
                                    </tr>
                                    
                                    @if($delivery_route->waypoints)
                                        @php
                                            $waypoints = explode('|', $delivery_route->waypoints);
                                            $statuses = ['Pending', 'Arrived', 'Completed'];
                                        @endphp
                                        
                                        @foreach($waypoints as $index => $waypoint)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    Waypoint {{ $index + 1 }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $waypoint }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $status = $statuses[array_rand($statuses)];
                                                        $statusClass = [
                                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                                            'Arrived' => 'bg-blue-100 text-blue-800',
                                                            'Completed' => 'bg-green-100 text-green-800',
                                                        ][$status];
                                                    @endphp
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                        {{ $status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ now()->addMinutes(($index + 1) * 15)->format('H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            End
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $delivery_route->end_location }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ now()->addHour()->format('H:i') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize map
            var map = L.map('map').setView([33.8938, 35.5018], 13); // Default to Beirut coordinates
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add markers for start and end locations
            var startMarker = L.marker([33.8938, 35.5018]).addTo(map);
            startMarker.bindPopup("<b>Start:</b> {{ $delivery_route->start_location }}").openPopup();
            
            var endMarker = L.marker([33.9, 35.48]).addTo(map);
            endMarker.bindPopup("<b>End:</b> {{ $delivery_route->end_location }}");
            
            // Add waypoints if available
            @if($delivery_route->waypoints)
                @php
                    $waypoints = explode('|', $delivery_route->waypoints);
                @endphp
                
                @foreach($waypoints as $index => $waypoint)
                    // Generate slightly different coordinates for each waypoint
                    var lat = 33.89 + (Math.random() * 0.05);
                    var lng = 35.48 + (Math.random() * 0.05);
                    
                    var waypointMarker = L.marker([lat, lng]).addTo(map);
                    waypointMarker.bindPopup("<b>Waypoint {{ $index + 1 }}:</b> {{ $waypoint }}");
                @endforeach
            @endif
            
            // Draw route line
            var routePoints = [
                [33.8938, 35.5018], // Start
                @if($delivery_route->waypoints)
                    @foreach($waypoints as $index => $waypoint)
                        [33.89 + (Math.random() * 0.05), 35.48 + (Math.random() * 0.05)],
                    @endforeach
                @endif
                [33.9, 35.48] // End
            ];
            
            var routeLine = L.polyline(routePoints, {color: 'blue', weight: 5, opacity: 0.7}).addTo(map);
            
            // Fit map to show all markers
            map.fitBounds(routeLine.getBounds());
        });
    </script>
    @endpush
</x-master-layout>
