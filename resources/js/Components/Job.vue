<template>
  <div class="job-detail-page">
    <div v-if="job">
      <MetaHead
        :title="`${job.title} at ${job.company_name} | Your Company`"
        :description="job.meta_description || job.description"
        :keywords="job.keywords ? job.keywords.split(',') : []"
      />
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading job details...</p>
    </div>

    <div v-if="error" class="error-state">
      <p>{{ error }}</p>
      <button @click="fetchJob(route.params.id)" class="retry-button">Retry</button>
    </div>

    <div v-if="job" class="job-container">
      <div class="job-header">
        <h1 class="job-title">{{ job.title }}</h1>
        <h2 class="company-name">{{ job.company_name }}</h2>

        <div class="job-meta">
          <span class="meta-item">
            <i class="bi bi-geo-alt"></i>
            {{ job.remote_ok ? 'Remote' : 'On-site' }}
          </span>
          <span class="meta-item">
            <i class="bi bi-clock"></i>
            Apply before {{ formatDate(job.expiry_date) }}
          </span>
          <span class="meta-item">
            <i class="bi bi-calendar"></i>
            Posted on {{ formatDate(job.created_at) }}
          </span>
        </div>
      </div>

      <div class="job-content">
        <div class="job-description">
          <h3>Job Description</h3>
          <div v-html="job.description"></div>
        </div>

        <div class="job-actions">
          <button 
            @click="handleApply" 
            class="apply-button"
            :disabled="applicationLoading || alreadyApplied"
          >
            <span v-if="applicationLoading" class="spinner"></span>
            {{ getApplyButtonText() }}
          </button>
          
          <div v-if="applicationError" class="alert alert-danger mt-3">
            <i class="bi bi-exclamation-octagon-fill me-2"></i>
            {{ applicationError }}
          </div>
          
          <div v-if="applicationSuccess" class="alert alert-success mt-3">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ applicationSuccess }}
          </div>
        </div>
      </div>

      <div class="job-stats">
        <span class="stat-item">
          <i class="bi bi-eye"></i>
          {{ job.view_count }} views
        </span>
        <span class="stat-item">
          <i class="bi bi-people"></i>
          {{ job.applications_count }} applicants
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { ref, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import MetaHead from "@/Components/MetaHead.vue";

export default {
  components: { MetaHead },
  setup() {
    const job = ref(null);
    const loading = ref(true);
    const error = ref(null);
    const route = useRoute();
    const router = useRouter();
    const applicationLoading = ref(false);
    const applicationError = ref(null);
    const applicationSuccess = ref(null);
    const alreadyApplied = ref(false);

    const getAuthToken = () => {
      try {
        return localStorage.getItem('authToken');
      } catch (e) {
        console.error("LocalStorage access error:", e);
        return null;
      }
    };

    const fetchJob = async (jobId) => {
  if (!jobId) {
    error.value = "Job ID is missing";
    loading.value = false;
    return;
  }

  loading.value = true;
  error.value = null;
  job.value = null;
  alreadyApplied.value = false;

  try {
    const token = getAuthToken();
    const headers = token ? { Authorization: `Bearer ${token}` } : {};
    
    const response = await axios.get(`/api/listings/${jobId}`, { 
      headers,
      validateStatus: status => status < 500 // Avoid throwing for 401, 403
    });

    if (response.status === 401 || response.status === 403) {
      // Token invalid or expired, redirect to login
      window.location.href = '/login';
      return;
    }

    const data = response.data;
    job.value = data;

    if (data.has_applied) {
      alreadyApplied.value = true;
    }
  } catch (err) {
    handleFetchError(err);
  } finally {
    loading.value = false;
  }
};


    const handleApply = async () => {
      if (alreadyApplied.value) return;

      const token = getAuthToken();
      if (!token) {
        router.push({ 
          path: '/login', 
          query: { redirect: route.fullPath } 
        });
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
          applicationSuccess.value = response.data.message || "Application submitted successfully!";
          alreadyApplied.value = true;
          fetchJob(route.params.id);
        }
      } catch (err) {
        handleApplicationError(err);
      } finally {
        applicationLoading.value = false;
      }
    };

    const handleFetchError = (err) => {
      console.error("Fetch error:", err);
      if (err.response?.status === 401) {
        localStorage.removeItem('authToken');
      }

      if (!err.response) {
        error.value = "Network error: Please check your internet connection and try again.";
        return;
      }

      switch (err.response.status) {
        case 400:
          error.value = "Invalid request: The job ID appears to be malformed.";
          break;
        case 401:
          error.value = "Session expired: Please log in again to view this job.";
          break;
        case 403:
          error.value = "Access denied: You don't have permission to view this job.";
          break;
        case 404:
          error.value = "Job not found: The requested job doesn't exist or may have been removed.";
          break;
        case 500:
          error.value = "Server error: Please try again later.";
          break;
        default:
          error.value = err.response.data?.error || 
                       "An unexpected error occurred while loading the job details.";
      }
    };

    const handleApplicationError = (err) => {
      console.error("Application error:", err);
      if (err.response?.status === 401) {
        localStorage.removeItem('authToken');
        applicationError.value = "Your session has expired. Redirecting to login...";
        setTimeout(() => router.push('/login'), 2000);
        return;
      }

      if (err.response?.status === 410) {
        alreadyApplied.value = true;
        applicationError.value = err.response.data.message || "You've already applied to this position.";
      } else {
        applicationError.value = err.response?.data?.message || 
                               "Failed to submit application. Please try again.";
      }

      setTimeout(() => {
        applicationError.value = null;
      }, 5000);
    };

    const getApplyButtonText = () => {
      if (applicationLoading.value) return 'Applying...';
      if (alreadyApplied.value) return 'Already Applied';
      return 'Apply Now';
    };

    const formatDate = (dateString) => {
      if (!dateString) return "N/A";
      try {
        return new Date(dateString).toLocaleDateString("en-US", {
          year: "numeric",
          month: "long",
          day: "numeric",
        });
      } catch {
        return "Invalid date";
      }
    };

    onMounted(() => fetchJob(route.params.id));
    watch(() => route.params.id, (newId) => newId && fetchJob(newId));

    return {
      job,
      loading,
      error,
      route,
      applicationLoading,
      applicationError,
      applicationSuccess,
      alreadyApplied,
      fetchJob,
      handleApply,
      formatDate,
      getApplyButtonText
    };
  }
};
</script>

