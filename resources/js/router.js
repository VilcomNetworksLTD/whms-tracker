import { createRouter, createWebHistory } from 'vue-router';

// We will create these components in the next step
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import ForgotPassword from './components/ForgotPassword.vue';
import Dashboard from './components/Dashboard.vue';

const routes = [
    { path: '/', component: Login, name: 'Login' },
    { path: '/register', component: Register, name: 'Register' },
     {
        path: '/forgot-password',
        name: 'ForgotPassword',
        component: ForgotPassword
    },
    { path:"/dashboard", component : Dashboard, name:"Dashboard"}
    
    
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;