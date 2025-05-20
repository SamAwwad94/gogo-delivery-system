// Enhanced Delivery Routes Table with Advanced Filtering
document.addEventListener("DOMContentLoaded", function () {
    // Initialize the table
    initDeliveryRoutesTable();
});

async function initDeliveryRoutesTable() {
    // Check if the table container exists
    const tableContainer = document.getElementById("delivery-routes-table");
    if (!tableContainer) return;

    try {
        // Load delivery men for the filter
        const deliveryMen = await loadDeliveryMen();

        // Prepare delivery men options
        const deliveryMenOptions = deliveryMen.map((dm) => ({
            value: dm.id,
            label: dm.name,
        }));

        // Get filter configurations from common config
        const filterConfigs =
            ShadcnFilterConfigs.getDeliveryRoutesFilters(deliveryMenOptions);

        // Define column configurations
        const columnConfigs = [
            {
                data: "id",
                title: "ID",
                width: "50px",
            },
            {
                data: "code",
                title: "Code",
            },
            {
                data: "stage",
                title: "Stage",
            },
            {
                data: "status",
                title: "Status",
                render: function (data, type, row) {
                    return row.status_badge;
                },
            },
            {
                data: "name",
                title: "Route",
            },
            {
                data: "service_type",
                title: "Services",
            },
            {
                data: "payment_status",
                title: "Payment Status",
            },
            {
                data: "created_at",
                title: "Created At",
            },
            {
                data: "updated_at",
                title: "Updated At",
            },
            {
                data: "start_location",
                title: "Pickup Zone",
            },
            {
                data: "end_location",
                title: "Delivery Zone",
            },
            {
                data: "delivery_method",
                title: "Delivery Method",
            },
            {
                data: "delivery_man",
                title: "Delivery Man",
            },
            {
                data: "orders_count",
                title: "Orders",
            },
            {
                data: "actions",
                title: "Actions",
                orderable: false,
                searchable: false,
                width: "120px",
                className: "text-center",
            },
        ];

        // Initialize the table component
        const table = new ShadcnTable({
            containerId: "delivery-routes-table",
            apiUrl: "/api/delivery-routes",
            columns: columnConfigs,
            filterConfig: {
                filters: filterConfigs,
            },
            onRowClick: function (data) {
                // Optional: Handle row click
                console.log("Row clicked:", data);
            },
        });
    } catch (error) {
        console.error("Error initializing delivery routes table:", error);
    }
}

async function loadDeliveryMen() {
    try {
        // Make AJAX request to get delivery men
        const response = await fetch("/api/delivery-men");
        const data = await response.json();
        return data;
    } catch (error) {
        console.error("Error loading delivery men:", error);
        return [];
    }
}
