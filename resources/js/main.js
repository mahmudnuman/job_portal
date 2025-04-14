// main.js or main.ts
import { createApp } from 'vue'
import { createPinia } from 'pinia'  // Import Pinia
import App from './App.vue'
import 'bootstrap-icons/font/bootstrap-icons.css';

// Create Pinia instance
const pinia = createPinia()
const app = createApp(App)

// Use Pinia before mounting
app.use(pinia)
app.mount('#app')