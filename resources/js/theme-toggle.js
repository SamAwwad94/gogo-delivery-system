/**
 * Theme Toggle for ShadCN UI
 * 
 * This script handles the theme toggling functionality for ShadCN UI components.
 * It supports light and dark modes, with system preference detection.
 */

// Theme configuration
const themeConfig = {
    defaultTheme: 'system', // 'light', 'dark', or 'system'
    storageKey: 'gogo-delivery-theme',
    rootElement: document.documentElement,
    mediaQuery: window.matchMedia('(prefers-color-scheme: dark)'),
};

// Initialize theme
function initializeTheme() {
    // Get theme from localStorage or use default
    const storedTheme = localStorage.getItem(themeConfig.storageKey);
    const theme = storedTheme || themeConfig.defaultTheme;
    
    // Set initial theme
    setTheme(theme);
    
    // Add event listener for system preference changes
    themeConfig.mediaQuery.addEventListener('change', handleSystemPreferenceChange);
    
    // Add event listeners to theme toggle buttons
    document.addEventListener('DOMContentLoaded', () => {
        const themeToggles = document.querySelectorAll('[data-theme-toggle]');
        themeToggles.forEach(toggle => {
            toggle.addEventListener('click', handleThemeToggle);
        });
    });
}

// Set theme
function setTheme(theme) {
    // Store theme preference
    localStorage.setItem(themeConfig.storageKey, theme);
    
    // Apply theme
    if (theme === 'system') {
        if (themeConfig.mediaQuery.matches) {
            themeConfig.rootElement.classList.add('dark');
        } else {
            themeConfig.rootElement.classList.remove('dark');
        }
    } else if (theme === 'dark') {
        themeConfig.rootElement.classList.add('dark');
    } else {
        themeConfig.rootElement.classList.remove('dark');
    }
    
    // Update theme toggle buttons
    document.querySelectorAll('[data-theme-toggle]').forEach(toggle => {
        toggle.setAttribute('data-active-theme', theme);
    });
}

// Handle theme toggle click
function handleThemeToggle(event) {
    const currentTheme = localStorage.getItem(themeConfig.storageKey) || themeConfig.defaultTheme;
    
    // Cycle through themes: light -> dark -> system -> light
    let newTheme;
    if (currentTheme === 'light') {
        newTheme = 'dark';
    } else if (currentTheme === 'dark') {
        newTheme = 'system';
    } else {
        newTheme = 'light';
    }
    
    setTheme(newTheme);
}

// Handle system preference change
function handleSystemPreferenceChange(event) {
    const currentTheme = localStorage.getItem(themeConfig.storageKey) || themeConfig.defaultTheme;
    
    if (currentTheme === 'system') {
        if (event.matches) {
            themeConfig.rootElement.classList.add('dark');
        } else {
            themeConfig.rootElement.classList.remove('dark');
        }
    }
}

// Initialize theme when script loads
initializeTheme();
