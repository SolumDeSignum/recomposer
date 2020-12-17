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

use function array_first;
use function base_path;
use function config;
use function exec;
use function explode;
use function implode;
use function now;

use const JSON_THROW_ON_ERROR;

class ReComposer
{
    public string $packageName;

    public array $laravelExtras = [];

    public array $serverExtras = [];

    public array $extraStats = [];

    public array $composer = [];

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
        $this->packageName = ReComposerServiceProvider::$namespaceSuffix . '/' . ReComposerServiceProvider::$alias;
    }

    /**
     * Get the ReComposer system report as a PHP array
     *
     * @throws FileNotFoundException
     * @throws JsonException
     * @return array
     */
    final public function report(): array
    {
        $reportResponse['Server Environment'] = $this->serverEnvironment();
        $reportResponse['Laravel Environment'] = $this->laravelEnvironment();
        $reportResponse['Installed Packages'] = $this->installedPackages();

        if (! empty($this->extraStats())) {
            $reportResponse['Extra Stats'] = $this->extraStats();
        }

        return $reportResponse;
    }

    /**
     * Add Extra stats by app or any other package dev
     *
     * @param array $extraStats
     */
    final public function addExtraStats(array $extraStats): void
    {
        $this->extraStats = \array_merge($this->extraStats, $extraStats);
    }

    /**
     * Add Laravel specific stats by app or any other package dev
     *
     * @param array $laravelStats
     */
    final public function addLaravelStats(array $laravelStats): void
    {
        $this->laravelExtras = \array_merge($this->laravelExtras, $laravelStats);
    }

    /**
     * Add Server specific stats by app or any other package dev
     *
     * @param array $serverStats
     */
    final public function addServerStats(array $serverStats): void
    {
        $this->serverExtras = \array_merge($this->serverExtras, $serverStats);
    }

    /**
     * Get the extra stats added by the app or any other package dev
     *
     * @return array
     */
    final public function extraStats(): array
    {
        return $this->extraStats;
    }

    /**
     * Get additional server info added by the app or any other package dev
     *
     * @return array
     */
    final public function serverExtras(): array
    {
        return $this->serverExtras;
    }

    /**
     * Get additional laravel info added by the app or any other package dev
     *
     * @return array
     */
    final public function laravelExtras(): array
    {
        return $this->laravelExtras;
    }

    /**
     * Get Laravel environment details
     *
     * @return array
     */
    final public function laravelEnvironment(): array
    {
        return \array_merge(
            [
                'version' => App::version(),
                'timezone' => config('app.timezone'),
                'debug_mode' => config('app.debug'),
                'storage_dir_writable' => \is_writable(base_path('storage')),
                'cache_dir_writable' => \is_writable(base_path('bootstrap/cache')),
                'decomposer_version' => $this->packageVersion(),
                'app_size' => Str::replaceFirst(
                    'MiB',
                    'mb',
                    $this->appSize()
                ),
            ],
            $this->laravelExtras()
        );
    }

    /**
     * @return string
     */
    final public function binaryFormat(): string
    {
        $binaryFormat = config('recomposer.binaryFormat');
        return Binary::$binaryFormat($this->directorySize())->format();
    }

    /**
     * @return string|null
     */
    final public function cacheRemember(): ?string
    {
        return Cache::remember(
            'recomposer.folderSize',
            now()->addHours(config('recomposer.cache.hours', 1)),
            function () {
                return $this->binaryFormat();
            }
        );
    }

    /**
     * @return mixed
     */
    final public function appSize()
    {
        return config('recomposer.cache.feature') ?
            $this->cacheRemember() :
            $this->binaryFormat();
    }

    /**
     * Get PHP/Server environment details
     *
     * @return array
     */
    public function serverEnvironment(): array
    {
        return \array_merge(
            [
                'version' => PHP_VERSION,
                'server_software' => $_SERVER['SERVER_SOFTWARE'],
                'server_os' => \php_uname(),
                'database_connection_name' => config('database.default'),
                'ssl_installed' => $this->isSecure(),
                'cache_driver' => config('cache.default'),
                'session_driver' => config('session.driver'),
                'openssl' => \extension_loaded('openssl'),
                'pdo' => \extension_loaded('pdo'),
                'mbstring' => \extension_loaded('mbstring'),
                'tokenizer' => \extension_loaded('tokenizer'),
                'xml' => \extension_loaded('xml'),
            ],
            $this->serverExtras()
        );
    }

    /**
     *  Get the Composer file contents as an array
     *
     * @return array
     * @throws JsonException
     */
    private function composerJson(): array
    {
        $composerJson = \file_get_contents(base_path('composer.json'));
        return \json_decode((string) $composerJson, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $key
     * @param array  $responseDependencies
     *
     * @return mixed|string
     */
    private function dependencies(string $key, array $responseDependencies)
    {
        return \array_key_exists(
            $key,
            $responseDependencies
        ) ?
            $responseDependencies[$key] :
            'No dependencies';
    }

    /**
     * Get Installed packages & their Dependencies
     *
     * @throws FileNotFoundException
     * @throws JsonException
     * @return array
     */
    private function packagesWithDependencies(): array
    {
        $responsePackages = [];
        foreach ($this->composer['require'] as $packageName => $version) {
            $packageComposerJson = base_path("/vendor/{$packageName}/composer.json");

            if (File::isFile($packageComposerJson)) {
                $packageComposerJson = File::get($packageComposerJson);

                $responseDependencies = \json_decode(
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
     * Get Installed packages & their version numbers as an associative array
     *
     * @throws FileNotFoundException
     * @throws JsonException
     * @return array
     */
    private function installedPackages(): array
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
    private function packageVersion(): string
    {
        $version = $this->composer['require-dev'][$this->packageName] ??
            $this->composer['require'][$this->packageName] ??
            'unknown';

        foreach ($this->packages as $package) {
            if (isset($package['dependencies'][$this->packageName])) {
                $version = $package['dependencies'][$this->packageName];
            }

            if (isset($package['dev-dependencies'][$this->packageName])) {
                $version = $package['dev-dependencies'][$this->packageName];
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
     * @return int
     */
    private function directorySize(): int
    {
        $basePath = config('recomposer.basePath');
        $excludeDirectories = implode(' ', config('recomposer.exclude_folders'));
        $execResponse = exec("du $basePath" . ' ' . $excludeDirectories);
        $directorySize = explode("\t", $execResponse);

        return (int) array_first($directorySize);
    }
}
