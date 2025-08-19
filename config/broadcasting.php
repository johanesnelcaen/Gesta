<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | Cette option contrôle la connexion de diffusion par défaut que Laravel
    | utilisera lorsque vous diffusez des événements. Vous pouvez toujours
    | spécifier une connexion différente lors de la diffusion d'un événement.
    |
    */

    'default' => env('BROADCAST_DRIVER', 'pusher'),

    /*
    |--------------------------------------------------------------------------
    | Connexions de diffusion
    |--------------------------------------------------------------------------
    |
    | Ici, vous pouvez définir toutes les connexions de diffusion pour votre
    | application. Laravel prend en charge "pusher", "ably", "redis",
    | "log" et "null" pour les environnements de test.
    |
    */

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
