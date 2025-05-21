// Enhanced Delivery Route Map with Real-time Tracking
let map, directionsService, directionsRenderer, deliveryManMarker, watchId;
let routeDetails = {};
let isTracking = false;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map
    initMap();
    
    // Add tracking controls
    addTrackingControls();
});

function initMap() {
    // Check if the map container exists
    const mapContainer = document.getElementById('delivery-route-map');
    if (!mapContainer) return;
    
    // Get route details from data attributes
    routeDetails = {
        start: mapContainer.dataset.start,
        end: mapContainer.dataset.end,
        waypoints: mapContainer.dataset.waypoints,
        name: mapContainer.dataset.name,
        deliveryMan: mapContainer.dataset.deliveryman
    };
    
    // Create map container
    const mapElement = document.createElement('div');
    mapElement.id = 'map';
    mapElement.style.height = '500px';
    mapElement.style.width = '100%';
    mapContainer.appendChild(mapElement);
    
    // Create info panel
    const infoPanel = document.createElement('div');
    infoPanel.className = 'mt-4 p-4 bg-muted rounded-lg';
    infoPanel.id = 'route-info-panel';
    infoPanel.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h5 class="text-lg font-medium mb-2">Route Details</h5>
                <p><strong>Start:</strong> ${routeDetails.start}</p>
                <p><strong>End:</strong> ${routeDetails.end}</p>
                <p><strong>Delivery Man:</strong> ${routeDetails.deliveryMan || 'N/A'}</p>
            </div>
            <div>
                <h5 class="text-lg font-medium mb-2">Route Statistics</h5>
                <p id="total-distance"><strong>Total Distance:</strong> Calculating...</p>
                <p id="estimated-time"><strong>Estimated Time:</strong> Calculating...</p>
                <p id="current-status"><strong>Status:</strong> <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-warning/10 text-warning ring-warning/20">Pending</span></p>
            </div>
        </div>
    `;
    mapContainer.appendChild(infoPanel);
    
    // Initialize Google Maps
    loadGoogleMapsScript();
}

function loadGoogleMapsScript() {
    // Check if Google Maps API is already loaded
    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
        initGoogleMap();
        return;
    }
    
    // Load Google Maps API
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${window.googleMapsApiKey}&callback=initGoogleMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
    
    // Define global callback
    window.initGoogleMap = initGoogleMap;
}

function initGoogleMap() {
    // Initialize directions service and renderer
    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer();
    
    // Create map
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: { lat: 33.8547, lng: 35.8623 }, // Default to Lebanon
        mapTypeControl: true,
        fullscreenControl: true,
        streetViewControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    
    // Set directions renderer
    directionsRenderer.setMap(map);
    
    // Calculate and display route
    calculateAndDisplayRoute();
}

function calculateAndDisplayRoute() {
    // Parse waypoints if they exist
    let waypoints = [];
    if (routeDetails.waypoints) {
        const waypointsList = routeDetails.waypoints.split(',').map(item => item.trim());
        waypoints = waypointsList.map(location => ({
            location: location,
            stopover: true
        }));
    }
    
    // Request directions
    directionsService.route(
        {
            origin: routeDetails.start,
            destination: routeDetails.end,
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
                
                // Update info panel
                document.getElementById('total-distance').innerHTML = `<strong>Total Distance:</strong> ${distanceKm} km`;
                document.getElementById('estimated-time').innerHTML = `<strong>Estimated Time:</strong> ${hours} hours ${minutes} minutes`;
            } else {
                alert("Directions request failed due to " + status);
            }
        }
    );
}

function addTrackingControls() {
    // Check if the map container exists
    const mapContainer = document.getElementById('delivery-route-map');
    if (!mapContainer) return;
    
    // Create tracking controls
    const trackingControls = document.createElement('div');
    trackingControls.className = 'mt-4';
    trackingControls.innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <button id="start-tracking" class="shadcn-button shadcn-button-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                    Start Real-time Tracking
                </button>
                <button id="stop-tracking" class="shadcn-button shadcn-button-destructive hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><circle cx="12" cy="12" r="10"></circle><rect x="9" y="9" width="6" height="6"></rect></svg>
                    Stop Tracking
                </button>
            </div>
            <div>
                <button id="refresh-map" class="shadcn-button shadcn-button-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                    Refresh Map
                </button>
            </div>
        </div>
    `;
    mapContainer.appendChild(trackingControls);
    
    // Add event listeners
    document.getElementById('start-tracking').addEventListener('click', startTracking);
    document.getElementById('stop-tracking').addEventListener('click', stopTracking);
    document.getElementById('refresh-map').addEventListener('click', refreshMap);
}

function startTracking() {
    if (isTracking) return;
    
    // Toggle buttons
    document.getElementById('start-tracking').classList.add('hidden');
    document.getElementById('stop-tracking').classList.remove('hidden');
    
    // Create delivery man marker if it doesn't exist
    if (!deliveryManMarker) {
        deliveryManMarker = new google.maps.Marker({
            map: map,
            icon: {
                url: '/images/delivery-man-marker.png',
                scaledSize: new google.maps.Size(40, 40)
            },
            title: 'Delivery Man'
        });
    }
    
    // Start tracking
    isTracking = true;
    
    // Simulate tracking for demo purposes
    // In a real application, you would use a WebSocket connection to get real-time updates
    simulateTracking();
}

function stopTracking() {
    if (!isTracking) return;
    
    // Toggle buttons
    document.getElementById('start-tracking').classList.remove('hidden');
    document.getElementById('stop-tracking').classList.add('hidden');
    
    // Stop tracking
    isTracking = false;
    
    // Clear tracking interval
    if (watchId) {
        clearInterval(watchId);
        watchId = null;
    }
}

function refreshMap() {
    // Recalculate and display route
    calculateAndDisplayRoute();
}

function simulateTracking() {
    // Get route coordinates
    const route = directionsRenderer.getDirections().routes[0];
    const path = route.overview_path;
    let currentIndex = 0;
    
    // Update marker position every 2 seconds
    watchId = setInterval(() => {
        if (!isTracking) {
            clearInterval(watchId);
            return;
        }
        
        // Update marker position
        if (currentIndex < path.length) {
            const position = path[currentIndex];
            deliveryManMarker.setPosition(position);
            map.panTo(position);
            currentIndex++;
        } else {
            // End of route
            stopTracking();
            alert('Delivery completed!');
        }
    }, 2000);
}
