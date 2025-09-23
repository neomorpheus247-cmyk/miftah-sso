import { defineStore } from 'pinia';
import axios from 'axios';

// Ensure axios sends cookies with every request (required for Sanctum)
axios.defaults.withCredentials = true;

// Function to get a specific cookie by name
const getCookie = (name) => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
};

// Manually set the X-XSRF-TOKEN header
const xsrfToken = getCookie('XSRF-TOKEN');
if (xsrfToken) {
    axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken);
}

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
            window.location.href = '/auth/google';
        },

        async fetchUser() {
            try {
                const { data } = await axios.get('/api/user');
                this.user = data;
            } catch (error) {
                this.user = null;
                throw error;
            }
        },

        async logout() {
            try {
                await axios.post('/logout');
                this.user = null;
            } catch (error) {
                console.error('Logout failed:', error);
                throw error;
            }
        }
    }
});