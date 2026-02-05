<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col items-center justify-center p-4">
    
    <div class="text-center mb-10">
      <div class="flex items-center justify-center mb-4">
        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
          Web Tracker
        </h1>
      </div>
      <p class="text-gray-600 text-lg">Monitor and analyze your web presence</p>
    </div>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
      <div class="px-8 py-6">
        
        <div class="text-center mb-8">
          <h2 class="text-2xl font-bold text-gray-800">
            {{ showOtpStep ? 'Verify Account' : 'Welcome Back' }}
          </h2>
          <p class="text-gray-500 mt-2">
            {{ showOtpStep ? `Enter code sent to ${form.email}` : 'Sign in to your Web Tracker account' }}
          </p>
        </div>

        <form v-if="!showOtpStep" @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <input 
                v-model="form.email" 
                id="email"
                type="email" 
                autocomplete="email"
                placeholder="name@vilcom.co.ke" 
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                required
                :disabled="loading"
              >
            </div>
          </div>

          <div>
            <div class="flex justify-between items-center mb-2">
              <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
              <router-link to="/forgot-password" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                  Forgot password?
              </router-link>
            </div>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </div>
              <input 
                v-model="form.password" 
                id="password"
                type="password" 
                autocomplete="current-password"
                placeholder="Enter your password" 
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                required
                :disabled="loading"
              >
            </div>
          </div>

          <button 
            type="submit" 
            :disabled="loading"
            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-md"
          >
            <div class="flex items-center justify-center">
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ loading ? 'Signing In...' : 'Sign In' }}</span>
            </div>
          </button>
        </form>

        <div v-else class="space-y-6 animate-fade-in">
           <OtpInput 
              v-model="otpCode"
              :length="6"
              label=""
              help-text="We just sent a fresh code to your email."
              :error="otpError"
              :show-error="!!otpError"
              @complete="handleVerifyOtp"
           />

           <button 
             @click="handleVerifyOtp"
             :disabled="loading || otpCode.length !== 6"
             class="w-full bg-green-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-md"
           >
             <div class="flex items-center justify-center">
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ loading ? 'Verifying...' : 'Verify Code' }}</span>
             </div>
           </button>

           <button @click="showOtpStep = false" class="w-full text-sm text-gray-500 hover:text-gray-700">
              Cancel
           </button>
        </div>

        <div v-if="error" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="h-5 w-5 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-red-700 text-sm font-medium">{{ error }}</p>
          </div>
        </div>

      </div>

      <div v-if="!showOtpStep" class="bg-gray-50 px-8 py-4 text-center">
        <p class="text-xs text-gray-500">
          By signing in, you agree to our 
          <a href="#" class="text-blue-600 hover:text-blue-500">Terms</a> and 
          <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
        </p>
      </div>

       <div v-if="!showOtpStep" class="text-center pb-6 border-t border-gray-100 pt-4 bg-white">
          <p class="text-gray-600">
            Don't have an account?
            <router-link to="/register" class="font-semibold text-blue-600 hover:text-blue-500 ml-1">
              Create an account
            </router-link>
          </p>
        </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios';
import OtpInput from './OtpInput.vue'; // Make sure this path is correct!

export default {
  name: 'Login',
  components: {
    OtpInput
  },
  data() {
    return {
      form: { email: '', password: '' },
      loading: false,
      error: '',
      
      // OTP State
      showOtpStep: false,
      otpCode: '',
      otpError: ''
    };
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.error = '';
      this.otpError = '';

      try {
        const response = await axios.post('/api/login', this.form);
        // Successful login
        localStorage.setItem('token', response.data.token);
        window.location.href = '/dashboard'; 
      } catch (err) {
        let msg = err.response?.data?.message || 'Login failed';
        
        // Handle Laravel validation array
        if (err.response?.data?.errors?.email) {
          msg = err.response.data.errors.email[0];
        }

        // CRITICAL: Check for the specific "unverified" message
        if (msg.includes('fresh verification code')) {
            // Switch to OTP view
            this.showOtpStep = true;
            this.error = ''; // Clear main error
        } else {
            this.error = msg;
        }
      } finally {
        this.loading = false;
      }
    },

    async handleVerifyOtp() {
      // Don't submit if code is incomplete
      if (this.otpCode.length !== 6) return;

      this.loading = true;
      this.otpError = '';
      
      try {
        const response = await axios.post('/api/verify-otp', {
            email: this.form.email,
            otp: this.otpCode
        });

        // OTP Valid? Get the token and login
        localStorage.setItem('token', response.data.token);
        window.location.href = '/dashboard';

      } catch (err) {
         this.otpError = err.response?.data?.message || 'Invalid code. Please try again.';
         // Also show in global error for visibility
         this.error = this.otpError;
      } finally {
        this.loading = false;
      }
    }
  },
  mounted() {
    document.getElementById('email')?.focus();
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }
};
</script>

<style scoped>
/* Simple fade in animation for the OTP flip */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.3s ease-out forwards;
}

input:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>