<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Welcome Section -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            Welcome back, {{ user?.name }}!
          </h1>
          <p class="mt-2 text-gray-600">
            Here's what's happening in your classroom
          </p>
        </div>
        <button
          @click="logout"
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
          </svg>
          Logout
        </button>
      </div>

      <!-- Stats Grid -->
      <div class="grid gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white/70 backdrop-blur-sm rounded-xl shadow-sm p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-600">Active Courses</h3>
            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </span>
          </div>
          <p class="mt-2 text-2xl font-semibold text-gray-900">{{ enrolledCourses.length }}</p>
        </div>

        <div class="bg-white/70 backdrop-blur-sm rounded-xl shadow-sm p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-600">Upcoming Tasks</h3>
            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100">
              <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
            </span>
          </div>
          <p class="mt-2 text-2xl font-semibold text-gray-900">0</p>
        </div>

        <div class="bg-white/70 backdrop-blur-sm rounded-xl shadow-sm p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-600">Messages</h3>
            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100">
              <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
              </svg>
            </span>
          </div>
          <p class="mt-2 text-2xl font-semibold text-gray-900">0</p>
        </div>

        <div class="bg-white/70 backdrop-blur-sm rounded-xl shadow-sm p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-600">Role</h3>
            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100">
              <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </span>
          </div>
          <p class="mt-2 text-2xl font-semibold text-gray-900 capitalize">{{ user?.roles[0] }}</p>
        </div>
      </div>

      <!-- Courses Section -->
      <div class="bg-white/70 backdrop-blur-sm rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Your Courses</h2>
          <router-link 
            v-if="hasRole(['admin', 'teacher'])"
            :to="{ name: 'courses' }" 
            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Course
          </router-link>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        </div>
        
        <div v-else-if="enrolledCourses.length === 0" class="text-center py-12">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No Courses Yet</h3>
          <p class="text-gray-500 max-w-sm mx-auto">
            {{ hasRole(['admin', 'teacher']) 
              ? 'Create your first course to get started.'
              : 'You are not enrolled in any courses yet.' }}
          </p>
        </div>
        
        <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="course in enrolledCourses"
            :key="course.id"
            class="group relative bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow"
          >
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                  {{ course.title }}
                </h3>
                <span class="text-xs px-2 py-1 rounded-full" 
                  :class="course.status === 'active' 
                    ? 'bg-green-100 text-green-700' 
                    : 'bg-yellow-100 text-yellow-700'"
                >
                  {{ course.status }}
                </span>
              </div>
              <p class="text-sm text-gray-500">{{ course.description }}</p>
              <div class="mt-4 flex items-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ course.schedule || 'No schedule set' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

import { computed, ref, onMounted } from 'vue';
import { useAuthStore } from '../store/auth';
import axios from 'axios';

const authStore = useAuthStore();
const user = computed(() => authStore.user);
const loading = ref(true);
const enrolledCourses = ref([]);

function hasRole(roles) {
  return authStore.hasRole(roles);
}

function logout() {
  authStore.logout();
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/courses');
    enrolledCourses.value = data;
  } catch (error) {
    console.error('Failed to fetch courses:', error);
  } finally {
    loading.value = false;
  }
});
</script>