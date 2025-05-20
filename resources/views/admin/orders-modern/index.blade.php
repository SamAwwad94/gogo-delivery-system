<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/logo-animation.css') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.modern_orders') }}</h4>
            <h6>{{ __('message.manage_your_orders') }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('order.create') }}" class="btn btn-added">
                <img src="{{ asset('assets/img/icons/plus.svg') }}" alt="img" class="me-1">
                {{ __('message.add_new_order') }}
            </a>
            <a href="{{ route('admin.orders-modern.export') }}" class="btn btn-added ms-2">
                <img src="{{ asset('assets/img/icons/download.svg') }}" alt="img" class="me-1">
                {{ __('message.export_csv') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- No filters as requested -->
            <div class="mb-4">
                <button id="test-loading" class="btn btn-primary">Test Loading Animation</button>
            </div>

            <!-- Custom Logo Loading Overlay -->
            <div id="logo-loading-overlay" class="logo-loading-overlay hidden">
                <img src="{{ asset('images/logos/site_logo_1746885404.png') }}" class="animated-logo" width="110"
                    height="75" alt="GoGo Delivery Logo">
            </div>

            <!-- Orders Table -->
            <div class="shadcn-table-container">
                @include('admin.orders-modern.partials._table', ['orders' => $orders])
            </div>
        </div>

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                // Add click event to test button
                document.getElementById('test-loading').addEventListener('click', function () {
                    showLoading();

                    // Hide after 5 seconds
                    setTimeout(function () {
                        hideLoading();
                    }, 5000);
                });

                // Add loading overlay to pagination links
                document.querySelectorAll('.pagination a').forEach(function (link) {
                    link.addEventListener('click', function () {
                        showLoading();
                    });
                });
            });
        </script>
</x-master-layout>