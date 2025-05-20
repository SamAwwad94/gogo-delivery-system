{{-- ShadCN City Wise Order Table Component --}}
<div class="mt-4">
    <div class="flex items-center justify-between mb-3">
        <h5 class="font-weight-bold">{{ __('message.city_wise_order') }}</h5>
    </div>
    
    <div id="city-data-table-filter-toolbar" class="shadcn-filter-toolbar mb-4">
        {{-- Filter toolbar will be inserted here by JavaScript --}}
    </div>
    
    <div class="shadcn-table-container">
        <table class="shadcn-table" id="city-data-table">
            <thead>
                <tr>
                    <th scope='col' class="shadcn-table-head" data-column-id="city" data-sortable="true" data-filterable="true" data-type="text">
                        {{ __('message.city_name') }}
                    </th>
                    <th scope='col' class="shadcn-table-head" data-column-id="count" data-sortable="true" data-filterable="true" data-type="number">
                        {{ __('message.total_number') }}
                    </th>
                    <th scope='col' class="shadcn-table-head" data-column-id="in_progress" data-sortable="true" data-filterable="true" data-type="number">
                        {{ __('message.parcel_in_progress') }}
                    </th>
                    <th scope='col' class="shadcn-table-head" data-column-id="delivered" data-sortable="true" data-filterable="true" data-type="number">
                        {{ __('message.delivered_package') }}
                    </th>
                    <th scope='col' class="shadcn-table-head" data-column-id="cancelled" data-sortable="true" data-filterable="true" data-type="number">
                        {{ __('message.cancelled_package') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @if($cityData > 0)
                    @foreach ($cityData as $cityList)
                        <tr class="shadcn-table-row">
                            <td class="shadcn-table-cell font-medium">{{ $cityList['city'] }}</td>
                            <td class="shadcn-table-cell">{{ $cityList['count'] }}</td>
                            <td class="shadcn-table-cell">
                                <span class="status-pill bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ $cityList['in_progress'] }}
                                </span>
                            </td>
                            <td class="shadcn-table-cell">
                                <span class="status-pill bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ $cityList['delivered'] }}
                                </span>
                            </td>
                            <td class="shadcn-table-cell">
                                <span class="status-pill bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ $cityList['cancelled'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="shadcn-table-row">
                        <td colspan="5" class="shadcn-table-cell text-center">{{ __('message.no_record_found') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <div id="city-data-table-pagination" class="shadcn-pagination mt-4">
        {{-- Pagination will be inserted here by JavaScript --}}
    </div>
</div>

@push('scripts')
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
                    "className": 'shadcn-button shadcn-button-outline',
                    "text": '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg> Print'
                },
                {
                    "extend": 'csv',
                    "className": 'shadcn-button shadcn-button-outline',
                    "text": '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg> Export CSV'
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
                
                // Style status pills
                $('.status-pill').each(function() {
                    $(this).addClass('inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium');
                });
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
                
                // Initialize ShadCN Table Filters
                new ShadcnTableFilters('city-data-table', {
                    enableGlobalFilter: true,
                    enableColumnFilters: true,
                    enableFacetedFilters: true,
                    facetedFilters: [
                        {
                            column: 'city',
                            type: 'select'
                        }
                    ]
                });
                
                // Initialize ShadCN Table Header
                new ShadcnTableHeader('city-data-table', {
                    enableSorting: true,
                    enableFiltering: true,
                    enableColumnVisibility: true
                });
            }
        });
    });
</script>
@endpush
