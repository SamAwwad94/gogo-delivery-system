import { createApp, defineAsyncComponent } from "vue";
import { createPinia } from "pinia";
import {
    create,
    NButton,
    NIcon,
    NInput,
    NSelect,
    NDatePicker,
    NPopover,
    NConfigProvider,
    NMessageProvider,
    NTag,
    NTooltip,
} from "naive-ui";

// Import vue3-easy-data-table styles
import "vue3-easy-data-table/dist/style.css";
import EasyDataTable from "vue3-easy-data-table";

// Show loading indicator
const loadingElement = document.createElement("div");
loadingElement.className = "loading-indicator";
loadingElement.innerHTML = `
  <div class="flex items-center justify-center p-6">
    <div class="inline-block h-6 w-6 animate-spin rounded-full border-4 border-solid border-blue-500 border-r-transparent"></div>
    <span class="ml-2 text-gray-600">Loading application...</span>
  </div>
`;
const mountPoint = document.getElementById("new-orders-shadcn-app");
if (mountPoint) {
    mountPoint.appendChild(loadingElement);
}

// Dynamically import the main App component
const App = defineAsyncComponent({
    loader: () => import("./components/new-orders-shadcn/App.vue"),
    delay: 200,
    timeout: 5000,
});

// Create the Vue application
const app = createApp(App);

// Add Pinia for state management
app.use(createPinia());

// Register Naive UI components
const naive = create({
    components: [
        NButton,
        NIcon,
        NInput,
        NSelect,
        NDatePicker,
        NPopover,
        NConfigProvider,
        NMessageProvider,
        NTag,
        NTooltip,
    ],
});
app.use(naive);

// Register EasyDataTable component
app.component("EasyDataTable", EasyDataTable);

// Mount the app and remove loading indicator when ready
app.mount("#new-orders-shadcn-app");

// Remove loading indicator after app is mounted
setTimeout(() => {
    if (loadingElement.parentNode) {
        loadingElement.parentNode.removeChild(loadingElement);
    }
}, 1000);
