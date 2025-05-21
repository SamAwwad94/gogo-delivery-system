/**
 * Orders Table Enhancements
 *
 * Additional features for the Orders table:
 * - Autocomplete for location fields
 * - Drag-and-drop column reordering
 * - Saved filters/views
 * - Lazy loading for large datasets
 * - Server-side pagination
 * - Caching for frequently accessed data
 * - Keyboard shortcuts
 * - Toast notifications
 * - Inline editing
 */

document.addEventListener("DOMContentLoaded", function () {
    // Initialize enhancements
    initEnhancements();
});

/**
 * Initialize all enhancements
 */
function initEnhancements() {
    // Check if we're on the orders page
    if (
        !document.querySelector(".shadcn-filter-container") ||
        !document.querySelector("table")
    ) {
        return;
    }

    // Initialize location autocomplete
    initLocationAutocomplete();

    // Initialize drag-and-drop column reordering
    initDragAndDropColumns();

    // Initialize saved filters
    initSavedFilters();

    // Initialize lazy loading
    initLazyLoading();

    // Initialize keyboard shortcuts
    initKeyboardShortcuts();

    // Initialize toast notifications
    initToastNotifications();

    // Initialize inline editing
    initInlineEditing();
}

/**
 * Initialize autocomplete for location fields
 */
function initLocationAutocomplete() {
    // Check if the required libraries are loaded
    if (
        typeof google === "undefined" ||
        typeof google.maps === "undefined" ||
        typeof google.maps.places === "undefined"
    ) {
        // Load Google Maps API with Places library - use a valid API key in production
        loadScript(
            "https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&libraries=places&callback=initLocationAutocomplete&loading=async"
        );
        return;
    }

    // Initialize autocomplete for pickup location
    const pickupInput = document.getElementById("pickup_location");
    if (pickupInput) {
        const pickupAutocomplete = new google.maps.places.Autocomplete(
            pickupInput
        );
        pickupAutocomplete.addListener("place_changed", function () {
            const place = pickupAutocomplete.getPlace();
            if (!place.geometry) {
                console.log(
                    "No details available for input: '" + place.name + "'"
                );
                return;
            }

            // Trigger change event to update filters
            pickupInput.dispatchEvent(new Event("change"));
        });
    }

    // Initialize autocomplete for delivery location
    const deliveryInput = document.getElementById("delivery_location");
    if (deliveryInput) {
        const deliveryAutocomplete = new google.maps.places.Autocomplete(
            deliveryInput
        );
        deliveryAutocomplete.addListener("place_changed", function () {
            const place = deliveryAutocomplete.getPlace();
            if (!place.geometry) {
                console.log(
                    "No details available for input: '" + place.name + "'"
                );
                return;
            }

            // Trigger change event to update filters
            deliveryInput.dispatchEvent(new Event("change"));
        });
    }
}

/**
 * Initialize drag-and-drop column reordering
 */
function initDragAndDropColumns() {
    // Check if Sortable.js is loaded
    if (typeof Sortable === "undefined") {
        // Load Sortable.js
        loadScript(
            "https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js",
            initDragAndDropColumns
        );
        return;
    }

    const table = document.querySelector("table");
    if (!table) return;

    const thead = table.querySelector("thead tr");
    if (!thead) return;

    // Initialize Sortable on the table header
    new Sortable(thead, {
        animation: 150,
        handle: "th",
        onEnd: function (evt) {
            // Get the new column order
            const newOrder = Array.from(thead.querySelectorAll("th")).map(
                (th) => {
                    return th.dataset.column || "";
                }
            );

            // Save the new column order to localStorage
            localStorage.setItem(
                "orders-column-order",
                JSON.stringify(newOrder)
            );

            // Reorder the table body columns
            reorderTableColumns(newOrder);
        },
    });

    // Apply saved column order if available
    const savedOrder = localStorage.getItem("orders-column-order");
    if (savedOrder) {
        try {
            const columnOrder = JSON.parse(savedOrder);
            reorderTableColumns(columnOrder);
        } catch (error) {
            console.error("Error applying saved column order:", error);
        }
    }
}

/**
 * Reorder table columns based on the given order
 *
 * @param {Array} columnOrder - Array of column names in the desired order
 */
