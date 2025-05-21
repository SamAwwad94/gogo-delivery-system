/**
 * ShadCN Filter Component
 * A reusable filter component for ShadCN tables
 */

class ShadcnFilter {
    /**
     * Initialize the filter component
     * @param {Object} options - Configuration options
     * @param {string} options.containerId - ID of the container element
     * @param {Array} options.filters - Array of filter configurations
     * @param {Function} options.onApply - Callback function when filters are applied
     * @param {Function} options.onClear - Callback function when filters are cleared
     * @param {boolean} options.collapsible - Whether the filter should be collapsible
     * @param {boolean} options.collapsed - Whether the filter should be initially collapsed
     * @param {boolean} options.saveState - Whether to save filter state in localStorage
     * @param {string} options.storageKey - Key to use for localStorage
     * @param {boolean} options.autoApply - Whether to automatically apply filters on change
     */
    constructor(options) {
        this.containerId = options.containerId;
        this.filters = options.filters || [];
        this.onApply = options.onApply || (() => {});
        this.onClear = options.onClear || (() => {});
        this.collapsible =
            options.collapsible !== undefined ? options.collapsible : true;
        this.collapsed =
            options.collapsed !== undefined ? options.collapsed : false;
        this.saveState =
            options.saveState !== undefined ? options.saveState : true;
        this.storageKey =
            options.storageKey || `shadcn-filter-${this.containerId}`;
        this.autoApply =
            options.autoApply !== undefined ? options.autoApply : false;
        this.container = document.getElementById(this.containerId);
        this.filterValues = {};

        if (!this.container) {
            console.error(`Container with ID "${this.containerId}" not found.`);
            return;
        }

        // Load saved state if enabled
        if (this.saveState) {
            this.loadState();
        }

        this.render();
        this.bindEvents();
    }

    /**
     * Render the filter component
     */
    render() {
        // Create filter container
        const filterContainer = document.createElement("div");
        filterContainer.className = "shadcn-filter-container mb-4";

        // Create filter header
        const filterHeader = document.createElement("div");
        filterHeader.className =
            "shadcn-filter-header flex items-center justify-between p-3 bg-muted rounded-t-lg border-b border-border";

        // Create left side of header with title and collapse toggle
        const headerLeft = document.createElement("div");
        headerLeft.className = "flex items-center";

        // Add filter icon
        const filterIcon = document.createElement("svg");
        filterIcon.setAttribute("xmlns", "http://www.w3.org/2000/svg");
        filterIcon.setAttribute("width", "18");
        filterIcon.setAttribute("height", "18");
        filterIcon.setAttribute("viewBox", "0 0 24 24");
        filterIcon.setAttribute("fill", "none");
        filterIcon.setAttribute("stroke", "currentColor");
        filterIcon.setAttribute("stroke-width", "2");
        filterIcon.setAttribute("stroke-linecap", "round");
        filterIcon.setAttribute("stroke-linejoin", "round");
        filterIcon.className = "mr-2";
        filterIcon.innerHTML =
            '<path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"></path>';
        headerLeft.appendChild(filterIcon);

        // Add title
        const title = document.createElement("h3");
        title.className = "text-base font-medium";
        title.textContent = "Filters";
        headerLeft.appendChild(title);

        // Add collapse toggle if collapsible
        if (this.collapsible) {
            const toggleButton = document.createElement("button");
            toggleButton.className = "ml-2 p-1 rounded-md hover:bg-accent";
            toggleButton.id = `${this.containerId}-toggle-btn`;

            const toggleIcon = document.createElement("span");
            toggleIcon.id = `${this.containerId}-toggle-icon`;
            toggleIcon.innerHTML = this.collapsed
                ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>';

            toggleButton.appendChild(toggleIcon);
            headerLeft.appendChild(toggleButton);

            // Add event listener
            toggleButton.addEventListener("click", () => this.toggleCollapse());
        }

        // Create right side of header with buttons
        const headerRight = document.createElement("div");
        headerRight.className = "flex items-center";

        // Add clear button
        const clearButton = document.createElement("button");
        clearButton.id = `${this.containerId}-clear-btn`;
        clearButton.className =
            "shadcn-button shadcn-button-ghost text-sm mr-2";
        clearButton.textContent = "Clear";
        headerRight.appendChild(clearButton);

        // Add apply button
        const applyButton = document.createElement("button");
        applyButton.id = `${this.containerId}-apply-btn`;
        applyButton.className = "shadcn-button shadcn-button-primary text-sm";
        applyButton.textContent = "Apply";
        headerRight.appendChild(applyButton);

        // Add badge showing active filter count
        const activeFilterCount = Object.values(this.filterValues).filter(
            (value) => value !== ""
        ).length;
        if (activeFilterCount > 0) {
            const badge = document.createElement("span");
            badge.className =
                "ml-2 inline-flex items-center rounded-full bg-primary px-2 py-1 text-xs font-medium text-primary-foreground";
            badge.textContent = activeFilterCount;
            headerRight.appendChild(badge);
        }

        // Add to header
        filterHeader.appendChild(headerLeft);
        filterHeader.appendChild(headerRight);

        // Create filter body
        const filterBody = document.createElement("div");
        filterBody.className =
            "shadcn-filter-body p-3 bg-background rounded-b-lg border border-t-0 border-border";

        // Apply collapsed state if needed
        if (this.collapsible && this.collapsed) {
            filterBody.style.display = "none";
        }

        // Create filter flex container (horizontal pills)
        const filterFlex = document.createElement("div");
        filterFlex.className = "flex flex-wrap items-center gap-4";

        // Add filters to flex container as pills
        this.filters.forEach((filter) => {
            const filterItem = this.createFilterPill(filter);
            filterFlex.appendChild(filterItem);
        });

        // Assemble filter component
        filterBody.appendChild(filterFlex);
        filterContainer.appendChild(filterHeader);
        filterContainer.appendChild(filterBody);

        // Add to container
        this.container.innerHTML = "";
        this.container.appendChild(filterContainer);
    }

