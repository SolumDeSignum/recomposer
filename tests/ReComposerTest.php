<?php

declare(strict_types=1);

namespace Tests;

namespace SolumDeSignum\ReComposer\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use SolumDeSignum\ReComposer\ReComposer;

use function chdir;
use function realpath;

class ReComposerTest extends BaseTestCase
{
    private ReComposer $recomposer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recomposer = new ReComposer();
    }

    /**
     * Filter out laravel package
     * Verify is laravel installed
     * Verify dependencies, dev-dependencies are array.
     *
     * @test
     */
    final public function laravel(): void
    {
        $laravel = collect($this->recomposer->packages)
            ->where(
                'name',
                'laravel/framework'
            )
            ->toArray();

        foreach ($laravel as $package) {
            self::assertSame($package['name'], 'laravel/framework');
            self::assertIsArray($package['dependencies']);
            self::assertIsArray($package['dev-dependencies']);
        }
    }


    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication(): \Illuminate\Foundation\Application
    {
        chdir(realpath('bootstrap'));
        $app = include 'app.php';
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
