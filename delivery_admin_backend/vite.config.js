import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import path from 'path'; // Added for path resolution

export default defineConfig({
    resolve: { // Added resolve configuration
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            // The user's example 'components/ui/button' implies a root-level 'components' alias
            // or that 'components' is directly under 'resources/js'.
            // Given the target structure `resources/js/Components/ui/button.jsx`,
            // an import like `import { Button } from '@/Components/ui/button'` would work with the @ alias.
            // If they want `import { Button } from 'components/ui/button'`,
            // then 'components' would need to be aliased to 'resources/js/Components'.
            // For now, I'll stick to the `@` alias as per the prompt's mention.
            // If they want `import { Button } from 'Components/ui/button'`, that would also work with `@`.
        },
    },
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
