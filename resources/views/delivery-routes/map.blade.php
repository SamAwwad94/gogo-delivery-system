@extends('layouts.app')
@section('title') {{__('message.route_map')}} @endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-bold">{{ $pageTitle ?? __('message.route_map') }}</h5>
                    <div>
                        <a href="{{ route('delivery-routes.show', $route->id) }}" class="btn btn-sm btn-secondary"><i class="fa fa-angle-double-left"></i> {{ __('message.back') }}</a>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">{{ __('message.route_details') }}</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="150">{{ __('message.name') }}</th>
                                        <td>{{ $route->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('message.status') }}</th>
                                        <td>{!! $route->status_badge !!}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('message.delivery_man') }}</th>
                                        <td>
                                            @if($route->deliveryMan)
                                                <a href="{{ route('deliveryman.show', $route->deliveryMan->id) }}">
                                                    {{ $route->deliveryMan->display_name }}
                                                </a>
                                            @else
                                                {{ __('message.not_found') }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('message.start_location') }}</th>
                                        <td>{{ $route->start_location }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">{{ __('message.orders') }} ({{ $route->orders->count() }})</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('message.client') }}</th>
                                                <th>{{ __('message.status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($route->orders as $order)
                                                <tr>
                                                    <td>{{ $order->id }}</td>
                                                    <td>{{ optional($order->client)->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ getStatusBadgeClass($order->status) }}">
                                                            {{ ucfirst($order->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">{{ __('message.no_data_found') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div id="map" style="height: 600px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('bottom_script')
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
        });
        
        // Initialize directions service and renderer
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true // We'll add custom markers
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
                        <div style="background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 6px rgba(0,0,0,0.3); margin: 10px; max-width: 300px;">
                            <h6 style="margin: 0 0 5px 0;">Route Information</h6>
                            <div>Total Distance: ${totalDistance} km</div>
                            <div>Estimated Time: ${hours > 0 ? hours + ' hr ' : ''}${minutes} min</div>
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
@endsection