function reorderTableColumns(columnOrder) {
    const table = document.querySelector("table");
    if (!table) return;

    const rows = table.querySelectorAll("tr");
    if (!rows.length) return;

    // For each row, reorder the cells according to the column order
    rows.forEach((row) => {
        const cells = Array.from(row.children);
        const parent = row.parentNode;

        // Remove all cells
        cells.forEach((cell) => cell.remove());

        // Add cells back in the new order
        columnOrder.forEach((column) => {
            const cell = cells.find((c) => c.dataset.column === column);
            if (cell) {
                row.appendChild(cell);
            }
        });
    });
}

/**
 * Initialize saved filters functionality
 */
function initSavedFilters() {
    const filterForm = document.getElementById("order-filter-form");
    if (!filterForm) return;

    // Add saved filters UI
    const filterHeader = document.querySelector(".shadcn-filter-header");
    if (!filterHeader) return;

    const savedFiltersContainer = document.createElement("div");
    savedFiltersContainer.className = "saved-filters-container ml-4";
    savedFiltersContainer.innerHTML = `
        <div class="dropdown inline-block relative">
            <button class="shadcn-button shadcn-button-outline text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Saved Filters
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div class="dropdown-menu hidden absolute z-10 bg-white shadow-lg rounded-md border border-gray-200 mt-1 py-1 w-48">
                <div class="saved-filters-list px-2 py-1 max-h-48 overflow-y-auto">
                    <div class="text-sm text-gray-500 italic text-center py-2">No saved filters</div>
                </div>
                <div class="border-t border-gray-200 mt-1 pt-1 px-2 py-1">
                    <button id="save-current-filter" class="w-full text-left text-sm px-2 py-1 hover:bg-gray-100 rounded flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Save Current Filter
                    </button>
                </div>
            </div>
        </div>
    `;

    // Insert saved filters container after the filter title
    const filterTitle = filterHeader.querySelector("h3");
    if (filterTitle) {
        filterTitle.parentNode.after(savedFiltersContainer);
    } else {
        filterHeader.prepend(savedFiltersContainer);
    }

    // Toggle dropdown
    const dropdownButton = savedFiltersContainer.querySelector("button");
    const dropdownMenu = savedFiltersContainer.querySelector(".dropdown-menu");

    dropdownButton.addEventListener("click", function () {
        dropdownMenu.classList.toggle("hidden");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
        if (!savedFiltersContainer.contains(event.target)) {
            dropdownMenu.classList.add("hidden");
        }
    });

    // Save current filter
    const saveFilterButton = document.getElementById("save-current-filter");
    if (saveFilterButton) {
        saveFilterButton.addEventListener("click", function () {
            saveCurrentFilter();
        });
    }

    // Load saved filters
    loadSavedFilters();
}

/**
 * Save current filter settings
 */
function saveCurrentFilter() {
    const filterForm = document.getElementById("order-filter-form");
    if (!filterForm) return;

    // Get current filter values
    const formData = new FormData(filterForm);
    const filterData = {};
    let hasValues = false;

    for (const [key, value] of formData.entries()) {
        if (value) {
            filterData[key] = value;
            hasValues = true;
        }
    }

    if (!hasValues) {
        showToast("No filter values to save", "warning");
        return;
    }

    // Prompt for filter name
    const filterName = prompt("Enter a name for this filter:");
    if (!filterName) return;

    // Get existing saved filters
    let savedFilters = JSON.parse(
        localStorage.getItem("orders-saved-filters") || "[]"
    );

    // Add new filter
    savedFilters.push({
        name: filterName,
        data: filterData,
        created: new Date().toISOString(),
    });

    // Save to localStorage
    localStorage.setItem("orders-saved-filters", JSON.stringify(savedFilters));

    // Reload saved filters list
    loadSavedFilters();

    // Show success message
    showToast(`Filter "${filterName}" saved successfully`, "success");
}

/**
 * Load saved filters from localStorage
 */
