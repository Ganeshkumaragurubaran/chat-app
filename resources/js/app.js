import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js'
import router from './routes.js'
import store from './store/store.js';
import '@fortawesome/fontawesome-free/css/all.css'

import App from './App.vue';
const app = createApp(App);



const token = localStorage.getItem('token');

if (token) {
    // Set the token in Vuex store on page load
    store.commit('setToken', token);
}

app.use(store);
app.use(router);
app.mount('#app');
