<template>
  <div class="container mt-4" style="max-width: 800px;">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading job details...</p>
    </div>

    <!-- Error State -->
    <div v-if="error" class="alert alert-danger text-center">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      {{ error }}
      <button @click="fetchJob(route.params.id)" class="btn btn-sm btn-outline-danger ms-3">
        Retry
      </button>
    </div>

    <!-- Job Content -->
    <div v-if="job" class="card border-0 shadow-sm">
      <div class="card-body">
        <!-- Job Header -->
        <div class="mb-4">
          <h1 class="h2 mb-2">{{ job.title }}</h1>
          <h2 class="h5 text-muted mb-3">{{ job.company_name }}</h2>
          
          <div class="d-flex flex-wrap gap-3 text-muted mb-3">
            <span>
              <i class="bi bi-geo-alt me-1"></i>
              {{ job.remote_ok ? 'Remote' : 'On-site' }}
            </span>
            <span>
              <i class="bi bi-clock me-1"></i>
              Apply before {{ formatDate(job.expiry_date) }}
            </span>
            <span>
              <i class="bi bi-calendar me-1"></i>
              Posted {{ formatDate(job.created_at) }}
            </span>
          </div>
        </div>

        <!-- Job Description -->
        <div class="mb-4">
          <h3 class="h5 mb-3">Job Description</h3>
          <div v-html="job.description" class="job-description"></div>
        </div>

        <!-- Application Section -->
        <div class="border-top pt-4">
          <div v-if="applicationSuccess" class="alert alert-success mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ applicationSuccess }}
          </div>

          <div v-if="applicationError" class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-octagon-fill me-2"></i>
            {{ applicationError }}
          </div>

          <button
            @click="handleApply"
            class="btn btn-primary w-100"
            :disabled="applicationLoading || alreadyApplied"
          >
            <template v-if="applicationLoading">
              <span class="spinner-border spinner-border-sm me-2"></span>
              Processing...
            </template>
            <template v-else>
              {{ alreadyApplied ? 'Already Applied' : 'Apply Now' }}
            </template>
          </button>
        </div>
      </div>

      <!-- Job Stats -->
      <div class="card-footer bg-transparent text-muted small">
        <i class="bi bi-eye me-1"></i>
        {{ job.view_count }} views
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

// Reactive state
const job = ref(null);
const loading = ref(true);
const error = ref(null);
const applicationLoading = ref(false);
const applicationError = ref(null);
const applicationSuccess = ref(null);
const alreadyApplied = ref(false);

// Helper functions
const getAuthToken = () => {
  try {
    return localStorage.getItem('authToken') || sessionStorage.getItem('authToken');
  } catch (e) {
    console.error('Storage access error:', e);
    return null;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  try {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  } catch {
    return 'Invalid date';
  }
};

// Data fetching
const fetchJob = async (jobId) => {
  if (!jobId) {
    error.value = 'Invalid job ID';
    loading.value = false;
    return;
  }

  loading.value = true;
  error.value = null;
  job.value = null;

  try {
    const token = getAuthToken();
    const headers = token ? { Authorization: `Bearer ${token}` } : {};
    
    const response = await axios.get(`/api/listings/${jobId}`, { 
      headers,
      validateStatus: status => status < 500
    });

    if (response.status === 401) {
      router.push({ path: '/login', query: { redirect: route.fullPath } });
      return;
    }

    if (response.status === 404) {
      error.value = 'Job not found';
      return;
    }

    job.value = response.data;
    alreadyApplied.value = response.data.has_applied || false;
  } catch (err) {
    handleFetchError(err);
  } finally {
    loading.value = false;
  }
};

const handleFetchError = (err) => {
  console.error('Fetch error:', err);
  
  if (!err.response) {
    error.value = 'Network error. Please check your connection.';
    return;
  }

  switch (err.response.status) {
    case 401:
      error.value = 'Please login to view this job';
      break;
    case 403:
      error.value = 'You dont have permission to view this job';
      break;
    case 404:
      error.value = 'Job not found or has been removed';
      break;
    default:
      error.value = err.response.data?.message || 
                   'Failed to load job details. Please try again.';
  }
};

// Application handling
const handleApply = async () => {
  if (alreadyApplied.value) return;

  const token = getAuthToken();
  if (!token) {
    router.push({ path: '/login', query: { redirect: route.fullPath } });
    return;
  }

  applicationLoading.value = true;
  applicationError.value = null;
  applicationSuccess.value = null;

  try {
    const response = await axios.post(
      `/api/listings/${route.params.id}/apply`,
      {},
      { headers: { Authorization: `Bearer ${token}` } }
    );

    if (response.status === 201) {
      applicationSuccess.value = response.data.message || 'Application submitted successfully!';
      alreadyApplied.value = true;
      // Refresh job data to reflect application status
      await fetchJob(route.params.id);
    }
  } catch (err) {
    handleApplicationError(err);
  } finally {
    applicationLoading.value = false;
  }
};

const handleApplicationError = (err) => {
  console.error('Application error:', err);
  
  if (err.response?.status === 401) {
    applicationError.value = 'Session expired. Please login again.';
    setTimeout(() => router.push('/login'), 2000);
    return;
  }

  if (err.response?.status === 410) {
    alreadyApplied.value = true;
    applicationError.value = err.response.data?.message || 'You have already applied to this position';
    return;
  }

  applicationError.value = err.response?.data?.message || 
                         'Failed to submit application. Please try again.';
  
  // Clear error after 5 seconds
  setTimeout(() => {
    applicationError.value = null;
  }, 5000);
};

// Lifecycle hooks
onMounted(() => fetchJob(route.params.id));
watch(() => route.params.id, (newId) => newId && fetchJob(newId));
</script>

<style scoped>
.job-description :deep(p) {
  margin-bottom: 1rem;
  line-height: 1.6;
}

.job-description :deep(ul) {
  padding-left: 1.5rem;
  margin-bottom: 1rem;
}

.job-description :deep(li) {
  margin-bottom: 0.5rem;
}
</style>