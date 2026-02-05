import { createRouter, createWebHistory } from 'vue-router';

// We will create these components in the next step
import Login from './components/Login.vue';
import Register from './components/Register.vue';


const routes = [
    { path: '/', component: Login, name: 'Login' },
    { path: '/register', component: Register, name: 'Register' },
    
    
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;