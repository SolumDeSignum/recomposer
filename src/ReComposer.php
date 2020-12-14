<?php

declare(strict_types=1);

namespace SolumDeSignum\ReComposer;

use App;
use ByteUnits\Binary;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use JsonException;

use function array_key_exists;
use function base_path;
use function config;
use function filesize;
use function now;

class ReComposer
{
    /**
     * Make ReComposer name as a constant to be used
     * in resolving its version number
     */
    public const PACKAGE_NAME = 'solumdesignum/recomposer';

    /**
     * Initialise blank arrays for extra stats to be added
     * by app or other package devs
     *
     * @var array
     */
    public array $laravelExtras = [];

    /**
     * @var array
     */
    public array $serverExtras = [];

    /**
     * @var array
     */
    public array $extraStats = [];

    /**
     * @var array
     */
    public array $composer = [];

    /**
     * @var array
     */
    public array $packages = [];

    /**
     * ReComposer constructor.
     * @throws FileNotFoundException
     * @throws JsonException
     */
    public function __construct()
    {
        $this->composer = $this->composerJson();
        $this->packages = $this->packagesWithDependencies();
    }

    /**
     * Get the ReComposer system report as a PHP array
     *
     * @return array
     * @throws FileNotFoundException
     * @throws JsonException
     */
    public function getReportArray(): array
    {
        $reportArray['Server Environment'] = $this->getServerEnv();
        $reportArray['Laravel Environment'] = $this->laravelEnvironment();
        $reportArray['Installed Packages'] = $this->getPackagesArray();

        if (! empty($this->getExtraStats())) {
            $reportArray['Extra Stats'] = $this->getExtraStats();
        }

        return $reportArray;
    }

    /**
     * Add Extra stats by app or any other package dev
     *
     * @param $extraStatsArray
     */
    public function addExtraStats(array $extraStatsArray)
    {
        $this->extraStats = array_merge($this->extraStats, $extraStatsArray);
    }

    /**
     * Add Laravel specific stats by app or any other package dev
     *
     * @param array $laravelStatsArray
     */
    public function addLaravelStats(array $laravelStatsArray)
    {
        $this->laravelExtras = array_merge($this->laravelExtras, $laravelStatsArray);
    }

    /**
     * Add Server specific stats by app or any other package dev
     *
     * @param $serverStatsArray
     */
    public function addServerStats(array $serverStatsArray)
    {
        $this->serverExtras = array_merge($this->serverExtras, $serverStatsArray);
    }

    /**
     * Get the extra stats added by the app or any other package dev
     *
     * @return array
     */

    public function getExtraStats(): array
    {
        return $this->extraStats;
    }

    /**
     * Get additional server info added by the app or any other package dev
     *
     * @return array
     */

    public function getServerExtras(): array
    {
        return $this->serverExtras;
    }

    /**
     * Get additional laravel info added by the app or any other package dev
     *
     * @return array
     */

    final public function getLaravelExtras(): array
    {
        return $this->laravelExtras;
    }

