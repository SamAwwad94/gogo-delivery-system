<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">

    <!-- Dashboard Visualizations Container -->
    <div id="dashboard-visualizations" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Orders by Status</h3>
            <div class="h-64">
                <canvas id="dashboard-status-chart"></canvas>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Orders Timeline</h3>
            <div class="h-64">
                <canvas id="dashboard-timeline-chart"></canvas>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Payment Status</h3>
            <div class="h-64">
                <canvas id="dashboard-payment-chart"></canvas>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 mb-3">
                <div class="d-flex align-items-center justify-content-between welcome-content">
                    <div class="navbar-breadcrumb">
                        <!-- <h4 class="mb-0 font-weight-700">Welcome To Dashboard</h4> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="col-md-12">
                        <div class="row">
                            <h5 class="font-weight-bold ml-3 mt-3">{{ __('message.today_order_counts') }}</h5>
                            <div class="ml-auto d-flex justify-content-end mr-3 mt-3">
                                <a href="{{ route('dashboard.filter.data', $params) }}"
                                    class="mr-2 mt-0 mb-1 btn btn-sm btn-success text-dark mt-1 pt-1 pb-1 loadRemoteModel">
                                    <i class="fas fa-filter" style="font-size:12px"></i> {{ __('message.filter')}}
                                </a>
                                <a href="{{ route('home') }}"
                                    class="mr-1 mt-0 mb-1 btn btn-sm btn-info text-dark mt-1 pt-1 pb-1">
                                    <i class="ri-repeat-line" style="font-size:12px"></i>
                                    {{ __('message.reset_filter')}}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-2 col-md-4">
                                <div class="card card-block card-stretch card-height">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="mm-cart-text">
                                                <p class="mb-0">{{ __('message.total_order') }}</p>
                                                <br>
                                                <h5 class="font-weight-700">
                                                    {{ $data['dashboard']['total_order_today'] }}</h5>
                                            </div>
                                            <div class="mm-cart-image text-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-icon" width="50"
                                                    height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Other stat cards here -->
                        </div>

                        <!-- ShadCN-styled table -->
                        <div class="mt-4">
                            <h5 class="font-weight-bold mb-3">{{ __('message.city_wise_order') }}</h5>
                            <div class="shadcn-table-container">
                                <table class="shadcn-table" id="city-data-table">
                                    <thead>
                                        <tr>
                                            <th scope='col'>{{ __('message.city_name') }}</th>
                                            <th scope='col'>{{ __('message.total_number') }}</th>
                                            <th scope='col'>{{ __('message.parcel_in_progress') }}</th>
                                            <th scope='col'>{{ __('message.delivered_package') }}</th>
                                            <th scope='col'>{{ __('message.cancelled_package') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($cityData > 0)
                                            @foreach ($cityData as $cityList)
                                                <tr>
                                                    <td>{{ $cityList['city'] }}</td>
                                                    <td>{{ $cityList['count'] }}</td>
                                                    <td>
                                                        <span
                                                            class="status-pill bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                            {{ $cityList['in_progress'] }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="status-pill bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                            {{ $cityList['delivered'] }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="status-pill bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                            {{ $cityList['cancelled'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center">{{ __('message.no_record_found') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="wave-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page end  -->
    </div>

    @section('bottom_script')
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

        <!-- Dashboard Visualizations -->
        <script src="{{ asset('js/orders-dashboard.js') }}"></script>

        <script>
            $(document).ready(function () {
                $('#city-data-table').DataTable({
                    "dom": '<"flex flex-col space-y-4"<"flex items-center justify-between"<"flex items-center space-x-2"l<"ml-2"f>><"flex items-center space-x-2"B>><"overflow-x-auto"rt><"flex items-center justify-between mt-4"<"text-sm text-muted-foreground"i><"flex"p>>>',
                    "order": [[1, "desc"]],
                    "buttons": [
                        {
                            "extend": 'print',
                            "text": '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect width="12" height="8" x="6" y="14"></rect></svg> Print',
                            "className": 'shadcn-button shadcn-button-outline text-sm h-9 px-4 py-2',
                        },
                        {
                            "extend": 'csv',
                            "text": '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline></svg> CSV',
                            "className": 'shadcn-button shadcn-button-outline text-sm h-9 px-4 py-2',
                        }
                    ],
                    "drawCallback": function () {
                        // Apply ShadCN styling to pagination
                        $('.dataTables_paginate > .pagination').addClass('flex items-center space-x-1');
                        $('.dataTables_paginate > .pagination .paginate_button').addClass('relative inline-flex h-9 min-w-[36px] items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50');
                        $('.dataTables_paginate > .pagination .paginate_button.current').addClass('bg-primary text-primary-foreground shadow hover:bg-primary/90');
                        $('.dataTables_paginate > .pagination .paginate_button.disabled').addClass('text-muted-foreground');

                        // Apply ShadCN styling to search and length inputs
                        $('.dataTables_filter input').addClass('flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50');
                        $('.dataTables_length select').addClass('flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50');

                        // Apply ShadCN styling to info text
                        $('.dataTables_info').addClass('text-sm text-muted-foreground');
                    },
                    "initComplete": function () {
                        // Add search icon to search input
                        $('.dataTables_filter').addClass('relative');
                        $('.dataTables_filter input').addClass('pl-8');
                        $('.dataTables_filter').prepend('<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg></div>');

                        // Add dropdown icon to length select
                        $('.dataTables_length').addClass('relative');
                        $('.dataTables_length select').addClass('appearance-none pr-8');
                        $('.dataTables_length').append('<div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><path d="m6 9 6 6 6-6"></path></svg></div>');
                    }
                });
            });
        </script>
    @endsection
</x-master-layout>