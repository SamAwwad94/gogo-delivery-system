import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react"; // Changed from vue to react

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                // "resources/css/new-orders.css", // Assuming this might be Vue specific or can be imported in app.jsx
                "resources/js/app.jsx", // Changed from app.js to app.jsx for React
                // "resources/js/new-orders-app.js", // Assuming this is Vue specific
                // "resources/js/modernization-test.js", // Assuming this is Vue specific or not primary entry
                // "resources/js/simple-test.js", // Assuming this is Vue specific or not primary entry
            ],
            refresh: true,
        }),
        react(), // Changed from vue() to react()
    ],
    build: {
        // Increase the warning limit to avoid unnecessary warnings
        chunkSizeWarningLimit: 800,
        // rollupOptions can be re-added if specific chunking for React is needed
        // Optimize CSS
        cssCodeSplit: true,
        // Source maps for production (can be disabled for smaller builds)
        sourcemap: false, // Consider true for easier debugging during development
    },
});
