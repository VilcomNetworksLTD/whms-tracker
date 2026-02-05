<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-sm p-6 bg-white shadow-lg rounded-lg">
      
      <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Create Account</h2>
      
      <form @submit.prevent="handleRegister">
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
          <input 
            v-model="form.name" 
            type="text" 
            class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none" 
            required
          >
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Email (Vilcom only)</label>
          <input 
            v-model="form.email" 
            type="email" 
            class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none" 
            placeholder="name@vilcom.co.ke"
            required
          >
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input 
            v-model="form.password" 
            type="password" 
            class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none" 
            required
          >
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
          <input 
            v-model="form.password_confirmation" 
            type="password" 
            class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none" 
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
          {{ loading ? 'Creating Account...' : 'Register' }}
        </button>

        <p class="mt-4 text-center text-sm text-gray-600">
          Already have an account? 
          <router-link to="/" class="text-blue-600 hover:underline font-medium">Login here</router-link>
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
  name: 'Register',
  components: { OtpModal },
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      loading: false,
      error: '',
      showOtpModal: false
    };
  },
  methods: {
    async handleRegister() {
      this.loading = true;
      this.error = '';

      try {
        // 1. Submit Registration Data
        await axios.post('/api/register', this.form);
        
        // 2. If successful, Open OTP Modal
        this.showOtpModal = true; 

      } catch (err) {
        // Handle Validation Errors
        if (err.response && err.response.data.message) {
          this.error = err.response.data.message;
        } else {
          this.error = 'Registration failed. Please check your connection.';
        }
      } finally {
        this.loading = false;
      }
    },
    onVerificationSuccess() {
      // 3. User verified OTP successfully inside modal -> Redirect
      this.$router.push('/dashboard');
    }
  }
};
</script>