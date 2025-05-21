/**
 * ShadCN Components
 * 
 * This file contains JavaScript implementations for ShadCN UI components.
 * It includes table, filter, and other UI component functionality.
 */

// Initialize all ShadCN components when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    initializeShadcnTables();
    initializeShadcnFilters();
    initializeShadcnDropdowns();
    initializeShadcnModals();
});

/**
 * Table Component
 */
function initializeShadcnTables() {
    const tables = document.querySelectorAll('.shadcn-table');
    
    tables.forEach(table => {
        // Add sorting functionality
        const sortableHeaders = table.querySelectorAll('th[data-sortable]');
        sortableHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const column = header.getAttribute('data-column');
                const currentDirection = header.getAttribute('data-direction') || 'asc';
                const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                
                // Reset all headers
                sortableHeaders.forEach(h => {
                    h.setAttribute('data-direction', '');
                    h.classList.remove('sorting-asc', 'sorting-desc');
                });
                
                // Set new sort direction
                header.setAttribute('data-direction', newDirection);
                header.classList.add(newDirection === 'asc' ? 'sorting-asc' : 'sorting-desc');
                
                // Sort the table
                sortTable(table, column, newDirection);
            });
        });
        
        // Add row selection functionality
        const selectableRows = table.querySelectorAll('tbody tr[data-selectable]');
        selectableRows.forEach(row => {
            row.addEventListener('click', () => {
                if (row.classList.contains('selected')) {
                    row.classList.remove('selected');
                } else {
                    row.classList.add('selected');
                }
            });
        });
    });
}

function sortTable(table, column, direction) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Sort rows
    rows.sort((a, b) => {
        const aValue = a.querySelector(`td[data-column="${column}"]`).textContent.trim();
        const bValue = b.querySelector(`td[data-column="${column}"]`).textContent.trim();
        
        if (direction === 'asc') {
            return aValue.localeCompare(bValue);
        } else {
            return bValue.localeCompare(aValue);
        }
    });
    
    // Reorder rows
    rows.forEach(row => {
        tbody.appendChild(row);
    });
}

/**
 * Filter Component
 */
function initializeShadcnFilters() {
    const filters = document.querySelectorAll('.shadcn-filter');
    
    filters.forEach(filter => {
        // Get filter elements
        const toggleButton = filter.querySelector('.shadcn-filter-toggle');
        const filterBody = filter.querySelector('.shadcn-filter-body');
        const applyButton = filter.querySelector('.shadcn-filter-button-primary');
        const resetButton = filter.querySelector('.shadcn-filter-button-secondary');
        const filterInputs = filter.querySelectorAll('select, input');
        const filterBadge = filter.querySelector('.shadcn-filter-badge');
        
        // Get filter ID for localStorage
        const filterId = filter.getAttribute('data-filter-id') || 'default-filter';
        const storageKey = `shadcn-filter-${filterId}`;
        const collapseKey = `shadcn-filter-collapse-${filterId}`;
        
        // Load saved filter state
        loadFilterState(filterInputs, storageKey);
        
        // Load collapse state
        const isCollapsed = localStorage.getItem(collapseKey) === 'true';
        if (isCollapsed) {
            filterBody.style.display = 'none';
            toggleButton.setAttribute('aria-expanded', 'false');
        }
        
        // Update filter badge
        updateFilterBadge(filterInputs, filterBadge);
        
        // Toggle filter visibility
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                const isExpanded = toggleButton.getAttribute('aria-expanded') === 'true';
                toggleButton.setAttribute('aria-expanded', !isExpanded);
                
                if (isExpanded) {
                    filterBody.style.display = 'none';
                    localStorage.setItem(collapseKey, 'true');
                } else {
                    filterBody.style.display = 'grid';
                    localStorage.setItem(collapseKey, 'false');
                }
            });
        }
        
        // Apply filters
        if (applyButton) {
            applyButton.addEventListener('click', () => {
                saveFilterState(filterInputs, storageKey);
                updateFilterBadge(filterInputs, filterBadge);
                
                // Trigger filter event
                const filterEvent = new CustomEvent('shadcn-filter-applied', {
                    detail: { filterId, values: getFilterValues(filterInputs) }
                });
                document.dispatchEvent(filterEvent);
            });
        }
        
        // Reset filters
        if (resetButton) {
            resetButton.addEventListener('click', () => {
                resetFilters(filterInputs);
                localStorage.removeItem(storageKey);
                updateFilterBadge(filterInputs, filterBadge);
                
                // Trigger filter event
                const filterEvent = new CustomEvent('shadcn-filter-reset', {
                    detail: { filterId }
                });
                document.dispatchEvent(filterEvent);
            });
        }
        
        // Auto-apply filters if enabled
        const autoApply = filter.getAttribute('data-auto-apply') === 'true';
        if (autoApply) {
            filterInputs.forEach(input => {
                input.addEventListener('change', () => {
                    saveFilterState(filterInputs, storageKey);
                    updateFilterBadge(filterInputs, filterBadge);
                    
                    // Trigger filter event
                    const filterEvent = new CustomEvent('shadcn-filter-applied', {
                        detail: { filterId, values: getFilterValues(filterInputs) }
                    });
                    document.dispatchEvent(filterEvent);
                });
            });
        }
    });
}

function getFilterValues(inputs) {
    const values = {};
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            values[name] = input.value;
        }
    });
    return values;
}

function saveFilterState(inputs, storageKey) {
    const state = {};
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            state[name] = input.value;
        }
    });
    localStorage.setItem(storageKey, JSON.stringify(state));
}

function loadFilterState(inputs, storageKey) {
    const savedState = localStorage.getItem(storageKey);
    if (savedState) {
        const state = JSON.parse(savedState);
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name && state[name] !== undefined) {
                input.value = state[name];
            }
        });
    }
}

function resetFilters(inputs) {
    inputs.forEach(input => {
        if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        } else if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
}

function updateFilterBadge(inputs, badge) {
    if (!badge) return;
    
    let activeFilters = 0;
    inputs.forEach(input => {
        if (input.tagName === 'SELECT' && input.selectedIndex > 0) {
            activeFilters++;
        } else if ((input.type === 'checkbox' || input.type === 'radio') && input.checked) {
            activeFilters++;
        } else if (input.type === 'text' && input.value.trim() !== '') {
            activeFilters++;
        }
    });
    
    badge.textContent = activeFilters;
    badge.style.display = activeFilters > 0 ? 'inline-flex' : 'none';
}

/**
 * Dropdown Component
 */
function initializeShadcnDropdowns() {
    const dropdowns = document.querySelectorAll('.shadcn-dropdown');
    
    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('.shadcn-dropdown-trigger');
        const content = dropdown.querySelector('.shadcn-dropdown-content');
        
        if (trigger && content) {
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                content.classList.toggle('open');
            });
            
            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    content.classList.remove('open');
                }
            });
        }
    });
}

/**
 * Modal Component
 */
function initializeShadcnModals() {
    const modalTriggers = document.querySelectorAll('[data-modal-trigger]');
    
    modalTriggers.forEach(trigger => {
        const modalId = trigger.getAttribute('data-modal-trigger');
        const modal = document.getElementById(modalId);
        
        if (modal) {
            const closeButtons = modal.querySelectorAll('[data-modal-close]');
            
            trigger.addEventListener('click', () => {
                modal.classList.add('open');
                document.body.classList.add('modal-open');
            });
            
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    modal.classList.remove('open');
                    document.body.classList.remove('modal-open');
                });
            });
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('open');
                    document.body.classList.remove('modal-open');
                }
            });
        }
    });
}
