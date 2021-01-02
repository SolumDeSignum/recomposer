<?php

declare(strict_types=1);

use SolumDeSignum\ReComposer\ReComposer;
use Tests\TestCase;

class ReComposerTest extends TestCase
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
     * Verify dependencies, dev-dependencies are array
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
}
