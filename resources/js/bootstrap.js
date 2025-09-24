import axios from 'axios'

// Detect backend API URL from .env or default to localhost:8000
const API_BASE_URL = import.meta.env.VITE_APP_URL || 'http://localhost:8000'

// Configure Axios defaults
axios.defaults.baseURL = API_BASE_URL
axios.defaults.withCredentials = true // Ensure cookies (Laravel Sanctum) are sent
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Expose globally (optional, but useful in Vue apps)
window.axios = axios