    /**
     * Create a filter item (traditional style)
     * @param {Object} filter - Filter configuration
     * @returns {HTMLElement} - Filter item element
     */
    createFilterItem(filter) {
        const filterItem = document.createElement("div");
        filterItem.className = "shadcn-filter-item";

        const label = document.createElement("label");
        label.className = "text-sm font-medium mb-1 block";
        label.textContent = filter.label;

        let input;

        switch (filter.type) {
            case "select":
                input = document.createElement("select");
                input.className =
                    "w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm";
                input.id = `${this.containerId}-filter-${filter.name}`;

                // Add placeholder option
                const placeholderOption = document.createElement("option");
                placeholderOption.value = "";
                placeholderOption.textContent = `All ${filter.label}`;
                input.appendChild(placeholderOption);

                // Add options
                if (filter.options) {
                    filter.options.forEach((option) => {
                        const optionElement = document.createElement("option");
                        optionElement.value = option.value;
                        optionElement.textContent = option.label;
                        input.appendChild(optionElement);
                    });
                }
                break;

            case "date":
                input = document.createElement("input");
                input.type = "date";
                input.className =
                    "w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm";
                input.id = `${this.containerId}-filter-${filter.name}`;
                break;

            case "text":
                input = document.createElement("input");
                input.type = "text";
                input.className =
                    "w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm";
                input.id = `${this.containerId}-filter-${filter.name}`;
                input.placeholder =
                    filter.placeholder || `Search ${filter.label}`;
                break;

            case "number":
                input = document.createElement("input");
                input.type = "number";
                input.className =
                    "w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm";
                input.id = `${this.containerId}-filter-${filter.name}`;
                input.placeholder =
                    filter.placeholder || `Enter ${filter.label}`;
                break;

            default:
                input = document.createElement("input");
                input.type = "text";
                input.className =
                    "w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm";
                input.id = `${this.containerId}-filter-${filter.name}`;
                input.placeholder =
                    filter.placeholder || `Search ${filter.label}`;
        }

        // Store reference to filter
        input.dataset.filterName = filter.name;

        filterItem.appendChild(label);
        filterItem.appendChild(input);

        return filterItem;
    }