function loadSavedFilters() {
    const savedFiltersList = document.querySelector(".saved-filters-list");
    if (!savedFiltersList) return;

    // Get saved filters
    const savedFilters = JSON.parse(
        localStorage.getItem("orders-saved-filters") || "[]"
    );

    // Clear the list
    savedFiltersList.innerHTML = "";

    if (savedFilters.length === 0) {
        savedFiltersList.innerHTML =
            '<div class="text-sm text-gray-500 italic text-center py-2">No saved filters</div>';
        return;
    }

    // Add each filter to the list
    savedFilters.forEach((filter, index) => {
        const filterItem = document.createElement("div");
        filterItem.className =
            "saved-filter-item flex items-center justify-between py-1 px-2 hover:bg-gray-100 rounded cursor-pointer";
        filterItem.innerHTML = `
            <span class="filter-name text-sm">${filter.name}</span>
            <div class="filter-actions flex items-center">
                <button class="apply-filter text-blue-600 hover:text-blue-800 p-1" data-index="${index}" title="Apply Filter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                </button>
                <button class="delete-filter text-red-600 hover:text-red-800 p-1" data-index="${index}" title="Delete Filter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        `;

        savedFiltersList.appendChild(filterItem);

        // Add event listeners
        const applyButton = filterItem.querySelector(".apply-filter");
        const deleteButton = filterItem.querySelector(".delete-filter");

        applyButton.addEventListener("click", function () {
            applyFilter(parseInt(this.dataset.index));
        });

        deleteButton.addEventListener("click", function () {
            deleteFilter(parseInt(this.dataset.index));
        });
    });
}

/**
 * Apply a saved filter
 *
 * @param {number} index - Index of the filter in the saved filters array
 */
function applyFilter(index) {
    const savedFilters = JSON.parse(
        localStorage.getItem("orders-saved-filters") || "[]"
    );
    if (!savedFilters[index]) return;

    const filter = savedFilters[index];
    const filterForm = document.getElementById("order-filter-form");
    if (!filterForm) return;

    // Reset form
    filterForm.reset();

    // Apply filter values
    Object.entries(filter.data).forEach(([key, value]) => {
        const input = filterForm.querySelector(`[name="${key}"]`);
        if (input) {
            input.value = value;

            // Trigger change event for select2 dropdowns
            if (input.tagName === "SELECT" && $.fn.select2) {
                $(input).trigger("change");
            }
        }
    });

    // Submit the form
    filterForm.dispatchEvent(new Event("submit"));

    // Close dropdown
    document.querySelector(".dropdown-menu").classList.add("hidden");

    // Show success message
    showToast(`Filter "${filter.name}" applied`, "success");
}

/**
 * Delete a saved filter
 *
 * @param {number} index - Index of the filter in the saved filters array
 */
function deleteFilter(index) {
    const savedFilters = JSON.parse(
        localStorage.getItem("orders-saved-filters") || "[]"
    );
    if (!savedFilters[index]) return;

    const filterName = savedFilters[index].name;

    // Confirm deletion
    if (
        !confirm(`Are you sure you want to delete the filter "${filterName}"?`)
    ) {
        return;
    }

    // Remove filter
    savedFilters.splice(index, 1);

    // Save to localStorage
    localStorage.setItem("orders-saved-filters", JSON.stringify(savedFilters));

    // Reload saved filters list
    loadSavedFilters();

    // Show success message
    showToast(`Filter "${filterName}" deleted`, "success");
}

/**
 * Initialize lazy loading for large datasets
 */
function initLazyLoading() {
    // Check if Intersection Observer API is supported
    if (!("IntersectionObserver" in window)) {
        console.warn(
            "Intersection Observer API is not supported in this browser"
        );
        return;
    }

    const table = document.querySelector("table");
    if (!table) return;

    // Add a loading indicator at the bottom of the table
    const loadingRow = document.createElement("tr");
    loadingRow.id = "lazy-loading-indicator";
    loadingRow.className = "hidden";
    loadingRow.innerHTML = `
        <td colspan="10" class="text-center py-4">
            <div class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Loading more orders...</span>
            </div>
        </td>
    `;

    table.querySelector("tbody").appendChild(loadingRow);

    // Create an observer for the loading indicator
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    // Load more data when the indicator is visible
                    loadMoreOrders();
                }
            });
        },
        {
            root: null,
            rootMargin: "0px",
            threshold: 0.1,
        }
    );

    // Start observing the loading indicator
    observer.observe(loadingRow);
}

