[![StyleCI](https://github.styleci.io/repos/326276520/shield?branch=master)](https://github.styleci.io/repos/145921620)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g//recomposer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SolumDeSignum/recomposer/?branch=master)
[![Total Downloads](https://poser.pugx.org/solumdesignum/recomposer/downloads)](https://packagist.org/packages/solumdesignum/recomposer)
[![Latest Stable Version](https://poser.pugx.org/solumdesignum/recomposer/v/stable)](https://packagist.org/packages/solumdesignum/recomposer)
[![Latest Unstable Version](https://poser.pugx.org/solumdesignum/recomposer/v/unstable)](https://packagist.org/packages/solumdesignum/recomposer)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Introduction
Laravel ReComposer decomposes and lists all the installed packages with their
dependencies along with the Laravel & the Server environment details your app is running in.

## Required before installation
Please make sure du is installed on linux, or unix, mac.

### Important
I regret to inform windows currently is not supported. 
#### Exceptions to this is rule is
Any type of virtualization docker, Virtual Machine and anything similar to it.

## Installation
To get started, install ReComposer using the Composer package manager:
```shell
composer require solumdesignum/recomposer
```

Next, publish ReComposer resources using the vendor:publish command:

```shell
php artisan vendor:publish --provider="SolumDeSignum\ReComposer\ReComposerServiceProvider"
```

This command will publish a config to your config directory, which will be
created if it does not exist.

### ReComposer Features
The configuration file contains configurations.
```php
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
            ]
        ]
    ]
];
````

Add a route in your web routes file:

```php
Route::get('recomposer','\SolumDeSignum\ReComposer\Controllers\ReComposerController@index');
```
Go to http://yourapp/recomposer or the route you configured above in the routes file.

## Contributing
Thank you for considering contributing to the Laravel ReComposer. You can read the contribution guidelines [here](CONTRIBUTING.md)

## Security
If you discover any security-related issues, please email to [Solum DeSignum](mailto:oskars_germovs@inbox.lv).

## Author
- [Oskars Germovs](https://github.com/Faks)

## About
[Solum DeSignum](https://solum-designum.eu) is a web design agency based in Latvia, Riga.

## License
Laravel ReComposer is open-sourced software licensed under the [MIT license](LICENSE.md)

## Idea
This package concept is based on a package decomposer (abandon).
