import './bootstrap';
import { createApp } from 'vue';

// Import your new component
import App from './components/app.vue';
import AuthComponent from './components/AuthComponent.vue';
import router from './router';

const app = createApp(App);


app.component('auth-component', AuthComponent);

app.use(router);
app.mount('#app');