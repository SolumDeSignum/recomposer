<?php

declare(strict_types=1);

namespace SolumDeSignum\ReComposer;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ReComposerServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Boot up the package. Load the views from the correct directory.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            'solumdesignum/recomposer'
        );

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/recomposer.php',
            'recomposer'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return parent::provides();
    }

    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
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
