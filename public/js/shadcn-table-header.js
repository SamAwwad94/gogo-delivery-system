/**
 * ShadCN-style Table Header with Sorting and Filtering
 */

class ShadcnTableHeader {
    constructor(tableId, options = {}) {
        this.tableId = tableId;
        this.table = document.getElementById(tableId);
        this.dataTable = null;
        this.options = {
            enableSorting: true,
            enableFiltering: true,
            enableColumnVisibility: true,
            ...options
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
        }
        
        this.setupTableHeader();
        this.setupFilterDropdowns();
        this.setupSortingButtons();
        
        if (this.options.enableColumnVisibility) {
            this.setupColumnVisibility();
        }
    }
    
    setupTableHeader() {
        const headerRow = this.table.querySelector('thead tr');
        if (!headerRow) return;
        
        // Add ShadCN styling to header cells
        const headerCells = headerRow.querySelectorAll('th');
        headerCells.forEach(cell => {
            // Remove existing classes and styles
            cell.className = 'shadcn-table-head';
            cell.removeAttribute('style');
            
            // Create the header content wrapper
            const headerContent = document.createElement('div');
            headerContent.className = 'flex items-center gap-1.5';
            
            // Get the original text
            const originalText = cell.textContent.trim();
            
            // Clear the cell
            cell.innerHTML = '';
            
            // Create the text span
            const textSpan = document.createElement('span');
            textSpan.textContent = originalText;
            headerContent.appendChild(textSpan);
            
            // Add sort indicator if sorting is enabled
            if (this.options.enableSorting && !cell.classList.contains('no-sort')) {
                const sortIcon = document.createElement('span');
                sortIcon.className = 'sort-icon ml-1';
                sortIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>';
                headerContent.appendChild(sortIcon);
            }
            
            cell.appendChild(headerContent);
            
            // Make the header cell interactive
            cell.style.cursor = 'pointer';
            cell.addEventListener('mouseover', () => {
                cell.classList.add('hover:bg-accent');
            });
            cell.addEventListener('mouseout', () => {
                cell.classList.remove('hover:bg-accent');
            });
        });
    }
    
    setupFilterDropdowns() {
        if (!this.options.enableFiltering) return;
        
        const headerRow = this.table.querySelector('thead tr');
        if (!headerRow) return;
        
        // Create filter row
        const filterRow = document.createElement('tr');
        filterRow.className = 'filter-row';
        
        // Add filter cells
        const headerCells = headerRow.querySelectorAll('th');
        headerCells.forEach((cell, index) => {
            const filterCell = document.createElement('th');
            filterCell.className = 'shadcn-table-head p-1';
            
            // Skip checkbox column or action column
            if (cell.classList.contains('no-filter') || 
                cell.textContent.trim() === '' || 
                cell.classList.contains('select-checkbox')) {
                filterRow.appendChild(filterCell);
                return;
            }
            
            // Create filter input
            const filterInput = document.createElement('input');
            filterInput.type = 'text';
            filterInput.className = 'shadcn-input h-7 text-xs w-full';
            filterInput.placeholder = `Filter ${cell.textContent.trim()}...`;
            
            // Add event listener for filtering
            filterInput.addEventListener('input', (e) => {
                if (this.dataTable) {
                    this.dataTable
                        .column(index)
                        .search(e.target.value)
                        .draw();
                }
            });
            
            filterCell.appendChild(filterInput);
            filterRow.appendChild(filterCell);
        });
        
        // Add filter row to table header
        const thead = this.table.querySelector('thead');
        thead.appendChild(filterRow);
    }
    
    setupSortingButtons() {
        if (!this.options.enableSorting || !this.dataTable) return;
        
        const headerRow = this.table.querySelector('thead tr');
        if (!headerRow) return;
        
        const headerCells = headerRow.querySelectorAll('th');
        headerCells.forEach((cell, index) => {
            if (cell.classList.contains('no-sort')) return;
            
            cell.addEventListener('click', () => {
                // Toggle sorting
                if (cell.classList.contains('sorting_asc')) {
                    this.dataTable.order([index, 'desc']).draw();
                } else if (cell.classList.contains('sorting_desc')) {
                    this.dataTable.order([index, 'asc']).draw();
                } else {
                    this.dataTable.order([index, 'asc']).draw();
                }
            });
        });
    }
    
    setupColumnVisibility() {
        if (!this.dataTable) return;
        
        // Create column visibility dropdown
        const tableContainer = this.table.closest('.shadcn-table-container');
        if (!tableContainer) return;
        
        // Create toolbar
        const toolbar = document.createElement('div');
        toolbar.className = 'flex items-center justify-between mb-2';
        
        // Create column visibility dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'relative inline-block';
        
        // Create dropdown button
        const dropdownButton = document.createElement('button');
        dropdownButton.className = 'shadcn-button shadcn-button-outline text-sm h-8 px-3';
        dropdownButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 3h18v18H3z"/><path d="M13 7h4v4h-4z"/><path d="M13 15h4v4h-4z"/><path d="M7 7h4v4H7z"/><path d="M7 15h4v4H7z"/></svg> Columns';
        dropdown.appendChild(dropdownButton);
        
        // Create dropdown content
        const dropdownContent = document.createElement('div');
        dropdownContent.className = 'absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10';
        
        // Add column options
        const headerCells = this.table.querySelectorAll('thead tr:first-child th');
        headerCells.forEach((cell, index) => {
            if (cell.textContent.trim() === '' || cell.classList.contains('select-checkbox')) return;
            
            const option = document.createElement('div');
            option.className = 'px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center';
            
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'mr-2 rounded border-gray-300';
            checkbox.checked = true;
            
            checkbox.addEventListener('change', (e) => {
                const column = this.dataTable.column(index);
                column.visible(e.target.checked);
            });
            
            const label = document.createElement('span');
            label.textContent = cell.textContent.trim();
            
            option.appendChild(checkbox);
            option.appendChild(label);
            dropdownContent.appendChild(option);
        });
        
        dropdown.appendChild(dropdownContent);
        
        // Toggle dropdown on button click
        dropdownButton.addEventListener('click', () => {
            dropdownContent.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) {
                dropdownContent.classList.add('hidden');
            }
        });
        
        toolbar.appendChild(dropdown);
        
        // Insert toolbar before table
        tableContainer.insertBefore(toolbar, tableContainer.firstChild);
    }
}

// Initialize on document ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize for all tables with class 'shadcn-table'
    document.querySelectorAll('table.shadcn-table').forEach(table => {
        new ShadcnTableHeader(table.id);
    });
});
