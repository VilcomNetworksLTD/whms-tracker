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
        <div class="mb-6">
            <OtpInput 
                v-model="form.otp"
                :length="6"
                :error="error"
                :show-error="!!error"
                label="Check your Email"
                help-text="We sent a 6-digit code to your email address."
                @complete="onOtpComplete" 
            />
        </div>

        <button 
            @click="step = 3" 
            :disabled="form.otp.length !== 6" 
            class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 transition-all"
        >
          Verify Code
        </button>
        
        <button @click="step = 1" class="w-full mt-4 text-sm text-gray-500 hover:text-gray-700">
            Wrong email? Go back
        </button>
      </div>

      <div v-if="step === 3">
        <form @submit.prevent="handleResetPassword">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input v-model="form.password" type="password" autocomplete="new-password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
          </div>
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" autocomplete="new-password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
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

      <div v-if="error && step !== 2" class="mt-4 p-3 bg-red-50 text-red-600 text-sm text-center rounded border border-red-100">
        {{ error }}
      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios';
import OtpInput from './OtpInput.vue'; // Import your component

export default {
  components: {
    OtpInput // Register it
  },
  data() {
    return {
      step: 1,
      loading: false,
      error: '',
      // We removed 'otpDigits' array because your component handles that!
      form: {
        email: '',
        otp: '', // This string binds directly to your component
        password: '',
        password_confirmation: ''
      }
    };
  },
  methods: {
    async handleSendCode() {
      this.loading = true;
      this.error = '';
      try {
        await axios.post('/api/forgot-password', { email: this.form.email });
        this.step = 2;
      } catch (err) {
        this.error = err.response?.data?.message || 'We could not find that email address.';
      } finally {
        this.loading = false;
      }
    },

    // Optional: Auto-advance when user fills all 6 digits
    onOtpComplete(code) {
        this.form.otp = code;
        // You could uncomment this line to auto-click the button:
        // this.step = 3; 
    },
    
    async handleResetPassword() {
      if (this.form.password !== this.form.password_confirmation) {
        this.error = "Passwords do not match.";
        return;
      }

      this.loading = true;
      this.error = '';

      try {
        // No need to join array anymore, form.otp is already a string
        await axios.post('/api/reset-password', this.form);
        this.step = 4;
      } catch (err) {
        this.error = err.response?.data?.message || 'Invalid code or password format.';
        // If the error is about the OTP code, move back to step 2 to show it nicely
        if (this.error.toLowerCase().includes('code')) {
             this.step = 2;
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>