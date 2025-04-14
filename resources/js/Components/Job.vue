<template>
    <div class="job-detail-page">
      <MetaHead
        :title="`${job.title} at ${job.company_name} | Your Company`"
        :description="job.meta_description || job.description"
        :keywords="job.keywords ? job.keywords.split(',') : []"
      />
  
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
      </div>
  
      <div v-if="error" class="error-state">
        <p>{{ error }}</p>
        <button @click="fetchJob" class="retry-button">Retry</button>
      </div>
  
      <div v-if="job" class="job-container">
        <div class="job-header">
          <h1 class="job-title">{{ job.title }}</h1>
          <h2 class="company-name">{{ job.company_name }}</h2>
          
          <div class="job-meta">
            <span class="meta-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
              </svg>
              Remote/On-site
            </span>
            <span class="meta-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M6 .5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1H9v1.07a7.001 7.001 0 0 1 3.274 12.474l.601.602a.5.5 0 0 1-.707.708l-.746-.746A6.97 6.97 0 0 1 8 16a6.97 6.97 0 0 1-3.422-.892l-.746.746a.5.5 0 0 1-.707-.708l.602-.602A7.001 7.001 0 0 1 7 2.07V1h-.5A.5.5 0 0 1 6 .5zm2.5 5a.5.5 0 0 0-1 0v3.5a.5.5 0 0 0 1 0V5.5z"/>
              </svg>
              Apply before {{ formatDate(job.expiry_date) }}
            </span>
            <span class="meta-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
              </svg>
              Posted on {{ formatDate(job.created_at) }}
            </span>
          </div>
        </div>
  
        <div class="job-content">
          <div class="job-description">
            <h3>Job Description</h3>
            <p>{{ job.description }}</p>
          </div>
  
          <div class="job-actions">
            <button class="apply-button">Apply Now</button>
          </div>
        </div>
  
        <div class="job-stats">
          <span class="stat-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
              <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
            </svg>
            {{ job.view_count }} views
          </span>
          <span class="stat-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
            </svg>
            {{ job.applications_count }} applicants
          </span>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  import { ref } from "vue";
  import { useRoute } from "vue-router";
  import MetaHead from "@/Components/MetaHead.vue";
  
  export default {
    components: {
      MetaHead
    },
    setup() {
      const job = ref(null);
      const loading = ref(true);
      const error = ref(null);
      const route = useRoute();
  
      const fetchJob = async () => {
        loading.value = true;
        error.value = null;
        try {
          const response = await axios.get(`/api/listings/${route.params.id}`);
          job.value = response.data;
        } catch (err) {
          error.value = "Failed to load job details. Please try again later.";
          console.error("Error fetching job:", err);
        } finally {
          loading.value = false;
        }
      };
  
      const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
      };
  
      fetchJob();
  
      return {
        job,
        loading,
        error,
        fetchJob,
        formatDate
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
  
  .loading-state, .error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 300px;
  }
  
  .spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-left-color: #4299e1;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
  
  .error-state {
    color: #e53e3e;
    text-align: center;
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
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
  }
  
  .apply-button, .save-button {
    padding: 0.75rem 1.5rem;
    border-radius: 0.25rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .apply-button {
    background-color: #4299e1;
    color: white;
    border: none;
  }
  
  .apply-button:hover {
    background-color: #3182ce;
  }
  
  .save-button {
    background-color: white;
    color: #4299e1;
    border: 1px solid #4299e1;
  }
  
  .save-button:hover {
    background-color: #ebf8ff;
  }
  
  .job-stats {
    display: flex;
    gap: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
    color: #718096;
    font-size: 0.875rem;
  }
  
  .stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  @media (max-width: 768px) {
    .job-title {
      font-size: 1.5rem;
    }
    
    .company-name {
      font-size: 1.1rem;
    }
    
    .job-meta {
      gap: 1rem;
    }
    
    .job-actions {
      flex-direction: column;
    }
  }
  </style>