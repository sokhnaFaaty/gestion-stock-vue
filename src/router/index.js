import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth.js';
import LoginView from '@/views/LoginView.vue';
import RegisterView from '@/views/RegisterView.vue';
import CategoriesView from '@/views/CategoriesView.vue';
import CommandesView from '@/views/CommandesView.vue';
 // En haut de src/index.js, avec les autres imports
import { cors } from 'hono/cors';

// Juste après la création de `app`, avant les routes
app.use('*', cors({
  origin: 'http://localhost:5173', // l'URL de ton serveur Vite
  allowMethods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
  allowHeaders: ['Content-Type', 'Authorization'],
}));

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
  {
  path: '/commandes',
  name: 'commandes',
  component: CommandesView,
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
