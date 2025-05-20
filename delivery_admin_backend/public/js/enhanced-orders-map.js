// Enhanced Orders Map
let map;
let markers = [];
let infoWindows = [];

function initMap() {
    // Initialize map
    map = new google.maps.Map(document.getElementById("orders-map"), {
        zoom: 12,
        center: { lat: 33.8938, lng: 35.5018 }, // Default to Beirut
        styles: getMapStyles(),
    });

    // Add orders to map
    if (window.ordersData && window.ordersData.length > 0) {
        addOrdersToMap(window.ordersData);
    } else {
        // Show message if no orders
        const mapElement = document.getElementById("orders-map");
        mapElement.innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-lg text-gray-500">No active orders to display on map</p></div>';
    }

    // Listen for theme changes
    window.addEventListener('theme-changed', function(e) {
        // Update map styles based on theme
        const isDark = e.detail.theme === 'dark';
        map.setOptions({ styles: getMapStyles(isDark) });
    });
}

function addOrdersToMap(orders) {
    // Clear existing markers
    clearMarkers();
    
    // Bounds to fit all markers
    const bounds = new google.maps.LatLngBounds();
    
    // Add markers for each order
    orders.forEach(order => {
        // Try to get pickup and delivery coordinates
        let pickupCoords = extractCoordinates(order.pickup_point);
        let deliveryCoords = extractCoordinates(order.delivery_point);
        
        // Add pickup marker if coordinates exist
        if (pickupCoords) {
            addMarker(pickupCoords, order, 'pickup');
            bounds.extend(pickupCoords);
        }
        
        // Add delivery marker if coordinates exist
        if (deliveryCoords) {
            addMarker(deliveryCoords, order, 'delivery');
            bounds.extend(deliveryCoords);
        }
        
        // Add route between pickup and delivery if both exist
        if (pickupCoords && deliveryCoords) {
            drawRoute(pickupCoords, deliveryCoords);
        }
    });
    
    // Fit map to bounds if markers exist
    if (markers.length > 0) {
        map.fitBounds(bounds);
    }
}

function extractCoordinates(point) {
    if (!point) return null;
    
    // If point is a string, parse it
    if (typeof point === 'string') {
        try {
            point = JSON.parse(point);
        } catch (e) {
            return null;
        }
    }
    
    // Extract latitude and longitude
    if (point.latitude && point.longitude) {
        return { lat: parseFloat(point.latitude), lng: parseFloat(point.longitude) };
    }
    
    if (point.lat && point.lng) {
        return { lat: parseFloat(point.lat), lng: parseFloat(point.lng) };
    }
    
    return null;
}

function addMarker(position, order, type) {
    // Define icon based on type
    const icon = {
        url: type === 'pickup' 
            ? '/assets/img/icons/pickup-marker.png' 
            : '/assets/img/icons/delivery-marker.png',
        scaledSize: new google.maps.Size(32, 32),
    };
    
    // Create marker
    const marker = new google.maps.Marker({
        position: position,
        map: map,
        icon: icon,
        title: type === 'pickup' ? 'Pickup: Order #' + order.id : 'Delivery: Order #' + order.id,
        animation: google.maps.Animation.DROP,
    });
    
    // Create info window content
    const contentString = `
        <div class="info-window">
            <h3 class="text-lg font-semibold">Order #${order.id}</h3>
            <p class="text-sm"><strong>Status:</strong> ${formatStatus(order.status)}</p>
            <p class="text-sm"><strong>Customer:</strong> ${order.client_name || 'N/A'}</p>
            <p class="text-sm"><strong>Type:</strong> ${type === 'pickup' ? 'Pickup Location' : 'Delivery Location'}</p>
            <p class="text-sm"><strong>Address:</strong> ${type === 'pickup' 
                ? (typeof order.pickup_point === 'object' ? order.pickup_point.address : 'N/A')
                : (typeof order.delivery_point === 'object' ? order.delivery_point.address : 'N/A')}</p>
            <div class="mt-2">
                <a href="/order/${order.id}" class="text-blue-600 hover:text-blue-800">View Details</a>
            </div>
        </div>
    `;
    
    // Create info window
    const infoWindow = new google.maps.InfoWindow({
        content: contentString,
    });
    
    // Add click listener to marker
    marker.addListener('click', () => {
        // Close all open info windows
        infoWindows.forEach(iw => iw.close());
        
        // Open this info window
        infoWindow.open(map, marker);
    });
    
    // Store marker and info window
    markers.push(marker);
    infoWindows.push(infoWindow);
}

function drawRoute(origin, destination) {
    const directionsService = new google.maps.DirectionsService();
    const directionsRenderer = new google.maps.DirectionsRenderer({
        map: map,
        suppressMarkers: true,
        polylineOptions: {
            strokeColor: '#4f46e5',
            strokeOpacity: 0.7,
            strokeWeight: 5,
        },
    });
    
    directionsService.route(
        {
            origin: origin,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING,
        },
        (response, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
            }
        }
    );
}

function clearMarkers() {
    // Clear all markers
    markers.forEach(marker => marker.setMap(null));
    markers = [];
    
    // Close all info windows
    infoWindows.forEach(iw => iw.close());
    infoWindows = [];
}

function formatStatus(status) {
    const statusMap = {
        'draft': 'Draft',
        'create': 'Created',
        'courier_assigned': 'Assigned',
        'courier_accepted': 'Accepted',
        'courier_arrived': 'Arrived',
        'courier_picked_up': 'Picked Up',
        'courier_departed': 'Departed',
        'completed': 'Delivered',
        'cancelled': 'Cancelled',
    };
    
    return statusMap[status] || status;
}

function getMapStyles(isDark = document.documentElement.classList.contains('dark')) {
    if (isDark) {
        return [
            { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
            { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
            { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
            { featureType: "administrative.locality", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
            { featureType: "poi", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
            { featureType: "poi.park", elementType: "geometry", stylers: [{ color: "#263c3f" }] },
            { featureType: "poi.park", elementType: "labels.text.fill", stylers: [{ color: "#6b9a76" }] },
            { featureType: "road", elementType: "geometry", stylers: [{ color: "#38414e" }] },
            { featureType: "road", elementType: "geometry.stroke", stylers: [{ color: "#212a37" }] },
            { featureType: "road", elementType: "labels.text.fill", stylers: [{ color: "#9ca5b3" }] },
            { featureType: "road.highway", elementType: "geometry", stylers: [{ color: "#746855" }] },
            { featureType: "road.highway", elementType: "geometry.stroke", stylers: [{ color: "#1f2835" }] },
            { featureType: "road.highway", elementType: "labels.text.fill", stylers: [{ color: "#f3d19c" }] },
            { featureType: "transit", elementType: "geometry", stylers: [{ color: "#2f3948" }] },
            { featureType: "transit.station", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
            { featureType: "water", elementType: "geometry", stylers: [{ color: "#17263c" }] },
            { featureType: "water", elementType: "labels.text.fill", stylers: [{ color: "#515c6d" }] },
            { featureType: "water", elementType: "labels.text.stroke", stylers: [{ color: "#17263c" }] }
        ];
    } else {
        return [
            { featureType: "administrative", elementType: "geometry", stylers: [{ visibility: "off" }] },
            { featureType: "poi", stylers: [{ visibility: "simplified" }] },
            { featureType: "road", elementType: "labels.icon", stylers: [{ visibility: "off" }] },
            { featureType: "transit", stylers: [{ visibility: "off" }] }
        ];
    }
}
