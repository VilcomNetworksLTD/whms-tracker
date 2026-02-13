import axios from 'axios';

const apiClient = axios.create({
    // baseURL: 'http://localhost:8001/api',
    baseURL:'https://webtracker.vilcom-net.co.ke/api',
    withCredentials: true, 
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
});

// Token interceptor
apiClient.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor for error handling
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        // Handle 401 Unauthorized errors
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            // Don't redirect if we're already on the login page
            if (!window.location.pathname.includes('/')) {
                window.location.href = '/';
            }
        }
        return Promise.reject(error);
    }
);

const api = {
    // Auth methods
    login(credentials) {
        return apiClient.post('/login', credentials);
    },
    
    register(userData) {
        return apiClient.post('/register', userData);
    },
    
    verifyOTP(data) {
        return apiClient.post('/verify-otp', data);
    },
    
    resendOTP(data) {
        return apiClient.post('/resend-otp', data);
    },
    
    logout() {
        return apiClient.post('/logout');
    },
    
    setToken(token) {
        localStorage.setItem('token', token);
        apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    },
    
    // Helper method to get current user
    getCurrentUser() {
        try {
            const userStr = localStorage.getItem('user');
            return userStr ? JSON.parse(userStr) : null;
        } catch (e) {
            console.error('Failed to parse user data:', e);
            return null;
        }
    },
    
    // Check if user is authenticated
    isAuthenticated() {
        const token = localStorage.getItem('token');
        const user = this.getCurrentUser();
        return !!(token && user && user.id);
    },
    
    // Tracker Forms methods
    getTrackerForms(params = {}) {
        return apiClient.get('/tracker-forms', { params });
    },
    
    createTrackerForm(data) {
        return apiClient.post('/tracker-forms', data);
    },
    
    updateTrackerForm(id, data) {
        return apiClient.put(`/tracker-forms/${id}`, data);
    },
    
    deleteTrackerForm(id) {
        return apiClient.delete(`/tracker-forms/${id}`);
    },
    
    getDashboardStats() {
        return apiClient.get('/tracker-forms/stats');
    },
    
    markAsCompleted(id, data) {
        return apiClient.post(`/tracker-forms/${id}/complete`, data);
    },
    
    bulkDeleteForms(ids) {
        return apiClient.post('/tracker-forms/bulk-delete', { ids });
    },
    
    exportToCSV(params = {}) {
        return apiClient.get('/tracker-forms/export', { 
            params,
            responseType: 'blob'
        });
    },
    
    getStats() {
        return apiClient.get('/tracker-forms/stats'); 
    },
};

export default api;