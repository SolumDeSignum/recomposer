<?php

declare(strict_types=1);

$excludePrefix = env('RECOMPOSER_EXCLUDE', '--exclude=');

return [
    'basePath' => base_path(),
    'binaryFormat' => 'kilobytes',
    'view' => 'solumdesignum/recomposer::index',

    'exclude_folders' => [
        //        $excludePrefix . base_path('bootstrap'),
        //        $excludePrefix . base_path('packages'),
        //        $excludePrefix . base_path('node_modules'),
        //        $excludePrefix . base_path('vendor'),
        //        $excludePrefix . base_path('storage/debugbar'),
        //        $excludePrefix . base_path('storage/framework'),
        //        $excludePrefix . base_path('storage/logs'),
        //        $excludePrefix . base_path('storage/medialibrary'),
    ],

    'cache' => [
        'feature' => false,
        'hours' => 1
    ],
];
