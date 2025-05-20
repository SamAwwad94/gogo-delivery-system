<template>
    <n-config-provider :theme="theme">
        <n-message-provider>
            <div class="min-h-screen bg-gray-50">
                <AppHeader />
                <div class="w-full px-6 py-6">
                    <EnhancedOrdersTable />
                </div>
            </div>
        </n-message-provider>
    </n-config-provider>
</template>

<script setup>
import { ref, defineAsyncComponent } from "vue";
import {
    NConfigProvider,
    NMessageProvider,
    darkTheme,
    NButton,
    NIcon,
    NInput,
    NSelect,
    NDatePicker,
    NPopover,
} from "naive-ui";

// Use dynamic imports for components
const AppHeader = defineAsyncComponent(() => import("./AppHeader.vue"));
const EnhancedOrdersTable = defineAsyncComponent({
    loader: () => import("./EnhancedOrdersTable.vue"),
    delay: 200,
    timeout: 5000,
    // Add a loading component if needed
    loadingComponent: {
        template: `
            <div class="p-4 text-center">
                <div class="inline-block h-6 w-6 animate-spin rounded-full border-4 border-solid border-blue-500 border-r-transparent"></div>
                <p class="mt-2 text-sm text-gray-600">Loading table...</p>
            </div>
        `,
    },
});

// Theme configuration (can be toggled between light/dark)
const theme = ref(null); // null is light theme, darkTheme for dark mode
</script>

<style>
/* Tailwind is imported in the main CSS file */
</style>
