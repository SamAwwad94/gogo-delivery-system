import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/new-orders.css",
                "resources/js/app.js",
                "resources/js/new-orders-app.js",
                "resources/js/modernization-test.js",
                "resources/js/simple-test.js",
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        // Increase the warning limit to avoid unnecessary warnings
        chunkSizeWarningLimit: 800,

        rollupOptions: {
            output: {
                // Manual chunks configuration
                manualChunks: {
                    // Single bundle for new-orders-shadcn
                    "new-orders-bundle": [
                        "vue",
                        "pinia",
                        "naive-ui",
                        "vue3-easy-data-table",
                        "@vicons/carbon",
                        "dayjs",
                        "axios",
                        "@heroicons/vue/24/outline",
                        "@heroicons/vue/24/solid",
                    ],
                    // Legacy libraries (to be phased out)
                    legacy: ["jquery", "bootstrap", "popper.js", "datatables"],
                },
                // Optimize chunk size
                minifyInternalExports: true,
            },
        },
        // Optimize CSS
        cssCodeSplit: true,
        // Source maps for production (can be disabled for smaller builds)
        sourcemap: false,
    },
});
