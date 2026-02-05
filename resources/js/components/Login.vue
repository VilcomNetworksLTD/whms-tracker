<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-sm p-6 bg-white shadow-lg rounded-lg">
      
      <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Login to WHMS</h2>

      <form @submit.prevent="handleLogin">
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
          <input 
            v-model="form.email" 
            type="email" 
            class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" 
            placeholder="name@vilcom.co.ke"
            required
          >
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input 
            v-model="form.password" 
            type="password" 
            class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" 
            placeholder="••••••••"
            required
          >
        </div>
        
        <div v-if="error" class="mb-4 p-3 bg-red-100 text-red-700 text-sm rounded border border-red-200">
          {{ error }}
        </div>

        <button 
          type="submit" 
          class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-blue-700 transition duration-200 flex justify-center items-center"
          :disabled="loading"
        >
          <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>

        <p class="mt-4 text-center text-sm text-gray-600">
          Don't have an account? 
          <router-link to="/register" class="text-blue-600 hover:underline font-medium">Register here</router-link>
        </p>
      </form>
    </div>

    <OtpModal 
        :is-visible="showOtpModal" 
        :email="form.email"
        @close="showOtpModal = false"
        @verified="onVerificationSuccess"
    />
  </div>
</template>

<script>
import axios from 'axios';
import OtpModal from './OtpModal.vue';

export default {
  name: 'Login',
  components: { OtpModal },
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      loading: false,
      error: '',
      showOtpModal: false
    };
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.error = '';

      try {
        const response = await axios.post('/api/login', this.form);
        
        // Success: Store token & redirect
        localStorage.setItem('token', response.data.token);
        this.$router.push('/dashboard');

      } catch (err) {
        // Handle Validation or Server Errors
        let message = 'Login failed. Please check your credentials.';
        
        if (err.response && err.response.data) {
           message = err.response.data.message || message;
           
           // If detailed errors object exists (Laravel validation)
           if (err.response.data.errors && err.response.data.errors.email) {
             message = err.response.data.errors.email[0];
           }
        }

        // INTELLIGENT CHECK: Is the error asking for verification?
        if (message.toLowerCase().includes('verify')) {
            this.showOtpModal = true; // Open the popup automatically
            this.error = ''; // Clear error since we are handling it
        } else {
            this.error = message;
        }
      } finally {
        this.loading = false;
      }
    },
    onVerificationSuccess() {
      // Called when the OtpModal successfully verifies the user
      this.showOtpModal = false;
      this.$router.push('/dashboard');
    }
  }
};
</script>