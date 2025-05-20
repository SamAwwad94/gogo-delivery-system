/**
 * Theme Toggle - Handles dark/light mode switching
 */

// Function to set the theme
function setTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    
    // Store the theme preference
    localStorage.setItem('theme', theme);
    
    // Dispatch an event that the theme has changed
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
}

// Function to get the current theme
function getTheme() {
    // Check if theme is stored in localStorage
    const storedTheme = localStorage.getItem('theme');
    
    if (storedTheme) {
        return storedTheme;
    }
    
    // Check if the user has a system preference
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }
    
    // Default to light theme
    return 'light';
}

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set the initial theme
    setTheme(getTheme());
    
    // Add event listeners to theme toggle buttons
    const themeToggles = document.querySelectorAll('[data-theme-toggle]');
    
    themeToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const currentTheme = getTheme();
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });
    });
    
    // Listen for system theme changes
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (localStorage.getItem('theme') === null) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    }
});

// Export functions for use in other scripts
window.themeToggle = {
    setTheme,
    getTheme
};
