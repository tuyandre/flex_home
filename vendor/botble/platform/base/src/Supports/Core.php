<?php

namespace Botble\Base\Supports;

use Botble\Base\Exceptions\LicenseIsAlreadyActivatedException;
use Botble\Base\Exceptions\MissingCURLExtensionException;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\ValueObjects\ProductUpdate;
use Botble\Menu\Facades\Menu;
use Botble\PluginManagement\Services\PluginService;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Services\ThemeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;
use ZipArchive;

/**
 * DO NOT MODIFY THIS FILE.
 *
 * @internal
 */
final class Core
{
    private string $basePath;

    private string $coreDataFilePath;

    private string $licenseFilePath;

    private string $productId;

    private string $productSource;

    private string $version = '1.0.0';

    private string $licenseUrl = 'https://license.botble.com';

    private string $licenseKey = 'CAF4B17F6D3F656125F9';

    private string $cacheLicenseKeyName = '44622179e10cab6';

    private int $verificationPeriod = 1;

    public function __construct(
        private Repository $cache,
        private Filesystem $files
    ) {
        $this->basePath = base_path();
        $this->licenseFilePath = storage_path('.license');
        $this->coreDataFilePath = core_path('core.json');

        $this->parseDataFromCoreDataFile();
    }

    public function version(): string
    {
        return $this->version;
    }

    public function getLicenseFilePath(): string
    {
        return $this->licenseFilePath;
    }

    public function getLicenseFile(): string|null
    {
        if (! $this->files->exists($this->licenseFilePath)) {
            return null;
        }

        return $this->files->get($this->licenseFilePath);
    }

    public function activateLicense(string $license, string $client): bool
    {
        $response = $this->createRequest('/api/activate_license', [
            'product_id' => $this->productId,
            'license_code' => $license,
            'client_name' => $client,
            'verify_type' => $this->productSource,
        ]);

        if (! $response->ok()) {
            return false;
        }

        $data = $response->json();

        if (! Arr::get($data, 'status')) {
            $this->files->delete($this->licenseFilePath);

            if (Str::contains(Arr::get($data, 'message'), 'License is already active')) {
                throw new LicenseIsAlreadyActivatedException();
            }

            return false;
        }

        $this->files->put($this->licenseFilePath, Arr::get($data, 'lic_response'), true);

        return true;
    }

    public function verifyLicense(bool $timeBasedCheck = false): bool
    {
        if ($timeBasedCheck && $this->verificationPeriod > 0) {
            $now = Carbon::now();

            if (! $this->cache->get($this->cacheLicenseKeyName)) {
                $this->cache->forever($this->cacheLicenseKeyName, '0000-00-00');
            }

            $lastTime = Carbon::createFromFormat('Y-m-d', $this->cache->get($this->cacheLicenseKeyName));

            if ($now->greaterThan($lastTime)) {
                $verifiedLicense = $this->verifyLicenseDirectly();

                $tomorrow = $now->copy()->addDays($this->verificationPeriod);

                if ($verifiedLicense) {
                    $this->cache->forever($this->cacheLicenseKeyName, $tomorrow->toDateString());
                }
            }
        }

        return $this->verifyLicenseDirectly();
    }

    private function verifyLicenseDirectly(): bool
    {
        if (! $this->files->exists($this->licenseFilePath)) {
            return false;
        }

        $data = [
            'product_id' => $this->productId,
            'license_file' => $this->getLicenseFile(),
        ];

        $response = $this->createRequest('/api/verify_license', $data);
        $data = $response->json();

        return $response->ok() && Arr::get($data, 'status');
    }

    public function revokeLicense(string $purchasedCode, string $buyer): bool
    {
        $data = [
            'product_id' => $this->productId,
            'license_code' => $purchasedCode,
            'client_name' => $buyer,
        ];

        $response = $this->createRequest('/api/deactivate_license', $data);
        $data = $response->json();

        if ($response->ok() && Arr::get($data, 'status')) {
            $this->cache->forget($this->cacheLicenseKeyName);
            $this->files->delete($this->licenseFilePath);

            return true;
        }

        return false;
    }

    public function deactivateLicense(): bool
    {
        if (! $this->files->exists($this->licenseFilePath)) {
            return false;
        }

        $data = [
            'product_id' => $this->productId,
            'license_file' => $this->getLicenseFile(),
        ];

        $response = $this->createRequest('/api/deactivate_license', $data);
        $data = $response->json();

        if ($response->ok() && Arr::get($data, 'status')) {
            $this->cache->forget($this->cacheLicenseKeyName);
            $this->files->delete($this->licenseFilePath);

            return true;
        }

        return false;
    }

