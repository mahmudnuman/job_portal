<template>
    <div class="job-listings-page">
      <h1 class="page-title">Job Listings</h1>
  
      <!-- Loading and Error States -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading job listings...</p>
      </div>
      
      <div v-if="error" class="error-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <p>{{ error }}</p>
        <button @click="fetchListings(listings.current_page)" class="retry-button">Retry</button>
      </div>
  
      <!-- Job Listings Grid -->
      <div v-if="!loading && !error" class="listings-container">
  <div v-for="listing in listings.data" :key="listing.id" class="listing-card">
    <div class="listing-header">
      <h2 class="listing-title">
        <a :href="'/listing/' + listing.id" class="listing-link" title="View details of {{ listing.title }}">
          {{ listing.title }}
        </a>
      </h2>
      <span class="company-badge">{{ listing.company_name }}</span>
    </div>
    
    <div class="listing-stats">
      <div class="stat-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
          <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
        </svg>
        <span>{{ listing.view_count }} views</span>
      </div>
    </div>

    <p class="listing-description">
      {{ truncateDescription(listing.description) }}
    </p>

    <div class="listing-dates">
      <div class="date-item">
        <span class="date-label">Posted:</span>
        <span class="date-value">{{ formatDate(listing.created_at) }}</span>
      </div>
      <div class="date-item">
        <span class="date-label">Expires:</span>
        <span class="date-value">{{ formatDate(listing.expiry_date) }}</span>
      </div>
    </div>

    <router-link :to="'/job/' + listing.id" class="view-details-button" title="View full job listing details">
      View Details
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
      </svg>
    </router-link>
  </div>
</div>

  
      <!-- Enhanced Pagination -->
      <div class="pagination-wrapper" v-if="!loading && listings.last_page > 1">
        <div class="pagination-container">
          <button 
            @click="fetchListings(listings.current_page - 1)" 
            :disabled="listings.current_page === 1"
            class="pagination-button prev-next"
            aria-label="Previous page"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
            </svg>
            Previous
          </button>
          
          <div class="page-numbers">
            <button 
              v-for="page in visiblePages" 
              :key="page"
              @click="page !== '...' ? fetchListings(page) : null"
              :class="{ 
                active: listings.current_page === page,
                ellipsis: page === '...'
              }"
              class="pagination-button"
              :aria-label="page === '...' ? 'More pages' : `Go to page ${page}`"
              :disabled="page === '...'"
            >
              {{ page }}
            </button>
          </div>
          
          <button 
            @click="fetchListings(listings.current_page + 1)" 
            :disabled="listings.current_page === listings.last_page"
            class="pagination-button prev-next"
            aria-label="Next page"
          >
            Next
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
            </svg>
          </button>
        </div>
        
        <div class="pagination-summary">
          Showing page {{ listings.current_page }} of {{ listings.last_page }} ({{ listings.total }} total jobs)
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  import { computed } from "vue";
  
  export default {
    data() {
      return {
        listings: {
          data: [],
          current_page: 1,
          last_page: 1,
          total: 0
        },
        loading: true,
        error: null,
      };
    },
    computed: {
      visiblePages() {
        const current = this.listings.current_page;
        const last = this.listings.last_page;
        const delta = 2;
        const range = [];
        
        for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
          range.push(i);
        }
        
        if (current - delta > 2) {
          range.unshift('...');
        }
        if (current + delta < last - 1) {
          range.push('...');
        }
        
        range.unshift(1);
        if (last !== 1) range.push(last);
        
        return range;
      }
    },
    mounted() {
      this.fetchListings(this.listings.current_page);
    },
    methods: {
      async fetchListings(page) {
        this.loading = true;
        this.error = null;
        try {
          const response = await axios.get(`/api/listings?page=${page}`);
          this.listings = response.data;
        } catch (err) {
          this.error = "Failed to fetch listings. Please try again later.";
          console.error("Error fetching listings:", err);
        } finally {
          this.loading = false;
        }
      },
      formatDate(date) {
        if (!date) return 'N/A';
        const formattedDate = new Date(date);
        return formattedDate.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      },
      truncateDescription(description) {
        if (!description) return '';
        const maxLength = 150;
        return description.length > maxLength 
          ? `${description.substring(0, maxLength)}...` 
          : description;
      }
    },
  };
  </script>
  
  <style scoped>
  .job-listings-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
  }
  
  .page-title {
    text-align: center;
    font-size: 2.25rem;
    margin-bottom: 2rem;
    color: #2d3748;
    font-weight: 600;
  }
  
  /* Loading State */
  .loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    text-align: center;
  }
  
  .spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-left-color: #4299e1;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
  }
  
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
  
  /* Error State */
  .error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background-color: #fff5f5;
    border-radius: 0.5rem;
    color: #e53e3e;
    text-align: center;
  }
  
  .error-state svg {
    margin-bottom: 1rem;
  }
  
  .retry-button {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background-color: #e53e3e;
    color: white;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .retry-button:hover {
    background-color: #c53030;
  }
  
  /* Listings Grid */
  .listings-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .listing-card {
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
    height: 100%;
  }
  
  .listing-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  .listing-header {
    margin-bottom: 1rem;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 0.75rem;
  }
  
  .listing-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
  }
  
  .company-badge {
    display: inline-block;
    background-color: #ebf8ff;
    color: #3182ce;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    font-weight: 500;
  }
  
  .listing-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
  }
  
  .stat-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: #4a5568;
  }
  
  .stat-item svg {
    color: #718096;
  }
  
  .listing-description {
    color: #4a5568;
    margin-bottom: 1rem;
    flex-grow: 1;
  }
  
  .listing-dates {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
  }
  
  .date-item {
    display: flex;
    justify-content: space-between;
  }
  
  .date-label {
    font-weight: 500;
    color: #4a5568;
  }
  
  .date-value {
    color: #718096;
  }
  
  .view-details-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background-color: #4299e1;
    color: white;
    border-radius: 0.25rem;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.2s;
  }
  
  .view-details-button:hover {
    background-color: #3182ce;
  }
  
  /* Pagination */
  .pagination-wrapper {
    margin-top: 3rem;
  }
  
  .pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
  }
  
  .page-numbers {
    display: flex;
    gap: 0.5rem;
  }
  
  .pagination-button {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    min-width: 40px;
    height: 40px;
    border: 1px solid #e2e8f0;
    background-color: white;
    color: #4a5568;
    border-radius: 0.25rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  .pagination-button:hover:not(:disabled):not(.ellipsis) {
    background-color: #f7fafc;
    border-color: #cbd5e0;
  }
  
  .pagination-button.active {
    background-color: #4299e1;
    color: white;
    border-color: #4299e1;
  }
  
  .pagination-button[disabled] {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  .pagination-button.ellipsis {
    cursor: default;
    background-color: transparent;
    border: none;
    min-width: auto;
  }
  
  .pagination-button.prev-next {
    background-color: #edf2f7;
    gap: 0.5rem;
  }
  
  .pagination-button.prev-next:hover:not(:disabled) {
    background-color: #e2e8f0;
  }
  
  .pagination-summary {
    text-align: center;
    color: #718096;
    font-size: 0.875rem;
  }
  
  /* Responsive Adjustments */
  @media (max-width: 768px) {
    .listings-container {
      grid-template-columns: 1fr;
    }
    
    .pagination-container {
      gap: 0.25rem;
    }
    
    .page-numbers {
      gap: 0.25rem;
    }
    
    .pagination-button {
      min-width: 36px;
      height: 36px;
      padding: 0.5rem;
    }
  }
  </style>