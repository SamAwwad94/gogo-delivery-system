/**
 * ShadCN-style Table Filters for jQuery DataTables
 * Inspired by the ShadCN Table GitHub implementation
 */

class ShadcnTableFilters {
    constructor(tableId, options = {}) {
        this.tableId = tableId;
        this.table = document.getElementById(tableId);
        this.dataTable = null;
        this.options = {
            enableGlobalFilter: true,
            enableColumnFilters: true,
            enableFacetedFilters: true,
            facetedFilters: [],
            ...options,
        };

        this.filterState = {
            globalFilter: "",
            columnFilters: {},
            facetedFilters: {},
        };

        this.init();
    }

    init() {
        if (!this.table) {
            console.error(`Table with ID "${this.tableId}" not found.`);
            return;
        }

        // Initialize DataTable if it exists
        if ($.fn.DataTable.isDataTable(`#${this.tableId}`)) {
            this.dataTable = $(`#${this.tableId}`).DataTable();
        } else {
            console.error(
                `DataTable with ID "${this.tableId}" not initialized.`
            );
            return;
        }

        this.createFilterToolbar();

        if (this.options.enableGlobalFilter) {
            this.setupGlobalFilter();
        }

        if (this.options.enableColumnFilters) {
            this.setupColumnFilters();
        }

        if (
            this.options.enableFacetedFilters &&
            this.options.facetedFilters.length > 0
        ) {
            this.setupFacetedFilters();
        }
    }

    createFilterToolbar() {
        const tableContainer = this.table.closest(".shadcn-table-container");
        if (!tableContainer) return;

        // Create filter toolbar
        const toolbar = document.createElement("div");
        toolbar.className = "flex flex-wrap items-center gap-2 py-4";
        toolbar.id = `${this.tableId}-filter-toolbar`;

        // Insert toolbar before table
        tableContainer.insertBefore(toolbar, this.table);
    }

    setupGlobalFilter() {
        const toolbar = document.getElementById(
            `${this.tableId}-filter-toolbar`
        );
        if (!toolbar) return;

        // Create global filter container
        const globalFilterContainer = document.createElement("div");
        globalFilterContainer.className = "flex-1 relative";

        // Create global filter input
        const globalFilterInput = document.createElement("input");
        globalFilterInput.type = "text";
        globalFilterInput.className = "shadcn-input h-9 w-full pl-9";
        globalFilterInput.placeholder = "Search all columns...";

        // Add search icon
        const searchIcon = document.createElement("div");
        searchIcon.className =
            "absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none";
        searchIcon.innerHTML =
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>';

        globalFilterContainer.appendChild(searchIcon);
        globalFilterContainer.appendChild(globalFilterInput);
        toolbar.appendChild(globalFilterContainer);

        // Add event listener for global filtering
        globalFilterInput.addEventListener("input", (e) => {
            this.filterState.globalFilter = e.target.value;
            this.dataTable.search(e.target.value).draw();
        });
    }

