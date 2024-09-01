<?php

declare(strict_types=1);

namespace SolumDeSignum\ReComposer\Tests\Feature;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Orchestra\Testbench\TestCase;
use SolumDeSignum\ReComposer\ReComposer;

class ReComposerTest extends TestCase
{
    use WithoutMiddleware;

    protected ReComposer $reComposer;

    protected function setUp(): void
    {
        parent::setUp();

        $mockRequest = Mockery::mock('alias:Illuminate\Support\Facades\Request');
        $mockRequest->shouldReceive('isSecure')
            ->andReturn(false);
        $mockRequest->shouldReceive('setUserResolver')
            ->andReturnNull();

        if (defined('LARAVEL_BASE_PATH') === false) {
            define('LARAVEL_BASE_PATH', __DIR__ . '/../../../../../'); // vendor escape to load root composer
        }

        if (defined('VENDOR_BASE_PATH') === false) {
            define('VENDOR_BASE_PATH', __DIR__ . '/../../../../../vendor/');
        }

        $this->reComposer = new ReComposer();
    }

    protected function tearDown(): void
    {
        Mockery::close(); // Ensure mocks are properly cleaned up
        parent::tearDown();
    }

    /** @test */
    public function itCanGetTheComposerJsonContent()
    {
        $getPackageName = "laravel/framework";
        $packages = $this->reComposer->collectPackages('require');

        $getPackage = array_filter($packages, function (array $package) use ($getPackageName) {
            $response = false;

            if ($package['name'] === $getPackageName) {
                $response = $package;
            }

            return $response;
        });

        $this->assertIsArray($packages);
        $this->assertContains($getPackageName, end($getPackage));
    }

    /** @test */
    public function itCanGetTheServerEnvironmentDetails()
    {
        $details = $this->reComposer->serverEnvironment();

        $this->assertIsArray($details);
        $this->assertCount(12, $details);

        $this->assertArrayHasKey('version', $details);
        $this->assertArrayHasKey('server_software', $details);
        $this->assertArrayHasKey('database_connection_name', $details);
        $this->assertArrayHasKey('ssl_installed', $details);
        $this->assertArrayHasKey('cache_driver', $details);
        $this->assertArrayHasKey('session_driver', $details);
        $this->assertArrayHasKey('openssl', $details);
        $this->assertArrayHasKey('pdo', $details);
        $this->assertArrayHasKey('mbstring', $details);
        $this->assertArrayHasKey('tokenizer', $details);
        $this->assertArrayHasKey('xml', $details);
    }

    /** @test */
    public function itCanGetTheLaravelEnvironmentDetails()
    {
        $details = $this->reComposer->laravelEnvironment();

        $this->assertIsArray($details);
        $this->assertCount(7, $details);

        $this->assertArrayHasKey('version', $details);
        $this->assertArrayHasKey('timezone', $details);
        $this->assertArrayHasKey('debug_mode', $details);
        $this->assertArrayHasKey('storage_dir_writable', $details);
        $this->assertArrayHasKey('cache_dir_writable', $details);
        $this->assertArrayHasKey('recomposer_version', $details);
        $this->assertArrayHasKey('app_size', $details);
    }

    /** @test */
    public function itCanGetInstalledPackages()
    {
        $packages = $this->reComposer->installedPackages();

        $this->assertIsArray($packages);
        $this->assertArrayHasKey('laravel/framework', $packages);
        $this->assertArrayHasKey('phpunit/phpunit', $packages);
    }

    /** @test */
    public function itCanGenerateASystemReport()
    {
        $report = $this->reComposer->report();

        $this->assertIsArray($report);
        $this->assertCount(3, $report);

        $this->assertArrayHasKey('Server Environment', $report);
        $this->assertArrayHasKey('Laravel Environment', $report);
        $this->assertArrayHasKey('Installed Packages', $report);
    }

    /** @test */
    public function itHandlesFileNotFoundExceptions()
    {
        $path = 'roxy.backdoor';

        try {
            $this->reComposer->composerJson($path);
        } catch (Exception $exception) {
            $this->assertSame(FileNotFoundException::class, $exception::class);
            $this->assertSame("File not found: $path", $exception->getMessage());
        }
    }

    /** @test */
    public function itCanHandleCacheRemember()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('recomposer.folderSize', Mockery::type('DateTime'), Mockery::type('Closure'))
            ->andReturn('1024 MiB');

        $result = $this->reComposer->cacheRemember();
        $this->assertEquals('1024 MiB', $result);
    }

    /** @test */
    public function itChecksSslInstallation()
    {
        $secure = $this->reComposer->isSecure();

        $this->assertFalse($secure);
    }

    /** @test */
    public function itCanCalculateDirectorySizeCorrectly()
    {
        $config = $this->app->make('config');
        $config->set('recomposer.basePath', '');
        $calculatedSize = $this->reComposer->directorySize();

        $this->assertEquals(0, $calculatedSize);
    }
}
