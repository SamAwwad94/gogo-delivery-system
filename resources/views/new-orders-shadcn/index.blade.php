<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/logo-loader.css') }}">
    <link rel="stylesheet" href="{{ asset('css/new-orders-shadcn.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fix-chevron.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/new-orders-C6G_3qQV.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/new-orders-app-Btl9b8Jn.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/EnhancedOrdersTable-D48L3EyA.css') }}">
    <!-- Include Naive UI styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/naive-ui/2.34.4/index.css" />



    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="flex justify-between items-center">
                            <div>
                                <!-- Header removed as requested -->
                            </div>
                            <!-- Action buttons removed as requested -->
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Vue App Container -->
                        <div id="new-orders-shadcn-app"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Load Vue App -->
    <script type="module" src="{{ asset('build/assets/new-orders-app-BDSCB8q5.js') }}"></script>
    <script type="module" src="{{ asset('build/assets/new-orders-bundle-YvbRubx2.js') }}"></script>
    <script type="module" src="{{ asset('build/assets/EnhancedOrdersTable-kmhNu8WW.js') }}"></script>


</x-master-layout>