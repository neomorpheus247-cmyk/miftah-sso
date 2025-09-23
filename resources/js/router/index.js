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
      if (authStore.hasRole('student')) return { name: 'student-dashboard' };
      if (authStore.hasRole('teacher')) return { name: 'teacher-dashboard' };
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

  // Always try to fetch user if not already loaded
  if (authStore.user === null && !authStore.loading) {
    authStore.loading = true;
    try {
      await authStore.fetchUser();
    } catch (e) {
      // Ignore error, user will remain null
    } finally {
      authStore.loading = false;
    }
  }

  // If user is authenticated but has no role, force choose-role page
  if (authStore.isAuthenticated && authStore.user && (!authStore.user.roles || authStore.user.roles.length === 0) && to.name !== 'choose-role') {
    return next({ name: 'choose-role' });
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' });
  } else if (to.meta.guest && authStore.isAuthenticated) {
    // Redirect to correct dashboard based on role
    if (authStore.hasRole('student')) {
      next({ name: 'student-dashboard' });
    } else if (authStore.hasRole('teacher')) {
      next({ name: 'teacher-dashboard' });
    } else {
      next({ name: 'dashboard' });
    }
  } else if (to.meta.roles && !authStore.hasRole(to.meta.roles)) {
    // Redirect to correct dashboard based on role
    if (authStore.hasRole('student')) {
      next({ name: 'student-dashboard' });
    } else if (authStore.hasRole('teacher')) {
      next({ name: 'teacher-dashboard' });
    } else {
      next({ name: 'dashboard' });
    }
  } else {
    next();
  }
});

export default router;