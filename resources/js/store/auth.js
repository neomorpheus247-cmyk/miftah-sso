import { defineStore } from 'pinia';
import axios from 'axios';

// Always send cookies (Laravel Sanctum sessions)
axios.defaults.withCredentials = true;

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
        async loginWithGoogle() {
            // Redirect to Google login
            window.location.href = '/auth/google';
        },

        async handleGoogleCallback(token, user) {
            // Store JWT token and user info after Google login
            localStorage.setItem('jwt_token', token);
            this.user = user;
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        },

        async fetchUser() {
            try {
                // Get JWT token from localStorage or cookie
                const token = localStorage.getItem('jwt_token');
                if (!token) throw new Error('No JWT token found');
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                const { data } = await axios.get('/api/user');
                this.user = data;
            } catch (error) {
                this.user = null;
                throw error;
            }
        },

        async logout() {
            try {
                const token = localStorage.getItem('jwt_token');
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                await axios.post('/api/logout');
                localStorage.removeItem('jwt_token');
                this.user = null;
                delete axios.defaults.headers.common['Authorization'];
            } catch (error) {
                console.error('Logout failed:', error);
                throw error;
            }
        }
    }
});
