/**
 * Orders Dashboard Visualizations
 *
 * This file contains functionality for data visualizations on the orders dashboard
 * using Chart.js.
 */

document.addEventListener("DOMContentLoaded", function () {
    // Only initialize visualizations on the dashboard page, not on the orders page
    if (
        window.location.pathname !== "/order" &&
        window.location.pathname !== "/order/shadcn" &&
        !window.location.pathname.includes("/order/")
    ) {
        // Initialize visualizations if Chart.js is available
        if (typeof Chart !== "undefined") {
            initOrdersVisualizations();
        } else {
            console.warn(
                "Chart.js is not available. Data visualizations will not work."
            );
        }
    }
});

/**
 * Initialize orders visualizations
 */
function initOrdersVisualizations() {
    // Add visualization container if it doesn't exist
    if (!document.getElementById("orders-visualization")) {
        addVisualizationContainer();
    }

    // Fetch order statistics
    fetchOrderStatistics()
        .then((data) => {
            // Create charts
            createStatusChart(data.statusCounts);
            createTimelineChart(data.timeline);
            createPaymentChart(data.paymentCounts);
        })
        .catch((error) => {
            console.error("Error fetching order statistics:", error);
        });
}

/**
 * Add visualization container to the page
 */
function addVisualizationContainer() {
    const container = document.createElement("div");
    container.id = "orders-visualization";
    container.className =
        "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6";
    container.innerHTML = `
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Orders by Status</h3>
            <div class="h-64">
                <canvas id="status-chart"></canvas>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Orders Timeline</h3>
            <div class="h-64">
                <canvas id="timeline-chart"></canvas>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Payment Status</h3>
            <div class="h-64">
                <canvas id="payment-chart"></canvas>
            </div>
        </div>
    `;

    // Insert before the filter container
    const filterContainer = document.querySelector(".shadcn-filter-container");
    if (filterContainer) {
        filterContainer.parentNode.insertBefore(container, filterContainer);
    }
}

/**
 * Fetch order statistics from the server
 *
 * @returns {Promise} - A promise that resolves with the order statistics
 */
function fetchOrderStatistics() {
    return fetch("/api/orders/statistics", {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Failed to fetch order statistics");
            }
            return response.json();
        })
        .then((data) => {
            return data;
        })
        .catch((error) => {
            console.error("Error fetching statistics:", error);

            // Return mock data for development
            return {
                statusCounts: {
                    pending: 15,
                    in_progress: 8,
                    completed: 25,
                    cancelled: 5,
                    courier_assigned: 7,
                    courier_accepted: 4,
                    courier_picked_up: 3,
                    courier_departed: 2,
                },
                timeline: {
                    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                    data: [12, 19, 15, 22, 30, 25],
                },
                paymentCounts: {
                    paid: 35,
                    unpaid: 18,
                    partial: 7,
                    refunded: 3,
                },
            };
        });
}

/**
 * Create a chart for order statuses
 *
 * @param {Object} statusCounts - Object with status counts
 */
function createStatusChart(statusCounts) {
    const ctx = document.getElementById("status-chart");
    if (!ctx) return;

    // Define colors for each status
    const statusColors = {
        pending: "#fbbf24",
        in_progress: "#60a5fa",
        completed: "#34d399",
        cancelled: "#f87171",
        courier_assigned: "#a78bfa",
        courier_accepted: "#818cf8",
        courier_arrived: "#60a5fa",
        courier_picked_up: "#f472b6",
        courier_departed: "#fb923c",
        draft: "#9ca3af",
    };

    // Format status labels
    const statusLabels = {
        pending: "Pending",
        in_progress: "In Progress",
        completed: "Completed",
        cancelled: "Cancelled",
        courier_assigned: "Assigned",
        courier_accepted: "Accepted",
        courier_arrived: "Arrived",
        courier_picked_up: "Picked Up",
        courier_departed: "Departed",
        draft: "Draft",
    };

    // Prepare data for the chart
    const labels = Object.keys(statusCounts).map(
        (status) => statusLabels[status] || status
    );
    const data = Object.values(statusCounts);
    const backgroundColor = Object.keys(statusCounts).map(
        (status) => statusColors[status] || "#9ca3af"
    );

    // Create the chart
    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: labels,
            datasets: [
                {
                    data: data,
                    backgroundColor: backgroundColor,
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "right",
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 11,
                        },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || "";
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce(
                                (a, b) => a + b,
                                0
                            );
                            const percentage = Math.round(
                                (value / total) * 100
                            );
                            return `${label}: ${value} (${percentage}%)`;
                        },
                    },
                },
            },
        },
    });
}

/**
 * Create a timeline chart for orders
 *
 * @param {Object} timeline - Object with timeline data
 */
function createTimelineChart(timeline) {
    const ctx = document.getElementById("timeline-chart");
    if (!ctx) return;

    // Create the chart
    new Chart(ctx, {
        type: "line",
        data: {
            labels: timeline.labels,
            datasets: [
                {
                    label: "Orders",
                    data: timeline.data,
                    borderColor: "#3b82f6",
                    backgroundColor: "rgba(59, 130, 246, 0.1)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    });
}

/**
 * Create a chart for payment statuses
 *
 * @param {Object} paymentCounts - Object with payment status counts
 */
function createPaymentChart(paymentCounts) {
    const ctx = document.getElementById("payment-chart");
    if (!ctx) return;

    // Define colors for each payment status
    const paymentColors = {
        paid: "#34d399",
        unpaid: "#f87171",
        partial: "#fbbf24",
        refunded: "#a78bfa",
    };

    // Format payment labels
    const paymentLabels = {
        paid: "Paid",
        unpaid: "Unpaid",
        partial: "Partial",
        refunded: "Refunded",
    };

    // Prepare data for the chart
    const labels = Object.keys(paymentCounts).map(
        (status) => paymentLabels[status] || status
    );
    const data = Object.values(paymentCounts);
    const backgroundColor = Object.keys(paymentCounts).map(
        (status) => paymentColors[status] || "#9ca3af"
    );

    // Create the chart
    new Chart(ctx, {
        type: "pie",
        data: {
            labels: labels,
            datasets: [
                {
                    data: data,
                    backgroundColor: backgroundColor,
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "right",
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 11,
                        },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || "";
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce(
                                (a, b) => a + b,
                                0
                            );
                            const percentage = Math.round(
                                (value / total) * 100
                            );
                            return `${label}: ${value} (${percentage}%)`;
                        },
                    },
                },
            },
        },
    });
}
