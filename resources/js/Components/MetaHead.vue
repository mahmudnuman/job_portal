<template>
    <!-- Vue template must have a single root element -->
    <div v-if="false"></div> <!-- Hidden element - we only use this component for its side effects -->
  </template>
  
  <script>
  import { watch, onMounted, onUnmounted } from 'vue';
  import { useHead } from '@unhead/vue'; // or your preferred meta management library
  
  export default {
    name: 'MetaHead',
    props: {
      title: {
        type: String,
        default: 'Job Portal | Find Your Dream Job'
      },
      description: {
        type: String,
        default: 'Browse thousands of job listings and find your perfect career opportunity'
      },
      keywords: {
        type: Array,
        default: () => ['jobs', 'careers', 'employment', 'hiring']
      },
      canonicalUrl: {
        type: String,
        default: ''
      },
      ogImage: {
        type: String,
        default: ''
      },
      ogType: {
        type: String,
        default: 'website'
      },
      twitterCard: {
        type: String,
        default: 'summary_large_image'
      }
    },
    setup(props) {
      const updateMetaTags = () => {
        useHead({
          title: props.title,
          meta: [
            // Standard meta tags
            { name: 'description', content: props.description },
            { name: 'keywords', content: props.keywords.join(', ') },
            
            // OpenGraph/Facebook
            { property: 'og:title', content: props.title },
            { property: 'og:description', content: props.description },
            { property: 'og:type', content: props.ogType },
            { property: 'og:url', content: window.location.href },
            { property: 'og:image', content: props.ogImage || `${window.location.origin}/images/og-default.jpg` },
            
            // Twitter
            { name: 'twitter:card', content: props.twitterCard },
            { name: 'twitter:title', content: props.title },
            { name: 'twitter:description', content: props.description },
            { name: 'twitter:image', content: props.ogImage || `${window.location.origin}/images/twitter-default.jpg` },
            
            // Canonical URL
            { rel: 'canonical', href: props.canonicalUrl || window.location.href }
          ]
        });
      };
  
      // Update on component mount
      onMounted(updateMetaTags);
  
      // Update when props change
      watch(props, updateMetaTags);
  
      // Cleanup on unmount
      onUnmounted(() => {
        useHead({
          title: '',
          meta: []
        });
      });
  
      return {};
    }
  };
  </script>