import './bootstrap';
import { createApp } from 'vue'
import  App  from '../js/App.vue';
import '../css/app.css'
import router from './router';
const app = createApp(App)
app.use(router).mount('#app')