/**
 * Load more orders for lazy loading
 */
function loadMoreOrders() {
    const loadingIndicator = document.getElementById("lazy-loading-indicator");
    if (!loadingIndicator) return;

    // Show loading indicator
    loadingIndicator.classList.remove("hidden");

    // Get current page and filters
    const currentPage = parseInt(
        localStorage.getItem("orders-current-page") || "1"
    );
    const nextPage = currentPage + 1;

    // Get filter values
    const filterForm = document.getElementById("order-filter-form");
    const formData = new FormData(filterForm);
    const searchParams = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
        if (value) {
            searchParams.append(key, value);
        }
    }

    // Add page parameter
    searchParams.append("page", nextPage);

    // Fetch more orders
    fetch(`${window.location.pathname}?${searchParams.toString()}`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "text/html, application/json",
        },
    })
        .then((response) => {
            // Check if the response is JSON or HTML
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return response.json().then((data) => ({ isJson: true, data }));
            } else {
                return response
                    .text()
                    .then((html) => ({ isJson: false, html }));
            }
        })
        .then((result) => {
            // Hide loading indicator
            loadingIndicator.classList.add("hidden");

            if (result.isJson) {
                // Handle JSON response
                const data = result.data;
                if (
                    data.orders &&
                    data.orders.data &&
                    data.orders.data.length > 0
                ) {
                    // Append new orders to the table
                    appendOrdersToTable(data.orders.data);

                    // Update current page
                    localStorage.setItem("orders-current-page", nextPage);

                    // If we've reached the last page, stop observing
                    if (nextPage >= data.orders.last_page) {
                        const observer = new IntersectionObserver(() => {});
                        observer.unobserve(loadingIndicator);
                        loadingIndicator.remove();
                    }
                } else {
                    // No more orders, stop observing
                    const observer = new IntersectionObserver(() => {});
                    observer.unobserve(loadingIndicator);
                    loadingIndicator.remove();
                }
            } else {
                // Handle HTML response - parse the HTML to extract the table rows
                const parser = new DOMParser();
                const doc = parser.parseFromString(result.html, "text/html");
                const rows = doc.querySelectorAll(
                    "tbody tr:not(#lazy-loading-indicator)"
                );

                if (rows.length > 0) {
                    // Append the rows to the table
                    const tbody = document.querySelector("table tbody");
                    if (tbody) {
                        rows.forEach((row) => {
                            tbody.insertBefore(
                                row.cloneNode(true),
                                loadingIndicator
                            );
                        });
                    }

                    // Update current page
                    localStorage.setItem("orders-current-page", nextPage);
                } else {
                    // No more orders, stop observing
                    const observer = new IntersectionObserver(() => {});
                    observer.unobserve(loadingIndicator);
                    loadingIndicator.remove();
                }

                // Reinitialize bulk actions
                initBulkActions();
            }
        })
        .catch((error) => {
            console.error("Error loading more orders:", error);
            loadingIndicator.classList.add("hidden");
        });
}

/**
 * Append orders to the table
 *
 * @param {Array} orders - Array of order objects to append
 */
