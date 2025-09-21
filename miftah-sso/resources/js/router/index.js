import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../store/auth';

// Views
import Login from '../views/Login.vue';
import Dashboard from '../views/Dashboard.vue';
import Courses from '../views/Courses.vue';
import Users from '../views/Users.vue';

const routes = [
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/courses',
    name: 'courses',
    component: Courses,
    meta: { requiresAuth: true, roles: ['admin', 'teacher'] }
  },
  {
    path: '/users',
    name: 'users',
    component: Users,
    meta: { requiresAuth: true, roles: ['admin'] }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' });
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'dashboard' });
  } else if (to.meta.roles && !authStore.hasRole(to.meta.roles)) {
    next({ name: 'dashboard' });
  } else {
    next();
  }
});

export default router;