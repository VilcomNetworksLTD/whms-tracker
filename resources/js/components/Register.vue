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
      <p class="text-gray-600 text-lg">Join thousands monitoring their web presence</p>
    </div>

    <!-- Registration Card -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
      <div class="px-8 py-6">
        <!-- Progress Steps -->
        <div class="flex items-center justify-between mb-8">
          <div class="flex-1">
            <div class="flex flex-col items-center">
              <div :class="[
                'w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300',
                step === 1 
                  ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' 
                  : 'bg-blue-100 text-blue-600'
              ]">
                1
              </div>
              <span class="text-xs mt-1 text-gray-600">Create Account</span>
            </div>
          </div>
          
          <div class="flex-1 h-1 mx-2">
            <div :class="[
              'h-full transition-all duration-500',
              step === 2 ? 'bg-gradient-to-r from-blue-600 to-indigo-600' : 'bg-gray-300'
            ]"></div>
          </div>
          
          <div class="flex-1">
            <div class="flex flex-col items-center">
              <div :class="[
                'w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300',
                step === 2 
                  ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-lg' 
                  : 'bg-gray-100 text-gray-400'
              ]">
                2
              </div>
              <span class="text-xs mt-1 text-gray-600">Verify Email</span>
            </div>
          </div>
        </div>

        <!-- Step 1: Registration Form -->
        <div v-if="step === 1">
          <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Create Your Account</h2>
            <p class="text-gray-500 mt-2">Start tracking your web presence in minutes</p>
          </div>

          <form @submit.prevent="handleRegister" class="space-y-5">
            <!-- Name Input -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <input 
                  v-model="form.name" 
                  id="name"
                  type="text" 
                  placeholder="John Doe" 
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                  required
                  :disabled="loading"
                >
              </div>
            </div>

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
                  placeholder="name@example.com" 
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                  required
                  :disabled="loading"
                >
              </div>
            </div>

            <!-- Password Input -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
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
                  placeholder="Create a strong password" 
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                  required
                  :disabled="loading"
                >
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <button type="button" @click="togglePasswordVisibility('password')" class="text-gray-400 hover:text-gray-600">
                    <svg v-if="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                  </button>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters with letters and numbers</p>
            </div>

            <!-- Confirm Password -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <input 
                  v-model="form.password_confirmation" 
                  id="password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'" 
                  placeholder="Re-enter your password" 
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                  required
                  :disabled="loading"
                >
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <button type="button" @click="togglePasswordVisibility('confirm')" class="text-gray-400 hover:text-gray-600">
                    <svg v-if="showConfirmPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Terms Agreement -->
            <div class="flex items-start">
              <input 
                id="terms" 
                type="checkbox" 
                v-model="termsAccepted"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1"
                required
              >
              <label for="terms" class="ml-2 block text-sm text-gray-700">
                I agree to the 
                <a href="#" class="text-blue-600 hover:text-blue-500 font-medium">Terms of Service</a> 
                and 
                <a href="#" class="text-blue-600 hover:text-blue-500 font-medium">Privacy Policy</a>
              </label>
            </div>

            <!-- Submit Button -->
            <button 
              type="submit" 
              :disabled="loading || !termsAccepted"
              class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-md"
            >
              <div class="flex items-center justify-center">
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ loading ? 'Sending Verification Code...' : 'Create Account' }}</span>
              </div>
            </button>
          </form>
        </div>

        <!-- Step 2: OTP Verification -->
        <div v-if="step === 2">
          <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-50 to-emerald-50 rounded-full mb-4">
              <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Verify Your Email</h2>
            <p class="text-gray-500 mt-2">
              We've sent a 6-digit code to <br>
              <span class="font-semibold text-gray-700">{{ form.email }}</span>
            </p>
          </div>

          <form @submit.prevent="handleVerify" class="space-y-5">
            <!-- OTP Input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2 text-center">Enter Verification Code</label>
              <div class="flex justify-center space-x-3 mb-2">
                <input 
                  v-for="i in 6" 
                  :key="i"
                  v-model="otpDigits[i-1]" 
                  type="text"
                  maxlength="1"
                  @input="focusNext(i, $event)"
                  @keydown.delete="focusPrev(i, $event)"
                  class="w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors duration-200"
                  :disabled="loading"
                  ref="otpInputs"
                />
              </div>
              <p class="text-center text-xs text-gray-500 mt-2">
                Enter the 6-digit code from your email
              </p>
            </div>

            <!-- Countdown Timer -->
            <div class="text-center">
              <p v-if="countdown > 0" class="text-sm text-gray-600">
                Resend code in <span class="font-semibold text-blue-600">{{ countdown }}s</span>
              </p>
              <button 
                v-else
                type="button"
                @click="resendOTP"
                :disabled="resending"
                class="text-sm text-blue-600 hover:text-blue-500 font-medium disabled:opacity-50"
              >
                {{ resending ? 'Resending...' : 'Resend Code' }}
              </button>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
              <button 
                type="submit" 
                :disabled="loading || otpDigits.join('').length !== 6"
                class="w-full bg-gradient-to-r from-green-500 to-emerald-500 text-white font-semibold py-3 px-4 rounded-lg hover:from-green-600 hover:to-emerald-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-md"
              >
                <div class="flex items-center justify-center">
                  <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <span>{{ loading ? 'Verifying...' : 'Verify & Continue' }}</span>
                </div>
              </button>

              <button 
                type="button" 
                @click="step = 1"
                class="w-full text-gray-600 hover:text-gray-800 font-medium py-2 text-sm hover:underline transition-colors duration-200"
              >
                ← Back to registration
              </button>
            </div>
          </form>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="h-5 w-5 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-red-700 text-sm font-medium">{{ error }}</p>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="bg-gray-50 px-8 py-4 text-center border-t border-gray-200">
        <p class="text-gray-600 text-sm">
          Already have an account?
          <router-link to="/" class="font-semibold text-blue-600 hover:text-blue-500 ml-1">
            Sign in here
          </router-link>
        </p>
      </div>
    </div>

    <!-- Security Badge -->
    <div class="mt-6 flex items-center text-sm text-gray-500">
      <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
      <span>Your data is secured with 256-bit encryption</span>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      step: 1,
      loading: false,
      resending: false,
      error: '',
      termsAccepted: false,
      showPassword: false,
      showConfirmPassword: false,
      countdown: 30,
      timer: null,
      
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      
      otpForm: {
        email: '',
        otp: ''
      },
      
      otpDigits: ['', '', '', '', '', '']
    };
  },
  watch: {
    otpDigits: {
      deep: true,
      handler(newDigits) {
        this.otpForm.otp = newDigits.join('');
      }
    },
    countdown(newValue) {
      if (newValue === 0 && this.timer) {
        clearInterval(this.timer);
      }
    }
  },
  methods: {
    async handleRegister() {
      if (!this.termsAccepted) {
        this.error = 'Please accept the Terms of Service and Privacy Policy';
        return;
      }
      
      if (this.form.password !== this.form.password_confirmation) {
        this.error = 'Passwords do not match';
        return;
      }
      
      this.loading = true;
      this.error = '';
      
      try {
        await axios.post('/api/register', this.form);
        this.otpForm.email = this.form.email;
        
        // Start countdown timer
        this.startCountdown();
        
        // Auto-focus first OTP input when moving to step 2
        this.$nextTick(() => {
          this.step = 2;
          setTimeout(() => {
            if (this.$refs.otpInputs && this.$refs.otpInputs[0]) {
              this.$refs.otpInputs[0].focus();
            }
          }, 100);
        });
        
      } catch (err) {
        this.error = err.response?.data?.message || 'Registration failed. Please check your information.';
      } finally {
        this.loading = false;
      }
    },

    async handleVerify() {
      if (this.otpForm.otp.length !== 6) {
        this.error = 'Please enter the complete 6-digit code';
        return;
      }
      
      this.loading = true;
      this.error = '';
      
      try {
        const response = await axios.post('/api/verify-otp', this.otpForm);
        localStorage.setItem('token', response.data.token);
        
        if (response.data.user) {
          localStorage.setItem('user', JSON.stringify(response.data.user));
        }
        
        // Show success animation
        this.$emit('registration-success', response.data.user);
        
        // Small delay for better UX
        await new Promise(resolve => setTimeout(resolve, 500));
        
        this.$router.push('/dashboard');
      } catch (err) {
        this.error = err.response?.data?.message || 'Invalid verification code. Please try again.';
        // Shake animation on error
        const form = document.querySelector('form');
        form.classList.add('animate-shake');
        setTimeout(() => form.classList.remove('animate-shake'), 500);
      } finally {
        this.loading = false;
      }
    },

    async resendOTP() {
      this.resending = true;
      this.error = '';
      
      try {
        await axios.post('/api/resend-otp', { email: this.form.email });
        this.startCountdown();
        this.error = 'Verification code has been resent to your email';
        setTimeout(() => this.error = '', 3000);
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to resend code. Please try again.';
      } finally {
        this.resending = false;
      }
    },

    startCountdown() {
      this.countdown = 30;
      if (this.timer) clearInterval(this.timer);
      this.timer = setInterval(() => {
        if (this.countdown > 0) {
          this.countdown--;
        } else {
          clearInterval(this.timer);
        }
      }, 1000);
    },

    focusNext(index, event) {
      const value = event.target.value;
      if (value && index < 6 && this.$refs.otpInputs[index]) {
        this.$refs.otpInputs[index].focus();
      }
      
      // Auto-submit if all digits filled
      if (index === 6 && this.otpDigits.every(d => d !== '')) {
        this.handleVerify();
      }
    },

    focusPrev(index, event) {
      if (event.key === 'Backspace' && !event.target.value && index > 1 && this.$refs.otpInputs[index-2]) {
        this.$refs.otpInputs[index-2].focus();
      }
    },

    togglePasswordVisibility(type) {
      if (type === 'password') {
        this.showPassword = !this.showPassword;
      } else {
        this.showConfirmPassword = !this.showConfirmPassword;
      }
    }
  },
  mounted() {
    // Auto-focus name input on mount
    document.getElementById('name')?.focus();
    
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
      
      /* OTP input focus styling */
      input:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
      }
    `;
    document.head.appendChild(style);
  },
  beforeUnmount() {
    if (this.timer) {
      clearInterval(this.timer);
    }
  }
};
</script>

<style scoped>
/* Custom gradient text animation */
.animate-gradient {
  background-size: 200% 200%;
  animation: gradient 3s ease infinite;
}

/* Custom styles for password strength indicator */
.password-strength {
  height: 4px;
  margin-top: 2px;
  border-radius: 2px;
}

/* Smooth transitions */
input, button {
  transition: all 0.2s ease-in-out;
}

/* Custom focus styles for OTP inputs */
input[type="text"]:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>