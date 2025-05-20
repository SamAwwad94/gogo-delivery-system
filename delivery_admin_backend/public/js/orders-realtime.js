/**
 * Orders Real-time Updates
 * 
 * This file contains functionality for real-time updates to the orders table
 * using WebSockets via Laravel Echo and Pusher.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize real-time updates if Echo is available
    if (typeof window.Echo !== 'undefined') {
        initRealTimeUpdates();
    } else {
        console.warn('Laravel Echo is not available. Real-time updates will not work.');
        
        // Check if we're on the orders page
        if (document.querySelector('.shadcn-table-container')) {
            // Add a polling fallback for environments without WebSockets
            initPollingFallback();
        }
    }
});

/**
 * Initialize real-time updates using Laravel Echo
 */
function initRealTimeUpdates() {
    // Check if we're on the orders page
    if (!document.querySelector('.shadcn-table-container')) {
        return;
    }
    
    // Subscribe to the orders channel
    window.Echo.channel('orders')
        .listen('OrderStatusChanged', (e) => {
            updateOrderStatus(e.order);
        })
        .listen('OrderCreated', (e) => {
            addNewOrder(e.order);
        })
        .listen('OrderUpdated', (e) => {
            updateOrder(e.order);
        })
        .listen('OrderDeleted', (e) => {
            removeOrder(e.orderId);
        });
    
    console.log('Real-time updates initialized');
}

/**
 * Initialize polling fallback for environments without WebSockets
 */
function initPollingFallback() {
    // Poll for updates every 30 seconds
    setInterval(function() {
        fetchLatestOrders();
    }, 30000);
    
    console.log('Polling fallback initialized');
}

/**
 * Fetch latest orders via AJAX
 */
function fetchLatestOrders() {
    // Get the timestamp of the last update
    const lastUpdate = localStorage.getItem('orders-last-update') || new Date(0).toISOString();
    
    // Get current filters
    const filterForm = document.getElementById('order-filter-form');
    if (!filterForm) return;
    
    const formData = new FormData(filterForm);
    const searchParams = new URLSearchParams();
    
    for (const [key, value] of formData.entries()) {
        if (value) {
            searchParams.append(key, value);
        }
    }
    
    // Add last update parameter
    searchParams.append('last_update', lastUpdate);
    
    // Fetch updates
    fetch(`${window.location.pathname}/updates?${searchParams.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.orders && data.orders.length > 0) {
            // Process updates
            data.orders.forEach(order => {
                if (order._deleted) {
                    removeOrder(order.id);
                } else if (document.querySelector(`tr[data-order-id="${order.id}"]`)) {
                    updateOrder(order);
                } else {
                    addNewOrder(order);
                }
            });
            
            // Update last update timestamp
            localStorage.setItem('orders-last-update', new Date().toISOString());
            
            // Show notification
            showToast(`${data.orders.length} orders updated`, 'info');
        }
    })
    .catch(error => {
        console.error('Error fetching order updates:', error);
    });
}

/**
 * Update an order's status in the table
 * 
 * @param {Object} order - The order object with updated data
 */
function updateOrderStatus(order) {
    const row = document.querySelector(`tr[data-order-id="${order.id}"]`);
    if (!row) return;
    
    const statusCell = row.querySelector('td[data-label="Status"]');
    if (!statusCell) return;
    
    // Determine status class and label
    let statusClass = '';
    let statusLabel = '';
    
    switch(order.status) {
        case 'delivered':
        case 'completed':
            statusClass = 'bg-green-100 text-green-800 ring-green-200';
            statusLabel = order.status === 'completed' ? 'Delivered' : 'Delivered';
            break;
        case 'pending':
        case 'create':
            statusClass = 'bg-yellow-100 text-yellow-800 ring-yellow-200';
            statusLabel = order.status === 'create' ? 'Created' : 'Pending';
            break;
        case 'in_progress':
            statusClass = 'bg-blue-100 text-blue-800 ring-blue-200';
            statusLabel = 'In Progress';
            break;
        case 'cancelled':
            statusClass = 'bg-red-100 text-red-800 ring-red-200';
            statusLabel = 'Cancelled';
            break;
        case 'courier_assigned':
            statusClass = 'bg-purple-100 text-purple-800 ring-purple-200';
            statusLabel = 'Assigned';
            break;
        case 'courier_accepted':
            statusClass = 'bg-indigo-100 text-indigo-800 ring-indigo-200';
            statusLabel = 'Accepted';
            break;
        case 'courier_arrived':
            statusClass = 'bg-blue-100 text-blue-800 ring-blue-200';
            statusLabel = 'Arrived';
            break;
        case 'courier_picked_up':
            statusClass = 'bg-pink-100 text-pink-800 ring-pink-200';
            statusLabel = 'Picked Up';
            break;
        case 'courier_departed':
            statusClass = 'bg-orange-100 text-orange-800 ring-orange-200';
            statusLabel = 'Departed';
            break;
        default:
            statusClass = 'bg-gray-100 text-gray-800 ring-gray-200';
            statusLabel = order.status.charAt(0).toUpperCase() + order.status.slice(1);
    }
    
    // Update the status cell
    const statusSpan = statusCell.querySelector('span');
    if (statusSpan) {
        statusSpan.className = `inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${statusClass}`;
        statusSpan.textContent = statusLabel;
        
        // Highlight the row to indicate it was updated
        row.classList.add('bg-yellow-50');
        setTimeout(() => {
            row.classList.remove('bg-yellow-50');
        }, 3000);
        
        // Show a toast notification
        showToast(`Order #${order.id} status updated to ${statusLabel}`, 'info');
    }
}