    public function checkUpdate(): ProductUpdate|false
    {
        $response = $this->createRequest('/api/check_update', [
            'product_id' => $this->productId,
            'current_version' => $this->version,
        ]);

        return $this->parseProductUpdateResponse($response);
    }

    public function getLatestVersion(): ProductUpdate|false
    {
        $response = $this->createRequest('/api/check_update', [
            'product_id' => $this->productId,
            'current_version' => '0.0.0',
        ]);

        return $this->parseProductUpdateResponse($response);
    }

    public function getUpdateSize(string $updateId): float
    {
        $sizeUpdateResponse = $this->createRequest('/api/get_update_size/' . $updateId, method: 'HEAD');

        return (float) $sizeUpdateResponse->header('Content-Length') ?: 1;
    }

    public function downloadUpdate(string $updateId, string $version): bool
    {
        if (! $this->files->exists($this->licenseFilePath)) {
            return false;
        }

        $data = [
            'product_id' => $this->productId,
            'license_file' => $this->getLicenseFile(),
        ];

        $filePath = $this->getUpdatedFilePath($version);

        if (! $this->files->exists($filePath)) {
            $response = $this->createRequest('/api/download_update/main/' . $updateId, $data);

            $this->files->put($filePath, $response->body());
        }

        if ($this->validateUpdateFile($filePath)) {
            return true;
        }

        $this->files->delete($filePath);

        return false;
    }

    private function getUpdatedFilePath(string $version): string
    {
        $version = str_replace('.', '_', $version);

        return $this->basePath . '/update_main_' . $version . '.zip';
    }

    public function updateFilesAndDatabase(string $version): bool
    {
        $filePath = $this->getUpdatedFilePath($version);

        if (! $this->files->exists($filePath)) {
            return false;
        }

        $this->cleanCaches();

        $coreTempPath = storage_path('app/core.json');

        try {
            $this->files->copy($this->coreDataFilePath, $coreTempPath);
            $zip = new Zipper();

            if ($zip->extract($filePath, $this->basePath)) {
                $this->files->delete($filePath);
                $this->files->delete($coreTempPath);
                $this->runMigrationFiles();

                return true;
            }

            if ($this->files->exists($coreTempPath)) {
                $this->files->move($coreTempPath, $this->coreDataFilePath);
            }

            return false;
        } catch (Throwable $exception) {
            rescue(fn () => $this->runMigrationFiles());

            if ($this->files->exists($coreTempPath)) {
                $this->files->move($coreTempPath, $this->coreDataFilePath);
            }

            $this->logError($exception);

            throw $exception;
        }
    }

    public function publishUpdateAssets(): void
    {
        $paths = [
            core_path(),
            package_path(),
        ];

        foreach ($paths as $path) {
            foreach (BaseHelper::scanFolder($path) as $module) {
                if ($path == plugin_path() && ! is_plugin_active($module)) {
                    continue;
                }

                $modulePath = $path . '/' . $module;

                if (! $this->files->isDirectory($modulePath)) {
                    continue;
                }

                $publishedPath = 'vendor/core/' . $this->files->basename($path);

                if (! $this->files->isDirectory($publishedPath)) {
                    $this->files->makeDirectory($publishedPath, 0755, true);
                }

                if ($this->files->isDirectory($modulePublicPath = $modulePath . '/public')) {
                    $this->files->copyDirectory($modulePublicPath, $publishedPath . '/' . $module);
                }

                if ($this->files->isDirectory($moduleLangPath = $modulePath . '/resources/lang')) {
                    $this->files->copyDirectory(
                        $moduleLangPath,
                        lang_path('vendor') . '/' . $this->files->basename($path) . '/' . $module
                    );
                }
            }
        }

        $pluginService = app(PluginService::class);

        foreach (BaseHelper::scanFolder(plugin_path()) as $plugin) {
            if ($path == plugin_path() && ! is_plugin_active($plugin)) {
                continue;
            }

            $pluginService->publishAssets($plugin);
        }

        $this->files->delete(theme_path(Theme::getThemeName() . '/public/css/style.integration.css'));

        $customCSS = Theme::getStyleIntegrationPath();

        if ($this->files->exists($customCSS)) {
            $this->files->copy($customCSS, storage_path('app/style.integration.css.') . time());
        }

        app(ThemeService::class)->publishAssets();
    }

    public function cleanUpUpdate(): void
    {
        $this->cleanCaches();
    }

    public function cleanCaches(): void
    {
        try {
            Helper::clearCache();
            Menu::clearCacheMenuItems();

            $this->files->delete(app()->getCachedConfigPath());
            $this->files->delete(app()->getCachedRoutesPath());
            $this->files->delete(app()->bootstrapPath('cache/packages.php'));
            $this->files->delete(app()->bootstrapPath('cache/services.php'));
            $this->files->delete(app()->bootstrapPath('cache/plugins.php'));
            foreach ($this->files->glob(storage_path('app/purifier') . '/*') as $view) {
                $this->files->delete($view);
            }
            foreach ($this->files->glob(storage_path('framework/views') . '/*') as $view) {
                $this->files->delete($view);
            }
        } catch (Throwable $exception) {
            $this->logError($exception);
        }
    }

