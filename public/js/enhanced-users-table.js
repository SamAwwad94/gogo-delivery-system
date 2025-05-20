// Enhanced Users Table with Advanced Filtering
document.addEventListener("DOMContentLoaded", function () {
    // Initialize the table
    initUsersTable();
});

async function initUsersTable() {
    // Check if the table container exists
    const tableContainer = document.getElementById("users-table");
    if (!tableContainer) return;

    try {
        // Get filter configurations from common config
        const filterConfigs = ShadcnFilterConfigs.getUsersFilters();
        
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
                data: "user_type_label",
                title: "User Type"
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
            containerId: "users-table",
            apiUrl: "/api/users",
            columns: columnConfigs,
            filterConfig: {
                filters: filterConfigs,
                collapsible: true,
                collapsed: false,
                saveState: true,
                storageKey: "users-filter-state",
                autoApply: false
            },
            onRowClick: function(data) {
                // Optional: Handle row click
                console.log("Row clicked:", data);
            }
        });
        
    } catch (error) {
        console.error("Error initializing users table:", error);
    }
}
