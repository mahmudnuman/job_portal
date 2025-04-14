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
          autocomplete="username"
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
          autocomplete="current-password"
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

// Secure storage helper
const authStorage = {
  setToken(token) {
    try {
      localStorage.setItem('authToken', token);
      sessionStorage.setItem('authToken', token); // For session persistence
    } catch (e) {
      console.error('Storage error:', e);
    }
  },
  setUser(user) {
    try {
      localStorage.setItem('userData', JSON.stringify({
        id: user.id,
        email: user.email,
        name: user.name
        // Only store non-sensitive data
      }));
    } catch (e) {
      console.error('Storage error:', e);
    }
  },
  clear() {
    localStorage.removeItem('authToken');
    localStorage.removeItem('userData');
    sessionStorage.removeItem('authToken');
  }
};

const handleLogin = async () => {
  error.value = null;
  loading.value = true;

  try {
    const response = await axios.post('/api/login', form.value, {
      validateStatus: (status) => status < 500
    });

    if (!response.data?.token) {
      throw new Error('Invalid server response');
    }

    // Store auth data
    authStorage.setToken(response.data.token);
    if (response.data.user) {
      authStorage.setUser(response.data.user);
    }

    // Redirect with query param handling
    const redirect = router.currentRoute.value.query.redirect || '/';
    router.push(redirect);

  } catch (err) {
    authStorage.clear();
    error.value = err.response?.data?.message || 
                 err.message || 
                 'Login failed. Please try again.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.container {
  max-width: 400px;
  margin: 0 auto;
}
.alert {
  margin-bottom: 1rem;
}
.spinner-border {
  vertical-align: text-top;
}
</style>