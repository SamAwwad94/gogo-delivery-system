import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import ModernizationTestApp from './components/modernization-test/App.vue';

// Create the Vue application
const app = createApp(ModernizationTestApp);

// Add Pinia for state management
app.use(createPinia());

// Add Toast plugin
app.use(Toast, {
    position: 'top-right',
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: 'button',
    icon: true,
    rtl: false
});

// Mount the app
app.mount('#modernization-test-app');
