<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col items-center justify-center p-4">
    <!-- App Header -->
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

    <!-- Login Card -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
      <div class="px-8 py-6">
        <!-- Header -->
        <div class="text-center mb-8">
          <h2 class="text-2xl font-bold text-gray-800">Welcome Back</h2>
          <p class="text-gray-500 mt-2">Sign in to your Web Tracker account</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="space-y-5">
          <!-- Email Input -->
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
                placeholder="you@example.com" 
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                required
                :disabled="loading"
              >
            </div>
          </div>

          <!-- Password Input -->
          <div>
            <div class="flex justify-between items-center mb-2">
              <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
              <a href="#" class="text-sm text-blue-600 hover:text-blue-500 font-medium">Forgot password?</a>
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
                placeholder="Enter your password" 
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                required
                :disabled="loading"
              >
            </div>
          </div>

          <!-- Remember Me Checkbox -->
          <div class="flex items-center">
            <input 
              id="remember" 
              type="checkbox" 
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label for="remember" class="ml-2 block text-sm text-gray-700">
              Remember me for 30 days
            </label>
          </div>

          <!-- Submit Button -->
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

          <!-- Error Message -->
          <div v-if="error" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
              <svg class="h-5 w-5 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="text-red-700 text-sm font-medium">{{ error }}</p>
            </div>
          </div>
        </form>      

        <!-- Register Link -->
        <div class="text-center pt-4 border-t border-gray-200">
          <p class="text-gray-600">
            Don't have an account?
            <router-link to="/register" class="font-semibold text-blue-600 hover:text-blue-500 ml-1">
              Create an account
            </router-link>
          </p>
        </div>
      </div>
      
      <!-- Footer -->
      <div class="bg-gray-50 px-8 py-4 text-center">
        <p class="text-xs text-gray-500">
          By signing in, you agree to our 
          <a href="#" class="text-blue-600 hover:text-blue-500">Terms</a> and 
          <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
        </p>
      </div>
    </div>

    <!-- Demo Credentials (for development only) -->
    <div v-if="isDevelopment" class="mt-8 p-4 bg-blue-50 rounded-lg max-w-md">
      <p class="text-sm text-blue-800 font-medium mb-2">Demo Credentials:</p>
      <div class="text-xs text-blue-700 space-y-1">
        <p>Email: demo@webtracker.app</p>
        <p>Password: demo123</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: { email: '', password: '' },
      loading: false,
      error: '',
      isDevelopment: process.env.NODE_ENV === 'development'
    };
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.error = '';
      try {
        const response = await axios.post('/api/login', this.form);
        // Save Token
        localStorage.setItem('token', response.data.token);
        // Save user info if provided
        if (response.data.user) {
          localStorage.setItem('user', JSON.stringify(response.data.user));
        }
        // Show success message
        this.$emit('login-success', response.data.user);
        
        // Small delay for better UX
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Redirect
        this.$router.push('/dashboard');
      } catch (err) {
        this.error = err.response?.data?.message || 'Login failed. Please check your credentials.';
        // Shake animation on error
        const form = document.querySelector('form');
        form.classList.add('animate-shake');
        setTimeout(() => form.classList.remove('animate-shake'), 500);
      } finally {
        this.loading = false;
      }
    }
  },
  mounted() {
    // Focus on email input when component mounts
    document.getElementById('email')?.focus();
    
    // Add custom animation for shake effect
    const style = document.createElement('style');
    style.textContent = `
      @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
      }
      .animate-shake {
        animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
      }
    `;
    document.head.appendChild(style);
  }
};
</script>

<style scoped>
/* Custom gradient text animation */
@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.animate-gradient {
  background-size: 200% 200%;
  animation: gradient 3s ease infinite;
}

/* Smooth transitions */
input, button {
  transition: all 0.2s ease-in-out;
}

/* Focus styles */
input:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>