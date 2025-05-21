<x-master-layout>
    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.delivery_route_map') }}</h4>
            <h6>{{ $delivery_route->name }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('delivery-routes.index') }}?shadcn=true" class="btn btn-added">
                <img src="{{ asset('assets/img/icons/return.svg') }}" alt="img" class="me-1">
                {{ __('message.back_to_list') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="delivery-route-map" data-start="{{ $delivery_route->start_location }}"
                data-end="{{ $delivery_route->end_location }}" data-waypoints="{{ $delivery_route->waypoints }}"
                data-name="{{ $delivery_route->name }}"
                data-deliveryman="{{ optional($delivery_route->deliveryman)->name }}">
            </div>
        </div>
    </div>
    <script>
        // Set Google Maps API key for use in JavaScript
        window.googleMapsApiKey = "{{ config('services.google.maps_api_key') }}";
    </script>
    <script src="{{ asset('js/enhanced-delivery-route-map.js') }}"></script>
</x-master-layout>