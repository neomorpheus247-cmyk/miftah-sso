<template>
  <div class="relative">
    <button
      @click="showDropdown = !showDropdown"
      class="flex items-center space-x-2 text-gray-700 hover:text-gray-900"
    >
      <span>{{ user.name }}</span>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <div
      v-if="showDropdown"
      class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1"
    >
      <button
        @click="logout"
        class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100"
      >
        Logout Now
      </button>
      <button
        v-if="!isLogoutScheduled"
        @click="scheduleLogout"
        class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100"
      >
        Logout in 5 minutes
      </button>
      <button
        v-else
        @click="cancelScheduledLogout"
        class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100"
      >
        Cancel Scheduled Logout
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
});

const router = useRouter();
const showDropdown = ref(false);
const isLogoutScheduled = ref(false);
let logoutTimer = null;

async function logout() {
  try {
    await axios.post('/api/logout');
    router.push({ name: 'login' });
  } catch (error) {
    console.error('Logout failed:', error);
  }
}

async function scheduleLogout() {
  try {
    const response = await axios.post('/api/logout/schedule');
    isLogoutScheduled.value = true;
    showDropdown.value = false;
    
    // Set up client-side timer
    const logoutTime = new Date(response.data.logout_at);
    const timeoutDuration = logoutTime - new Date();
    
    logoutTimer = setTimeout(() => {
      router.push({ name: 'login' });
    }, timeoutDuration);
    
  } catch (error) {
    console.error('Failed to schedule logout:', error);
  }
}

async function cancelScheduledLogout() {
  try {
    await axios.post('/api/logout/cancel');
    isLogoutScheduled.value = false;
    showDropdown.value = false;
    
    if (logoutTimer) {
      clearTimeout(logoutTimer);
      logoutTimer = null;
    }
  } catch (error) {
    console.error('Failed to cancel scheduled logout:', error);
  }
}
</script>