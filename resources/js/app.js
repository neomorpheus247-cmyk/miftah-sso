import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';

// Create Vue app instance
const app = createApp(App);

// Install plugins
app.use(createPinia());
app.use(router);

// Mount to DOM
app.mount('#app');
