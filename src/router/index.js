import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth.js';
import LoginView from '@/views/LoginView.vue';
import RegisterView from '@/views/RegisterView.vue';
import CategoriesView from '@/views/CategoriesView.vue';
 
const routes = [
  {
    path: '/login',
    name: 'login',
    component: LoginView,
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterView,
  },
  {
    path: '/',
    redirect: '/categories',
  },
  {
    path: '/categories',
    name: 'categories',
    component: CategoriesView,
    meta: { requiresAuth: true },
  },
];
 
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});
 
router.beforeEach((to) => {
  const auth = useAuthStore();
 
  if (to.meta.requiresAuth && !auth.connecte) {
    return { name: 'login' };
  }
 
  if ((to.name === 'login' || to.name === 'register') && auth.connecte) {
    return { name: 'categories' };
  }
});
 
export default router;
