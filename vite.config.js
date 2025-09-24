import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                }
            }
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        // Generate source maps for production build
        sourcemap: true,
        // Improve chunking strategy for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'vue-router', 'pinia'],
                },
            },
        },
    },
    // Ensure HTTPS URLs for assets in production
    base: process.env.NODE_ENV === 'production' ? 'https://a01b4fc5-5fcc-4094-83ca-05ea9f578e8a-00-3o32c7wi8465z.kirk.replit.dev/' : '/',
});
