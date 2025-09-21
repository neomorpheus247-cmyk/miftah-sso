<template>
  <div class="min-h-screen bg-gray-100">
    <nav v-if="user" class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
          <div class="flex">
            <router-link :to="{ name: 'dashboard' }" class="flex items-center text-gray-700 hover:text-gray-900">
              Dashboard
            </router-link>
            <router-link v-if="hasRole(['admin', 'teacher'])" :to="{ name: 'courses' }" class="ml-8 flex items-center text-gray-700 hover:text-gray-900">
              Courses
            </router-link>
            <router-link v-if="hasRole(['admin'])" :to="{ name: 'users' }" class="ml-8 flex items-center text-gray-700 hover:text-gray-900">
              Users
            </router-link>
          </div>
          <div class="flex items-center">
            <LogoutDropdown :user="user" />
          </div>
        </div>
      </div>
    </nav>

    <main>
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from './store/auth';
import LogoutDropdown from './components/LogoutDropdown.vue';

const authStore = useAuthStore();
const user = computed(() => authStore.user);

function hasRole(roles) {
  return authStore.hasRole(roles);
}
</script>