function appendOrdersToTable(orders) {
    const table = document.querySelector("table");
    if (!table) return;

    const tbody = table.querySelector("tbody");
    if (!tbody) return;

    // Get the loading indicator to insert before it
    const loadingIndicator = document.getElementById("lazy-loading-indicator");

    // Create a document fragment to improve performance
    const fragment = document.createDocumentFragment();

    orders.forEach((order) => {
        const row = document.createElement("tr");
        row.className = "border-b border-border hover:bg-muted/50";

        // Add checkbox cell
        const checkboxCell = document.createElement("td");
        checkboxCell.className = "p-2 text-center align-middle";
        checkboxCell.innerHTML = `<input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-primary focus:ring-primary" value="${order.id}">`;
        row.appendChild(checkboxCell);

        // Add order ID cell
        const idCell = document.createElement("td");
        idCell.className = "p-4 align-middle";
        idCell.setAttribute("data-label", "Order ID");
        idCell.textContent = order.id;
        row.appendChild(idCell);

        // Add date cell
        const dateCell = document.createElement("td");
        dateCell.className = "p-4 align-middle";
        dateCell.setAttribute("data-label", "Date");
        dateCell.textContent = new Date(order.created_at).toLocaleDateString(
            "en-US",
            { month: "short", day: "numeric", year: "numeric" }
        );
        row.appendChild(dateCell);

        // Add status cell
        const statusCell = document.createElement("td");
        statusCell.className = "p-4 align-middle";
        statusCell.setAttribute("data-label", "Status");

        // Determine status class and label
        let statusClass = "";
        let statusLabel = "";

        switch (order.status) {
            case "delivered":
            case "completed":
                statusClass = "bg-green-100 text-green-800 ring-green-200";
                statusLabel = "Delivered";
                break;
            case "pending":
            case "create":
                statusClass = "bg-yellow-100 text-yellow-800 ring-yellow-200";
                statusLabel = order.status === "create" ? "Created" : "Pending";
                break;
            case "in_progress":
                statusClass = "bg-blue-100 text-blue-800 ring-blue-200";
                statusLabel = "In Progress";
                break;
            case "cancelled":
                statusClass = "bg-red-100 text-red-800 ring-red-200";
                statusLabel = "Cancelled";
                break;
            case "courier_assigned":
                statusClass = "bg-purple-100 text-purple-800 ring-purple-200";
                statusLabel = "Assigned";
                break;
            case "courier_accepted":
                statusClass = "bg-indigo-100 text-indigo-800 ring-indigo-200";
                statusLabel = "Accepted";
                break;
            case "courier_arrived":
                statusClass = "bg-blue-100 text-blue-800 ring-blue-200";
                statusLabel = "Arrived";
                break;
            case "courier_picked_up":
                statusClass = "bg-pink-100 text-pink-800 ring-pink-200";
                statusLabel = "Picked Up";
                break;
            case "courier_departed":
                statusClass = "bg-orange-100 text-orange-800 ring-orange-200";
                statusLabel = "Departed";
                break;
            default:
                statusClass = "bg-gray-100 text-gray-800 ring-gray-200";
                statusLabel =
                    order.status.charAt(0).toUpperCase() +
                    order.status.slice(1);
        }

        statusCell.innerHTML = `<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${statusClass}">${statusLabel}</span>`;
        row.appendChild(statusCell);

        // Add customer cell
        const customerCell = document.createElement("td");
        customerCell.className = "p-4 align-middle";
        customerCell.setAttribute("data-label", "Customer");
        customerCell.textContent = order.client ? order.client.name : "N/A";
        row.appendChild(customerCell);

        // Add phone cell
        const phoneCell = document.createElement("td");
        phoneCell.className = "p-4 align-middle";
        phoneCell.setAttribute("data-label", "Phone");
        phoneCell.textContent = order.phone || "N/A";
        row.appendChild(phoneCell);

        // Add pickup location cell
        const pickupCell = document.createElement("td");
        pickupCell.className = "p-4 align-middle";
        pickupCell.setAttribute("data-label", "Pickup Location");

        let pickupLocation = "N/A";
        if (order.pickup_point) {
            const pickupPoint =
                typeof order.pickup_point === "string"
                    ? JSON.parse(order.pickup_point)
                    : order.pickup_point;
            if (pickupPoint && pickupPoint.address) {
                pickupLocation = pickupPoint.address;
            }
        }

        pickupCell.textContent = pickupLocation;
        row.appendChild(pickupCell);

        // Add delivery location cell
        const deliveryCell = document.createElement("td");
        deliveryCell.className = "p-4 align-middle";
        deliveryCell.setAttribute("data-label", "Delivery Location");

        let deliveryLocation = "N/A";
        if (order.delivery_point) {
            const deliveryPoint =
                typeof order.delivery_point === "string"
                    ? JSON.parse(order.delivery_point)
                    : order.delivery_point;
            if (deliveryPoint && deliveryPoint.address) {
                deliveryLocation = deliveryPoint.address;
            }
        }

        deliveryCell.textContent = deliveryLocation;
        row.appendChild(deliveryCell);

        // Add payment status cell
        const paymentCell = document.createElement("td");
        paymentCell.className = "p-4 align-middle";
        paymentCell.setAttribute("data-label", "Payment Status");

        // Determine payment status class
        let paymentClass = "";
        switch (order.payment_status) {
            case "paid":
                paymentClass = "bg-green-100 text-green-800 ring-green-200";
                break;
            case "unpaid":
                paymentClass = "bg-red-100 text-red-800 ring-red-200";
                break;
            case "partial":
                paymentClass = "bg-yellow-100 text-yellow-800 ring-yellow-200";
                break;
            default:
                paymentClass = "bg-gray-100 text-gray-800 ring-gray-200";
        }

        paymentCell.innerHTML = `<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ${paymentClass}">${
            order.payment_status.charAt(0).toUpperCase() +
            order.payment_status.slice(1)
        }</span>`;
        row.appendChild(paymentCell);

        // Add actions cell
        const actionsCell = document.createElement("td");
        actionsCell.className = "p-4 align-middle";
        actionsCell.setAttribute("data-label", "Actions");
        actionsCell.innerHTML = `
            <div class="flex space-x-2">
                <a href="${window.location.origin}/order/${
            order.id
        }" class="text-blue-600 hover:text-blue-900" title="View">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="${window.location.origin}/order/${
            order.id
        }/edit" class="text-green-600 hover:text-green-900" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="${window.location.origin}/order/${
            order.id
        }" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                    <input type="hidden" name="_token" value="${document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content")}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        `;
        row.appendChild(actionsCell);

        // Add the row to the fragment
        fragment.appendChild(row);
    });

    // Insert the fragment before the loading indicator
    if (loadingIndicator) {
        tbody.insertBefore(fragment, loadingIndicator);
    } else {
        tbody.appendChild(fragment);
    }

    // Reinitialize bulk actions
    initBulkActions();
}

