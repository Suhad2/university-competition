<?php

return [
    'default' => env('BROADCAST_DRIVER', 'pusher'),

    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY', '17ec3014a90b3757e007'),
            'secret' => env('PUSHER_APP_SECRET', 'de4d4d2dc7d766f4b783'),
            'app_id' => env('PUSHER_APP_ID', '2096105'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
                'useTLS' => true,
            ],
        ],
    ],
];