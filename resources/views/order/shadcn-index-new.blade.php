<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.order_list') }}</h4>
            <h6>{{ __('message.manage_your_orders') }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('order.create') }}" class="btn btn-added">
                <img src="{{ asset('assets/img/icons/plus.svg') }}" alt="img" class="me-1">
                {{ __('message.add_new_order') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="orders-table"></div>
        </div>
    </div>
    <link rel="stylesheet" href="{{ asset('css/shadcn-filter.css') }}">
    <script>
        // Set Google Maps API key for use in JavaScript
        window.googleMapsApiKey = "{{ config('services.google.maps_api_key') }}";
    </script>
    <script src="{{ asset('js/shadcn-filter-component.js') }}"></script>
    <script src="{{ asset('js/shadcn-table-component.js') }}"></script>
    <script src="{{ asset('js/shadcn-filter-configs.js') }}"></script>
    <script src="{{ asset('js/enhanced-orders-table.js') }}"></script>
</x-master-layout>