/**
 * Initialize keyboard shortcuts
 */
function initKeyboardShortcuts() {
    // Add keyboard shortcut help button
    const filterHeader = document.querySelector(".shadcn-filter-header");
    if (!filterHeader) return;

    const keyboardShortcutsButton = document.createElement("button");
    keyboardShortcutsButton.className =
        "shadcn-button shadcn-button-ghost text-sm flex items-center ml-2";
    keyboardShortcutsButton.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect><path d="M6 8h.001"></path><path d="M10 8h.001"></path><path d="M14 8h.001"></path><path d="M18 8h.001"></path><path d="M8 12h.001"></path><path d="M12 12h.001"></path><path d="M16 12h.001"></path><path d="M7 16h10"></path></svg>
        Keyboard Shortcuts
    `;

    // Add to the filter header
    const filterActions = filterHeader.querySelector("div:last-child");
    if (filterActions) {
        filterActions.prepend(keyboardShortcutsButton);
    }

    // Create keyboard shortcuts modal
    const modal = document.createElement("div");
    modal.className =
        "fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden";
    modal.id = "keyboard-shortcuts-modal";
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Keyboard Shortcuts</h3>
                <button id="close-shortcuts-modal" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="font-medium">Apply Filters</span>
                    <span class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">Alt + Enter</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Clear Filters</span>
                    <span class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">Alt + C</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Save Current Filter</span>
                    <span class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">Alt + S</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Export to CSV</span>
                    <span class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">Alt + E</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Export to PDF</span>
                    <span class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">Alt + P</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Select All</span>
                    <span class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">Alt + A</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Delete Selected</span>
                    <span class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">Alt + D</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Add New Order</span>
                    <span class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">Alt + N</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Show Keyboard Shortcuts</span>
                    <span class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">?</span>
                </div>
            </div>
        </div>
    `;

    // Add modal to the body
    document.body.appendChild(modal);

    // Show modal when clicking the button
    keyboardShortcutsButton.addEventListener("click", function () {
        modal.classList.remove("hidden");
    });

    // Close modal when clicking the close button
    const closeButton = document.getElementById("close-shortcuts-modal");
    if (closeButton) {
        closeButton.addEventListener("click", function () {
            modal.classList.add("hidden");
        });
    }

    // Close modal when clicking outside
    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.classList.add("hidden");
        }
    });

    // Add keyboard event listeners
    document.addEventListener("keydown", function (event) {
        // Show keyboard shortcuts when pressing ?
        if (event.key === "?") {
            modal.classList.remove("hidden");
            event.preventDefault();
        }

        // Alt + Enter: Apply filters
        if (event.altKey && event.key === "Enter") {
            const applyButton = document.querySelector(
                ".shadcn-button-primary"
            );
            if (applyButton) {
                applyButton.click();
            }
            event.preventDefault();
        }

        // Alt + C: Clear filters
        if (event.altKey && event.key === "c") {
            const clearButton = document.querySelector(".shadcn-button-ghost");
            if (clearButton && clearButton.textContent.trim() === "Clear") {
                clearButton.click();
            }
            event.preventDefault();
        }

        // Alt + S: Save current filter
        if (event.altKey && event.key === "s") {
            saveCurrentFilter();
            event.preventDefault();
        }

        // Alt + E: Export to CSV
        if (event.altKey && event.key === "e") {
            const exportCsvButton = document.getElementById("export-csv");
            if (exportCsvButton) {
                exportCsvButton.click();
            }
            event.preventDefault();
        }

        // Alt + P: Export to PDF
        if (event.altKey && event.key === "p") {
            const exportPdfButton = document.getElementById("export-pdf");
            if (exportPdfButton) {
                exportPdfButton.click();
            }
            event.preventDefault();
        }

        // Alt + A: Select all
        if (event.altKey && event.key === "a") {
            const selectAllCheckbox = document.getElementById("select-all");
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = !selectAllCheckbox.checked;
                selectAllCheckbox.dispatchEvent(new Event("change"));
            }
            event.preventDefault();
        }

        // Alt + D: Delete selected
        if (event.altKey && event.key === "d") {
            const bulkDeleteButton = document.getElementById("bulk-delete");
            if (bulkDeleteButton && !bulkDeleteButton.disabled) {
                bulkDeleteButton.click();
            }
            event.preventDefault();
        }

        // Alt + N: Add new order
        if (event.altKey && event.key === "n") {
            const addButton = document.querySelector(".btn-added");
            if (addButton) {
                addButton.click();
            }
            event.preventDefault();
        }
    });
}

