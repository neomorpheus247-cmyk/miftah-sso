import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    vue(),
  ],

  resolve: {
    alias: {
      '@': '/resources/js',
    },
  },

  build: {
    outDir: 'public/build',   // Laravel will serve built assets
    emptyOutDir: true,        // Clean old build before new one
  },
})
