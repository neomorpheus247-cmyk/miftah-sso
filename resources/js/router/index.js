import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../store/auth';

// Views
import Login from '../views/Login.vue';

import Dashboard from '../views/Dashboard.vue';
import Courses from '../views/Courses.vue';
import Users from '../views/Users.vue';
import ChooseRole from '../views/ChooseRole.vue';
const StudentDashboard = () => import('../views/StudentDashboard.vue');
const TeacherDashboard = () => import('../views/TeacherDashboard.vue');

const routes = [
  {
    path: '/',
    redirect: to => {
      const authStore = useAuthStore();
      if (!authStore.isAuthenticated) return { name: 'login' };
      if (authStore.user && (!authStore.user.roles || authStore.user.roles.length === 0)) {
        return { name: 'choose-role' };
      }
      // Existing user: show dashboard by role
      if (authStore.hasRole('student')) return { name: 'student-dashboard' };
      if (authStore.hasRole('teacher')) return { name: 'teacher-dashboard' };
      if (authStore.hasRole('admin')) return { name: 'dashboard' };
      // Fallback: generic dashboard
      return { name: 'dashboard' };
    }
  },
  {
    path: '/register/choose-role',
    name: 'choose-role',
    component: ChooseRole,
    meta: { requiresAuth: true }
  },
  {
    path: '/student/dashboard',
    name: 'student-dashboard',
    component: StudentDashboard,
    meta: { requiresAuth: true, roles: ['student'] }
  },
  {
    path: '/teacher/dashboard',
    name: 'teacher-dashboard',
    component: TeacherDashboard,
    meta: { requiresAuth: true, roles: ['teacher'] }
  },
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
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('../views/NotFound.vue')
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});



router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Wait for user state to finish loading before redirecting
  if (authStore.loading) {
    // Prevent navigation until loading is done
    return;
  }

  // Fetch user if not loaded
  if (authStore.user === null && !authStore.loading) {
    authStore.loading = true;
    try {
      await authStore.fetchUser();
    } catch (e) {
      // Ignore error
    } finally {
      authStore.loading = false;
    }
    // After fetching, re-run guard
    return next(to);
  }

  // If not authenticated, always go to login
  if (!authStore.isAuthenticated && to.name !== 'login') {
    return next({ name: 'login' });
  }

  // If authenticated and no role, always go to choose-role
  if (authStore.isAuthenticated && authStore.user && (!authStore.user.roles || authStore.user.roles.length === 0) && to.name !== 'choose-role') {
    return next({ name: 'choose-role' });
  }

  // If authenticated and has role, route to dashboard by role
  if (authStore.isAuthenticated && authStore.user && authStore.user.roles && authStore.user.roles.length > 0) {
    if (to.name === 'login' || to.name === 'choose-role') {
      // Redirect to dashboard by role
      if (authStore.hasRole('student')) return next({ name: 'student-dashboard' });
      if (authStore.hasRole('teacher')) return next({ name: 'teacher-dashboard' });
      if (authStore.hasRole('admin')) return next({ name: 'dashboard' });
      return next({ name: 'dashboard' });
    }
  }

  // Otherwise, allow navigation
  next();
});

export default router;