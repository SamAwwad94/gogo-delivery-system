import { createApp } from 'vue';

// Create a simple Vue app
const app = createApp({
    template: `
        <div class="p-4">
            <h1 class="text-2xl font-bold mb-4">Simple Vue Test</h1>
            <p class="mb-4">Counter: {{ counter }}</p>
            <button 
                @click="increment" 
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
                Increment
            </button>
        </div>
    `,
    data() {
        return {
            counter: 0
        };
    },
    methods: {
        increment() {
            this.counter++;
        }
    }
});

// Mount the app
app.mount('#simple-test-app');
