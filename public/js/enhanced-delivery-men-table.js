// Enhanced Delivery Men Table with Advanced Filtering
document.addEventListener("DOMContentLoaded", function () {
    // Initialize the table
    initDeliveryMenTable();
});

async function initDeliveryMenTable() {
    // Check if the table container exists
    const tableContainer = document.getElementById("delivery-men-table");
    if (!tableContainer) return;

    try {
        // Get filter configurations from common config
        const filterConfigs = ShadcnFilterConfigs.getDeliveryMenFilters();
        
        // Define column configurations
        const columnConfigs = [
            {
                data: "id",
                title: "ID",
                width: "50px"
            },
            {
                data: "name",
                title: "Name"
            },
            {
                data: "email",
                title: "Email"
            },
            {
                data: "phone",
                title: "Phone"
            },
            {
                data: "status",
                title: "Status",
                render: function(data, type, row) {
                    return row.status_badge;
                }
            },
            {
                data: "is_verified",
                title: "Verification",
                render: function(data, type, row) {
                    return row.verification_badge;
                }
            },
            {
                data: "active_orders",
                title: "Active Orders"
            },
            {
                data: "completed_orders",
                title: "Completed Orders"
            },
            {
                data: "created_at",
                title: "Created At"
            },
            {
                data: "actions",
                title: "Actions",
                orderable: false,
                searchable: false,
                width: "120px",
                className: "text-center"
            }
        ];
        
        // Initialize the table component
        const table = new ShadcnTable({
            containerId: "delivery-men-table",
            apiUrl: "/api/users?user_type=delivery_man",
            columns: columnConfigs,
            filterConfig: {
                filters: filterConfigs,
                collapsible: true,
                collapsed: false,
                saveState: true,
                storageKey: "delivery-men-filter-state",
                autoApply: false
            },
            onRowClick: function(data) {
                // Optional: Handle row click
                console.log("Row clicked:", data);
            }
        });
        
    } catch (error) {
        console.error("Error initializing delivery men table:", error);
    }
}