/**
 * Initialize toast notifications
 */
function initToastNotifications() {
    // Check if toastr is available
    if (typeof toastr === "undefined") {
        console.warn(
            "Toastr is not loaded. Toast notifications will not be available."
        );

        // Create a simple toast implementation
        window.toastr = {
            options: {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 5000,
            },
            success: function (message) {
                showSimpleToast(message, "success");
            },
            error: function (message) {
                showSimpleToast(message, "error");
            },
            warning: function (message) {
                showSimpleToast(message, "warning");
            },
            info: function (message) {
                showSimpleToast(message, "info");
            },
        };
    } else {
        // Configure toastr
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 5000,
        };
    }
}

/**
 * Show a simple toast notification
 *
 * @param {string} message - The message to display
 * @param {string} type - The type of toast (success, error, warning, info)
 */
function showSimpleToast(message, type) {
    // Create toast container if it doesn't exist
    let container = document.getElementById("toast-container");
    if (!container) {
        container = document.createElement("div");
        container.id = "toast-container";
        container.className = "fixed top-4 right-4 z-50 flex flex-col gap-2";
        document.body.appendChild(container);
    }

    // Create toast element
    const toast = document.createElement("div");
    toast.className = "rounded-md p-4 flex items-center shadow-lg max-w-sm";

    // Set background color based on type
    switch (type) {
        case "success":
            toast.classList.add(
                "bg-green-50",
                "border-l-4",
                "border-green-500",
                "text-green-800"
            );
            break;
        case "error":
            toast.classList.add(
                "bg-red-50",
                "border-l-4",
                "border-red-500",
                "text-red-800"
            );
            break;
        case "warning":
            toast.classList.add(
                "bg-yellow-50",
                "border-l-4",
                "border-yellow-500",
                "text-yellow-800"
            );
            break;
        case "info":
        default:
            toast.classList.add(
                "bg-blue-50",
                "border-l-4",
                "border-blue-500",
                "text-blue-800"
            );
            break;
    }

    // Set toast content
    toast.innerHTML = `
        <div class="flex-shrink-0 mr-3">
            ${
                type === "success"
                    ? '<svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                    : ""
            }
            ${
                type === "error"
                    ? '<svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                    : ""
            }
            ${
                type === "warning"
                    ? '<svg class="h-5 w-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>'
                    : ""
            }
            ${
                type === "info"
                    ? '<svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
                    : ""
            }
        </div>
        <div class="flex-1">
            <p class="text-sm">${message}</p>
        </div>
        <div class="ml-3 flex-shrink-0">
            <button type="button" class="close-toast inline-flex text-gray-400 hover:text-gray-500">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;

    // Add toast to container
    container.appendChild(toast);

    // Add close button event listener
    const closeButton = toast.querySelector(".close-toast");
    if (closeButton) {
        closeButton.addEventListener("click", function () {
            toast.remove();
        });
    }

    // Auto-remove toast after 5 seconds
    setTimeout(function () {
        toast.classList.add("opacity-0", "transition-opacity", "duration-500");
        setTimeout(function () {
            toast.remove();
        }, 500);
    }, 5000);
}

/**
 * Show a toast notification
 *
 * @param {string} message - The message to display
 * @param {string} type - The type of toast (success, error, warning, info)
 */
function showToast(message, type = "info") {
    if (typeof toastr !== "undefined") {
        toastr[type](message);
    } else {
        showSimpleToast(message, type);
    }
}

/**
 * Initialize inline editing
 */
function initInlineEditing() {
    const table = document.querySelector("table");
    if (!table) return;

    // Add double-click event listeners to editable cells
    const editableCells = table.querySelectorAll('td[data-editable="true"]');

    editableCells.forEach((cell) => {
        cell.addEventListener("dblclick", function () {
            // Get current value
            const currentValue = this.textContent.trim();
            const fieldName = this.dataset.field;
            const recordId = this.closest("tr").dataset.id;

            if (!fieldName || !recordId) return;

            // Create input element
            const input = document.createElement("input");
            input.type = "text";
            input.value = currentValue;
            input.className = "w-full p-1 border border-blue-500 rounded";

            // Replace cell content with input
            const originalContent = this.innerHTML;
            this.innerHTML = "";
            this.appendChild(input);

            // Focus input
            input.focus();

            // Handle input blur (save changes)
            input.addEventListener("blur", function () {
                const newValue = this.value.trim();

                // If value hasn't changed, restore original content
                if (newValue === currentValue) {
                    cell.innerHTML = originalContent;
                    return;
                }

                // Save changes
                saveInlineEdit(recordId, fieldName, newValue)
                    .then((response) => {
                        if (response.success) {
                            // Update cell content
                            cell.textContent = newValue;
                            showToast(
                                `Updated ${fieldName} to "${newValue}"`,
                                "success"
                            );
                        } else {
                            // Restore original content
                            cell.innerHTML = originalContent;
                            showToast(
                                response.message || "Failed to update",
                                "error"
                            );
                        }
                    })
                    .catch((error) => {
                        // Restore original content
                        cell.innerHTML = originalContent;
                        showToast("An error occurred while updating", "error");
                        console.error("Error saving inline edit:", error);
                    });
            });

            // Handle Enter key
            input.addEventListener("keydown", function (event) {
                if (event.key === "Enter") {
                    this.blur();
                } else if (event.key === "Escape") {
                    cell.innerHTML = originalContent;
                }
            });
        });
    });
}

/**
 * Save inline edit
 *
 * @param {string|number} id - The record ID
 * @param {string} field - The field name
 * @param {string} value - The new value
 * @returns {Promise} - A promise that resolves with the response
 */
function saveInlineEdit(id, field, value) {
    return fetch(`/api/orders/${id}/inline-edit`, {
        method: "PATCH",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            Accept: "application/json",
        },
        body: JSON.stringify({
            field: field,
            value: value,
        }),
    }).then((response) => response.json());
}

/**
 * Load a script dynamically
 *
 * @param {string} src - The script source URL
 * @param {Function} callback - The callback function to call when the script is loaded
 */
function loadScript(src, callback) {
    const script = document.createElement("script");
    script.src = src;
    script.async = true;

    if (callback) {
        script.onload = callback;
    }

    document.head.appendChild(script);
}
