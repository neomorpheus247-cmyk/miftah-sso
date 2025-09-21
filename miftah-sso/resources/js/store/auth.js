import { defineStore } from 'pinia';
import axios from 'axios';

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