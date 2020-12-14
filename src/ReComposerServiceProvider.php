<?php

declare(strict_types=1);

namespace SolumDeSignum\ReComposer;

use Illuminate\Support\ServiceProvider;

class ReComposerServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    public static string $alias = 'recomposer';

    /**
     * @var string
     */
    public static string $namespaceSuffix = 'solumdesignum';

    /**
     * Boot up the package. Load the views from the correct directory.
     */
    public function boot()
    {
        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            self::$namespaceSuffix . '/' . self::$alias
        );

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__ . '/../config/recomposer.php' => config_path(
                        'recomposer.php'
                    ),
                ],
                'config'
            );
        }
    }

    /**
     *
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/recomposer.php',
            'recomposer'
        );
    }
}
