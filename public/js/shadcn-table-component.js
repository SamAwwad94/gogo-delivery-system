/**
 * ShadCN Table Component
 * A reusable table component for ShadCN tables
 */

class ShadcnTable {
    /**
     * Initialize the table component
     * @param {Object} options - Configuration options
     * @param {string} options.containerId - ID of the container element
     * @param {string} options.apiUrl - API URL for data
     * @param {Array} options.columns - Array of column configurations
     * @param {Object} options.filterConfig - Filter configuration
     * @param {Function} options.onRowClick - Callback function when a row is clicked
     */
    constructor(options) {
        this.containerId = options.containerId;
        this.apiUrl = options.apiUrl;
        this.columns = options.columns || [];
        this.filterConfig = options.filterConfig || null;
        this.onRowClick = options.onRowClick || null;
        this.container = document.getElementById(this.containerId);
        this.dataTable = null;
        this.filter = null;

        if (!this.container) {
            console.error(`Container with ID "${this.containerId}" not found.`);
            return;
        }

        this.init();
    }

    /**
     * Initialize the table component
     */
    init() {
        // Initialize filter if configured
        if (this.filterConfig) {
            this.initFilter();
        }

        // Initialize table
        this.initTable();
    }

    /**
     * Initialize the filter component
     */
    initFilter() {
        // Create filter container
        const filterContainer = document.createElement("div");
        filterContainer.id = `${this.containerId}-filters`;
        this.container.parentNode.insertBefore(filterContainer, this.container);

        // Initialize filter component
        this.filter = new ShadcnFilter({
            containerId: filterContainer.id,
            filters: this.filterConfig.filters,
            onApply: (filterValues) => {
                this.applyFilters(filterValues);
            },
            onClear: () => {
                this.resetFilters();
            },
        });
    }

    /**
     * Initialize the table
     */
    initTable() {
        // Prepare columns for DataTable
        const dtColumns = this.columns.map((column) => {
            return {
                data: column.data,
                name: column.name || column.data,
                title: column.title || column.data,
                render: column.render || null,
                className: column.className || "",
                width: column.width || null,
                orderable:
                    column.orderable !== undefined ? column.orderable : true,
                searchable:
                    column.searchable !== undefined ? column.searchable : true,
            };
        });

        // Initialize DataTable
        this.dataTable = $(this.container).DataTable({
            processing: true,
            serverSide: false, // Set to true if using server-side processing
            ajax: {
                url: this.apiUrl,
                dataSrc: "data",
            },
            columns: dtColumns,
            dom: '<"flex flex-col space-y-4"<"flex items-center justify-between"<"flex items-center space-x-2"l<"ml-2"f>><"flex items-center space-x-2"B>><"overflow-x-auto"rt><"flex items-center justify-between mt-4"<"text-sm text-muted-foreground"i><"flex"p>>>',
            buttons: [
                {
                    extend: "print",
                    text: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect width="12" height="8" x="6" y="14"></rect></svg> Print',
                    className:
                        "shadcn-button shadcn-button-outline text-sm h-9 px-4 py-2",
                },
                {
                    extend: "csv",
                    text: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline></svg> CSV',
                    className:
                        "shadcn-button shadcn-button-outline text-sm h-9 px-4 py-2",
                },
            ],
            drawCallback: function () {
                // Apply ShadCN styling to pagination
                $(".dataTables_paginate > .pagination").addClass(
                    "flex items-center space-x-1"
                );
                $(
                    ".dataTables_paginate > .pagination .paginate_button"
                ).addClass(
                    "relative inline-flex h-9 min-w-[36px] items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50"
                );
                $(
                    ".dataTables_paginate > .pagination .paginate_button.current"
                ).addClass(
                    "bg-primary text-primary-foreground shadow hover:bg-primary/90"
                );
                $(
                    ".dataTables_paginate > .pagination .paginate_button.disabled"
                ).addClass("text-muted-foreground");

                // Apply ShadCN styling to table
                $(this.nTable).addClass("w-full");
                $(this.nTable)
                    .find("thead th")
                    .addClass(
                        "h-10 px-2 text-left align-middle font-medium text-muted-foreground"
                    );
                $(this.nTable).find("tbody td").addClass("p-2 align-middle");
                $(this.nTable)
                    .find("tbody tr")
                    .addClass("border-b transition-colors hover:bg-muted/50");

                // Apply ShadCN styling to search and length inputs
                $(".dataTables_filter input").addClass(
                    "flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                );
                $(".dataTables_length select").addClass(
                    "flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                );

                // Apply ShadCN styling to info text
                $(".dataTables_info").addClass("text-sm text-muted-foreground");

                // Remove default styling from buttons
                $(".dt-buttons button").removeClass("btn-secondary");
            },
            language: {
                search: "",
                searchPlaceholder: "Search...",
                lengthMenu: "_MENU_ per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>',
                    last: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline></svg>',
                    next: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                    previous:
                        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                },
            },
            initComplete: function () {
                // Apply ShadCN styling to buttons
                $(".dt-buttons button").removeClass("btn-secondary");

                // Add search icon to search input
                $(".dataTables_filter").addClass("relative");
                $(".dataTables_filter input").addClass("pl-8");
                $(".dataTables_filter").prepend(
                    '<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg></div>'
                );

                // Add dropdown icon to length select
                $(".dataTables_length").addClass("relative");
                $(".dataTables_length select").addClass("appearance-none pr-8");
                $(".dataTables_length").append(
                    '<div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><path d="m6 9 6 6 6-6"></path></svg></div>'
                );
            },
        });

        // Add row click event if configured
        if (this.onRowClick) {
            $(this.container).on("click", "tbody tr", (e) => {
                const data = this.dataTable.row(e.currentTarget).data();
                this.onRowClick(data, e);
            });
        }
    }

    /**
     * Apply filters to the table
     * @param {Object} filterValues - Filter values
     */
    applyFilters(filterValues) {
        // Build query string
        let queryParams = [];

        Object.keys(filterValues).forEach((key) => {
            if (filterValues[key]) {
                queryParams.push(
                    `${key}=${encodeURIComponent(filterValues[key])}`
                );
            }
        });

        // Reload the table with filters
        const queryString =
            queryParams.length > 0 ? `?${queryParams.join("&")}` : "";
        this.dataTable.ajax.url(`${this.apiUrl}${queryString}`).load();
    }

    /**
     * Reset filters
     */
    resetFilters() {
        // Reload the table without filters
        this.dataTable.ajax.url(this.apiUrl).load();
    }

    /**
     * Refresh the table
     */
    refresh() {
        this.dataTable.ajax.reload();
    }

    /**
     * Get the DataTable instance
     * @returns {Object} - DataTable instance
     */
    getDataTable() {
        return this.dataTable;
    }
}

// Make available globally
window.ShadcnTable = ShadcnTable;
