<template>
  <div class="max-w-sm mx-auto mt-20 p-6 bg-white shadow-lg rounded-lg">
    
    <div v-if="step === 1">
      <h2 class="text-2xl font-bold mb-4 text-center">Create Account</h2>
      <form @submit.prevent="handleRegister">
        
        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input v-model="form.name" type="text" class="w-full p-2 border rounded mt-1" required>
        </div>

        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input v-model="form.email" type="email" class="w-full p-2 border rounded mt-1" placeholder="name@vilcom.co.ke" required>
        </div>

        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <input v-model="form.password" type="password" class="w-full p-2 border rounded mt-1" required>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" class="w-full p-2 border rounded mt-1" required>
        </div>
        
        <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 disabled:opacity-50" :disabled="loading">
          {{ loading ? 'Sending OTP...' : 'Register' }}
        </button>

        <p class="mt-4 text-center text-sm">
          Already have an account? <router-link to="/" class="text-blue-500">Login here</router-link>
        </p>
      </form>
    </div>

    <div v-if="step === 2">
      <h2 class="text-2xl font-bold mb-2 text-center">Verify Email</h2>
      <p class="text-center text-gray-500 text-sm mb-4">
        We sent a code to <strong>{{ form.email }}</strong>
      </p>

      <form @submit.prevent="handleVerify">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Enter 6-digit Code</label>
          <input v-model="otpForm.otp" type="text" class="w-full p-2 border rounded mt-1 text-center tracking-widest text-xl" maxlength="6" required>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white p-2 rounded hover:bg-green-700 disabled:opacity-50" :disabled="loading">
          {{ loading ? 'Verifying...' : 'Verify & Login' }}
        </button>
        
        <button type="button" @click="step = 1" class="w-full mt-2 text-gray-500 text-sm hover:underline">
          Back to Register
        </button>
      </form>
    </div>

    <div v-if="error" class="mt-4 p-3 bg-red-100 text-red-700 text-sm rounded border border-red-200">
      {{ error }}
    </div>

  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      step: 1, // Controls which form is visible
      loading: false,
      error: '',
      // Form 1: Registration Details
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      // Form 2: OTP Details
      otpForm: {
        email: '', // Will be copied from form.email
        otp: ''
      }
    };
  },
  methods: {
    async handleRegister() {
      this.loading = true;
      this.error = '';
      
      try {
        // 1. Send Registration Data
        await axios.post('/api/register', this.form);
        
        // 2. On Success: Move to Step 2
        this.otpForm.email = this.form.email; // Copy email for verification
        this.step = 2; 
        this.error = ''; // Clear any old errors
        
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

    async handleVerify() {
      this.loading = true;
      this.error = '';

      try {
        // 1. Send OTP
        const response = await axios.post('/api/verify-otp', this.otpForm);
        
        // 2. Save the Token (This logs the user in)
        localStorage.setItem('token', response.data.token);
        
        // 3. Redirect to Dashboard
        this.$router.push('/dashboard');

      } catch (err) {
        if (err.response && err.response.data.message) {
          this.error = err.response.data.message;
        } else {
          this.error = 'Invalid code. Please try again.';
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>