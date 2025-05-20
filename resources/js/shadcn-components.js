/**
 * ShadCN Components - JavaScript for ShadCN UI components
 * 
 * This file contains the JavaScript functionality for ShadCN UI components
 * that require JavaScript to function properly.
 */

// Dropdown component
class ShadcnDropdown {
    constructor(element) {
        this.dropdown = element;
        this.trigger = element.querySelector('[data-dropdown-trigger]');
        this.content = element.querySelector('[data-dropdown-content]');
        this.isOpen = false;
        
        this.init();
    }
    
    init() {
        if (!this.trigger || !this.content) return;
        
        // Set initial state
        this.content.classList.add('hidden');
        
        // Add event listeners
        this.trigger.addEventListener('click', this.toggle.bind(this));
        document.addEventListener('click', this.handleOutsideClick.bind(this));
    }
    
    toggle(event) {
        event.preventDefault();
        event.stopPropagation();
        
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
    
    open() {
        this.content.classList.remove('hidden');
        this.content.classList.add('animate-fade-in');
        this.isOpen = true;
        this.trigger.setAttribute('aria-expanded', 'true');
    }
    
    close() {
        this.content.classList.add('hidden');
        this.content.classList.remove('animate-fade-in');
        this.isOpen = false;
        this.trigger.setAttribute('aria-expanded', 'false');
    }
    
    handleOutsideClick(event) {
        if (this.isOpen && !this.dropdown.contains(event.target)) {
            this.close();
        }
    }
}

// Tabs component
class ShadcnTabs {
    constructor(element) {
        this.tabsContainer = element;
        this.tabTriggers = Array.from(element.querySelectorAll('[data-tab-trigger]'));
        this.tabContents = Array.from(element.querySelectorAll('[data-tab-content]'));
        
        this.init();
    }
    
    init() {
        if (this.tabTriggers.length === 0 || this.tabContents.length === 0) return;
        
        // Set initial state - activate first tab
        this.activateTab(this.tabTriggers[0]);
        
        // Add event listeners
        this.tabTriggers.forEach(trigger => {
            trigger.addEventListener('click', (event) => {
                event.preventDefault();
                this.activateTab(trigger);
            });
        });
    }
    
    activateTab(trigger) {
        const tabId = trigger.getAttribute('data-tab-trigger');
        
        // Update trigger states
        this.tabTriggers.forEach(t => {
            if (t === trigger) {
                t.setAttribute('aria-selected', 'true');
                t.classList.add('border-primary', 'text-foreground');
                t.classList.remove('border-transparent', 'text-muted-foreground');
            } else {
                t.setAttribute('aria-selected', 'false');
                t.classList.remove('border-primary', 'text-foreground');
                t.classList.add('border-transparent', 'text-muted-foreground');
            }
        });
        
        // Update content states
        this.tabContents.forEach(content => {
            const contentId = content.getAttribute('data-tab-content');
            if (contentId === tabId) {
                content.classList.remove('hidden');
            } else {
                content.classList.add('hidden');
            }
        });
    }
}

// Accordion component
class ShadcnAccordion {
    constructor(element) {
        this.accordion = element;
        this.items = Array.from(element.querySelectorAll('[data-accordion-item]'));
        this.allowMultiple = element.getAttribute('data-accordion-multiple') === 'true';
        
        this.init();
    }
    
    init() {
        if (this.items.length === 0) return;
        
        this.items.forEach(item => {
            const trigger = item.querySelector('[data-accordion-trigger]');
            const content = item.querySelector('[data-accordion-content]');
            
            if (!trigger || !content) return;
            
            // Set initial state
            content.style.maxHeight = '0';
            content.style.overflow = 'hidden';
            content.style.transition = 'max-height 0.2s ease-out';
            trigger.setAttribute('aria-expanded', 'false');
            
            // Add event listener
            trigger.addEventListener('click', () => this.toggleItem(item, trigger, content));
        });
    }
    
    toggleItem(item, trigger, content) {
        const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
        
        if (!this.allowMultiple) {
            // Close other items
            this.items.forEach(otherItem => {
                if (otherItem !== item) {
                    const otherTrigger = otherItem.querySelector('[data-accordion-trigger]');
                    const otherContent = otherItem.querySelector('[data-accordion-content]');
                    
                    if (otherTrigger && otherContent) {
                        otherTrigger.setAttribute('aria-expanded', 'false');
                        otherContent.style.maxHeight = '0';
                    }
                }
            });
        }
        
        // Toggle current item
        if (isExpanded) {
            trigger.setAttribute('aria-expanded', 'false');
            content.style.maxHeight = '0';
        } else {
            trigger.setAttribute('aria-expanded', 'true');
            content.style.maxHeight = content.scrollHeight + 'px';
        }
    }
}

// Initialize components on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdowns
    document.querySelectorAll('[data-dropdown]').forEach(element => {
        new ShadcnDropdown(element);
    });
    
    // Initialize tabs
    document.querySelectorAll('[data-tabs]').forEach(element => {
        new ShadcnTabs(element);
    });
    
    // Initialize accordions
    document.querySelectorAll('[data-accordion]').forEach(element => {
        new ShadcnAccordion(element);
    });
});
