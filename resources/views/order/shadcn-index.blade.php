<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shadcn-filter.css') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <!-- Include jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
        // Set Google Maps API key for use in JavaScript
        window.googleMapsApiKey = "{{ config('services.google.maps_api_key') }}";

        // Set CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <!-- Include ShadCN components -->
    <script src="{{ asset('js/shadcn-filter-component.js') }}"></script>
    <script src="{{ asset('js/shadcn-table-component.js') }}"></script>
    <script src="{{ asset('js/shadcn-filter-configs.js') }}"></script>
    <script src="{{ asset('js/enhanced-orders-table.js') }}"></script>
</x-master-layout>