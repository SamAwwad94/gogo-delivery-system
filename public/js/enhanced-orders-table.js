// Enhanced Orders Table with Advanced Filtering
document.addEventListener("DOMContentLoaded", function () {
    // Initialize the table
    initOrdersTable();
});

async function initOrdersTable() {
    // Check if the table container exists
    const tableContainer = document.getElementById("orders-table");
    if (!tableContainer) return;

    try {
        // Load delivery men and clients for the filter
        const [deliveryMen, clients] = await Promise.all([
            loadDeliveryMen(),
            loadClients(),
        ]);

        // Prepare options
        const deliveryMenOptions = deliveryMen.map((dm) => ({
            value: dm.id,
            label: dm.name,
        }));

        const clientOptions = clients.map((client) => ({
            value: client.id,
            label: client.name,
        }));

        // Get filter configurations from common config
        const filterConfigs = [
            {
                name: "id",
                label: "Order ID",
                type: "text",
                placeholder: "Search by order ID",
            },
            {
                name: "status",
                label: "Status",
                type: "select",
                options: [
                    { value: "draft", label: "Draft" },
                    { value: "create", label: "Created" },
                    { value: "courier_assigned", label: "Assigned" },
                    { value: "courier_accepted", label: "Accepted" },
                    { value: "courier_arrived", label: "Arrived" },
                    { value: "courier_picked_up", label: "Picked Up" },
                    { value: "courier_departed", label: "Departed" },
                    { value: "completed", label: "Delivered" },
                    { value: "cancelled", label: "Cancelled" },
                ],
            },
            {
                name: "client_id",
                label: "Customer",
                type: "select",
                options: clientOptions,
            },
            {
                name: "phone",
                label: "Phone",
                type: "text",
                placeholder: "Search by phone",
            },
            {
                name: "pickup_point",
                label: "Pickup Location",
                type: "text",
                placeholder: "Search by location",
            },
            {
                name: "delivery_point",
                label: "Delivery Location",
                type: "text",
                placeholder: "Search by location",
            },
            {
                name: "payment_status",
                label: "Payment Status",
                type: "select",
                options: [
                    { value: "paid", label: "Paid" },
                    { value: "unpaid", label: "Unpaid" },
                    { value: "partial", label: "Partial" },
                ],
            },
            {
                name: "date_range",
                label: "Date Range",
                type: "daterange",
                placeholder: "Select date range",
            },
        ];

        // Define column configurations
        const columnConfigs = [
            {
                data: "id",
                title: "ID",
                width: "50px",
            },
            {
                data: "created_at",
                title: "Date",
            },
            {
                data: "status",
                title: "Status",
                render: function (data, type, row) {
                    const statusClasses = {
                        draft: "bg-gray-100 text-gray-800 ring-gray-200",
                        create: "bg-blue-100 text-blue-800 ring-blue-200",
                        courier_assigned:
                            "bg-yellow-100 text-yellow-800 ring-yellow-200",
                        courier_accepted:
                            "bg-indigo-100 text-indigo-800 ring-indigo-200",
                        courier_arrived:
                            "bg-purple-100 text-purple-800 ring-purple-200",
                        courier_picked_up:
                            "bg-pink-100 text-pink-800 ring-pink-200",
                        courier_departed:
                            "bg-orange-100 text-orange-800 ring-orange-200",
                        completed: "bg-green-100 text-green-800 ring-green-200",
                        cancelled: "bg-red-100 text-red-800 ring-red-200",
                    };

                    const statusClass =
                        statusClasses[data] ||
                        "bg-gray-100 text-gray-800 ring-gray-200";
                    const statusLabel =
                        {
                            draft: "Draft",
                            create: "Created",
                            courier_assigned: "Assigned",
                            courier_accepted: "Accepted",
                            courier_arrived: "Arrived",
                            courier_picked_up: "Picked Up",
                            courier_departed: "Departed",
                            completed: "Delivered",
                            cancelled: "Cancelled",
                        }[data] || data;

                    return `<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${statusClass}">${statusLabel}</span>`;
                },
            },
            {
                data: "client_name",
                title: "Customer",
            },
            {
                data: "phone",
                title: "Phone",
            },
            {
                data: "pickup_point",
                title: "Pickup Location",
                render: function (data, type, row) {
                    if (
                        typeof data === "object" &&
                        data !== null &&
                        data.address
                    ) {
                        return data.address;
                    }
                    return data || "N/A";
                },
            },
            {
                data: "delivery_point",
                title: "Delivery Location",
                render: function (data, type, row) {
                    if (
                        typeof data === "object" &&
                        data !== null &&
                        data.address
                    ) {
                        return data.address;
                    }
                    return data || "N/A";
                },
            },
            {
                data: "payment_status",
                title: "Payment Status",
                render: function (data, type, row) {
                    const paymentClasses = {
                        paid: "bg-green-100 text-green-800 ring-green-200",
                        unpaid: "bg-red-100 text-red-800 ring-red-200",
                        partial:
                            "bg-yellow-100 text-yellow-800 ring-yellow-200",
                    };

                    const paymentClass =
                        paymentClasses[data] ||
                        "bg-gray-100 text-gray-800 ring-gray-200";

                    return `<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${paymentClass}">${
                        data.charAt(0).toUpperCase() + data.slice(1)
                    }</span>`;
                },
            },
            {
                data: "total_amount",
                title: "Amount",
                render: function (data, type, row) {
                    const currency = row.currency || "USD";
                    return new Intl.NumberFormat("en-US", {
                        style: "currency",
                        currency: currency,
                    }).format(data);
                },
            },
            {
                data: "actions",
                title: "Actions",
                orderable: false,
                searchable: false,
                width: "120px",
                className: "text-center",
                render: function (data, type, row) {
                    return `
                        <div class="flex space-x-2 justify-center">
                            <a href="/order/${row.id}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/order/${row.id}/edit" class="text-green-600 hover:text-green-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteOrder(${row.id})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                },
            },
        ];

        // Initialize the table component
        const table = new ShadcnTable({
            containerId: "orders-table",
            apiUrl: "/api/orders",
            columns: columnConfigs,
            filterConfig: {
                filters: filterConfigs,
                collapsible: true,
                collapsed: false,
                saveState: true,
                storageKey: "orders-filter-state",
                autoApply: true,
            },
            viewToggle: {
                enabled: true,
                options: [
                    { value: "table", label: "Table", icon: "table" },
                    { value: "map", label: "Map", icon: "map" },
                ],
                defaultView: "table",
                onViewChange: function (view) {
                    if (view === "map") {
                        window.location.href = "/order/map";
                    }
                },
            },
            pagination: {
                enabled: true,
                perPage: 10,
                perPageOptions: [10, 25, 50, 100],
            },
            exportOptions: {
                enabled: true,
                formats: ["csv", "excel", "pdf"],
            },
            responsive: true,
            onRowClick: function (data) {
                window.location.href = `/order/${data.id}`;
            },
        });
    } catch (error) {
        console.error("Error initializing orders table:", error);
    }
}

async function loadDeliveryMen() {
    try {
        console.log("Fetching delivery men from API");
        // Make AJAX request to get delivery men
        const response = await fetch("/api/delivery-men");
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log("Delivery men data received:", data);
        return data;
    } catch (error) {
        console.error("Error loading delivery men:", error);
        return [];
    }
}

async function loadClients() {
    try {
        console.log("Fetching clients from API");
        // Make AJAX request to get clients
        const response = await fetch("/api/clients");
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log("Clients data received:", data);
        return data;
    } catch (error) {
        console.error("Error loading clients:", error);
        return [];
    }
}

function deleteOrder(id) {
    if (confirm("Are you sure you want to delete this order?")) {
        fetch(`/order/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // Refresh the table
                    document
                        .getElementById("orders-table")
                        .dispatchEvent(new CustomEvent("refresh"));
                } else {
                    alert("Error deleting order");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
            });
    }
}
