/**
 * ShadCN Filter Configurations
 * 
 * This file contains predefined filter configurations for different tables.
 * It provides consistent filter options across the application.
 */

// Common filter options
const filterOptions = {
    // Status options
    status: [
        { value: '', label: 'All Statuses' },
        { value: 'active', label: 'Active' },
        { value: 'inactive', label: 'Inactive' },
        { value: 'pending', label: 'Pending' },
        { value: 'suspended', label: 'Suspended' }
    ],
    
    // Payment status options
    paymentStatus: [
        { value: '', label: 'All Payment Statuses' },
        { value: 'paid', label: 'Paid' },
        { value: 'unpaid', label: 'Unpaid' },
        { value: 'partial', label: 'Partially Paid' },
        { value: 'refunded', label: 'Refunded' },
        { value: 'failed', label: 'Failed' }
    ],
    
    // Order status options
    orderStatus: [
        { value: '', label: 'All Order Statuses' },
        { value: 'pending', label: 'Pending' },
        { value: 'processing', label: 'Processing' },
        { value: 'shipped', label: 'Shipped' },
        { value: 'delivered', label: 'Delivered' },
        { value: 'cancelled', label: 'Cancelled' },
        { value: 'returned', label: 'Returned' },
        { value: 'refunded', label: 'Refunded' }
    ],
    
    // Delivery status options
    deliveryStatus: [
        { value: '', label: 'All Delivery Statuses' },
        { value: 'pending', label: 'Pending' },
        { value: 'assigned', label: 'Assigned' },
        { value: 'in_transit', label: 'In Transit' },
        { value: 'delivered', label: 'Delivered' },
        { value: 'failed', label: 'Failed' },
        { value: 'returned', label: 'Returned' }
    ],
    
    // User role options
    userRole: [
        { value: '', label: 'All Roles' },
        { value: 'admin', label: 'Admin' },
        { value: 'manager', label: 'Manager' },
        { value: 'customer', label: 'Customer' },
        { value: 'delivery', label: 'Delivery Person' }
    ],
    
    // Date range options
    dateRange: [
        { value: '', label: 'All Time' },
        { value: 'today', label: 'Today' },
        { value: 'yesterday', label: 'Yesterday' },
        { value: 'this_week', label: 'This Week' },
        { value: 'last_week', label: 'Last Week' },
        { value: 'this_month', label: 'This Month' },
        { value: 'last_month', label: 'Last Month' },
        { value: 'this_year', label: 'This Year' },
        { value: 'last_year', label: 'Last Year' },
        { value: 'custom', label: 'Custom Range' }
    ],
    
    // Currency options
    currency: [
        { value: '', label: 'All Currencies' },
        { value: 'USD', label: 'US Dollar (USD)' },
        { value: 'LBP', label: 'Lebanese Pound (LBP)' }
    ],
    
    // Language options
    language: [
        { value: '', label: 'All Languages' },
        { value: 'en', label: 'English' },
        { value: 'ar', label: 'Arabic' }
    ],
    
    // Boolean options (Yes/No)
    boolean: [
        { value: '', label: 'All' },
        { value: '1', label: 'Yes' },
        { value: '0', label: 'No' }
    ]
};

// Table-specific filter configurations
const filterConfigs = {
    // Orders table filters
    orders: {
        id: 'orders-filter',
        title: 'Filter Orders',
        filters: [
            {
                name: 'status',
                label: 'Order Status',
                type: 'select',
                options: filterOptions.orderStatus
            },
            {
                name: 'payment_status',
                label: 'Payment Status',
                type: 'select',
                options: filterOptions.paymentStatus
            },
            {
                name: 'delivery_status',
                label: 'Delivery Status',
                type: 'select',
                options: filterOptions.deliveryStatus
            },
            {
                name: 'date_range',
                label: 'Date Range',
                type: 'select',
                options: filterOptions.dateRange
            },
            {
                name: 'currency',
                label: 'Currency',
                type: 'select',
                options: filterOptions.currency
            }
        ]
    },
    
    // Users table filters
    users: {
        id: 'users-filter',
        title: 'Filter Users',
        filters: [
            {
                name: 'status',
                label: 'Status',
                type: 'select',
                options: filterOptions.status
            },
            {
                name: 'role',
                label: 'Role',
                type: 'select',
                options: filterOptions.userRole
            },
            {
                name: 'date_range',
                label: 'Registration Date',
                type: 'select',
                options: filterOptions.dateRange
            }
        ]
    },
    
    // Delivery routes table filters
    deliveryRoutes: {
        id: 'delivery-routes-filter',
        title: 'Filter Delivery Routes',
        filters: [
            {
                name: 'status',
                label: 'Status',
                type: 'select',
                options: filterOptions.deliveryStatus
            },
            {
                name: 'date_range',
                label: 'Date Range',
                type: 'select',
                options: filterOptions.dateRange
            },
            {
                name: 'delivery_man',
                label: 'Delivery Person',
                type: 'select',
                options: [] // Will be populated dynamically
            }
        ]
    },
    
    // Delivery men table filters
    deliveryMen: {
        id: 'delivery-men-filter',
        title: 'Filter Delivery Personnel',
        filters: [
            {
                name: 'status',
                label: 'Status',
                type: 'select',
                options: filterOptions.status
            },
            {
                name: 'availability',
                label: 'Availability',
                type: 'select',
                options: [
                    { value: '', label: 'All' },
                    { value: 'available', label: 'Available' },
                    { value: 'busy', label: 'Busy' },
                    { value: 'offline', label: 'Offline' }
                ]
            },
            {
                name: 'date_range',
                label: 'Registration Date',
                type: 'select',
                options: filterOptions.dateRange
            }
        ]
    }
};

// Export filter configurations
window.shadcnFilterConfigs = filterConfigs;
window.shadcnFilterOptions = filterOptions;
