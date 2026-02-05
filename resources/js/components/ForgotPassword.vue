<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col items-center justify-center p-4">
    
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 p-8">
      
      <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Reset Password</h2>
        <p class="text-gray-500 mt-2 text-sm">Follow the steps to recover your account</p>
      </div>

      <div v-if="step === 1">
        <form @submit.prevent="handleSendCode">
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input v-model="form.email" type="email" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="name@vilcom.co.ke" required>
          </div>
          <button type="submit" :disabled="loading" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50">
            {{ loading ? 'Sending Code...' : 'Send Reset Code' }}
          </button>
        </form>
      </div>

      <div v-if="step === 2">
        <p class="text-center text-sm text-gray-600 mb-6">Enter the 6-digit code sent to <strong>{{ form.email }}</strong></p>
        
        <div class="flex justify-center gap-2 mb-6">
            <input v-for="i in 6" :key="i" v-model="otpDigits[i-1]" type="text" maxlength="1" 
                   @input="focusNext(i, $event)" 
                   ref="otpInputs"
                   class="w-12 h-14 text-center text-xl font-bold border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <button @click="step = 3" :disabled="otpDigits.join('').length !== 6" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50">
          Verify Code
        </button>
      </div>

      <div v-if="step === 3">
        <form @submit.prevent="handleResetPassword">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input v-model="form.password" type="password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
          </div>
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
          </div>
          
          <button type="submit" :disabled="loading" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 disabled:opacity-50">
            {{ loading ? 'Resetting...' : 'Reset Password' }}
          </button>
        </form>
      </div>

      <div v-if="step === 4" class="text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800">Success!</h3>
        <p class="text-gray-600 mb-6 mt-2">Your password has been updated.</p>
        <router-link to="/" class="block w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">
          Go to Login
        </router-link>
      </div>

      <div v-if="error" class="mt-4 p-3 bg-red-50 text-red-600 text-sm text-center rounded border border-red-100">
        {{ error }}
      </div>

      <div v-if="step < 4" class="mt-6 text-center border-t pt-4">
        <router-link to="/" class="text-sm text-blue-600 hover:underline">Back to Login</router-link>
      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      step: 1,
      loading: false,
      error: '',
      otpDigits: ['', '', '', '', '', ''],
      form: {
        email: '',
        otp: '',
        password: '',
        password_confirmation: ''
      }
    };
  },
  methods: {
    // Step 1: Send Email
    async handleSendCode() {
      this.loading = true;
      this.error = '';
      try {
        await axios.post('/api/forgot-password', { email: this.form.email });
        this.step = 2;
        this.$nextTick(() => this.$refs.otpInputs[0].focus());
      } catch (err) {
        this.error = err.response?.data?.message || 'We could not find that email address.';
      } finally {
        this.loading = false;
      }
    },
    
    // Step 3: Final Reset
    async handleResetPassword() {
      if (this.form.password !== this.form.password_confirmation) {
        this.error = "Passwords do not match.";
        return;
      }

      this.loading = true;
      this.error = '';
      this.form.otp = this.otpDigits.join('');

      try {
        await axios.post('/api/reset-password', this.form);
        this.step = 4; // Success Screen
      } catch (err) {
        this.error = err.response?.data?.message || 'Invalid code or password format.';
      } finally {
        this.loading = false;
      }
    },

    focusNext(index, event) {
      if (event.target.value && index < 6) {
        this.$refs.otpInputs[index].focus();
      }
    }
  }
};
</script>