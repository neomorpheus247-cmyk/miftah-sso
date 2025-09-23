import { defineStore } from 'pinia';
import axios from 'axios';

// Always send cookies
axios.defaults.withCredentials = true;
// Base API URL (production + local flexibility)
axios.defaults.baseURL = import.meta.env.VITE_API_URL || '/';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        loading: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.user,
        hasRole: (state) => (roles) => {
            if (!state.user) return false;
            if (!Array.isArray(roles)) roles = [roles];
            return roles.some(role => state.user.roles.includes(role));
        }
    },

    actions: {
        loginWithGoogle() {
            window.location.href = '/auth/google';
        },

        async fetchUser() {
            try {
                // Get CSRF cookie first (required by Sanctum)
                await axios.get('/sanctum/csrf-cookie');
                const { data } = await axios.get('/api/user');
                this.user = data;
            } catch (error) {
                this.user = null;
                throw error;
            }
        },

        async logout() {
            try {
                await axios.post('/auth/logout'); // ✅ matches Laravel route
                this.user = null;
            } catch (error) {
                console.error('Logout failed:', error);
                throw error;
            }
        }
    }
});
