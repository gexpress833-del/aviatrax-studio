import axios from 'axios';

window.axios = axios;

const csrfToken = document.head.querySelector('meta[name="csrf-token"]');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

if (csrfToken) {
    const token = csrfToken.getAttribute('content');
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    window.axios.defaults.headers.common['X-XSRF-TOKEN'] = token;
}
