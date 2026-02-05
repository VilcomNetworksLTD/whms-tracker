<template>
  <div class="max-w-sm mx-auto mt-20 p-6 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4 text-center">Login</h2>
    
    <form @submit.prevent="handleLogin">
      <input v-model="form.email" type="email" placeholder="Email" class="w-full p-2 border mb-3 rounded" required>
      <input v-model="form.password" type="password" placeholder="Password" class="w-full p-2 border mb-3 rounded" required>
      
      <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
        {{ loading ? 'Logging in...' : 'Login' }}
      </button>

      <p class="mt-4 text-center text-sm">
        No account? <router-link to="/register" class="text-blue-500">Register here</router-link>
      </p>
      
      <p v-if="error" class="text-red-500 text-sm mt-2 text-center">{{ error }}</p>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: { email: '', password: '' },
      loading: false,
      error: ''
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
        // Redirect
        this.$router.push('/dashboard');
      } catch (err) {
        this.error = err.response?.data?.message || 'Login failed';
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>