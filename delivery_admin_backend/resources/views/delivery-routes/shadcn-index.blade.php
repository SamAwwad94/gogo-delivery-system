<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.delivery_routes') }}</h4>
            <h6>{{ __('message.manage_delivery_routes') }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('delivery-routes.create') }}" class="btn btn-added">
                <img src="{{ asset('assets/img/icons/plus.svg') }}" alt="img" class="me-1">
                {{ __('message.add_delivery_route') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="delivery-routes-table"></div>
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
    <script src="{{ asset('js/enhanced-delivery-routes-table.js') }}"></script>
</x-master-layout><link rel="stylesheet" href="{{ asset(css/logo-animation.css) }}">

    <!-- Custom Logo Loading Overlay -->
    <div id="logo-loading-overlay" class="logo-loading-overlay hidden">
        <img src="{{ asset('images/logos/site_logo_1746885404.png') }}" class="animated-logo" alt="GoGo Delivery Logo">
    </div>

    <script>
        // Function to show the loading overlay
        function showLoading() {
            document.getElementById('logo-loading-overlay').classList.remove('hidden');
        }

        // Function to hide the loading overlay
        function hideLoading() {
            document.getElementById('logo-loading-overlay').classList.add('hidden');
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function () {
            // Add loading overlay to pagination links
            document.querySelectorAll('.pagination a').forEach(function (link) {
                link.addEventListener('click', function () {
                    showLoading();
                });
            });
        });
    </script>
