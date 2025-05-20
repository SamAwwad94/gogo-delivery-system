/**
 * Unified Orders Table with Advanced Filtering
 * Handles all order types in a single view
 */
document.addEventListener("DOMContentLoaded", function () {
    // Initialize the table
    initUnifiedOrdersTable();
    
    // Initialize filter functionality
    initFilterFunctionality();
    
    // Initialize date pickers
    initDatePickers();
    
    // Initialize select2 dropdowns
    initSelect2Dropdowns();
    
    // Initialize bulk actions
    initBulkActions();
    
    // Initialize inline editing
    initInlineEditing();
});

/**
 * Initialize the unified orders table
 */
function initUnifiedOrdersTable() {
    // Add responsive behavior to the table
    const table = document.querySelector('.responsive-table');
    if (!table) return;
    
    // Add data-label attributes to cells for mobile view
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
    
    table.querySelectorAll('tbody tr').forEach(row => {
        row.querySelectorAll('td').forEach((cell, index) => {
            if (index > 0 && index < headers.length) { // Skip checkbox column
                cell.setAttribute('data-label', headers[index]);
            }
        });
    });
    
    // Add click event to rows for mobile view
    table.querySelectorAll('tbody tr').forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on a button, link, or checkbox
            if (e.target.tagName === 'BUTTON' || 
                e.target.tagName === 'A' || 
                e.target.tagName === 'INPUT' ||
                e.target.closest('button') ||
                e.target.closest('a') ||
                e.target.closest('input')) {
                return;
            }
            
            // Toggle expanded class for mobile view
            if (window.innerWidth < 768) {
                this.classList.toggle('expanded');
            }
        });
    });
}

/**
 * Initialize filter functionality
 */
function initFilterFunctionality() {
    const filterForm = document.getElementById('order-filter-form');
    if (!filterForm) return;
    
    // Toggle filter visibility
    const filterHeader = document.querySelector('.shadcn-filter-header');
    const filterBody = document.querySelector('.shadcn-filter-body');
    
    if (filterHeader && filterBody) {
        filterHeader.addEventListener('click', function(e) {
            // Don't toggle if clicking on buttons
            if (e.target.tagName === 'BUTTON' || 
                e.target.tagName === 'A' || 
                e.target.closest('button') ||
                e.target.closest('a')) {
                return;
            }
            
            filterBody.classList.toggle('hidden');
            
            // Store preference in localStorage
            localStorage.setItem('orderFilterVisible', filterBody.classList.contains('hidden') ? 'false' : 'true');
        });
        
        // Check localStorage for filter visibility preference
        const filterVisible = localStorage.getItem('orderFilterVisible');
        if (filterVisible === 'false') {
            filterBody.classList.add('hidden');
        }
    }
    
    // Handle filter form submission
    filterForm.addEventListener('submit', function(e) {
        // Show loading indicator
        showLoading();
    });
    
    // Handle filter clear button
    const clearButton = filterForm.querySelector('a.shadcn-button-ghost');
    if (clearButton) {
        clearButton.addEventListener('click', function() {
            // Show loading indicator
            showLoading();
        });
    }
}

/**
 * Initialize date pickers
 */
function initDatePickers() {
    // Check if flatpickr is available
    if (typeof flatpickr === 'function') {
        // Initialize date pickers
        flatpickr('#from_date', {
            dateFormat: 'Y-m-d',
            allowInput: true
        });
        
        flatpickr('#to_date', {
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }
}

/**
 * Initialize select2 dropdowns
 */
function initSelect2Dropdowns() {
    // Check if select2 is available
    if (typeof $.fn.select2 === 'function') {
        $('#client_id, #status, #payment_status').select2({
            theme: 'classic',
            width: '100%'
        });
    }
}

/**
 * Initialize bulk actions
 */
function initBulkActions() {
    const selectAllCheckbox = document.getElementById('select-all');
    const bulkCheckboxes = document.querySelectorAll('.bulk-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            bulkCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            
            updateBulkActionButtons();
        });
    }
    
    bulkCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionButtons();
            
            // Update select all checkbox
            if (!this.checked) {
                selectAllCheckbox.checked = false;
            } else {
                // Check if all checkboxes are checked
                const allChecked = Array.from(bulkCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            }
        });
    });
    
    function updateBulkActionButtons() {
        const checkedCount = document.querySelectorAll('.bulk-checkbox:checked').length;
        const bulkActionButtons = document.querySelectorAll('.bulk-action-button');
        
        bulkActionButtons.forEach(button => {
            if (checkedCount > 0) {
                button.removeAttribute('disabled');
            } else {
                button.setAttribute('disabled', 'disabled');
            }
        });
    }
}

/**
 * Initialize inline editing
 */
