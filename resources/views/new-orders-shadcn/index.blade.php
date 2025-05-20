<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/logo-loader.css') }}">
    <link rel="stylesheet" href="{{ asset('css/new-orders-shadcn.css') }}">

    <div class="page-header">
        <div class="page-title">
            <h4>{{ $pageTitle }}</h4>
            <h6>Manage your orders with ShadCN components</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- This is a completely new and blank page -->
            <!-- No content from other order pages -->

            <div class="p-6 text-center">
                <h2 class="text-2xl font-bold mb-4">New Orders ShadCN</h2>
                <p class="text-gray-600">This is a fresh start for your new orders page using ShadCN components.</p>
                <p class="text-gray-600 mt-2">You can build your custom implementation here without any legacy code.</p>
            </div>
        </div>
    </div>
</x-master-layout>