import { createApp } from 'vue';
import App from './App.vue';
import router from './Router';
import 'bootstrap/dist/css/bootstrap.min.css';  // Importing Bootstrap CSS globally

const app = createApp(App);
app.use(router);  // Use the router
app.mount('#app');