    public function logError(Exception|Throwable $exception): void
    {
        logger()->error($exception->getMessage() . ' - ' . $exception->getFile() . ':' . $exception->getLine());
    }

    private function runMigrationFiles(): void
    {
        $migrator = app('migrator');

        $migrator->run(database_path('migrations'));

        $paths = [
            core_path(),
            package_path(),
            plugin_path(),
        ];

        foreach ($paths as $path) {
            foreach (BaseHelper::scanFolder($path) as $module) {
                if ($path == plugin_path() && ! is_plugin_active($module)) {
                    continue;
                }

                $modulePath = $path . '/' . $module;

                if (! $this->files->isDirectory($modulePath)) {
                    continue;
                }

                if ($this->files->isDirectory($moduleMigrationPath = $modulePath . '/database/migrations')) {
                    $migrator->run($moduleMigrationPath);
                }
            }
        }
    }

    private function validateUpdateFile(string $filePath): bool
    {
        if (! class_exists('ZipArchive', false)) {
            return true;
        }

        $zip = new ZipArchive();

        if ($zip->open($filePath)) {
            if ($zip->getFromName('.env')) {
                return false;
            }

            $content = json_decode($zip->getFromName('platform/core/core.json'), true);

            if (! $content) {
                return false;
            }

            $validator = Validator::make($content, [
                'productId' => ['required', 'string'],
                'source' => ['required', 'string'],
                'apiUrl' => ['required', 'url'],
                'apiKey' => ['required', 'string'],
                'version' => ['required', 'string'],
                'marketplaceUrl' => ['required', 'url'],
                'marketplaceToken' => ['required', 'string'],
                'minimumPhpVersion' => ['nullable', 'string'],
            ])->stopOnFirstFailure();

            if ($validator->fails()) {
                $zip->close();

                return false;
            }

            $core = BaseHelper::getFileData(core_path('core.json'));

            if ($content['productId'] !== $core['productId']) {
                $zip->close();

                return false;
            }

            if (version_compare($content['version'], $this->version, '<')) {
                $zip->close();

                return false;
            }

            if (isset($content['minimumPhpVersion']) && version_compare($content['minimumPhpVersion'], phpversion(), '>')) {
                $zip->close();

                return false;
            }
        }

        $zip->close();

        return true;
    }

    private function parseDataFromCoreDataFile(): void
    {
        if (! $this->files->exists($this->coreDataFilePath)) {
            return;
        }

        try {
            $data = json_decode($this->files->get($this->coreDataFilePath), true) ?: [];

            $this->productId = Arr::get($data, 'productId');
            $this->productSource = Arr::get($data, 'source');
            $this->licenseUrl = rtrim(Arr::get($data, 'apiUrl', $this->licenseUrl), '/');
            $this->licenseKey = Arr::get($data, 'apiKey', $this->licenseKey);
            $this->version = Arr::get($data, 'version', $this->version);
        } catch (FileNotFoundException) {
        }
    }

    private function createRequest(string $path, array $data = [], string $method = 'POST'): Response
    {
        if (! extension_loaded('curl')) {
            throw new MissingCURLExtensionException();
        }

        $request = Http::baseUrl($this->licenseUrl)
            ->withHeaders([
                'LB-API-KEY' => $this->licenseKey,
                'LB-URL' => rtrim(url('/'), '/'),
                'LB-IP' => $this->getClientIpAddress(),
                'LB-LANG' => 'english',
            ])
            ->asJson()
            ->acceptJson()
            ->withoutVerifying()
            ->connectTimeout(100)
            ->timeout(300);

        return match (Str::upper($method)) {
            'GET' => $request->get($path, $data),
            'HEAD' => $request->head($path),
            default => $request->post($path, $data)
        };
    }

    private function getClientIpAddress(): string
    {
        return Helper::getIpFromThirdParty();
    }

    private function parseProductUpdateResponse(Response $response): ProductUpdate|false
    {
        $data = $response->json();

        if ($response->ok() && Arr::get($data, 'status')) {
            return new ProductUpdate(
                Arr::get($data, 'update_id'),
                Arr::get($data, 'version'),
                Carbon::createFromFormat('Y-m-d', Arr::get($data, 'release_date')),
                trim((string)Arr::get($data, 'summary')),
                trim((string)Arr::get($data, 'changelog')),
                (bool)Arr::get($data, 'has_sql'),
            );
        }

        return false;
    }
}
