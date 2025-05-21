/**
 * ShadCN Filter Configurations
 * Common filter configurations for different tables
 */

const ShadcnFilterConfigs = {
    /**
     * Common status filter options
     */
    statusOptions: [
        { value: "pending", label: "Pending" },
        { value: "in_progress", label: "In Progress" },
        { value: "completed", label: "Completed" },
        { value: "cancelled", label: "Cancelled" }
    ],
    
    /**
     * Common payment status filter options
     */
    paymentStatusOptions: [
        { value: "paid", label: "Paid" },
        { value: "unpaid", label: "Unpaid" },
        { value: "partial", label: "Partial" }
    ],
    
    /**
     * Common service type filter options
     */
    serviceTypeOptions: [
        { value: "standard", label: "Standard" },
        { value: "express", label: "Express" },
        { value: "same_day", label: "Same Day" }
    ],
    
    /**
     * Common stage filter options
     */
    stageOptions: [
        { value: "pickup", label: "Pickup" },
        { value: "in_transit", label: "In Transit" },
        { value: "delivered", label: "Delivered" }
    ],
    
    /**
     * Common user type filter options
     */
    userTypeOptions: [
        { value: "admin", label: "Admin" },
        { value: "client", label: "Client" },
        { value: "delivery_man", label: "Delivery Man" }
    ],
    
    /**
     * Get delivery routes filter configuration
     * @param {Array} deliveryMenOptions - Delivery men options for the filter
     * @returns {Array} - Filter configuration
     */
    getDeliveryRoutesFilters: function(deliveryMenOptions = []) {
        return [
            {
                name: "code",
                label: "Code",
                type: "text",
                placeholder: "Search by code"
            },
            {
                name: "stage",
                label: "Stage",
                type: "select",
                options: this.stageOptions
            },
            {
                name: "status",
                label: "Status",
                type: "select",
                options: this.statusOptions
            },
            {
                name: "route",
                label: "Route",
                type: "text",
                placeholder: "Search by route"
            },
            {
                name: "services",
                label: "Services",
                type: "select",
                options: this.serviceTypeOptions
            },
            {
                name: "payment_status",
                label: "Payment Status",
                type: "select",
                options: this.paymentStatusOptions
            },
            {
                name: "created_at_from",
                label: "Created From",
                type: "date"
            },
            {
                name: "created_at_to",
                label: "Created To",
                type: "date"
            },
            {
                name: "updated_at_from",
                label: "Updated From",
                type: "date"
            },
            {
                name: "updated_at_to",
                label: "Updated To",
                type: "date"
            },
            {
                name: "pickup_zone",
                label: "Pickup Zone",
                type: "text",
                placeholder: "Search by pickup zone"
            },
            {
                name: "delivery_zone",
                label: "Delivery Zone",
                type: "text",
                placeholder: "Search by delivery zone"
            },
            {
                name: "delivery_method",
                label: "Delivery Method",
                type: "select",
                options: this.serviceTypeOptions
            },
            {
                name: "pickup_phone",
                label: "Pickup Phone",
                type: "text",
                placeholder: "Search by phone"
            },
            {
                name: "deliveryman_id",
                label: "Delivery Man",
                type: "select",
                options: deliveryMenOptions
            }
        ];
    },
    
    /**
     * Get orders filter configuration
     * @param {Array} deliveryMenOptions - Delivery men options for the filter
     * @param {Array} clientOptions - Client options for the filter
     * @returns {Array} - Filter configuration
     */
    getOrdersFilters: function(deliveryMenOptions = [], clientOptions = []) {
        return [
            {
                name: "order_code",
                label: "Order Code",
                type: "text",
                placeholder: "Search by order code"
            },
            {
                name: "stage",
                label: "Stage",
                type: "select",
                options: this.stageOptions
            },
            {
                name: "status",
                label: "Status",
                type: "select",
                options: this.statusOptions
            },
            {
                name: "client_id",
                label: "Client",
                type: "select",
                options: clientOptions
            },
            {
                name: "deliveryman_id",
                label: "Delivery Man",
                type: "select",
                options: deliveryMenOptions
            },
            {
                name: "payment_status",
                label: "Payment Status",
                type: "select",
                options: this.paymentStatusOptions
            },
            {
                name: "payment_method",
                label: "Payment Method",
                type: "select",
                options: [
                    { value: "cash", label: "Cash" },
                    { value: "card", label: "Card" },
                    { value: "bank_transfer", label: "Bank Transfer" }
                ]
            },
            {
                name: "created_at_from",
                label: "Created From",
                type: "date"
            },
            {
                name: "created_at_to",
                label: "Created To",
                type: "date"
            },
            {
                name: "pickup_zone",
                label: "Pickup Zone",
                type: "text",
                placeholder: "Search by pickup zone"
            },
            {
                name: "delivery_zone",
                label: "Delivery Zone",
                type: "text",
                placeholder: "Search by delivery zone"
            },
            {
                name: "amount_from",
                label: "Amount From",
                type: "number",
                placeholder: "Min amount"
            },
            {
                name: "amount_to",
                label: "Amount To",
                type: "number",
                placeholder: "Max amount"
            }
        ];
    },
    
    /**
     * Get users filter configuration
     * @returns {Array} - Filter configuration
     */
    getUsersFilters: function() {
        return [
            {
                name: "name",
                label: "Name",
                type: "text",
                placeholder: "Search by name"
            },
            {
                name: "email",
                label: "Email",
                type: "text",
                placeholder: "Search by email"
            },
            {
                name: "phone",
                label: "Phone",
                type: "text",
                placeholder: "Search by phone"
            },
            {
                name: "user_type",
                label: "User Type",
                type: "select",
                options: this.userTypeOptions
            },
            {
                name: "status",
                label: "Status",
                type: "select",
                options: [
                    { value: "1", label: "Active" },
                    { value: "0", label: "Inactive" }
                ]
            },
            {
                name: "created_at_from",
                label: "Created From",
                type: "date"
            },
            {
                name: "created_at_to",
                label: "Created To",
                type: "date"
            }
        ];
    },
    
    /**
     * Get delivery men filter configuration
     * @returns {Array} - Filter configuration
     */
    getDeliveryMenFilters: function() {
        return [
            {
                name: "name",
                label: "Name",
                type: "text",
                placeholder: "Search by name"
            },
            {
                name: "email",
                label: "Email",
                type: "text",
                placeholder: "Search by email"
            },
            {
                name: "phone",
                label: "Phone",
                type: "text",
                placeholder: "Search by phone"
            },
            {
                name: "status",
                label: "Status",
                type: "select",
                options: [
                    { value: "1", label: "Active" },
                    { value: "0", label: "Inactive" }
                ]
            },
            {
                name: "is_verified",
                label: "Verification",
                type: "select",
                options: [
                    { value: "1", label: "Verified" },
                    { value: "0", label: "Unverified" }
                ]
            },
            {
                name: "created_at_from",
                label: "Created From",
                type: "date"
            },
            {
                name: "created_at_to",
                label: "Created To",
                type: "date"
            }
        ];
    }
};

// Make available globally
window.ShadcnFilterConfigs = ShadcnFilterConfigs;
