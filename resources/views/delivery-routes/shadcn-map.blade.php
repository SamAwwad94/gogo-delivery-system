<x-shadcn-layout :assets="['leaflet']">
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ __('message.route_map') }}</h1>
            <p class="text-muted-foreground">{{ $route->name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('delivery-routes.show', $route->id) }}" class="shadcn-button shadcn-button-outline">
                <i class="fas fa-angle-double-left mr-2"></i>
                {{ __('message.back') }}
            </a>
        </div>
    </div>
    
    <div class="grid gap-4 md:grid-cols-3">
        <!-- Route details -->
        <div class="md:col-span-1 space-y-4">
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col space-y-1.5 p-6 pb-3">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">{{ __('message.route_details') }}</h3>
                </div>
                <div class="p-6 pt-0">
                    <dl class="space-y-4">
                        <div class="flex flex-row justify-between">
                            <dt class="font-medium text-muted-foreground">{{ __('message.name') }}</dt>
                            <dd>{{ $route->name }}</dd>
                        </div>
                        <div class="flex flex-row justify-between">
                            <dt class="font-medium text-muted-foreground">{{ __('message.status') }}</dt>
                            <dd>{!! $route->status_badge !!}</dd>
                        </div>
                        <div class="flex flex-row justify-between">
                            <dt class="font-medium text-muted-foreground">{{ __('message.delivery_man') }}</dt>
                            <dd>
                                @if($route->deliveryMan)
                                    <a href="{{ route('deliveryman.show', $route->deliveryMan->id) }}" class="text-primary hover:underline">
                                        {{ $route->deliveryMan->display_name }}
                                    </a>
                                @else
                                    {{ __('message.not_found') }}
                                @endif
                            </dd>
                        </div>
                        <div class="flex flex-row justify-between">
                            <dt class="font-medium text-muted-foreground">{{ __('message.start_location') }}</dt>
                            <dd class="text-right">{{ $route->start_location }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <!-- Status update -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col space-y-1.5 p-6 pb-3">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">{{ __('message.change_status') }}</h3>
                </div>
                <div class="p-6 pt-0">
                    <form action="{{ route('delivery-routes.status', $route->id) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="status" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">{{ __('message.status') }}</label>
                                <select name="status" id="status" class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="pending" {{ $route->status == 'pending' ? 'selected' : '' }}>{{ __('message.pending') }}</option>
                                    <option value="in_progress" {{ $route->status == 'in_progress' ? 'selected' : '' }}>{{ __('message.in_progress') }}</option>
                                    <option value="completed" {{ $route->status == 'completed' ? 'selected' : '' }}>{{ __('message.completed') }}</option>
                                    <option value="cancelled" {{ $route->status == 'cancelled' ? 'selected' : '' }}>{{ __('message.cancelled') }}</option>
                                </select>
                            </div>
                            <button type="submit" class="shadcn-button shadcn-button-primary">{{ __('message.update_status') }}</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Orders list -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col space-y-1.5 p-6 pb-3">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">{{ __('message.orders') }} ({{ $route->orders->count() }})</h3>
                </div>
                <div class="p-6 pt-0">
                    <div class="space-y-2">
                        @forelse($route->orders as $order)
                            <div class="flex items-center justify-between p-2 rounded-md hover:bg-muted/50">
                                <div>
                                    <div class="font-medium">#{{ $order->id }}</div>
                                    <div class="text-sm text-muted-foreground">{{ optional($order->client)->name ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                        @if($order->status == 'completed')
                                            bg-success/10 text-success ring-success/20
                                        @elseif($order->status == 'cancelled')
                                            bg-destructive/10 text-destructive ring-destructive/20
                                        @elseif($order->status == 'pending')
                                            bg-warning/10 text-warning ring-warning/20
                                        @else
                                            bg-primary/10 text-primary ring-primary/20
                                        @endif
                                    ">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted-foreground">
                                {{ __('message.no_data_found') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Map -->
        <div class="md:col-span-2">
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="p-6">
                    <div id="map" class="h-[600px] rounded-md"></div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
    <script>
        let map;
        let directionsService;
        let directionsRenderer;
        
        function initMap() {
            // Initialize map
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: { lat: {{ $route->start_latitude }}, lng: {{ $route->start_longitude }} },
                styles: [
                    {
                        "featureType": "all",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": document.documentElement.classList.contains('dark') ? "#ffffff" : "#000000"}]
                    },
                    {
                        "featureType": "all",
                        "elementType": "labels.text.stroke",
                        "stylers": [{"visibility": "on"}, {"color": document.documentElement.classList.contains('dark') ? "#000000" : "#ffffff"}, {"lightness": 16}]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "geometry.fill",
                        "stylers": [{"color": document.documentElement.classList.contains('dark') ? "#212121" : "#fefefe"}]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "geometry.stroke",
                        "stylers": [{"color": document.documentElement.classList.contains('dark') ? "#444444" : "#fefefe"}, {"lightness": 17}, {"weight": 1.2}]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "geometry",
                        "stylers": [{"color": document.documentElement.classList.contains('dark') ? "#171717" : "#f5f5f5"}, {"lightness": 20}]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry",
                        "stylers": [{"color": document.documentElement.classList.contains('dark') ? "#18222b" : "#e9e9e9"}, {"lightness": 17}]
                    }
                ]
            });
            
            // Initialize directions service and renderer
            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: true, // We'll add custom markers
                polylineOptions: {
                    strokeColor: document.documentElement.classList.contains('dark') ? "#4f46e5" : "#3b82f6",
                    strokeWeight: 5,
                    strokeOpacity: 0.8
                }
            });
            
            // Add start location marker
            const startMarker = new google.maps.Marker({
                position: { lat: {{ $route->start_latitude }}, lng: {{ $route->start_longitude }} },
                map: map,
                icon: {
                    url: "https://maps.google.com/mapfiles/ms/icons/green-dot.png",
                    scaledSize: new google.maps.Size(40, 40)
                },
                title: "{{ __('message.start_location') }}: {{ $route->start_location }}"
            });
            
            // Add info window for start location
            const startInfoWindow = new google.maps.InfoWindow({
                content: `<div><strong>{{ __('message.start_location') }}</strong><br>{{ $route->start_location }}</div>`
            });
            
            startMarker.addListener("click", () => {
                startInfoWindow.open(map, startMarker);
            });
            
            // Add order markers and calculate route
            const orders = @json($route->orders);
            const waypoints = [];
            const markers = [];
            
            if (orders.length > 0) {
                orders.forEach((order, index) => {
                    // Skip orders without coordinates
                    if (!order.pickup_latitude || !order.pickup_longitude || !order.delivery_latitude || !order.delivery_longitude) {
                        return;
                    }
                    
                    // Add pickup marker
                    const pickupMarker = new google.maps.Marker({
                        position: { lat: parseFloat(order.pickup_latitude), lng: parseFloat(order.pickup_longitude) },
                        map: map,
                        icon: {
                            url: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png",
                            scaledSize: new google.maps.Size(30, 30)
                        },
                        title: `Order #${order.id} - Pickup: ${order.pickup_point}`
                    });
                    
                    // Add pickup info window
                    const pickupInfoWindow = new google.maps.InfoWindow({
                        content: `<div><strong>Order #${order.id} - Pickup</strong><br>${order.pickup_point}<br>Client: ${order.client ? order.client.name : 'N/A'}</div>`
                    });
                    
                    pickupMarker.addListener("click", () => {
                        pickupInfoWindow.open(map, pickupMarker);
                    });
                    
                    // Add delivery marker
                    const deliveryMarker = new google.maps.Marker({
                        position: { lat: parseFloat(order.delivery_latitude), lng: parseFloat(order.delivery_longitude) },
                        map: map,
                        icon: {
                            url: "https://maps.google.com/mapfiles/ms/icons/red-dot.png",
                            scaledSize: new google.maps.Size(30, 30)
                        },
                        title: `Order #${order.id} - Delivery: ${order.delivery_point}`
                    });
                    
                    // Add delivery info window
                    const deliveryInfoWindow = new google.maps.InfoWindow({
                        content: `<div><strong>Order #${order.id} - Delivery</strong><br>${order.delivery_point}<br>Client: ${order.client ? order.client.name : 'N/A'}</div>`
                    });
                    
                    deliveryMarker.addListener("click", () => {
                        deliveryInfoWindow.open(map, deliveryMarker);
                    });
                    
                    // Add to markers array
                    markers.push(pickupMarker, deliveryMarker);
                    
                    // Add to waypoints
                    waypoints.push({
                        location: { lat: parseFloat(order.pickup_latitude), lng: parseFloat(order.pickup_longitude) },
                        stopover: true
                    });
                    
                    waypoints.push({
                        location: { lat: parseFloat(order.delivery_latitude), lng: parseFloat(order.delivery_longitude) },
                        stopover: true
                    });
                });
                
                // Calculate and display route
                if (waypoints.length > 0) {
                    calculateAndDisplayRoute(
                        { lat: {{ $route->start_latitude }}, lng: {{ $route->start_longitude }} },
                        waypoints
                    );
                }
            }
            
            // Listen for theme changes
            window.addEventListener('theme-changed', function(e) {
                // Update map styles based on theme
                const isDark = e.detail.theme === 'dark';
                
                const styles = [
                    {
                        "featureType": "all",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": isDark ? "#ffffff" : "#000000"}]
                    },
                    {
                        "featureType": "all",
                        "elementType": "labels.text.stroke",
                        "stylers": [{"visibility": "on"}, {"color": isDark ? "#000000" : "#ffffff"}, {"lightness": 16}]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "geometry.fill",
                        "stylers": [{"color": isDark ? "#212121" : "#fefefe"}]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "geometry.stroke",
                        "stylers": [{"color": isDark ? "#444444" : "#fefefe"}, {"lightness": 17}, {"weight": 1.2}]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "geometry",
                        "stylers": [{"color": isDark ? "#171717" : "#f5f5f5"}, {"lightness": 20}]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry",
                        "stylers": [{"color": isDark ? "#18222b" : "#e9e9e9"}, {"lightness": 17}]
                    }
                ];
                
                map.setOptions({ styles: styles });
                
                // Update route color
                if (directionsRenderer) {
                    directionsRenderer.setOptions({
                        polylineOptions: {
                            strokeColor: isDark ? "#4f46e5" : "#3b82f6",
                            strokeWeight: 5,
                            strokeOpacity: 0.8
                        }
                    });
                    
                    // Re-render directions to apply new style
                    directionsRenderer.setMap(map);
                }
            });
        }
        
        function calculateAndDisplayRoute(start, waypoints) {
            // If there are too many waypoints, we need to split the request
            // Google Maps API has a limit of 25 waypoints per request
            const maxWaypoints = 23; // 25 - 2 (start and end)
            
            if (waypoints.length <= maxWaypoints) {
                // Single request
                const request = {
                    origin: start,
                    destination: start, // Return to start
                    waypoints: waypoints,
                    optimizeWaypoints: true,
                    travelMode: google.maps.TravelMode.DRIVING,
                };
                
                directionsService.route(request, (result, status) => {
                    if (status === "OK") {
                        directionsRenderer.setDirections(result);
                        
                        // Display route information
                        let totalDistance = 0;
                        let totalDuration = 0;
                        
                        result.routes[0].legs.forEach((leg) => {
                            totalDistance += leg.distance.value;
                            totalDuration += leg.duration.value;
                        });
                        
                        // Convert to km and hours/minutes
                        totalDistance = (totalDistance / 1000).toFixed(2);
                        const hours = Math.floor(totalDuration / 3600);
                        const minutes = Math.floor((totalDuration % 3600) / 60);
                        
                        // Add info to the map
                        const infoDiv = document.createElement('div');
                        infoDiv.className = 'map-info';
                        infoDiv.innerHTML = `
                            <div class="bg-card p-3 rounded-md shadow-md m-2 max-w-xs border border-border">
                                <h6 class="text-sm font-medium mb-1">Route Information</h6>
                                <div class="text-xs text-muted-foreground">Total Distance: ${totalDistance} km</div>
                                <div class="text-xs text-muted-foreground">Estimated Time: ${hours > 0 ? hours + ' hr ' : ''}${minutes} min</div>
                            </div>
                        `;
                        
                        map.controls[google.maps.ControlPosition.TOP_LEFT].push(infoDiv);
                    } else {
                        console.error("Directions request failed due to " + status);
                        alert("Could not calculate route: " + status);
                    }
                });
            } else {
                // Multiple requests needed
                alert("Route has too many stops to display at once. Only showing a partial route.");
                
                // Just show the first maxWaypoints
                const firstBatch = waypoints.slice(0, maxWaypoints);
                
                const request = {
                    origin: start,
                    destination: start,
                    waypoints: firstBatch,
                    optimizeWaypoints: true,
                    travelMode: google.maps.TravelMode.DRIVING,
                };
                
                directionsService.route(request, (result, status) => {
                    if (status === "OK") {
                        directionsRenderer.setDirections(result);
                    } else {
                        console.error("Directions request failed due to " + status);
                        alert("Could not calculate route: " + status);
                    }
                });
            }
        }
    </script>
    @endpush
</x-shadcn-layout>
