/**
 * Unified Orders Table JavaScript
 * Handles all interactive functionality for the ShadCN unified orders table
 */

$(document).ready(function() {
    // Initialize Select2 for dropdown filters
    $('.shadcn-filter-item select').select2({
        width: '100%',
        minimumResultsForSearch: 6,
        dropdownCssClass: 'select2-dropdown-shadcn'
    });

    // Initialize Flatpickr for date inputs
    flatpickr('input[type="date"]', {
        dateFormat: 'Y-m-d',
        allowInput: true
    });

    // Handle bulk selection
    $('#select-all').on('change', function() {
        $('.bulk-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkActionVisibility();
    });

    $('.bulk-checkbox').on('change', function() {
        updateBulkActionVisibility();
        
        // If any checkbox is unchecked, uncheck the "select all" checkbox
        if (!$(this).prop('checked')) {
            $('#select-all').prop('checked', false);
        }
        
        // If all checkboxes are checked, check the "select all" checkbox
        if ($('.bulk-checkbox:checked').length === $('.bulk-checkbox').length) {
            $('#select-all').prop('checked', true);
        }
    });

    function updateBulkActionVisibility() {
        const checkedCount = $('.bulk-checkbox:checked').length;
        if (checkedCount > 0) {
            // Show bulk actions
            if ($('#bulk-actions-container').length === 0) {
                const bulkActionsHtml = `
                    <div id="bulk-actions-container" class="fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-white shadow-lg rounded-lg p-3 flex items-center space-x-3 z-50 border border-gray-200">
                        <span class="text-sm font-medium">${checkedCount} items selected</span>
                        <div class="flex space-x-2">
                            <button id="bulk-print" class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors">
                                <i class="fas fa-print mr-1"></i> Print
                            </button>
                            <button id="bulk-export" class="px-3 py-1.5 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 transition-colors">
                                <i class="fas fa-file-export mr-1"></i> Export
                            </button>
                            <button id="bulk-delete" class="px-3 py-1.5 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                            <button id="bulk-cancel" class="px-3 py-1.5 bg-gray-200 text-gray-800 rounded-md text-sm hover:bg-gray-300 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                `;
                $('body').append(bulkActionsHtml);
                
                // Attach event handlers to bulk action buttons
                $('#bulk-print').on('click', handleBulkPrint);
                $('#bulk-export').on('click', handleBulkExport);
                $('#bulk-delete').on('click', handleBulkDelete);
                $('#bulk-cancel').on('click', function() {
                    $('.bulk-checkbox, #select-all').prop('checked', false);
                    updateBulkActionVisibility();
                });
            }
        } else {
            // Hide bulk actions
            $('#bulk-actions-container').remove();
        }
    }

    // Bulk action handlers
    function handleBulkPrint() {
        const selectedIds = getSelectedOrderIds();
        // Implement print functionality
        window.open(`/order/print-multiple?ids=${selectedIds.join(',')}`, '_blank');
    }

    function handleBulkExport() {
        const selectedIds = getSelectedOrderIds();
        // Implement export functionality
        window.open(`/order/export-selected?ids=${selectedIds.join(',')}`, '_blank');
    }

    function handleBulkDelete() {
        const selectedIds = getSelectedOrderIds();
        if (confirm(`Are you sure you want to delete ${selectedIds.length} orders? This action cannot be undone.`)) {
            // Implement delete functionality
            $.ajax({
                url: '/order/bulk-delete',
                type: 'POST',
                data: {
                    ids: selectedIds,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'Orders deleted successfully');
                        // Reload the page after successful deletion
                        window.location.reload();
                    } else {
                        toastr.error(response.message || 'Failed to delete orders');
                    }
                },
                error: function() {
                    toastr.error('An error occurred while deleting orders');
                }
            });
        }
    }

    function getSelectedOrderIds() {
        return $('.bulk-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
    }

    // Inline editing functionality
    $('[data-editable="true"]').on('dblclick', function() {
        const cell = $(this);
        const field = cell.data('field');
        const orderId = cell.closest('tr').data('order-id');
        const currentValue = cell.text().trim();
        
        // Don't allow editing if already in edit mode
        if (cell.find('input, select').length > 0) {
            return;
        }
        
        let inputHtml;
        
        // Create appropriate input based on field type
        if (field === 'status') {
            inputHtml = `
                <select class="inline-edit-input w-full rounded-md border border-input bg-background px-2 py-1 text-sm">
                    <option value="draft" ${currentValue === 'Draft' ? 'selected' : ''}>Draft</option>
                    <option value="create" ${currentValue === 'Created' ? 'selected' : ''}>Created</option>
                    <option value="courier_assigned" ${currentValue === 'Assigned' ? 'selected' : ''}>Assigned</option>
                    <option value="courier_accepted" ${currentValue === 'Accepted' ? 'selected' : ''}>Accepted</option>
                    <option value="courier_arrived" ${currentValue === 'Arrived' ? 'selected' : ''}>Arrived</option>
                    <option value="courier_picked_up" ${currentValue === 'Picked Up' ? 'selected' : ''}>Picked Up</option>
                    <option value="courier_departed" ${currentValue === 'Departed' ? 'selected' : ''}>Departed</option>
                    <option value="completed" ${currentValue === 'Delivered' ? 'selected' : ''}>Delivered</option>
                    <option value="cancelled" ${currentValue === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                    <option value="reschedule" ${currentValue === 'Rescheduled' ? 'selected' : ''}>Rescheduled</option>
                </select>
            `;
        } else if (field === 'payment_status') {
            inputHtml = `
                <select class="inline-edit-input w-full rounded-md border border-input bg-background px-2 py-1 text-sm">
                    <option value="paid" ${currentValue === 'Paid' ? 'selected' : ''}>Paid</option>
                    <option value="unpaid" ${currentValue === 'Unpaid' ? 'selected' : ''}>Unpaid</option>
                    <option value="partial" ${currentValue === 'Partial' ? 'selected' : ''}>Partial</option>
                </select>
            `;
        } else {
            // Default to text input for other fields
            inputHtml = `<input type="text" class="inline-edit-input w-full rounded-md border border-input bg-background px-2 py-1 text-sm" value="${currentValue}">`;
        }
        
        // Store original content and replace with input
        cell.data('original-content', cell.html());
        cell.html(inputHtml);
        
        // Focus the input
        const input = cell.find('input, select').focus();
        
        // Handle save on enter or blur
        input.on('keydown', function(e) {
            if (e.key === 'Enter') {
                saveInlineEdit(cell, orderId, field);
            } else if (e.key === 'Escape') {
                cancelInlineEdit(cell);
            }
        });
        
        input.on('blur', function() {
            saveInlineEdit(cell, orderId, field);
        });
    });

    function saveInlineEdit(cell, orderId, field) {
        const input = cell.find('input, select');
        const newValue = input.val();
        
        // Send update to server
        $.ajax({
            url: `/order/${orderId}/update-field`,
            type: 'POST',
            data: {
                field: field,
                value: newValue,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update the cell with new formatted value
                    if (field === 'status') {
                        const statusLabel = getStatusLabel(newValue);
                        const statusClass = getStatusClass(newValue);
                        cell.html(`<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${statusClass}">${statusLabel}</span>`);
                    } else if (field === 'payment_status') {
                        const paymentClass = getPaymentStatusClass(newValue);
                        cell.html(`<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${paymentClass}">${newValue.charAt(0).toUpperCase() + newValue.slice(1)}</span>`);
                    } else {
                        cell.text(newValue);
                    }
                    
                    toastr.success(response.message || 'Updated successfully');
                } else {
                    toastr.error(response.message || 'Failed to update');
                    cancelInlineEdit(cell);
                }
            },
            error: function() {
                toastr.error('An error occurred while updating');
                cancelInlineEdit(cell);
            }
        });
    }

    function cancelInlineEdit(cell) {
        cell.html(cell.data('original-content'));
    }

    function getStatusLabel(status) {
        const statusLabels = {
            'draft': 'Draft',
            'create': 'Created',
            'courier_assigned': 'Assigned',
            'courier_accepted': 'Accepted',
            'courier_arrived': 'Arrived',
            'courier_picked_up': 'Picked Up',
            'courier_departed': 'Departed',
            'completed': 'Delivered',
            'cancelled': 'Cancelled',
            'reschedule': 'Rescheduled'
        };
        return statusLabels[status] || status.charAt(0).toUpperCase() + status.slice(1);
    }

    function getStatusClass(status) {
        switch(status) {
            case 'completed':
            case 'delivered':
                return 'bg-green-100 text-green-800 ring-green-200';
            case 'pending':
            case 'create':
                return 'bg-yellow-100 text-yellow-800 ring-yellow-200';
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

    function getPaymentStatusClass(status) {
        switch(status) {
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
});
