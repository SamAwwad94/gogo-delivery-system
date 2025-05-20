/**
 * ShadCN Table Transformer
 * 
 * This utility transforms existing tables into ShadCN-styled tables with advanced filtering,
 * sorting, and other features inspired by the Dice UI Data Table.
 */

class ShadcnTableTransformer {
    constructor(options = {}) {
        this.options = {
            tableSelector: '.transform-to-shadcn',
            enableFiltering: true,
            enableSorting: true,
            enablePagination: true,
            enableColumnVisibility: true,
            ...options
        };
        
        this.init();
    }
    
    init() {
        // Find all tables that should be transformed
        const tables = document.querySelectorAll(this.options.tableSelector);
        
        tables.forEach(table => this.transformTable(table));
    }
    
    transformTable(table) {
        if (!table || !table.id) {
            console.error('Table must have an ID to be transformed');
            return;
        }
        
        // Skip if already transformed
        if (table.classList.contains('shadcn-table')) {
            return;
        }
        
        // Add ShadCN classes
        table.classList.add('shadcn-table');
        
        // Wrap table in container if not already wrapped
        let container = table.closest('.shadcn-table-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'shadcn-table-container';
            table.parentNode.insertBefore(container, table);
            container.appendChild(table);
        }
        
        // Create wrapper for the entire component
        let wrapper = table.closest('.shadcn-table-wrapper');
        if (!wrapper) {
            wrapper = document.createElement('div');
            wrapper.className = 'shadcn-table-wrapper';
            container.parentNode.insertBefore(wrapper, container);
            wrapper.appendChild(container);
        }
        
        // Create filter toolbar
        if (this.options.enableFiltering) {
            let toolbar = document.getElementById(`${table.id}-filter-toolbar`);
            if (!toolbar) {
                toolbar = document.createElement('div');
                toolbar.id = `${table.id}-filter-toolbar`;
                toolbar.className = 'shadcn-filter-toolbar';
                wrapper.insertBefore(toolbar, container);
            }
        }
        
        // Create pagination container
        if (this.options.enablePagination) {
            let pagination = document.getElementById(`${table.id}-pagination`);
            if (!pagination) {
                pagination = document.createElement('div');
                pagination.id = `${table.id}-pagination`;
                pagination.className = 'shadcn-pagination';
                wrapper.appendChild(pagination);
            }
        }
        
        // Process table headers
        this.processTableHeaders(table);
        
        // Process table rows
        this.processTableRows(table);
        
        // Initialize DataTable if not already initialized
        this.initializeDataTable(table);
    }
    
    processTableHeaders(table) {
        const headers = table.querySelectorAll('thead th');
        
        headers.forEach((header, index) => {
            // Add shadcn-table-head class
            header.classList.add('shadcn-table-head');
            
            // Set data attributes for column configuration
            if (!header.dataset.columnId) {
                header.dataset.columnId = `column-${index}`;
            }
            
            // Set sortable attribute if not explicitly set
            if (this.options.enableSorting && !header.hasAttribute('data-sortable')) {
                header.dataset.sortable = 'true';
            }
            
            // Set filterable attribute if not explicitly set
            if (this.options.enableFiltering && !header.hasAttribute('data-filterable')) {
                header.dataset.filterable = 'true';
            }
            
            // Determine column type if not set
            if (!header.dataset.type) {
                header.dataset.type = this.inferColumnType(table, index);
            }
        });
    }
    
    processTableRows(table) {
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            // Add shadcn-table-row class
            row.classList.add('shadcn-table-row');
            
            // Process cells
            const cells = row.querySelectorAll('td');
            cells.forEach(cell => {
                cell.classList.add('shadcn-table-cell');
            });
        });
    }
    
    inferColumnType(table, columnIndex) {
        // Try to infer column type based on content
        const cells = table.querySelectorAll(`tbody tr td:nth-child(${columnIndex + 1})`);
        
        if (cells.length === 0) return 'text';
        
        // Sample a few cells to determine type
        const sampleSize = Math.min(5, cells.length);
        const samples = [];
        
        for (let i = 0; i < sampleSize; i++) {
            samples.push(cells[i].textContent.trim());
        }
        
        // Check if all samples are numbers
        const allNumbers = samples.every(sample => !isNaN(parseFloat(sample)) && isFinite(sample));
        if (allNumbers) return 'number';
        
        // Check if all samples are dates
        const dateRegex = /^\d{1,4}[-/]\d{1,2}[-/]\d{1,4}$/;
        const allDates = samples.every(sample => dateRegex.test(sample) || !isNaN(Date.parse(sample)));
        if (allDates) return 'date';
        
        // Check if all samples are boolean-like
        const booleanValues = ['true', 'false', 'yes', 'no', '0', '1'];
        const allBooleans = samples.every(sample => 
            booleanValues.includes(sample.toLowerCase())
        );
        if (allBooleans) return 'boolean';
        
        // Default to text
        return 'text';
    }
    
    initializeDataTable(table) {
        // Check if DataTable is already initialized
        if ($.fn.DataTable.isDataTable(`#${table.id}`)) {
            return;
        }
        
        // Initialize DataTable with ShadCN styling
        const dataTable = $(`#${table.id}`).DataTable({
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
}

// Initialize on document ready
document.addEventListener('DOMContentLoaded', function() {
    new ShadcnTableTransformer();
});
