<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    
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
                                <a href="{{ route('dashboard.filter.data', $params) }}" class="mr-2 mt-0 mb-1 btn btn-sm btn-success text-dark mt-1 pt-1 pb-1 loadRemoteModel">
                                    <i class="fas fa-filter" style="font-size:12px"></i>  {{ __('message.filter')}}
                                </a>
                                <a href="{{ route('home') }}" class="mr-1 mt-0 mb-1 btn btn-sm btn-info text-dark mt-1 pt-1 pb-1">
                                    <i class="ri-repeat-line" style="font-size:12px"></i>  {{ __('message.reset_filter')}}
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
                                                <h5 class="font-weight-700">{{ $data['dashboard']['total_order_today'] }}</h5>
                                            </div>
                                            <div class="mm-cart-image text-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-icon" width="50" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Other stat cards here -->
                        </div>
                        
                        <!-- ShadCN-styled table with enhanced filters -->
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="font-weight-bold">{{ __('message.city_wise_order') }}</h5>
                            </div>
                            
                            <!-- The filter toolbar will be inserted here by ShadcnTableFilters -->
                            
                            <div class="shadcn-table-container">
                                <table class="shadcn-table" id="city-data-table">
                                    <thead>
                                        <tr>
                                            <th scope='col' class="shadcn-table-head">{{ __('message.city_name') }}</th>
                                            <th scope='col' class="shadcn-table-head">{{ __('message.total_number') }}</th>
                                            <th scope='col' class="shadcn-table-head">{{ __('message.parcel_in_progress') }}</th>
                                            <th scope='col' class="shadcn-table-head">{{ __('message.delivered_package') }}</th>
                                            <th scope='col' class="shadcn-table-head">{{ __('message.cancelled_package') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($cityData > 0)
                                            @foreach ($cityData as $cityList)
                                                <tr class="shadcn-table-row">
                                                    <td class="shadcn-table-cell font-medium">{{ $cityList['city'] }}</td>
                                                    <td class="shadcn-table-cell">{{ $cityList['count'] }}</td>
                                                    <td class="shadcn-table-cell">
                                                        <div class="flex items-center">
                                                            <span class="status-pill bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                                {{ $cityList['in_progress'] }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="shadcn-table-cell">
                                                        <div class="flex items-center">
                                                            <span class="status-pill bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                                {{ $cityList['delivered'] }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="shadcn-table-cell">
                                                        <div class="flex items-center">
                                                            <span class="status-pill bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                                {{ $cityList['cancelled'] }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="shadcn-table-cell text-center">{{ __('message.no_record_found') }}</td>
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
        <!-- Include ShadCN Table Header JS -->
        <script src="{{ asset('js/shadcn-table-header.js') }}"></script>
        <script src="{{ asset('js/shadcn-table-filters.js') }}"></script>
        
        <script>
            $(document).ready(function() {
                // Initialize DataTable with ShadCN styling
                const dataTable = $('#city-data-table').DataTable({
                    "dom": '<"flex flex-col space-y-4"<"flex items-center justify-between"<"flex items-center space-x-2"l<"ml-2"f>><"flex items-center space-x-2"B>><"overflow-x-auto"rt><"flex items-center justify-between mt-4"<"text-sm text-muted-foreground"i><"flex"p>>>',
                    "order": [[ 1, "desc" ]],
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
                        // Initialize ShadCN Table Filters
                        new ShadcnTableFilters('city-data-table', {
                            enableGlobalFilter: true,
                            enableColumnFilters: true,
                            enableFacetedFilters: true,
                            facetedFilters: [
                                {
                                    column: 'Status',
                                    title: 'Status',
                                    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
                                    options: [
                                        { label: 'In Progress', value: 'in_progress' },
                                        { label: 'Delivered', value: 'delivered' },
                                        { label: 'Cancelled', value: 'cancelled' }
                                    ]
                                }
                            ]
                        });
                    }
                });
            });
        </script>
    @endsection
</x-master-layout>
