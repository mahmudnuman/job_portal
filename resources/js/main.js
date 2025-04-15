// main.js or main.ts
import { createApp } from 'vue'
import { createPinia } from 'pinia'  // Import Pinia
import App from './App.vue'
import 'bootstrap-icons/font/bootstrap-icons.css';
import { createHead } from '@unhead/vue'; 


// Create Pinia instance
const pinia = createPinia()
const app = createApp(App)
const head = createHead(); // 👈 create the head context


// Use Pinia before mounting
app.use(pinia)
app.use(head); // 👈 VERY important line

app.mount('#app')