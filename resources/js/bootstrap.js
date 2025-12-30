// import axios from 'axios';
// window.axios = axios;

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';

// window.Pusher = Pusher;

// // Get Pusher configuration from meta tags (set in layout blade file)
// const pusherKey = document.querySelector('meta[name="pusher-key"]')?.getAttribute('content') || '';
// const pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.getAttribute('content') || 'mt1';

// // Validate Pusher configuration
// if (!pusherKey) {
//     console.warn('Pusher key not configured. Make sure PUSHER_APP_KEY is set in .env file and meta tags are loaded.');
//     console.log('Trying to use key from window or falling back to default...');
// }

// // Configure Laravel Echo with Pusher
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: pusherKey || '17ec3014a90b3757e007', // Use your key as fallback
//     cluster: pusherCluster,
//     encrypted: true,
//     forceTLS: true,
//     disableStats: true,
//     enabledTransports: ['ws', 'wss'],
//     authorizer: (channel) => {
//         return {
//             authorize: (socketId, callback) => {
//                 callback(false, { auth: '' });
//             }
//         };
//     }
// });

// // Add connection error handling
// if (window.Echo) {
//     window.Echo.connector.pusher.connection.bind('error', function(err) {
//         console.error('Pusher connection error:', err);
//     });
    
//     window.Echo.connector.pusher.connection.bind('disconnected', function() {
//         console.warn('Pusher disconnected. Attempting to reconnect...');
//     });
    
//     window.Echo.connector.pusher.connection.bind('connected', function() {
//         console.log('Pusher connected successfully');
//     });
// }
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
