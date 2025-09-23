<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header text-center">Choose Your Role</div>
          <div class="card-body">
            <form @submit.prevent="submitRole">
              <div class="form-group mb-4">
                <label class="form-label">Register as:</label>
                <div class="form-check">
                  <input class="form-check-input" type="radio" v-model="role" id="student" value="student" required>
                  <label class="form-check-label" for="student">Student</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" v-model="role" id="teacher" value="teacher" required>
                  <label class="form-check-label" for="teacher">Teacher</label>
                </div>
              </div>
              <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                Continue
              </button>
            </form>
            <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../store/auth';

const role = ref('');
const loading = ref(false);
const error = ref('');
const router = useRouter();
const authStore = useAuthStore();

async function submitRole() {
  loading.value = true;
  error.value = '';
  try {
    await axios.post('/register/choose-role', { role: role.value });
    await authStore.fetchUser();
    // Redirect to respective dashboard
    if (authStore.hasRole('student')) {
      router.push('/student/dashboard');
    } else if (authStore.hasRole('teacher')) {
      router.push('/teacher/dashboard');
    } else {
      router.push('/dashboard');
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to register role.';
  } finally {
    loading.value = false;
  }
}
</script>
