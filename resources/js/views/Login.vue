<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
      <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl p-8 space-y-8">
        <div class="text-center">
          <div class="flex justify-center mb-4">
            <div class="rounded-full bg-indigo-100 p-3">
              <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </div>
          </div>
          <h1 class="text-2xl font-bold text-indigo-600 mb-2">
            Welcome to Miftah Classroom
          </h1>
          <p class="text-gray-600">
            Sign in to access your courses and resources
          </p>
        </div>
        
        <div class="space-y-4">
          <button
            @click="loginWithGoogle"
            class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-lg text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200 shadow-md hover:shadow-lg"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24">
              <path fill="currentColor" d="M21.35,11.1H12.18V13.83H18.69C18.36,17.64 15.19,19.27 12.19,19.27C8.36,19.27 5,16.25 5,12C5,7.9 8.2,4.73 12.2,4.73C15.29,4.73 17.1,6.7 17.1,6.7L19,4.72C19,4.72 16.56,2 12.1,2C6.42,2 2.03,6.8 2.03,12C2.03,17.05 6.16,22 12.25,22C17.6,22 21.5,18.33 21.5,12.91C21.5,11.76 21.35,11.1 21.35,11.1V11.1Z"/>
            </svg>
            Continue with Google
          </button>
        </div>

        <div class="text-center text-sm text-gray-500">
          By signing in, you agree to our
          <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a>
          and
          <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '../store/auth';

const authStore = useAuthStore();

function loginWithGoogle() {
  authStore.loginWithGoogle();
}
</script>