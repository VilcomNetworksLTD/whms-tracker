<script setup>
import { ref } from 'vue';
import api from '../services/api'; // Note the relative path change

const isLogin = ref(true);
const errorMessage = ref('');
const successMessage = ref('');

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const handleSubmit = async () => {
  errorMessage.value = '';
  successMessage.value = '';

  try {
    if (isLogin.value) {
      const response = await api.login({
        email: form.value.email,
        password: form.value.password
      });
      api.setToken(response.data.token);
      successMessage.value = "Login Successful!";
    } else {
      await api.register(form.value);
      successMessage.value = "Registration Successful! Please login.";
      isLogin.value = true;
    }
  } catch (error) {
    if (error.response && error.response.data.errors) {
        // Laravel validation errors
      errorMessage.value = Object.values(error.response.data.errors).flat().join(', ');
    } else {
      errorMessage.value = "An error occurred.";
    }
  }
};
</script>

<template>
  <div class="p-6 max-w-sm mx-auto bg-white rounded-xl shadow-md space-y-4">
    <h2 class="text-xl font-bold">{{ isLogin ? 'Login' : 'Register' }}</h2>

    <form @submit.prevent="handleSubmit">
      <div v-if="!isLogin" class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
        <input v-model="form.name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline" required />
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Email (@vilcom.co.ke)</label>
        <input v-model="form.email" type="email" class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline" required />
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
        <input v-model="form.password" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline" required />
      </div>

      <div v-if="!isLogin" class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline" required />
      </div>

      <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
        {{ isLogin ? 'Login' : 'Register' }}
      </button>
    </form>

    <p v-if="errorMessage" class="text-red-500 text-xs italic">{{ errorMessage }}</p>
    <p v-if="successMessage" class="text-green-500 text-xs italic">{{ successMessage }}</p>

    <p class="text-center text-gray-500 text-xs mt-4">
      <a href="#" @click.prevent="isLogin = !isLogin" class="text-blue-500 hover:text-blue-800">
        {{ isLogin ? 'Need an account? Register' : 'Have an account? Login' }}
      </a>
    </p>
  </div>
</template>