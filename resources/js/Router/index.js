import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  { path: '/login', component: () => import('@/Components/Login.vue') },
  { path: '/register', component: () => import('@/Components/Register.vue') },
  { path: '/jobs', component: () => import('@/Components/Jobs.vue') },
  { path: '/job/:id', component: () => import('@/Components/Job.vue') },
  // Add other routes as needed
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
