/**
 * Orders Performance Optimizations
 * 
 * This file contains performance optimizations for the orders page:
 * - Request debouncing and throttling
 * - Aggressive caching strategies
 * - Optimized database queries
 * - Virtual scrolling for large datasets
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize performance optimizations
    initPerformanceOptimizations();
});

/**
 * Initialize performance optimizations
 */
function initPerformanceOptimizations() {
    // Check if we're on the orders page
    if (!document.querySelector('.shadcn-table-container')) {
        return;
    }
    
    // Initialize request debouncing for search inputs
    initRequestDebouncing();
    
    // Initialize aggressive caching
    initAggressiveCaching();
    
    // Initialize virtual scrolling
    initVirtualScrolling();
    
    // Initialize image lazy loading
    initImageLazyLoading();
    
    // Initialize prefetching
    initPrefetching();
}

/**
 * Initialize request debouncing for search inputs
 */
function initRequestDebouncing() {
    // Get all search inputs
    const searchInputs = document.querySelectorAll('input[type="search"], input[type="text"].search-input');
    
    searchInputs.forEach(input => {
        // Add debounce to search inputs
        let debounceTimeout;
        
        input.addEventListener('input', function() {
            // Clear previous timeout
            clearTimeout(debounceTimeout);
            
            // Set new timeout
            debounceTimeout = setTimeout(() => {
                // Get the form
                const form = input.closest('form');
                if (form) {
                    // Add loading state
                    form.classList.add('loading');
                    
                    // Submit the form
                    form.dispatchEvent(new Event('submit'));
                }
            }, 500); // 500ms debounce
        });
    });
    
    // Add throttling to form submissions
    const filterForm = document.getElementById('order-filter-form');
    if (filterForm) {
        let isThrottled = false;
        
        // Store the original submit handler
        const originalSubmitHandler = filterForm.onsubmit;
        
        // Override the submit handler
        filterForm.onsubmit = function(event) {
            if (isThrottled) {
                event.preventDefault();
                return false;
            }
            
            // Set throttle flag
            isThrottled = true;
            
            // Add loading state
            filterForm.classList.add('loading');
            
            // Reset throttle after 1 second
            setTimeout(() => {
                isThrottled = false;
                filterForm.classList.remove('loading');
            }, 1000);
            
            // Call original handler if it exists
            if (typeof originalSubmitHandler === 'function') {
                return originalSubmitHandler.call(this, event);
            }
        };
    }
}

/**
 * Initialize aggressive caching
 */
function initAggressiveCaching() {
    // Cache API responses
    if ('caches' in window) {
        // Create a cache for API responses
        caches.open('orders-api-cache').then(cache => {
            // Cache common API requests
            const urlsToCache = [
                '/api/orders/statistics',
                '/api/orders/updates'
            ];
            
            // Cache each URL
            urlsToCache.forEach(url => {
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    // Clone the response and put it in the cache
                    const responseClone = response.clone();
                    cache.put(url, responseClone);
                })
                .catch(error => {
                    console.error('Error caching API response:', error);
                });
            });
        });
    }
    
    // Cache filter selections in localStorage
    const filterForm = document.getElementById('order-filter-form');
    if (filterForm) {
        // Load cached filters
        const cachedFilters = localStorage.getItem('orders-filter-cache');
        if (cachedFilters) {
            try {
                const filters = JSON.parse(cachedFilters);
                
                // Apply cached filters to form
                Object.entries(filters).forEach(([key, value]) => {
                    const input = filterForm.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = value;
                    }
                });
            } catch (error) {
                console.error('Error loading cached filters:', error);
            }
        }
        
        // Save filters on form submit
        filterForm.addEventListener('submit', function() {
            const formData = new FormData(filterForm);
            const filters = {};
            
            for (const [key, value] of formData.entries()) {
                if (value) {
                    filters[key] = value;
                }
            }
            
            // Save to localStorage
            localStorage.setItem('orders-filter-cache', JSON.stringify(filters));
        });
    }
}

