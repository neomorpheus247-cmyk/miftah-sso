import axios from 'axios';
window.axios = axios;

// Set global base URL for all Axios requests
window.axios.defaults.baseURL = 'https://miftah-sso-main-nngest.laravel.cloud';
// Ensure cookies are always sent
window.axios.defaults.withCredentials = true;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