/**
 * Update an order in the table
 * 
 * @param {Object} order - The order object with updated data
 */
function updateOrder(order) {
    const row = document.querySelector(`tr[data-order-id="${order.id}"]`);
    if (!row) return;
    
    // Update status
    updateOrderStatus(order);
    
    // Update other fields
    const phoneCell = row.querySelector('td[data-label="Phone"]');
    if (phoneCell) {
        phoneCell.textContent = order.phone || 'N/A';
    }
    
    const paymentStatusCell = row.querySelector('td[data-label="Payment Status"]');
    if (paymentStatusCell) {
        const paymentSpan = paymentStatusCell.querySelector('span');
        if (paymentSpan) {
            // Determine payment status class
            let paymentClass = '';
            switch(order.payment_status) {
                case 'paid':
                    paymentClass = 'bg-green-100 text-green-800 ring-green-200';
                    break;
                case 'unpaid':
                    paymentClass = 'bg-red-100 text-red-800 ring-red-200';
                    break;
                case 'partial':
                    paymentClass = 'bg-yellow-100 text-yellow-800 ring-yellow-200';
                    break;
                case 'refunded':
                    paymentClass = 'bg-purple-100 text-purple-800 ring-purple-200';
                    break;
                default:
                    paymentClass = 'bg-gray-100 text-gray-800 ring-gray-200';
            }
            
            paymentSpan.className = `inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${paymentClass}`;
            paymentSpan.textContent = order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1);
        }
    }
    
    // Highlight the row to indicate it was updated
    row.classList.add('bg-yellow-50');
    setTimeout(() => {
        row.classList.remove('bg-yellow-50');
    }, 3000);
}

/**
 * Add a new order to the table
 * 
 * @param {Object} order - The new order object
 */
function addNewOrder(order) {
    // Check if the order already exists
    if (document.querySelector(`tr[data-order-id="${order.id}"]`)) {
        return updateOrder(order);
    }
    
    // Create a new row for the order
    const tbody = document.querySelector('table tbody');
    if (!tbody) return;
    
    // Check if we have the "No orders found" row
    const noOrdersRow = tbody.querySelector('tr td[colspan]');
    if (noOrdersRow) {
        tbody.innerHTML = '';
    }
    
    // Create the new row
    const newRow = document.createElement('tr');
    newRow.className = 'border-b border-border hover:bg-muted/50 bg-green-50';
    newRow.setAttribute('data-order-id', order.id);
    newRow.setAttribute('data-order-status', order.status);
    
    // Add the row to the table
    tbody.insertBefore(newRow, tbody.firstChild);
    
    // Populate the row with order data
    appendOrderToTable([order]);
    
    // Remove the highlight after 3 seconds
    setTimeout(() => {
        newRow.classList.remove('bg-green-50');
    }, 3000);
    
    // Show a toast notification
    showToast(`New order #${order.id} received`, 'success');
}

/**
 * Remove an order from the table
 * 
 * @param {number|string} orderId - The ID of the order to remove
 */
function removeOrder(orderId) {
    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
    if (!row) return;
    
    // Add a fade-out effect
    row.style.transition = 'opacity 0.5s, height 0.5s, padding 0.5s';
    row.style.opacity = '0';
    row.style.height = '0';
    row.style.padding = '0';
    
    // Remove the row after the animation
    setTimeout(() => {
        row.remove();
        
        // Check if we need to show the "No orders found" message
        const tbody = document.querySelector('table tbody');
        if (tbody && tbody.children.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="p-4 text-center text-gray-500">No orders found</td></tr>';
        }
    }, 500);
    
    // Show a toast notification
    showToast(`Order #${orderId} has been deleted`, 'warning');
}
