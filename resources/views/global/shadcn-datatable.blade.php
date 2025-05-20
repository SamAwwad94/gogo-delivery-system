<x-master-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="max-w-full mx-auto py-6">
            <div class="bg-card rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 flex justify-between items-center border-b border-border">
                    <h2 class="text-xl font-semibold text-card-foreground">{{ $pageTitle ?? ''}}</h2>
                    <div class="flex items-center space-x-2">
                        @if(isset($button))
                        {!! $button !!}
                        @endif 
                        @if(isset($helpbutton))
                        {!! $helpbutton !!}
                        @endif 
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        @if(isset($multi_checkbox_delete))
                           {!! $multi_checkbox_delete !!}
                        @endif
                    </div>
                    
                    <div class="flex flex-col space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="relative">
                                    <x-shadcn.input type="search" placeholder="Search..." class="pl-8" id="datatable-search-input" />
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                                    </div>
                                </div>
                                
                                <div class="relative">
                                    <select id="datatable-length-select" class="shadcn-input pr-8">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><path d="m6 9 6 6 6-6"></path></svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <x-shadcn.button variant="outline" size="sm" id="datatable-csv-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    CSV
                                </x-shadcn.button>
                                
                                <x-shadcn.button variant="outline" size="sm" id="datatable-print-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect width="12" height="8" x="6" y="14"></rect></svg>
                                    Print
                                </x-shadcn.button>
                            </div>
                        </div>
                        
                        <div class="rounded-md border border-border overflow-hidden">
                            {{ $dataTable->table(['class' => 'shadcn-table w-full'], false) }}
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
                
                // Custom search input
                $('#datatable-search-input').on('keyup', function() {
                    dataTable.search(this.value).draw();
                });
                
                // Custom length select
                $('#datatable-length-select').on('change', function() {
                    dataTable.page.len($(this).val()).draw();
                });
                
                // Custom CSV button
                $('#datatable-csv-button').on('click', function() {
                    $('.buttons-csv').click();
                });
                
                // Custom print button
                $('#datatable-print-button').on('click', function() {
                    $('.buttons-print').click();
                });
                
                // Apply ShadCN classes to table elements
                $('#dataTableBuilder_wrapper .dataTables_paginate .paginate_button').addClass('relative inline-flex items-center px-4 py-2 text-sm font-medium border border-border rounded-md hover:bg-muted');
                $('#dataTableBuilder_wrapper .dataTables_paginate .paginate_button.current').addClass('bg-primary text-primary-foreground border-primary');
                
                // Observe DOM changes to apply styles to dynamically added elements
                const observer = new MutationObserver(function(mutations) {
                    $('#dataTableBuilder_wrapper .dataTables_paginate .paginate_button').addClass('relative inline-flex items-center px-4 py-2 text-sm font-medium border border-border rounded-md hover:bg-muted');
                    $('#dataTableBuilder_wrapper .dataTables_paginate .paginate_button.current').addClass('bg-primary text-primary-foreground border-primary');
                });
                
                observer.observe(document.querySelector('#dataTableBuilder_wrapper'), {
                    childList: true,
                    subtree: true
                });
            });
        </script>
    @endsection
</x-master-layout>