    /**
     * Get the DeComposer system report as JSON
     *
     * @return false|string
     * @throws JsonException
     * @throws FileNotFoundException
     */
    final public function getReportJson()
    {
        return json_encode($this->getReportArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * Get the Composer file contents as an array
     *
     * @return array
     * @throws JsonException
     */
    final public function composerJson(): array
    {
        $composerJson = file_get_contents(base_path('composer.json'));
        return json_decode($composerJson, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $key
     * @param array  $responseDependencies
     *
     * @return mixed|string
     */
    final public function dependencies(string $key, array $responseDependencies)
    {
        return array_key_exists(
            $key,
            $responseDependencies
        ) ? $responseDependencies[$key] : 'No dependencies';
    }

    /**
     * Get Installed packages & their Dependencies
     *
     * @return array
     * @throws FileNotFoundException
     * @throws JsonException
     */
    public function packagesWithDependencies(): array
    {
        $responsePackages = [];

        foreach ($this->composer['require'] as $packageName => $version) {
            $packageComposerJson = base_path("/vendor/{$packageName}/composer.json");

            if (File::isFile($packageComposerJson)) {
                $packageComposerJson = File::get($packageComposerJson);

                $responseDependencies = json_decode(
                    $packageComposerJson,
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );

                $responsePackages[] = [
                    'name' => $packageName,
                    'version' => $version,
                    'dependencies' => $this->dependencies(
                        'require',
                        $responseDependencies
                    ),
                    'dev-dependencies' => $this->dependencies(
                        'require-dev',
                        $responseDependencies
                    ),
                ];
            }
        }

        return $responsePackages;
    }

    /**
     * Get Laravel environment details
     *
     * @return array
     */
    public function laravelEnvironment(): array
    {
        return array_merge(
            [
                'version' => App::version(),
                'timezone' => config('app.timezone'),
                'debug_mode' => config('app.debug'),
                'storage_dir_writable' => is_writable(base_path('storage')),
                'cache_dir_writable' => is_writable(base_path('bootstrap/cache')),
                'decomposer_version' => $this->packageVersion(),
                'app_size' => Str::replaceFirst(
                    'MiB',
                    'mb',
                    (new self)->appSize()
                ),
            ],
            $this->getLaravelExtras()
        );
    }

    /**
     * @return string
     */
    final public function binaryBytes(): string
    {
        return Binary::bytes(self::folderSize(base_path()))->format();
    }

    /**
     * @return string|null
     */
    final public function cacheRememberBytes(): ?string
    {
        return Cache::remember(
            'recomposer.folderSize',
            now()->addHours(config('recomposer.cache.hours', 1)),
            function () {
                return $this->binaryBytes();
            }
        );
    }

    /**
     * @return mixed
     */
    final public function appSize()
    {
        return config('recomposer.cache.feature') ?
            $this->cacheRememberBytes() :
            $this->binaryBytes();
    }

    /**
     * Get PHP/Server environment details
     *
     * @return array
     */
    public function getServerEnv(): array
    {
        return array_merge(
            [
                'version' => PHP_VERSION,
                'server_software' => $_SERVER['SERVER_SOFTWARE'],
                'server_os' => php_uname(),
                'database_connection_name' => config('database.default'),
                'ssl_installed' => $this->isSecure(),
                'cache_driver' => config('cache.default'),
                'session_driver' => config('session.driver'),
                'openssl' => extension_loaded('openssl'),
                'pdo' => extension_loaded('pdo'),
                'mbstring' => extension_loaded('mbstring'),
                'tokenizer' => extension_loaded('tokenizer'),
                'xml' => extension_loaded('xml'),
            ],
            $this->getServerExtras()
        );
    }

    /**
     * Get Installed packages & their version numbers as an associative array
     *
     * @return array
     * @throws FileNotFoundException
     * @throws JsonException
     */
    private function getPackagesArray(): array
    {
        $packagesWithDependencies = [];
        foreach ($this->packagesWithDependencies() as $packageWithDependencies) {
            $packages[$packageWithDependencies['name']] = $packageWithDependencies['version'];
        }

        return $packagesWithDependencies;
    }

    /**
     * Get current installed ReComposer version
     *
     * @return string
     */
    public function packageVersion(): string
    {
        $version = $this->composer['require-dev'][self::PACKAGE_NAME] ??
            $this->composer['require'][self::PACKAGE_NAME] ??
            'unknown';

        foreach ($this->packages as $package) {
            if (isset($package['dependencies'][self::PACKAGE_NAME])) {
                $version = $package['dependencies'][self::PACKAGE_NAME];
            }

            if (isset($package['dev-dependencies'][self::PACKAGE_NAME])) {
                $version = $package['dev-dependencies'][self::PACKAGE_NAME];
            }
        }

        return $version;
    }

    /**
     * Check if SSL is installed or not
     *
     * @return boolean
     */

    private function isSecure(): bool
    {
        return Request::isSecure();
    }

    /**
     * Get the laravel app's size
     *
     * @param $dir
     *
     * @return int
     */

    private static function folderSize($dir): int
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
            if (! is_file($each) && Str::contains(
                    $each,
                    config(
                        'recomposer.folders_exclude'
                    )
                )) {
                continue;
            }

            $sizes[] = $size += is_file($each) ?
                filesize($each) :
                self::folderSize($each);
        }

        return $size;
    }
}
