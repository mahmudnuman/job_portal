<template>
    <div class="container mt-5" style="max-width: 400px;">
      <h3 class="text-center mb-4">Login</h3>
  
      <div v-if="error" class="alert alert-danger">
        {{ error }}
      </div>
  
      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input
            type="email"
            class="form-control"
            id="email"
            v-model="form.email"
            required
          />
        </div>
  
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input
            type="password"
            class="form-control"
            id="password"
            v-model="form.password"
            required
          />
        </div>
  
        <button type="submit" class="btn btn-primary w-100" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          Login
        </button>
      </form>
  
      <div class="text-center mt-3">
        <router-link to="/register">Don't have an account? Register</router-link>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from 'axios';
  
  const router = useRouter();
  const loading = ref(false);
  const error = ref(null);
  const form = ref({
    email: '',
    password: ''
  });
  
  const handleLogin = async () => {
    error.value = null;
    loading.value = true;
  
    try {
      const response = await axios.post('/api/login', form.value);
  
      localStorage.setItem('token', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
  
      router.push('/');
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed. Please try again.';
    } finally {
      loading.value = false;
    }
  };
  </script>
  