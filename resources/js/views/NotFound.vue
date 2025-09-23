<template>
  <div class="min-h-[50vh] flex flex-col items-center justify-center">
    <div class="text-6xl font-bold text-indigo-200 mb-4">404</div>
    <h1 class="text-2xl font-semibold text-gray-700 mb-4">Page Not Found</h1>
    <p class="text-gray-500 mb-8">The page you're looking for doesn't exist or has been moved.</p>
    <router-link
      :to="{ name: 'dashboard' }"
      class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
    >
      Return to Dashboard
    </router-link>
  </div>
</template>