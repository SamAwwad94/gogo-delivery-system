<?php

namespace App\Traits;

use Yajra\DataTables\Services\DataTable;

trait ShadcnDataTableTrait {

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->ajax('')
            ->parameters($this->getShadcnBuilderParameters());
    }

    public function getShadcnBuilderParameters(): array
    {
        return [
            'lengthMenu'   => [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            'dom'          => '<"flex flex-col space-y-4"<"flex items-center justify-between"<"flex items-center space-x-2"l<"ml-2"f>><"flex items-center space-x-2"B>><"overflow-x-auto"rt><"flex items-center justify-between mt-4"<"text-sm text-muted-foreground"i><"flex"p>>>',
            'buttons' => [
                [
                    'extend' => 'print',
                    'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect width="12" height="8" x="6" y="14"></rect></svg> Print',
                    'className' => 'shadcn-button shadcn-button-outline text-sm h-9 px-4 py-2',
                ],
                [
                    'extend' => 'csv',
                    'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline></svg> CSV',
                    'className' => 'shadcn-button shadcn-button-outline text-sm h-9 px-4 py-2',
                ]
            ],
            'drawCallback' => "function () {
                // Apply ShadCN styling to pagination
                $('.dataTables_paginate > .pagination').addClass('flex items-center space-x-1');
                $('.dataTables_paginate > .pagination .paginate_button').addClass('relative inline-flex h-9 min-w-[36px] items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50');
                $('.dataTables_paginate > .pagination .paginate_button.current').addClass('bg-primary text-primary-foreground shadow hover:bg-primary/90');
                $('.dataTables_paginate > .pagination .paginate_button.disabled').addClass('text-muted-foreground');
                
                // Apply ShadCN styling to table
                $('#dataTableBuilder').addClass('w-full');
                $('#dataTableBuilder thead th').addClass('h-10 px-2 text-left align-middle font-medium text-muted-foreground');
                $('#dataTableBuilder tbody td').addClass('p-2 align-middle');
                $('#dataTableBuilder tbody tr').addClass('border-b transition-colors hover:bg-muted/50');
                
                // Apply ShadCN styling to search and length inputs
                $('.dataTables_filter input').addClass('flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50');
                $('.dataTables_length select').addClass('flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50');
                
                // Apply ShadCN styling to info text
                $('.dataTables_info').addClass('text-sm text-muted-foreground');
                
                // Remove default styling from buttons
                $('.dt-buttons button').removeClass('btn-secondary');
            }",
            'language' => [
                'search' => '',
                'searchPlaceholder' => 'Search...',
                'lengthMenu' => '_MENU_ per page',
                'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
                'infoEmpty' => 'Showing 0 to 0 of 0 entries',
                'infoFiltered' => '(filtered from _MAX_ total entries)',
                'paginate' => [
                    'first' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>',
                    'last' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline></svg>',
                    'next' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                    'previous' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>'
                ]
            ],
            'initComplete' => "function () {
                // Apply ShadCN styling to buttons
                $('#dataTableBuilder_wrapper .dt-buttons button').removeClass('btn-secondary');
                
                // Add search icon to search input
                $('.dataTables_filter').addClass('relative');
                $('.dataTables_filter input').addClass('pl-8');
                $('.dataTables_filter').prepend('<div class=\"absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"text-muted-foreground\"><circle cx=\"11\" cy=\"11\" r=\"8\"></circle><path d=\"m21 21-4.3-4.3\"></path></svg></div>');
                
                // Add dropdown icon to length select
                $('.dataTables_length').addClass('relative');
                $('.dataTables_length select').addClass('appearance-none pr-8');
                $('.dataTables_length').append('<div class=\"absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"text-muted-foreground\"><path d=\"m6 9 6 6 6-6\"></path></svg></div>');
            }",
            'createdRow' => "function (row, data, dataIndex) {
                if (data.deleted_at) {
                    if(data.deleted_at != null){
                        $(row).addClass('bg-destructive/10');
                    }
                }
            }"
        ];
    }
}
