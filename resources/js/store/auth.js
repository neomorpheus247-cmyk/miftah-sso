import { defineStore } from 'pinia'
import axios from 'axios'

// Always send cookies with requests
axios.defaults.withCredentials = true

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        loading: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.user,
        hasRole: (state) => (roles) => {
            if (!state.user || !state.user.roles) return false
            if (!Array.isArray(roles)) roles = [roles]
            return roles.some((role) => state.user.roles.includes(role))
        },
    },

    actions: {
        // Redirect to Google login
        loginWithGoogle() {
            window.location.href = '/auth/google'
        },

        // Fetch authenticated user
        async fetchUser() {
            this.loading = true
            try {
                // Step 1: Get CSRF cookie (needed for session auth)
                await axios.get('/sanctum/csrf-cookie')

                // Step 2: Fetch the current user from Laravel
                const { data } = await axios.get('/api/user')
                this.user = data
            } catch (error) {
                console.error('Failed to fetch user:', error)
                this.user = null
            } finally {
                this.loading = false
            }
        },

        // Logout
        async logout() {
            try {
                await axios.post('/auth/logout')
            } catch (error) {
                console.error('Logout failed:', error)
            } finally {
                this.user = null
            }
        },

        // Optional: schedule automatic logout
        async scheduleLogout() {
            try {
                await axios.post('/auth/logout/schedule')
            } catch (error) {
                console.error('Scheduling logout failed:', error)
            }
        },

        // Optional: cancel scheduled logout
        async cancelLogout() {
            try {
                await axios.post('/auth/logout/cancel')
            } catch (error) {
                console.error('Cancel logout failed:', error)
            }
        },
    },
})
