// Import dan konfigurasi Axios untuk HTTP requests
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Import konfigurasi Laravel Echo untuk real-time broadcasting
 * Echo digunakan untuk subscribe ke channels dan listen events
 * yang di-broadcast dari Laravel menggunakan Reverb
 */
import './echo';

/**
 * Import handler untuk transaction events
 * Menangani Echo events dan dispatch Livewire refresh
 */
import './transactionEvents';

/**
 * Import fungsi-fungsi utility
 * Berisi logger dan utility functions lainnya
 */
import './utils/logger';

