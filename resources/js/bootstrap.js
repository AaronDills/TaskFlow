import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import session expiry handler to show toast on auth failures
import './session-expiry-handler';
