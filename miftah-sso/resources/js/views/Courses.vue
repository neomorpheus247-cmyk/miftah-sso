<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Courses</h1>
        <button
          @click="showCreateModal = true"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
        >
          Create Course
        </button>
      </div>

      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul v-if="courses.length" class="divide-y divide-gray-200">
          <li v-for="course in courses" :key="course.id">
            <div class="px-4 py-4 sm:px-6">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-medium text-gray-900">{{ course.title }}</h3>
                  <p class="mt-1 text-sm text-gray-600">{{ course.description }}</p>
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="editCourse(course)"
                    class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteCourse(course.id)"
                    class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </li>
        </ul>
        <div v-else class="p-4 text-center text-gray-500">
          No courses available.
        </div>
      </div>

      <!-- Create/Edit Modal -->
      <div v-if="showCreateModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full">
          <h2 class="text-xl font-semibold mb-4">{{ editingCourse ? 'Edit' : 'Create' }} Course</h2>
          <form @submit.prevent="saveCourse">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <input
                  v-model="courseForm.title"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea
                  v-model="courseForm.description"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                ></textarea>
              </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
              <button
                type="button"
                @click="showCreateModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
              >
                {{ editingCourse ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const courses = ref([]);
const showCreateModal = ref(false);
const editingCourse = ref(null);
const courseForm = ref({
  title: '',
  description: ''
});

onMounted(fetchCourses);

async function fetchCourses() {
  try {
    const { data } = await axios.get('/api/courses');
    courses.value = data;
  } catch (error) {
    console.error('Failed to fetch courses:', error);
  }
}

function editCourse(course) {
  editingCourse.value = course;
  courseForm.value = {
    title: course.title,
    description: course.description
  };
  showCreateModal.value = true;
}

async function saveCourse() {
  try {
    if (editingCourse.value) {
      await axios.put(`/api/courses/${editingCourse.value.id}`, courseForm.value);
    } else {
      await axios.post('/api/courses', courseForm.value);
    }
    await fetchCourses();
    showCreateModal.value = false;
    courseForm.value = { title: '', description: '' };
    editingCourse.value = null;
  } catch (error) {
    console.error('Failed to save course:', error);
  }
}

async function deleteCourse(id) {
  if (!confirm('Are you sure you want to delete this course?')) return;
  
  try {
    await axios.delete(`/api/courses/${id}`);
    await fetchCourses();
  } catch (error) {
    console.error('Failed to delete course:', error);
  }
}
</script>