/**
 * Initialize virtual scrolling for large datasets
 */
function initVirtualScrolling() {
    const table = document.querySelector('table');
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    if (!tbody) return;
    
    // Check if we have a large number of rows
    if (tbody.children.length < 50) return;
    
    // Create a container for virtual scrolling
    const container = document.createElement('div');
    container.className = 'virtual-scroll-container';
    container.style.height = '600px';
    container.style.overflowY = 'auto';
    
    // Wrap the table in the container
    table.parentNode.insertBefore(container, table);
    container.appendChild(table);
    
    // Initialize virtual scrolling
    let visibleRows = [];
    let rowHeight = 0;
    let totalRows = tbody.children.length;
    let containerHeight = container.clientHeight;
    let tableWidth = table.clientWidth;
    
    // Calculate row height
    if (tbody.children.length > 0) {
        rowHeight = tbody.children[0].offsetHeight;
    }
    
    // Create spacers
    const topSpacer = document.createElement('tr');
    topSpacer.className = 'virtual-scroll-spacer';
    topSpacer.style.height = '0px';
    
    const bottomSpacer = document.createElement('tr');
    bottomSpacer.className = 'virtual-scroll-spacer';
    bottomSpacer.style.height = `${(totalRows - 20) * rowHeight}px`;
    
    // Add spacers
    tbody.insertBefore(topSpacer, tbody.firstChild);
    tbody.appendChild(bottomSpacer);
    
    // Hide all rows except the first 20
    for (let i = 0; i < tbody.children.length; i++) {
        if (i > 0 && i <= 20) {
            visibleRows.push(tbody.children[i]);
        } else if (i > 20 && i < tbody.children.length - 1) {
            tbody.children[i].style.display = 'none';
        }
    }
    
    // Handle scroll events
    container.addEventListener('scroll', function() {
        const scrollTop = container.scrollTop;
        const startIndex = Math.floor(scrollTop / rowHeight);
        const endIndex = Math.min(startIndex + 20, totalRows);
        
        // Update spacers
        topSpacer.style.height = `${startIndex * rowHeight}px`;
        bottomSpacer.style.height = `${(totalRows - endIndex) * rowHeight}px`;
        
        // Hide previously visible rows
        visibleRows.forEach(row => {
            row.style.display = 'none';
        });
        
        // Show new visible rows
        visibleRows = [];
        for (let i = startIndex + 1; i <= endIndex; i++) {
            if (i < tbody.children.length - 1) {
                tbody.children[i].style.display = '';
                visibleRows.push(tbody.children[i]);
            }
        }
    });
}

/**
 * Initialize image lazy loading
 */
function initImageLazyLoading() {
    // Check if Intersection Observer API is supported
    if (!('IntersectionObserver' in window)) {
        return;
    }
    
    // Get all images
    const images = document.querySelectorAll('img[data-src]');
    
    // Create an observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.getAttribute('data-src');
                
                if (src) {
                    img.src = src;
                    img.removeAttribute('data-src');
                }
                
                observer.unobserve(img);
            }
        });
    });
    
    // Observe each image
    images.forEach(img => {
        observer.observe(img);
    });
}

/**
 * Initialize prefetching for common actions
 */
function initPrefetching() {
    // Prefetch common pages
    const commonPages = [
        '/order/create',
        '/order?status=pending',
        '/order?status=completed',
        '/order?status=cancelled'
    ];
    
    // Create link elements for prefetching
    commonPages.forEach(url => {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        document.head.appendChild(link);
    });
    
    // Prefetch on hover for action links
    const actionLinks = document.querySelectorAll('a[href^="/order/"]');
    
    actionLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            const href = this.getAttribute('href');
            
            // Check if already prefetched
            if (!document.querySelector(`link[rel="prefetch"][href="${href}"]`)) {
                const prefetchLink = document.createElement('link');
                prefetchLink.rel = 'prefetch';
                prefetchLink.href = href;
                document.head.appendChild(prefetchLink);
            }
        });
    });
}
