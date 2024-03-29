<?php

declare(strict_types=1);

namespace SolumDeSignum\ReComposer;

use App;
use ByteUnits\Binary;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use JsonException;

use function array_key_exists;
use function array_merge;
use function base_path;
use function collect;
use function config;
use function exec;
use function explode;
use function extension_loaded;
use function file_get_contents;
use function get_loaded_extensions;
use function implode;
use function in_array;
use function is_writable;
use function json_decode;
use function now;
use function php_uname;
use function strtolower;

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
     *
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
     *  Get the Composer file contents as an array.
     *
     * @return array
     * @throws JsonException
     *
     */
    private function composerJson(): array
    {
        $composerJson = file_get_contents(base_path('composer.json'));

        return json_decode((string)$composerJson, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array
     * @throws JsonException
     *
     * @throws FileNotFoundException
     */
    private function packagesWithDependencies(): array
    {
        $responseRequirePackages = $this->collectPackages('require');
        $responseRequireDevPackages = $this->collectPackages('require-dev');

        return array_merge($responseRequirePackages, $responseRequireDevPackages);
    }

    /**
     * Get Installed packages & their Dependencies.
     *
     * @param string $requireType
     *
     * @return array
     * @throws JsonException
     *
     * @throws FileNotFoundException
     */
    private function collectPackages(string $requireType): array
    {
        $responsePackages = [];
        foreach ($this->composer[$requireType] as $packageName => $version) {
            if (!in_array($packageName, $this->excludeBlacklistPackages(), true)) {
                $packageComposerJson = base_path(
                    "/vendor/{$packageName}/composer.json"
                );

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
     * @return array
     */
    private function excludeBlacklistPackages(): array
    {
        $extensions = collect(get_loaded_extensions())
            ->map(
                function (string $ext) {
                    return 'ext-' . strtolower($ext);
                }
            );

        if (config('recomposer.exclude.packages.enabled')) {
            foreach (config('recomposer.exclude.packages.blacklist') as $package) {
                $extensions->add($package);
            }
        }

        return $extensions->toArray();
    }

    /**
     * @param string $key
     * @param array $responseDependencies
     *
     * @return mixed
     */
    private function dependencies(string $key, array $responseDependencies)
    {
        return array_key_exists(
            $key,
            $responseDependencies
        ) ?
            $responseDependencies[$key] :
            'No dependencies';
    }

    /**
     * Get the ReComposer system report as a PHP array.
     *
     * @return array
     * @throws JsonException
     *
     * @throws FileNotFoundException
     */
    final public function report(): array
    {
        $reportResponse = [];
        $reportResponse['Server Environment'] = $this->serverEnvironment();
        $reportResponse['Laravel Environment'] = $this->laravelEnvironment();
        $reportResponse['Installed Packages'] = $this->installedPackages();

        if (!empty($this->extraStats())) {
            $reportResponse['Extra Stats'] = $this->extraStats();
        }

        return $reportResponse;
    }

    /**
     * Get PHP/Server environment details.
     *
     * @return array
     */
    public function serverEnvironment(): array
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
            $this->serverExtras()
        );
    }

    /**
     * Check if SSL is installed or not.
     *
     * @return bool
     */
    private function isSecure(): bool
    {
        return Request::isSecure();
    }

    /**
     * Get additional server info added by the app or any other package dev.
     *
     * @return array
     */
    final public function serverExtras(): array
    {
        return $this->serverExtras;
    }

    /**
     * Get Laravel environment details.
     *
     * @return array
     */
    final public function laravelEnvironment(): array
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
                    config('recomposer.binary.search', 'MiB'),
                    config('recomposer.binary.replace', 'mb'),
                    (string)$this->appSize()
                ),
            ],
            $this->laravelExtras()
        );
    }

    /**
     * Get current installed ReComposer version.
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
     * @return string|null
     */
    final public function appSize(): ?string
    {
        return config('recomposer.cache.feature') ?
            $this->cacheRemember() :
            $this->binaryFormat();
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
     * @return string
     */
    final public function binaryFormat(): string
    {
        $binaryFormat = config('recomposer.binary.format');

        return Binary::$binaryFormat($this->directorySize())->format();
    }

    /**
     * @return int
     */
    private function directorySize(): int
    {
        $basePath = config('recomposer.basePath');
        $excludeDirectories = implode(
            ' ',
            config('recomposer.exclude.folder.blacklist')
        );
        $execResponse = exec("du $basePath" . ' ' . $excludeDirectories);
        $directorySize = explode("\t", $execResponse);

        /** @scrutinizer ignore-call */
        return (int)Arr::first($directorySize);
    }

    /**
     * Get additional laravel info added by the app or any other package dev.
     *
     * @return array
     */
    final public function laravelExtras(): array
    {
        return $this->laravelExtras;
    }

    /**
     * Get Installed packages & their version numbers as an associative array.
     *
     * @return array
     * @throws FileNotFoundException
     *
     * @throws JsonException
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
     * Get the extra stats added by the app or any other package dev.
     *
     * @return array
     */
    final public function extraStats(): array
    {
        return $this->extraStats;
    }

    /**
     * Add Extra stats by app or any other package dev.
     *
     * @param array $extraStats
     */
    final public function addExtraStats(array $extraStats): void
    {
        $this->extraStats = array_merge($this->extraStats, $extraStats);
    }

    /**
     * Add Laravel specific stats by app or any other package dev.
     *
     * @param array $laravelStats
     */
    final public function addLaravelStats(array $laravelStats): void
    {
        $this->laravelExtras = array_merge($this->laravelExtras, $laravelStats);
    }

    /**
     * Add Server specific stats by app or any other package dev.
     *
     * @param array $serverStats
     */
    final public function addServerStats(array $serverStats): void
    {
        $this->serverExtras = array_merge($this->serverExtras, $serverStats);
    }
}
