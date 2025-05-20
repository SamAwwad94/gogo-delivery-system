<x-master-layout :assets="$assets ?? []">
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title mb-0">{{ $pageTitle ?? ''}}</h4>
                        </div>

                        <div class="card-header-toolbar">                       
                            @if(isset($button))
                            {!! $button !!}
                            @endif 
                            @if(isset($helpbutton))
                            {!! $helpbutton !!}
                            @endif 
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-header-toolbar mb-3">
                            @if(isset($multi_checkbox_delete))
                               {!! $multi_checkbox_delete !!}
                            @endif
                        </div>
                        <div class="shadcn-table-container">
                            {{ $dataTable->table(['class' => 'shadcn-table w-100'], false) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @section('bottom_script')
        {{ $dataTable->scripts() }}
        <script>
            $(document).ready(function() {
                // Apply ShadCN styling to DataTable
                const dataTable = $('#dataTableBuilder').DataTable();
                
                // Add search icon to search input
                $('.dataTables_filter').addClass('relative');
                $('.dataTables_filter input').addClass('pl-8');
                $('.dataTables_filter').prepend('<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg></div>');
                
                // Add dropdown icon to length select
                $('.dataTables_length').addClass('relative');
                $('.dataTables_length select').addClass('appearance-none pr-8');
                $('.dataTables_length').append('<div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><path d="m6 9 6 6 6-6"></path></svg></div>');
                
                // Style the pagination
                $('.dataTables_paginate > .pagination').addClass('flex items-center space-x-1');
                
                // Style the buttons
                $('.dt-buttons button').addClass('shadcn-button shadcn-button-outline');
                
                // Style the table headers
                $('#dataTableBuilder thead th').addClass('h-10 px-2 text-left align-middle font-medium');
                
                // Style the table cells
                $('#dataTableBuilder tbody td').addClass('p-2 align-middle');
                
                // Style the table rows
                $('#dataTableBuilder tbody tr').addClass('transition-colors hover:bg-muted/50');
                
                // Style the info text
                $('.dataTables_info').addClass('text-sm text-muted-foreground');
                
                // Create a MutationObserver to apply styles to dynamically added elements
                const observer = new MutationObserver(function(mutations) {
                    // Style the pagination buttons
                    $('.dataTables_paginate .paginate_button').addClass('relative inline-flex items-center px-4 py-2 text-sm font-medium border border-border rounded-md hover:bg-muted');
                    $('.dataTables_paginate .paginate_button.current').addClass('bg-primary text-primary-foreground border-primary');
                    $('.dataTables_paginate .paginate_button.disabled').addClass('opacity-50 cursor-not-allowed');
                    
                    // Style status pills
                    $('.status-pill').each(function() {
                        $(this).addClass('inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium');
                    });
                    
                    // Style action buttons
                    $('.action-button').each(function() {
                        $(this).addClass('inline-flex items-center justify-center w-8 h-8 rounded-md hover:bg-accent hover:text-accent-foreground');
                    });
                });
                
                // Start observing the document with the configured parameters
                observer.observe(document.querySelector('#dataTableBuilder_wrapper'), {
                    childList: true,
                    subtree: true
                });
            });
        </script>
    @endsection
</x-master-layout>
