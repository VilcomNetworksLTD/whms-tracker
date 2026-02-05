<template>
  <div v-if="isVisible" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
    
    <div class="bg-white p-8 rounded-lg shadow-xl w-96 relative">
      
      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>

      <h2 class="text-2xl font-bold mb-2 text-center text-gray-800">Enter Verification Code</h2>
      <p class="text-center text-sm text-gray-600 mb-6">
        Code sent to <strong>{{ email }}</strong>
      </p>

      <form @submit.prevent="handleVerify">
        <div class="mb-4">
            <input 
                v-model="otp" 
                type="text" 
                class="w-full text-center text-2xl tracking-[0.5em] p-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-0 font-bold" 
                maxlength="6" 
                placeholder="000000"
                required
            >
        </div>

        <div v-if="error" class="mb-4 text-red-500 text-sm text-center bg-red-50 p-2 rounded">
            {{ error }}
        </div>

        <button 
            type="submit" 
            class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 disabled:opacity-50"
            :disabled="loading"
        >
            {{ loading ? 'Verifying...' : 'Verify Email' }}
        </button>
      </form>
      
       div class="mt-4 text-center">
    <button 
        type="button" 
        @click="handleResend" 
        class="text-sm text-blue-500 hover:underline disabled:text-gray-400 disabled:no-underline"
        :disabled="resendLoading"
    >
        {{ resendLoading ? 'Sending...' : 'Resend Code' }}
    </button>
    
    <p v-if="resendSuccess" class="text-xs text-green-600 mt-1">
        New code sent! Check your inbox.
    </p>
</div>
    </div>
  
</template>

<script>
import axios from 'axios';

export default {
    name: 'OtpModal',
    props: {
        isVisible: Boolean, // Controls showing/hiding
        email: String       // The email we are verifying
    },
    data() {
        return {
            otp: '',
            loading: false,
            error: ''
        };
    },
    methods: {
        async handleVerify() {
            this.loading = true;
            this.error = '';

            try {
                const response = await axios.post('/api/verify-otp', {
                    email: this.email,
                    otp: this.otp
                });

                // Success! Save token and tell parent we are done
                localStorage.setItem('token', response.data.token);
                this.$emit('verified'); 

            } catch (err) {
                this.error = err.response?.data?.message || 'Invalid code. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>