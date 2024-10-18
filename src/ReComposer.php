<?php

declare(strict_types=1);

namespace SolumDeSignum\ReComposer;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use JsonException;

use function array_key_exists;
use function array_merge;
use function base_path;
use function collect;
use function config;
use function escapeshellarg;
use function exec;
use function explode;
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
        $this->packageName = 'solumdesignum/recomposer';
    }

    /**
     * Get the Composer file contents as an array.
     *
     * @param null $path
     * @return array
     * @throws FileNotFoundException
     * @throws JsonException
     */
    public function composerJson($path = null): array
    {
        // Ensure the correct base path during tests
        $basePath = defined('LARAVEL_BASE_PATH')
            ? LARAVEL_BASE_PATH
            : base_path();

        $filePath = $path !== null
            ? $path
            : $basePath . '/composer.json';

        if (!file_exists($filePath)) {
            throw new FileNotFoundException("File not found: {$filePath}");
        }

        $composerJson = file_get_contents($filePath);

        if ($composerJson === false) {
            throw new \RuntimeException("Failed to read file: {$filePath}");
        }

        return json_decode($composerJson, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Get the ReComposer system report as a PHP array.
     *
     * @return array
     * @throws JsonException
     * @throws FileNotFoundException
     */
    public function report(): array
    {
        $reportResponse = [
            'Server Environment' => $this->serverEnvironment(),
            'Laravel Environment' => $this->laravelEnvironment(),
            'Installed Packages' => $this->installedPackages(),
        ];

        if (!empty($this->extraStats)) {
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
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
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
            $this->serverExtras
        );
    }

    /**
     * Get additional server info added by the app or any other package dev.
     *
     * @return array
     */
    public function serverExtras(): array
    {
        return $this->serverExtras;
    }

    /**
     * Get Laravel environment details.
     *
     * @return array
     */
    public function laravelEnvironment(): array
    {
        return array_merge(
            [
                'version' => App::version(),
                'timezone' => config('app.timezone'),
                'debug_mode' => config('app.debug') ? 'enabled' : 'disabled',
                'storage_dir_writable' => is_writable(base_path('storage')),
                'cache_dir_writable' => is_writable(base_path('bootstrap/cache')),
                'recomposer_version' => $this->packageVersion(),
                'app_size' => Str::replaceFirst(
                    config('recomposer.binary.search', 'MiB'),
                    config('recomposer.binary.replace', 'mb'),
                    (string)$this->appSize()
                ),
            ],
            $this->laravelExtras
        );
    }

    /**
     * Get application size in a formatted string.
     *
     * @return string|null
     */
    public function appSize(): ?string
    {
        return config('recomposer.cache.feature')
            ? $this->cacheRemember()
            : $this->binaryFormat();
    }

    /**
     * Get application size from cache or calculate if not cached.
     *
     * @return string|null
     */
    public function cacheRemember(): ?string
    {
        return Cache::remember(
            'recomposer.folderSize',
            now()->addHours(config('recomposer.cache.hours', 1)),
            fn() => $this->binaryFormat()
        );
    }

    /**
     * Get formatted size of the application directory.
     *
     * @return string
     */
    public function binaryFormat(): string
    {
        return round($this->directorySize() / (1024 * 1024), 2) . 'MB';
    }

    /**
     * Get additional Laravel info added by the app or any other package dev.
     *
     * @return array
     */
    public function laravelExtras(): array
    {
        return $this->laravelExtras;
    }

    /**
     * Get the extra stats added by the app or any other package dev.
     *
     * @return array
     */
    public function extraStats(): array
    {
        return $this->extraStats;
    }

    /**
     * Add extra stats.
     *
     * @param array $extraStats
     */
    public function addExtraStats(array $extraStats): void
    {
        $this->extraStats = array_merge($this->extraStats, $extraStats);
    }

    /**
     * Add Laravel specific stats.
     *
     * @param array $laravelStats
     */
    public function addLaravelStats(array $laravelStats): void
    {
        $this->laravelExtras = array_merge($this->laravelExtras, $laravelStats);
    }

    /**
     * Add server specific stats.
     *
     * @param array $serverStats
     */
    public function addServerStats(array $serverStats): void
    {
        $this->serverExtras = array_merge($this->serverExtras, $serverStats);
    }

    /**
     * Check if the request is secure (HTTPS).
     *
     * @return bool
     */
    public function isSecure(): bool
    {
        return Request::isSecure();
    }

    /**
     * Get installed packages with their dependencies.
     *
     * @return array
     * @throws JsonException
     */
    public function packagesWithDependencies(): array
    {
        $responseRequirePackages = $this->collectPackages('require');
        $responseRequireDevPackages = $this->collectPackages('require-dev');

        return array_merge($responseRequirePackages, $responseRequireDevPackages);
    }

    /**
     * Get installed packages based on type (require or require-dev).
     *
     * @param string $requireType
     *
     * @return array
     * @throws JsonException
     */
    public function collectPackages(string $requireType): array
    {
        $responsePackages = [];

        if (!isset($this->composer[$requireType])) {
            return $responsePackages; // Return an empty array if key is not set
        }

        foreach ($this->composer[$requireType] as $packageName => $version) {
            if (!in_array($packageName, $this->excludeBlacklistPackages(), true)) {
                $packageComposerJson = defined('VENDOR_BASE_PATH')
                    ? VENDOR_BASE_PATH . "{$packageName}/composer.json"
                    : base_path("/vendor/{$packageName}/composer.json");

                if (!File::exists($packageComposerJson)) {
                    Log::warning("Composer file not found for package: {$packageName}");
                    continue;
                }

                $packageComposerJsonContent = File::get($packageComposerJson);

                $responseDependencies = json_decode(
                    $packageComposerJsonContent,
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
     * Get the list of excluded packages.
     *
     * @return array
     */
    public function excludeBlacklistPackages(): array
    {
        $extensions = collect(get_loaded_extensions())
            ->map(fn(string $ext) => 'ext-' . strtolower($ext));

        if (config('recomposer.exclude.packages.enabled')) {
            foreach (config('recomposer.exclude.packages.blacklist') as $package) {
                $extensions->add($package);
            }
        }

        return $extensions->toArray();
    }

    /**
     * Get the dependencies from the package's composer.json.
     *
     * @param string $key
     * @param array $responseDependencies
     *
     * @return mixed
     */
    public function dependencies(string $key, array $responseDependencies): mixed
    {
        return array_key_exists($key, $responseDependencies)
            ? $responseDependencies[$key]
            : 'No dependencies';
    }

    /**
     * Get installed packages and their version numbers.
     *
     * @return array
     * @throws JsonException
     */
    public function installedPackages(): array
    {
        $packagesWithDependencies = [];
        foreach ($this->packagesWithDependencies() as $packageWithDependencies) {
            $packagesWithDependencies[$packageWithDependencies['name']] = $packageWithDependencies['version'];
        }

        return $packagesWithDependencies;
    }

    /**
     * Get current installed ReComposer version.
     *
     * @return string
     */
    public function packageVersion(): string
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

    public function directorySize(): int
    {
        $basePath = escapeshellarg(config('recomposer.basePath', base_path()));

        if (empty($basePath) || $basePath === "''") {
            return 0;
        }

        $excludeDirectories = implode(
            ' ',
            array_map('escapeshellarg', config('recomposer.exclude.folder.blacklist', []))
        );

        try {
            // Execute the shell command to get the directory size
            $command = "du -sb $basePath $excludeDirectories";
            $responseExec = exec($command, $output, $responseCode);

            if ($responseCode !== 0) {
                throw new \RuntimeException('Failed to execute directory size command.');
            }

            $size = explode("\t", trim($responseExec))[0] ?? '0';
            return is_numeric($size)
                ? (int)$size
                : 0;
        } catch (\Throwable $e) {
            Log::error('Error calculating directory size', [
                'exception' => $e,
                'command' => $command,
                'output' => $output,
                'basePath' => $basePath,
                'excludeDirectories' => $excludeDirectories,
            ]);

            return 0;
        }
    }
}
