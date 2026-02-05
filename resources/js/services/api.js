import axios from 'axios';

// Laravel handles the base URL automatically if you use relative paths
// or point to your localhost port 8000
const apiClient = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
    withCredentials: true, 
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

export default {
    login(credentials) {
        return apiClient.post('/login', credentials);
    },
    register(userData) {
        return apiClient.post('/register', userData);
    },
    setToken(token) {
        localStorage.setItem('auth_token', token);
        apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
};