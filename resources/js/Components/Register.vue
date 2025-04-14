<template>
    <div class="container mt-5">
      <h2 class="mb-4">Register</h2>
      <form @submit.prevent="handleRegister">
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input v-model="form.name" type="text" class="form-control" id="name" required />
        </div>
  
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input v-model="form.email" type="email" class="form-control" id="email" required />
        </div>
  
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input v-model="form.password" type="password" class="form-control" id="password" required />
        </div>
  
        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" class="form-control" id="password_confirmation" required />
        </div>
  
        <button type="submit" class="btn btn-primary" :disabled="loading">
          {{ loading ? 'Registering...' : 'Register' }}
        </button>
      </form>
  
      <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
      <div v-if="success" class="alert alert-success mt-3">Registration successful!</div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  import { useRouter } from 'vue-router';

  
  const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  });
  
  const loading = ref(false);
  const error = ref(null);
  const success = ref(false);
  const router = useRouter();

  
  const handleRegister = async () => {
    loading.value = true;
    error.value = null;
    success.value = false;
  
    try {
      const response = await axios.post('/api/register', form.value);
      success.value = true;
      form.value = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      };
      setTimeout(() => {
      router.push('/login');
    }, 500);
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed.';
    } finally {
      loading.value = false;
    }
  };
  </script>
  