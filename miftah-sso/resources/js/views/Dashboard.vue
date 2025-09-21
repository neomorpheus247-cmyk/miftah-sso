<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <h1 class="text-2xl font-semibold text-gray-900">Welcome {{ user?.name }}!</h1>
          
          <div class="mt-6">
            <h2 class="text-lg font-medium text-gray-900">Your Courses</h2>
            <div v-if="loading" class="mt-4">Loading...</div>
            <div v-else-if="enrolledCourses.length === 0" class="mt-4 text-gray-500">
              You are not enrolled in any courses yet.
            </div>
            <div v-else class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="course in enrolledCourses"
                :key="course.id"
                class="bg-gray-50 p-4 rounded-lg shadow"
              >
                <h3 class="font-medium text-gray-900">{{ course.title }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ course.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../store/auth';
import axios from 'axios';

const authStore = useAuthStore();
const user = authStore.user;
const loading = ref(true);
const enrolledCourses = ref([]);

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