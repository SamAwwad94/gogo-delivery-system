<x-master-layout :assets="$assets ?? []">
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shadcn-filter.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shadcn-responsive.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.order_list') }}</h4>
            <h6>{{ __('message.manage_your_orders') }}</h6>
        </div>
        <div class="page-btn">
            {!! $button !!}
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- View Toggle -->
            @include('orders.partials._view_toggle')

            <!-- Filter Pills -->
            @include('orders.partials._filters')

            <!-- Orders Table -->
            @include('orders.partials._table')
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <!-- Pusher for Real-time Updates -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="{{ asset('js/laravel-echo.js') }}"></script>
    <script>
        // Create a fallback Echo object if real-time updates are not available
        window.Echo = window.Echo || {
            channel: function () {
                return {
                    listen: function () {
                        return this;
                    }
                };
            }
        };

        // Initialize Pusher only if it's available and we have API keys
        if (typeof Pusher !== 'undefined' && '{{ env('PUSHER_APP_KEY') }}') {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: '{{ env('PUSHER_APP_KEY') }}',
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                forceTLS: true
            });
        }
    </script>

    <!-- Enhanced Orders Table JavaScript -->
    <script src="{{ asset('js/enhanced-orders-table-new.js') }}"></script>
    <script src="{{ asset('js/orders-enhancements.js') }}"></script>
    <script src="{{ asset('js/orders-realtime.js') }}"></script>
    <script src="{{ asset('js/orders-dashboard.js') }}"></script>
    <script src="{{ asset('js/orders-performance.js') }}"></script>
</x-master-layout>