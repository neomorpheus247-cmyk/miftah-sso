
<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            Student Dashboard
            <LogoutDropdown :user="user" />
          </div>
          <div class="card-body">
            <h5>Welcome, Student!</h5>
            <p>You now have access to student features.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import LogoutDropdown from '../components/LogoutDropdown.vue';
import { useAuthStore } from '../store/auth';
const user = useAuthStore().user;
</script>
