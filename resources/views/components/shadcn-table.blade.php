@props(['id', 'columns', 'dataUrl' => null, 'data' => null, 'enableFiltering' => true, 'enableSorting' => true, 'enablePagination' => true, 'enableColumnVisibility' => true])

<div class="shadcn-table-wrapper">
    @if($enableFiltering)
        <div id="{{ $id }}-filter-toolbar" class="shadcn-filter-toolbar">
            <!-- Filter toolbar will be inserted here by JavaScript -->
        </div>
    @endif

    <div class="shadcn-table-container">
        <table id="{{ $id }}" {{ $attributes->merge(['class' => 'shadcn-table']) }}>
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th scope="col" class="shadcn-table-head" data-column-id="{{ $column['id'] ?? $column['key'] }}"
                            @if(isset($column['sortable']) && $column['sortable']) data-sortable="true" @endif
                            @if(isset($column['filterable']) && $column['filterable']) data-filterable="true" @endif
                            @if(isset($column['type'])) data-type="{{ $column['type'] }}" @endif
                            @if(isset($column['options'])) data-options="{{ json_encode($column['options']) }}" @endif>
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if($enablePagination)
        <div id="{{ $id }}-pagination" class="shadcn-pagination">
            <!-- Pagination will be inserted here by JavaScript -->
        </div>
    @endif
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('js/shadcn-table-header.js') }}"></script>
        <script src="{{ asset('js/shadcn-table-filters.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize ShadCN tables
                document.querySelectorAll('.shadcn-table').forEach(table => {
                    if (!table.id) return;

                    // Check if DataTable is already initialized
                    if (!$.fn.DataTable.isDataTable(`#${table.id}`)) {
                        // Initialize DataTable with ShadCN styling
                        const dataTable = $(`#${table.id}`).DataTable({
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

                                // Get column definitions from table headers
                                const columns = [];
                                table.querySelectorAll('thead th').forEach((th, index) => {
                                    if (th.dataset.columnId) {
                                        columns.push({
                                            id: th.dataset.columnId,
                                            label: th.textContent.trim(),
                                            sortable: th.dataset.sortable === 'true',
                                            filterable: th.dataset.filterable === 'true',
                                            type: th.dataset.type || 'text',
                                            options: th.dataset.options ? JSON.parse(th.dataset.options) : null
                                        });
                                    }
                                });

                                // Initialize ShadCN Table Filters if filter toolbar exists
                                const filterToolbar = document.getElementById(`${table.id}-filter-toolbar`);
                                if (filterToolbar) {
                                    // Create faceted filters based on column definitions
                                    const facetedFilters = columns
                                        .filter(col => col.filterable && col.type === 'select' && col.options)
                                        .map(col => ({
                                            column: col.id,
                                            title: col.label,
                                            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
                                            options: col.options
                                        }));

                                    new ShadcnTableFilters(table.id, {
                                        enableGlobalFilter: true,
                                        enableColumnFilters: true,
                                        enableFacetedFilters: facetedFilters.length > 0,
                                        facetedFilters: facetedFilters
                                    });
                                }

                                // Initialize ShadCN Table Header
                                new ShadcnTableHeader(table.id, {
                                    enableSorting: true,
                                    enableFiltering: true,
                                    enableColumnVisibility: true
                                });
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
@endonce