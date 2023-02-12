<?php

declare(strict_types=1);

$excludePrefix = '--exclude=';

return [
    'basePath' => base_path(),
    'binary' => [
        'format' => 'kilobytes',
        'search' => 'MiB',
        'replace' => 'mb',
    ],
    'view' => 'solumdesignum/recomposer::index',
    'cache' => [
        'feature' => false,
        'hours' => 1,
    ],
    'icon' => [
        'check' => '<i class="fas fa-check"></i>',
        'uncheck' => '<i class="fas fa-times"></i>',
    ],
    'exclude' => [
        'folder' => [
            'blacklist' => [
                //                $excludePrefix . base_path('bootstrap'),
                //                $excludePrefix . base_path('packages'),
                //                $excludePrefix . base_path('node_modules'),
                //                $excludePrefix . base_path('vendor'),
                //                $excludePrefix . base_path('storage/debugbar'),
                //                $excludePrefix . base_path('storage/framework'),
                //                $excludePrefix . base_path('storage/logs'),
                //                $excludePrefix . base_path('storage/medialibrary'),
            ],
        ],
        'packages' => [
            'enabled' => true,
            'blacklist' => [
                'php',
                'roave/security-advisories',
            ],
        ],
    ],
];