<style scoped>
.job-detail-page {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 2rem;
}

.spinner {
  width: 2rem;
  height: 2rem;
  border: 0.25rem solid rgba(66, 153, 225, 0.2);
  border-top-color: #4299e1;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-state {
  text-align: center;
  padding: 2rem;
  color: #e53e3e;
}

.retry-button {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background-color: #e53e3e;
  color: white;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
}

.job-header {
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e2e8f0;
}

.job-title {
  font-size: 2rem;
  color: #2d3748;
  margin-bottom: 0.5rem;
}

.company-name {
  font-size: 1.25rem;
  color: #4a5568;
  margin-bottom: 1.5rem;
}

.job-meta {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #4a5568;
  font-size: 0.875rem;
}

.job-content {
  margin-bottom: 2rem;
}

.job-description {
  line-height: 1.6;
  color: #4a5568;
  margin-bottom: 2rem;
}

.job-description h3 {
  font-size: 1.25rem;
  color: #2d3748;
  margin-bottom: 1rem;
}

.job-actions {
  margin-top: 2rem;
}

.apply-button {
  padding: 0.75rem 1.5rem;
  background-color: #4299e1;
  color: white;
  border: none;
  border-radius: 0.25rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.apply-button:hover {
  background-color: #3182ce;
}

.apply-button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
  opacity: 0.7;
}

.alert {
  border-left: 4px solid;
  padding: 0.75rem 1.25rem;
  margin-top: 1rem;
  border-radius: 0.25rem;
}

.alert-danger {
  background-color: #fff5f5;
  border-color: #f56565;
  color: #c53030;
}

.alert-success {
  background-color: #f0fff4;
  border-color: #48bb78;
  color: #2f855a;
}

.job-stats {
  margin-top: 2rem;
  display: flex;
  gap: 1.5rem;
  font-size: 0.875rem;
  color: #4a5568;
}
</style>
