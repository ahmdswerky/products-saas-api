<?php

return [
    'models' => 'App\Models',

    'quality' => 75,

    'table' => 'media',

    'filename' => [
        'length' => 20,
    ],

    'photos' => [
        'path' => env('APP_PHOTOS_PATH', 'public/photos/'),
    ],
];
