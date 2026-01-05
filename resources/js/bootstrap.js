
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.PUSHER_APP_KEY ?? '17ec3014a90b3757e007',
    cluster: import.meta.env.PUSHER_APP_CLUSTER ?? 'mt1',
    forceTLS: true,
});