    /**
     * Create a filter pill (modern style)
     * @param {Object} filter - Filter configuration
     * @returns {HTMLElement} - Filter pill element
     */
    createFilterPill(filter) {
        // Create pill container
        const pillContainer = document.createElement("div");
        pillContainer.className = "filter-pill-container relative w-[180px]";

        // Create the pill button
        const pill = document.createElement("button");
        pill.className =
            "filter-pill w-full flex items-center justify-between px-3 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors";
        pill.id = `${this.containerId}-pill-${filter.name}`;

        // Create pill content
        const pillContent = document.createElement("div");
        pillContent.className = "flex items-center";

        // Add label
        const pillLabel = document.createElement("span");
        pillLabel.className = "text-sm font-medium";
        pillLabel.textContent = filter.label;

        // Add dropdown icon
        const pillIcon = document.createElement("span");
        pillIcon.className = "ml-1";
        pillIcon.innerHTML =
            '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>';

        // Assemble pill
        pillContent.appendChild(pillLabel);
        pill.appendChild(pillContent);
        pill.appendChild(pillIcon);
        pillContainer.appendChild(pill);

        // Create dropdown container (hidden by default)
        const dropdown = document.createElement("div");
        dropdown.className =
            "filter-dropdown absolute left-0 top-full mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 p-2 z-50 hidden";
        dropdown.id = `${this.containerId}-dropdown-${filter.name}`;

        // Create dropdown content based on filter type
        let dropdownContent;

        switch (filter.type) {
            case "select":
                dropdownContent = document.createElement("select");
                dropdownContent.className =
                    "w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm";
                dropdownContent.id = `${this.containerId}-filter-${filter.name}`;

                // Add placeholder option
                const placeholderOption = document.createElement("option");
                placeholderOption.value = "";
                placeholderOption.textContent = `All ${filter.label}`;
                dropdownContent.appendChild(placeholderOption);

                // Add options
                if (filter.options) {
                    filter.options.forEach((option) => {
                        const optionElement = document.createElement("option");
                        optionElement.value = option.value;
                        optionElement.textContent = option.label;
                        dropdownContent.appendChild(optionElement);
                    });
                }
                break;

            case "date":
                dropdownContent = document.createElement("input");
                dropdownContent.type = "date";
                dropdownContent.className =
                    "w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm";
                dropdownContent.id = `${this.containerId}-filter-${filter.name}`;
                break;

            case "text":
                dropdownContent = document.createElement("input");
                dropdownContent.type = "text";
                dropdownContent.className =
                    "w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm";
                dropdownContent.id = `${this.containerId}-filter-${filter.name}`;
                dropdownContent.placeholder =
                    filter.placeholder || `Search ${filter.label}`;
                break;

            case "number":
                dropdownContent = document.createElement("input");
                dropdownContent.type = "number";
                dropdownContent.className =
                    "w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm";
                dropdownContent.id = `${this.containerId}-filter-${filter.name}`;
                dropdownContent.placeholder =
                    filter.placeholder || `Enter ${filter.label}`;
                break;

            default:
                dropdownContent = document.createElement("input");
                dropdownContent.type = "text";
                dropdownContent.className =
                    "w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm";
                dropdownContent.id = `${this.containerId}-filter-${filter.name}`;
                dropdownContent.placeholder =
                    filter.placeholder || `Search ${filter.label}`;
        }

        // Store reference to filter
        dropdownContent.dataset.filterName = filter.name;

        // Add dropdown content
        dropdown.appendChild(dropdownContent);

        // Add apply button for non-auto-apply filters
        if (!this.autoApply) {
            const applyButton = document.createElement("button");
            applyButton.className =
                "w-full mt-2 px-3 py-1.5 text-sm bg-primary text-white rounded-md hover:bg-primary-dark transition-colors";
            applyButton.textContent = "Apply";
            applyButton.addEventListener("click", () => {
                this.applyFilters();
                dropdown.classList.add("hidden");
            });
            dropdown.appendChild(applyButton);
        }

        // Add dropdown to container
        pillContainer.appendChild(dropdown);

        // Toggle dropdown on pill click
        pill.addEventListener("click", (e) => {
            e.preventDefault();

            // Close all other dropdowns
            document.querySelectorAll(".filter-dropdown").forEach((el) => {
                if (el.id !== dropdown.id) {
                    el.classList.add("hidden");
                }
            });

            // Toggle this dropdown
            dropdown.classList.toggle("hidden");
        });

        // Close dropdown when clicking outside
        document.addEventListener("click", (e) => {
            if (!pillContainer.contains(e.target)) {
                dropdown.classList.add("hidden");
            }
        });

        // Auto-apply filter on change if enabled
        dropdownContent.addEventListener("change", () => {
            this.filterValues[filter.name] = dropdownContent.value;

            // Update pill label to show selected value
            if (dropdownContent.value) {
                let displayValue = dropdownContent.value;

                // For select elements, show the selected option text
                if (
                    filter.type === "select" &&
                    dropdownContent.selectedOptions[0]
                ) {
                    displayValue =
                        dropdownContent.selectedOptions[0].textContent;
                }

                pillLabel.innerHTML = `${filter.label}: <span class="font-bold">${displayValue}</span>`;
                pill.classList.add("active");
            } else {
                pillLabel.textContent = filter.label;
                pill.classList.remove("active");
            }

            // Auto-apply if enabled
            if (this.autoApply) {
                this.applyFilters();
                dropdown.classList.add("hidden");
            }
        });

        // For text inputs, add keyup event with Enter key handling
        if (filter.type === "text") {
            dropdownContent.addEventListener("keyup", (e) => {
                this.filterValues[filter.name] = dropdownContent.value;

                // Update pill label
                if (dropdownContent.value) {
                    pillLabel.innerHTML = `${filter.label}: <span class="font-bold">${dropdownContent.value}</span>`;
                    pill.classList.add("active");
                } else {
                    pillLabel.textContent = filter.label;
                    pill.classList.remove("active");
                }

                // Apply on Enter key
                if (e.key === "Enter") {
                    this.applyFilters();
                    dropdown.classList.add("hidden");
                }
                // Auto-apply if enabled (with debounce)
                else if (this.autoApply) {
                    clearTimeout(dropdownContent.debounceTimer);
                    dropdownContent.debounceTimer = setTimeout(() => {
                        this.applyFilters();
                    }, 500); // 500ms debounce
                }
            });
        }

        return pillContainer;
    }

