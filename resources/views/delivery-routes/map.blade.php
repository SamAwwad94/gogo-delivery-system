<x-master-layout>
    <style>
        #map {
            height: 600px;
            width: 100%;
        }
    </style>
    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.delivery_route_map') }}</h4>
            <h6>{{ $delivery_route->name }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('delivery-routes.index') }}" class="btn btn-added">
                <img src="{{ asset('assets/img/icons/return.svg') }}" alt="img" class="me-1">
                {{ __('message.back_to_list') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>{{ __('message.route_details') }}</h5>
                    <p><strong>{{ __('message.start_location') }}:</strong> {{ $delivery_route->start_location }}</p>
                    <p><strong>{{ __('message.end_location') }}:</strong> {{ $delivery_route->end_location }}</p>
                    <p><strong>{{ __('message.deliveryman') }}:</strong>
                        {{ optional($delivery_route->deliveryman)->name }}</p>
                </div>
                <div class="col-md-6">
                    <h5>{{ __('message.waypoints') }}</h5>
                    <p>{{ $delivery_route->waypoints ?: __('message.no_waypoints') }}</p>
                </div>
            </div>
            <div id="map"></div>
        </div>
    </div>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap"
        async defer></script>
    <script>
        function initMap() {
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 7,
                center: { lat: 33.8547, lng: 35.8623 }, // Default to Lebanon
            });
            directionsRenderer.setMap(map);

            calculateAndDisplayRoute(directionsService, directionsRenderer);
        }

        function calculateAndDisplayRoute(directionsService, directionsRenderer) {
            const start = "{{ $delivery_route->start_location }}";
            const end = "{{ $delivery_route->end_location }}";

            // Parse waypoints if they exist
            let waypoints = [];
            @if($delivery_route->waypoints)
                const waypointsStr = "{{ $delivery_route->waypoints }}";
                const waypointsList = waypointsStr.split(',').map(item => item.trim());
                waypoints = waypointsList.map(location => ({
                    location: location,
                    stopover: true
                }));
            @endif

            directionsService.route(
                {
                    origin: start,
                    destination: end,
                    waypoints: waypoints,
                    optimizeWaypoints: true,
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                (response, status) => {
                    if (status === "OK") {
                        directionsRenderer.setDirections(response);

                        // Display route information
                        const route = response.routes[0];
                        let totalDistance = 0;
                        let totalDuration = 0;

                        // Calculate total distance and duration
                        route.legs.forEach((leg) => {
                            totalDistance += leg.distance.value;
                            totalDuration += leg.duration.value;
                        });

                        // Convert to kilometers and hours/minutes
                        const distanceKm = (totalDistance / 1000).toFixed(2);
                        const hours = Math.floor(totalDuration / 3600);
                        const minutes = Math.floor((totalDuration % 3600) / 60);

                        // Display in the UI
                        const routeInfoHtml = `
                            <div class="alert alert-info mt-3">
                                <strong>{{ __('message.total_distance') }}:</strong> ${distanceKm} km<br>
                                <strong>{{ __('message.estimated_time') }}:</strong> ${hours} {{ __('message.hours') }} ${minutes} {{ __('message.minutes') }}
                            </div>
                        `;

                        document.getElementById('map').insertAdjacentHTML('afterend', routeInfoHtml);
                    } else {
                        window.alert("Directions request failed due to " + status);
                    }
                }
            );
        }
    </script>
</x-master-layout>