<?php

declare(strict_types=1);

return [
    'view' => 'solumdesignum/recomposer::index',

    'folders_exclude' => [
        base_path('bootstrap'),
        'node_modules',
        'vendor',
        base_path('storage/debugbar'),
        base_path('storage/framework'),
        base_path('storage/logs'),
        base_path('storage/medialibrary'),
    ],

    'cache' => [
        'feature' => false,
        'hours' => 1
    ],
];