    /**
     * Bind events to filter elements
     */
    bindEvents() {
        const applyBtn = document.getElementById(
            `${this.containerId}-apply-btn`
        );
        const clearBtn = document.getElementById(
            `${this.containerId}-clear-btn`
        );

        if (applyBtn) {
            applyBtn.addEventListener("click", () => {
                this.applyFilters();
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener("click", () => {
                this.clearFilters();
            });
        }

        // Add event listeners for filter inputs (both traditional and pill-style)
        this.filters.forEach((filter) => {
            const input = document.getElementById(
                `${this.containerId}-filter-${filter.name}`
            );

            if (input) {
                // For traditional filter inputs
                input.addEventListener("change", () => {
                    this.filterValues[filter.name] = input.value;

                    // Auto-apply if enabled
                    if (this.autoApply) {
                        this.applyFilters();
                    }
                });

                // For text inputs, add keyup event
                if (filter.type === "text") {
                    input.addEventListener("keyup", (e) => {
                        this.filterValues[filter.name] = input.value;

                        // Apply on Enter key
                        if (e.key === "Enter") {
                            this.applyFilters();
                        }
                        // Auto-apply if enabled (with debounce)
                        else if (this.autoApply) {
                            clearTimeout(input.debounceTimer);
                            input.debounceTimer = setTimeout(() => {
                                this.applyFilters();
                            }, 500); // 500ms debounce
                        }
                    });
                }
            }

            // For pill-style filters, events are already bound in createFilterPill method
        });
    }

    /**
     * Apply filters
     */
    applyFilters() {
        // Collect filter values
        this.filters.forEach((filter) => {
            const input = document.getElementById(
                `${this.containerId}-filter-${filter.name}`
            );
            if (input) {
                this.filterValues[filter.name] = input.value;

                // Update pill display if using pill-style filters
                const pill = document.getElementById(
                    `${this.containerId}-pill-${filter.name}`
                );
                if (pill) {
                    const pillLabel = pill.querySelector("span");
                    if (pillLabel) {
                        if (input.value) {
                            let displayValue = input.value;

                            // For select elements, show the selected option text
                            if (
                                input.tagName === "SELECT" &&
                                input.selectedOptions[0]
                            ) {
                                displayValue =
                                    input.selectedOptions[0].textContent;
                            }

                            pillLabel.innerHTML = `${filter.label}: <span class="font-bold">${displayValue}</span>`;
                            pill.classList.add("active");
                        } else {
                            pillLabel.textContent = filter.label;
                            pill.classList.remove("active");
                        }
                    }
                }
            }
        });

        // Save state if enabled
        if (this.saveState) {
            this.saveState();
        }

        // Call onApply callback
        this.onApply(this.filterValues);
    }

    /**
     * Clear filters
     */
    clearFilters() {
        // Reset filter values
        this.filters.forEach((filter) => {
            const input = document.getElementById(
                `${this.containerId}-filter-${filter.name}`
            );
            if (input) {
                input.value = "";
                this.filterValues[filter.name] = "";

                // Reset pill display if using pill-style filters
                const pill = document.getElementById(
                    `${this.containerId}-pill-${filter.name}`
                );
                if (pill) {
                    const pillLabel = pill.querySelector("span");
                    if (pillLabel) {
                        pillLabel.textContent = filter.label;
                        pill.classList.remove("active");
                    }
                }
            }
        });

        // Close all dropdowns
        document.querySelectorAll(".filter-dropdown").forEach((dropdown) => {
            dropdown.classList.add("hidden");
        });

        // Save state if enabled
        if (this.saveState) {
            this.saveState();
        }

        // Call onClear callback
        this.onClear();
    }

    /**
     * Get filter values
     * @returns {Object} - Filter values
     */
    getFilterValues() {
        return this.filterValues;
    }

    /**
     * Set filter values
     * @param {Object} values - Filter values
     */
    setFilterValues(values) {
        this.filterValues = values || {};

        // Update filter inputs
        Object.keys(this.filterValues).forEach((name) => {
            const input = document.getElementById(
                `${this.containerId}-filter-${name}`
            );
            if (input) {
                input.value = this.filterValues[name];
            }
        });

        // Save state if enabled
        if (this.saveState) {
            this.saveState();
        }
    }

    /**
     * Save filter state to localStorage
     */
    saveState() {
        if (typeof localStorage !== "undefined") {
            try {
                localStorage.setItem(
                    this.storageKey,
                    JSON.stringify({
                        filterValues: this.filterValues,
                        collapsed: this.collapsed,
                    })
                );
            } catch (e) {
                console.error("Failed to save filter state:", e);
            }
        }
    }

    /**
     * Load filter state from localStorage
     */
    loadState() {
        if (typeof localStorage !== "undefined") {
            try {
                const savedState = localStorage.getItem(this.storageKey);
                if (savedState) {
                    const state = JSON.parse(savedState);
                    this.filterValues = state.filterValues || {};
                    this.collapsed =
                        state.collapsed !== undefined
                            ? state.collapsed
                            : this.collapsed;
                }
            } catch (e) {
                console.error("Failed to load filter state:", e);
            }
        }
    }

    /**
     * Toggle filter collapse state
     */
    toggleCollapse() {
        this.collapsed = !this.collapsed;

        const filterBody = document.querySelector(
            `#${this.containerId} .shadcn-filter-body`
        );
        const toggleIcon = document.querySelector(
            `#${this.containerId}-toggle-icon`
        );

        if (filterBody && toggleIcon) {
            if (this.collapsed) {
                filterBody.style.display = "none";
                toggleIcon.innerHTML =
                    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
            } else {
                filterBody.style.display = "block";
                toggleIcon.innerHTML =
                    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>';
            }
        }

        // Save state if enabled
        if (this.saveState) {
            this.saveState();
        }
    }
}

// Make available globally
window.ShadcnFilter = ShadcnFilter;
