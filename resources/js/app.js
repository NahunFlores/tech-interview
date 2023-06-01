require('./bootstrap');
import { createApp } from 'vue'
import App from './views/App.vue'


import { createRouter, createWebHistory } from 'vue-router'
import { routes } from './routes';

import axios from 'axios';
import VueAxios from 'vue-axios';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import store from './store'

const router = createRouter({
    history: createWebHistory(),
    routes,
})

const app = createApp(App);
app.use(VueAxios, axios);
app.use(router);
app.use(store);
app.use(VueSweetalert2);

app.mount('#app');