function initInlineEditing() {
    const editableCells = document.querySelectorAll('td[data-editable="true"]');
    
    editableCells.forEach(cell => {
        cell.addEventListener('dblclick', function() {
            const currentValue = this.textContent.trim();
            const field = this.getAttribute('data-field');
            const orderId = this.closest('tr').getAttribute('data-order-id');
            
            // Create input or select based on field type
            let input;
            
            if (field === 'status') {
                // For status, create a select dropdown
                input = document.createElement('select');
                
                const statusOptions = [
                    { value: 'draft', label: 'Draft' },
                    { value: 'create', label: 'Created' },
                    { value: 'courier_assigned', label: 'Assigned' },
                    { value: 'courier_accepted', label: 'Accepted' },
                    { value: 'courier_arrived', label: 'Arrived' },
                    { value: 'courier_picked_up', label: 'Picked Up' },
                    { value: 'courier_departed', label: 'Departed' },
                    { value: 'completed', label: 'Completed' },
                    { value: 'cancelled', label: 'Cancelled' }
                ];
                
                statusOptions.forEach(option => {
                    const optionEl = document.createElement('option');
                    optionEl.value = option.value;
                    optionEl.textContent = option.label;
                    
                    // Set selected based on current status
                    if (this.closest('tr').getAttribute('data-order-status') === option.value) {
                        optionEl.selected = true;
                    }
                    
                    input.appendChild(optionEl);
                });
            } else if (field === 'payment_status') {
                // For payment status, create a select dropdown
                input = document.createElement('select');
                
                const paymentStatusOptions = [
                    { value: 'paid', label: 'Paid' },
                    { value: 'unpaid', label: 'Unpaid' },
                    { value: 'partial', label: 'Partial' }
                ];
                
                paymentStatusOptions.forEach(option => {
                    const optionEl = document.createElement('option');
                    optionEl.value = option.value;
                    optionEl.textContent = option.label;
                    
                    // Set selected based on current text
                    if (currentValue.toLowerCase().includes(option.value)) {
                        optionEl.selected = true;
                    }
                    
                    input.appendChild(optionEl);
                });
            } else {
                // For other fields, create a text input
                input = document.createElement('input');
                input.type = 'text';
                input.value = currentValue;
            }
            
            // Add common attributes and styles
            input.className = 'w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm';
            
            // Replace cell content with input
            const originalContent = this.innerHTML;
            this.innerHTML = '';
            this.appendChild(input);
            
            // Focus the input
            input.focus();
            
            // Handle input blur (save changes)
            input.addEventListener('blur', function() {
                saveChanges();
            });
            
            // Handle Enter key
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    saveChanges();
                } else if (e.key === 'Escape') {
                    cell.innerHTML = originalContent;
                }
            });
            
            function saveChanges() {
                const newValue = input.value;
                
                // Send AJAX request to update the field
                fetch(`/order/${orderId}/update-field`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        field: field,
                        value: newValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the cell with the new value
                        if (field === 'status') {
                            // Update status badge
                            const statusLabel = statusOptions.find(option => option.value === newValue)?.label || newValue;
                            const statusClass = getStatusClass(newValue);
                            
                            cell.innerHTML = `
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${statusClass}">
                                    ${statusLabel}
                                </span>
                            `;
                            
                            // Update data attribute
                            cell.closest('tr').setAttribute('data-order-status', newValue);
                        } else if (field === 'payment_status') {
                            // Update payment status badge
                            const paymentClass = getPaymentStatusClass(newValue);
                            
                            cell.innerHTML = `
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${paymentClass}">
                                    ${newValue.charAt(0).toUpperCase() + newValue.slice(1)}
                                </span>
                            `;
                        } else {
                            cell.textContent = newValue;
                        }
                        
                        // Show success message
                        showToast('Success', 'Field updated successfully', 'success');
                    } else {
                        // Show error message
                        showToast('Error', data.message || 'Failed to update field', 'error');
                        
                        // Restore original content
                        cell.innerHTML = originalContent;
                    }
                })
                .catch(error => {
                    console.error('Error updating field:', error);
                    
                    // Show error message
                    showToast('Error', 'Failed to update field', 'error');
                    
                    // Restore original content
                    cell.innerHTML = originalContent;
                });
            }
        });
    });
}

/**
 * Show loading indicator
 */
function showLoading() {
    // Create loading overlay if it doesn't exist
    let loadingOverlay = document.querySelector('.loading-overlay');
    
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50';
        loadingOverlay.innerHTML = '<div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>';
        document.body.appendChild(loadingOverlay);
    } else {
        loadingOverlay.classList.remove('hidden');
    }
}

/**
 * Hide loading indicator
 */
function hideLoading() {
    const loadingOverlay = document.querySelector('.loading-overlay');
    
    if (loadingOverlay) {
        loadingOverlay.classList.add('hidden');
    }
}

/**
 * Show toast notification
 * @param {string} title - Toast title
 * @param {string} message - Toast message
 * @param {string} type - Toast type (success, error, warning, info)
 */
function showToast(title, message, type = 'info') {
    // Check if toastr is available
    if (typeof toastr === 'object') {
        toastr[type](message, title);
    } else {
        // Fallback to alert
        alert(`${title}: ${message}`);
    }
}

/**
 * Get status class for badge
 * @param {string} status - Order status
 * @returns {string} - CSS class for badge
 */
function getStatusClass(status) {
    switch (status) {
        case 'delivered':
        case 'completed':
            return 'bg-green-100 text-green-800 ring-green-200';
        case 'pending':
        case 'create':
            return 'bg-yellow-100 text-yellow-800 ring-yellow-200';
        case 'in_progress':
        case 'courier_assigned':
        case 'courier_accepted':
        case 'courier_arrived':
        case 'courier_picked_up':
        case 'courier_departed':
            return 'bg-blue-100 text-blue-800 ring-blue-200';
        case 'cancelled':
            return 'bg-red-100 text-red-800 ring-red-200';
        case 'reschedule':
            return 'bg-purple-100 text-purple-800 ring-purple-200';
        default:
            return 'bg-gray-100 text-gray-800 ring-gray-200';
    }
}

/**
 * Get payment status class for badge
 * @param {string} status - Payment status
 * @returns {string} - CSS class for badge
 */
function getPaymentStatusClass(status) {
    switch (status) {
        case 'paid':
            return 'bg-green-100 text-green-800 ring-green-200';
        case 'unpaid':
            return 'bg-red-100 text-red-800 ring-red-200';
        case 'partial':
            return 'bg-yellow-100 text-yellow-800 ring-yellow-200';
        default:
            return 'bg-gray-100 text-gray-800 ring-gray-200';
    }
}
