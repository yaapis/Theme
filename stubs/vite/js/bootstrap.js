import axios from 'axios';

import.meta.glob(['../img/**']);

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
