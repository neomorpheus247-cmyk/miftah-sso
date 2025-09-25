import { defineStore } from 'pinia'
import axios from 'axios'
import router from '../router'

// Always send cookies with requests
axios.defaults.withCredentials = true

// Global interceptor for session/auth errors
axios.interceptors.response.use(
    response => response,
    error => {
        const code = error.response?.status
        if (code === 401 || code === 419) {
            // Force logout and redirect to login
            const authStore = useAuthStore()
            authStore.user = null
            router.push({ name: 'login' })
        }
        return Promise.reject(error)
    }
)

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
                // If 401 or any error, set user to null and loading to false
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

        // After role assignment, refetch user to update roles
        async afterRoleAssigned() {
            await this.fetchUser()
        },
    },
})