    setupColumnFilters() {
        const toolbar = document.getElementById(
            `${this.tableId}-filter-toolbar`
        );
        if (!toolbar) return;

        // Create column filters button
        const columnFiltersButton = document.createElement("button");
        columnFiltersButton.className =
            "shadcn-button shadcn-button-outline h-9 px-4 flex items-center gap-2";
        columnFiltersButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
            </svg>
            <span>Filter</span>
        `;

        // Create column filters dropdown
        const columnFiltersDropdown = document.createElement("div");
        columnFiltersDropdown.className =
            "absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10 p-4 hidden";
        columnFiltersDropdown.id = `${this.tableId}-column-filters-dropdown`;

        // Add column filter options
        const headerCells = this.table.querySelectorAll(
            "thead tr:first-child th"
        );
        headerCells.forEach((cell, index) => {
            const columnName = cell.textContent.trim();
            if (!columnName || cell.classList.contains("no-filter")) return;

            const filterGroup = document.createElement("div");
            filterGroup.className = "mb-4";

            const filterLabel = document.createElement("label");
            filterLabel.className = "block text-sm font-medium mb-2";
            filterLabel.textContent = columnName;

            const filterInput = document.createElement("input");
            filterInput.type = "text";
            filterInput.className = "shadcn-input h-9 w-full";
            filterInput.placeholder = `Filter ${columnName}...`;

            // Add event listener for column filtering
            filterInput.addEventListener("input", (e) => {
                this.filterState.columnFilters[index] = e.target.value;
                this.dataTable.column(index).search(e.target.value).draw();
            });

            filterGroup.appendChild(filterLabel);
            filterGroup.appendChild(filterInput);
            columnFiltersDropdown.appendChild(filterGroup);
        });

        // Add clear filters button
        const clearFiltersButton = document.createElement("button");
        clearFiltersButton.className =
            "shadcn-button shadcn-button-outline w-full mt-2";
        clearFiltersButton.textContent = "Clear Filters";
        clearFiltersButton.addEventListener("click", () => {
            // Clear all inputs in the dropdown
            columnFiltersDropdown.querySelectorAll("input").forEach((input) => {
                input.value = "";
            });

            // Reset filter state
            this.filterState.columnFilters = {};

            // Clear all column filters
            this.dataTable.columns().search("").draw();
        });

        columnFiltersDropdown.appendChild(clearFiltersButton);

        // Create container for button and dropdown
        const columnFiltersContainer = document.createElement("div");
        columnFiltersContainer.className = "relative";
        columnFiltersContainer.appendChild(columnFiltersButton);
        columnFiltersContainer.appendChild(columnFiltersDropdown);

        toolbar.appendChild(columnFiltersContainer);

        // Toggle dropdown on button click
        columnFiltersButton.addEventListener("click", () => {
            columnFiltersDropdown.classList.toggle("hidden");
        });

        // Close dropdown when clicking outside
        document.addEventListener("click", (e) => {
            if (!columnFiltersContainer.contains(e.target)) {
                columnFiltersDropdown.classList.add("hidden");
            }
        });
    }

    setupFacetedFilters() {
        const toolbar = document.getElementById(
            `${this.tableId}-filter-toolbar`
        );
        if (!toolbar) return;

        this.options.facetedFilters.forEach((filter) => {
            // Create faceted filter container
            const facetedFilterContainer = document.createElement("div");
            facetedFilterContainer.className = "relative";

            // Create faceted filter button
            const facetedFilterButton = document.createElement("button");
            facetedFilterButton.className =
                "shadcn-button shadcn-button-outline h-9 px-4 flex items-center gap-2";
            facetedFilterButton.innerHTML = `
                ${filter.icon || ""}
                <span>${filter.title}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            `;

            // Create faceted filter dropdown
            const facetedFilterDropdown = document.createElement("div");
            facetedFilterDropdown.className =
                "absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10 p-2 hidden";

            // Add faceted filter options
            filter.options.forEach((option) => {
                const optionElement = document.createElement("div");
                optionElement.className =
                    "flex items-center px-3 py-2 text-sm rounded-md hover:bg-gray-100 cursor-pointer";

                const checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.className = "mr-2 rounded border-gray-300";

                const label = document.createElement("span");
                label.textContent = option.label;

                optionElement.appendChild(checkbox);
                optionElement.appendChild(label);

                // Add event listener for faceted filtering
                optionElement.addEventListener("click", () => {
                    checkbox.checked = !checkbox.checked;

                    // Update filter state
                    if (!this.filterState.facetedFilters[filter.column]) {
                        this.filterState.facetedFilters[filter.column] = [];
                    }

                    if (checkbox.checked) {
                        this.filterState.facetedFilters[filter.column].push(
                            option.value
                        );
                    } else {
                        this.filterState.facetedFilters[filter.column] =
                            this.filterState.facetedFilters[
                                filter.column
                            ].filter((value) => value !== option.value);
                    }

                    // Apply faceted filter
                    this.applyFacetedFilter(filter.column);
                });

                facetedFilterDropdown.appendChild(optionElement);
            });

            facetedFilterContainer.appendChild(facetedFilterButton);
            facetedFilterContainer.appendChild(facetedFilterDropdown);

            toolbar.appendChild(facetedFilterContainer);

            // Toggle dropdown on button click
            facetedFilterButton.addEventListener("click", () => {
                facetedFilterDropdown.classList.toggle("hidden");
            });

            // Close dropdown when clicking outside
            document.addEventListener("click", (e) => {
                if (!facetedFilterContainer.contains(e.target)) {
                    facetedFilterDropdown.classList.add("hidden");
                }
            });
        });
    }

    applyFacetedFilter(column) {
        const columnIndex = this.getColumnIndexByName(column);
        if (columnIndex === -1) return;

        const values = this.filterState.facetedFilters[column] || [];

        if (values.length === 0) {
            // Clear filter if no values selected
            this.dataTable.column(columnIndex).search("").draw();
            return;
        }

        // Create regex for OR condition
        const regex = values.map((value) => `^${value}$`).join("|");
        this.dataTable.column(columnIndex).search(regex, true, false).draw();
    }

    getColumnIndexByName(columnName) {
        const headerCells = this.table.querySelectorAll(
            "thead tr:first-child th"
        );
        for (let i = 0; i < headerCells.length; i++) {
            if (
                headerCells[i].textContent.trim().toLowerCase() ===
                columnName.toLowerCase()
            ) {
                return i;
            }
        }
        return -1;
    }
}

// Initialize on document ready
document.addEventListener("DOMContentLoaded", function () {
    // This will be initialized manually in the page script
});
