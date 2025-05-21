<x-master-layout :assets="$assets ?? []">
    <link rel="stylesheet" href="{{ asset('css/shadcn-map.css') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.order_map') }}</h4>
            <h6>{{ __('message.view_orders_on_map') }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('order.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>
                {{ __('message.back_to_orders') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="orders-map" class="h-[600px]"></div>
        </div>
    </div>

    <script>
        // Set Google Maps API key for use in JavaScript
        window.googleMapsApiKey = "{{ config('services.google.maps_api_key') }}";
        
        // Pass orders data to JavaScript
        window.ordersData = @json($orders);
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap" async defer></script>
    <script src="{{ asset('js/enhanced-orders-map.js') }}"></script>
</x-master-layout>
