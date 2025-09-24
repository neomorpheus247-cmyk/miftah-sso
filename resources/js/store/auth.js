import { defineStore } from 'pinia';
import axios from 'axios';

// Always send cookies with requests (Laravel Sanctum sessions)
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

        async fetchUser() {
            this.loading = true;
            try {
                const { data } = await axios.get('/api/user');
                this.user = data;
            } catch (error) {
                this.user = null;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                await axios.post('/auth/logout');
            } catch (error) {
                console.error('Logout failed:', error);
            } finally {
                this.user = null;
            }
        },

        async scheduleLogout() {
            try {
                await axios.post('/auth/logout/schedule');
            } catch (error) {
                console.error('Scheduling logout failed:', error);
            }
        },

        async cancelLogout() {
            try {
                await axios.post('/auth/logout/cancel');
            } catch (error) {
                console.error('Cancel logout failed:', error);
            }
        }
    }
});
