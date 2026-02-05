import axios from 'axios';

// Create axios instance
const apiClient = axios.create({
    baseURL: 'http://localhost:8000/api',
    withCredentials: true, 
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Add token interceptor
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

// Add response interceptor for error handling
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/';
        }
        return Promise.reject(error);
    }
);

// Create and export API methods
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
    }
};

// Export the API object
export default api;