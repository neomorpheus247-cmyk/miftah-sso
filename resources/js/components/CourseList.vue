<template>
  <div class="courses">
    <div class="header">
      <h1>Courses</h1>
      <button v-if="canCreateCourse" @click="showCreateModal = true" class="btn btn-primary">
        Create Course
      </button>
    </div>

    <div v-if="loading" class="loading">Loading courses...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else class="course-grid">
      <div v-for="course in courses" :key="course.id" class="course-card">
        <h3>{{ course.title }}</h3>
        <p>{{ course.description }}</p>
        <div class="creator">Created by: {{ course.creator?.name }}</div>
        <div class="actions">
          <router-link :to="{ name: 'course.show', params: { id: course.id }}" class="btn btn-info">
            View Details
          </router-link>
          <template v-if="canManageCourse(course)">
            <button @click="editCourse(course)" class="btn btn-warning">Edit</button>
            <button @click="confirmDelete(course)" class="btn btn-danger">Delete</button>
          </template>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <modal v-model="showCreateModal">
      <template #header>
        {{ isEditing ? 'Edit Course' : 'Create New Course' }}
      </template>
      
      <template #default>
        <form @submit.prevent="saveCourse">
          <div class="form-group">
            <label for="title">Title</label>
            <input
              id="title"
              v-model="courseForm.title"
              type="text"
              class="form-control"
              required
            >
          </div>
          
          <div class="form-group">
            <label for="description">Description</label>
            <textarea
              id="description"
              v-model="courseForm.description"
              class="form-control"
              rows="4"
              required
            ></textarea>
          </div>
          
          <div class="actions">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" @click="showCreateModal = false" class="btn btn-secondary">
              Cancel
            </button>
          </div>
        </form>
      </template>
    </modal>

    <!-- Delete Confirmation Modal -->
    <modal v-model="showDeleteModal">
      <template #header>
        Confirm Delete
      </template>
      
      <template #default>
        <p>Are you sure you want to delete "{{ courseToDelete?.title }}"?</p>
        <div class="actions">
          <button @click="deleteCourse" class="btn btn-danger">Delete</button>
          <button @click="showDeleteModal = false" class="btn btn-secondary">Cancel</button>
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useCourseStore } from '../store/courseStore';
import { useAuthStore } from '../store/authStore';
import Modal from './Modal.vue';

export default {
  name: 'CourseList',
  
  components: {
    Modal
  },
  
  setup() {
    const courseStore = useCourseStore();
    const authStore = useAuthStore();
    
    const showCreateModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const courseToDelete = ref(null);
    const courseForm = ref({
      title: '',
      description: ''
    });

    const canCreateCourse = computed(() => {
      return authStore.user?.roles.some(role => ['admin', 'teacher'].includes(role));
    });

    const canManageCourse = (course) => {
      const user = authStore.user;
      return user?.roles.includes('admin') || course.created_by === user?.id;
    };

    const loadCourses = async () => {
      await courseStore.fetchCourses();
    };

    const editCourse = (course) => {
      courseForm.value = { ...course };
      isEditing.value = true;
      showCreateModal.value = true;
    };

    const saveCourse = async () => {
      try {
        if (isEditing.value) {
          await courseStore.updateCourse(courseForm.value.id, courseForm.value);
        } else {
          await courseStore.createCourse(courseForm.value);
        }
        showCreateModal.value = false;
        courseForm.value = { title: '', description: '' };
        isEditing.value = false;
      } catch (error) {
        console.error('Error saving course:', error);
      }
    };

    const confirmDelete = (course) => {
      courseToDelete.value = course;
      showDeleteModal.value = true;
    };

    const deleteCourse = async () => {
      try {
        await courseStore.deleteCourse(courseToDelete.value.id);
        showDeleteModal.value = false;
        courseToDelete.value = null;
      } catch (error) {
        console.error('Error deleting course:', error);
      }
    };

    onMounted(() => {
      loadCourses();
    });

    return {
      courses: computed(() => courseStore.courses),
      loading: computed(() => courseStore.loading),
      error: computed(() => courseStore.error),
      showCreateModal,
      showDeleteModal,
      isEditing,
      courseForm,
      courseToDelete,
      canCreateCourse,
      canManageCourse,
      editCourse,
      saveCourse,
      confirmDelete,
      deleteCourse
    };
  }
};
</script>

<style scoped>
.courses {
  padding: 2rem;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.course-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.course-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1.5rem;
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.course-card h3 {
  margin-top: 0;
  margin-bottom: 1rem;
}

.creator {
  font-size: 0.9rem;
  color: #666;
  margin: 1rem 0;
}

.actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  font-size: 0.9rem;
}

.btn-primary {
  background: #4CAF50;
  color: white;
}

.btn-warning {
  background: #FFC107;
  color: black;
}

.btn-danger {
  background: #DC3545;
  color: white;
}

.btn-info {
  background: #17A2B8;
  color: white;
  text-decoration: none;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
}

.form-control {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.error {
  color: #DC3545;
  text-align: center;
  padding: 1rem;
}
</style>