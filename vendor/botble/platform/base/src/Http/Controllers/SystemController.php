<?php

namespace Botble\Base\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Illuminate\Support\Arr;
use Botble\Base\Events\UpdatedEvent;
use Botble\Base\Events\UpdatingEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Services\CleanDatabaseService;
use Botble\Base\Supports\Core;
use Botble\Base\Supports\Helper;
use Botble\Base\Supports\Language;
use Botble\Base\Supports\MembershipAuthorization;
use Botble\Base\Supports\SystemManagement;
use Botble\Base\Tables\InfoTable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Botble\Menu\Facades\Menu;
use Throwable;

class SystemController extends Controller
{
    public function getInfo(Request $request, InfoTable $infoTable)
    {
        PageTitle::setTitle(trans('core/base::system.info.title'));

        Assets::addScriptsDirectly('vendor/core/core/base/js/system-info.js')
            ->addStylesDirectly(['vendor/core/core/base/css/system-info.css']);

        $composerArray = SystemManagement::getComposerArray();
        $packages = SystemManagement::getPackagesAndDependencies($composerArray['require']);

        if ($request->expectsJson()) {
            return $infoTable->renderTable();
        }

        $systemEnv = SystemManagement::getSystemEnv();
        $serverEnv = SystemManagement::getServerEnv();

        $requiredPhpVersion = Arr::get($composerArray, 'require.php', get_minimum_php_version());
        $requiredPhpVersion = str_replace('^', '', $requiredPhpVersion);
        $requiredPhpVersion = str_replace('~', '', $requiredPhpVersion);

        $matchPHPRequirement = version_compare(phpversion(), $requiredPhpVersion, '>=') > 0;

        return view(
            'core/base::system.info',
            compact(
                'packages',
                'infoTable',
                'systemEnv',
                'serverEnv',
                'matchPHPRequirement',
                'requiredPhpVersion'
            )
        );
    }

    public function getCacheManagement()
    {
        PageTitle::setTitle(trans('core/base::cache.cache_management'));

        Assets::addScriptsDirectly('vendor/core/core/base/js/cache.js');

        return view('core/base::system.cache');
    }

    public function postClearCache(Request $request, BaseHttpResponse $response, Filesystem $files, Application $app)
    {
        switch ($request->input('type')) {
            case 'clear_cms_cache':
                Helper::clearCache();
                Menu::clearCacheMenuItems();
                $pluginCachePath = $app->bootstrapPath('cache/plugins.php');

                if ($files->exists($pluginCachePath)) {
                    $files->delete($pluginCachePath);
                }

                if (config('core.base.general.google_fonts_enabled_cache') && $files->isDirectory(Storage::path('fonts'))) {
                    $files->deleteDirectory(Storage::path('fonts'));
                }

                break;
            case 'refresh_compiled_views':
                foreach ($files->glob(config('view.compiled') . '/*') as $view) {
                    $files->delete($view);
                }

                break;
            case 'clear_config_cache':
                $files->delete($app->getCachedConfigPath());

                break;
            case 'clear_route_cache':
                foreach ($files->glob(app()->bootstrapPath('cache/*')) as $cacheFile) {
                    if (Str::contains($cacheFile, 'cache/routes-v7')) {
                        $files->delete($cacheFile);
                    }
                }

                break;
            case 'clear_log':
                if ($files->isDirectory(storage_path('logs'))) {
                    foreach ($files->allFiles(storage_path('logs')) as $file) {
                        $files->delete($file->getPathname());
                    }
                }

                break;
        }

        return $response->setMessage(trans('core/base::cache.commands.' . $request->input('type') . '.success_msg'));
    }

    public function authorize(MembershipAuthorization $authorization, BaseHttpResponse $response)
    {
        $authorization->authorize();

        return $response;
    }

    public function getLanguage(string $lang, Request $request)
    {
        if ($lang && array_key_exists($lang, Language::getAvailableLocales())) {
            if (Auth::check()) {
                cache()->forget(md5('cache-dashboard-menu-' . $request->user()->getKey()));
            }
            session()->put('site-locale', $lang);
        }

        return redirect()->back();
    }

    public function getMenuItemsCount(BaseHttpResponse $response)
    {
        $data = apply_filters(BASE_FILTER_MENU_ITEMS_COUNT, []);

        return $response->setData($data);
    }

    public function getCheckUpdate(BaseHttpResponse $response, Core $core)
    {
        if (! config('core.base.general.enable_system_updater')) {
            return $response;
        }

        $response->setData(['has_new_version' => false]);

        $updateData = $core->checkUpdate();

        if ($updateData) {
            $response
                ->setData(['has_new_version' => true])
                ->setMessage(
                    'A new version (' . $updateData->version . ' / released on ' . $updateData->releasedDate->format('Y-m-d') . ') is available to update'
                );
        }

        return $response;
    }

    public function getUpdater(Core $core)
    {
        if (! config('core.base.general.enable_system_updater')) {
            abort(404);
        }

        header('Cache-Control: no-cache');

        Assets::addScriptsDirectly('vendor/core/core/base/js/system-update.js');
        Assets::usingVueJS();

        try {
            if (! File::exists($publicPath = public_path('vendor/core/core/base/js/system-update.js'))) {
                File::copy(core_path('base/public/js/system-update.js'), $publicPath);
            }
        } catch (Throwable) {
        }

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        PageTitle::setTitle(trans('core/base::system.updater'));

        $latestUpdate = $core->getLatestVersion();
        $isOutdated = version_compare($core->version(), $latestUpdate->version, '<');

        $updateData = ['message' => null, 'status' => false];

        return view('core/base::system.updater', compact('latestUpdate', 'isOutdated', 'updateData'));
    }

    public function postUpdater(Core $core, Request $request)
    {
        $request->validate([
            'step' => ['required', 'integer', 'min:1', 'max:4'],
            'update_id' => ['required', 'string'],
            'version' => ['required', 'string'],
        ]);

        $updateId = $request->input('update_id');
        $version = $request->input('version');

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        try {
            switch ($request->integer('step', 1)) {
                case 1:
                    event(new UpdatingEvent());

                    if ($core->downloadUpdate($updateId, $version)) {
                        return response()->json([
                            'message' => __('The update files have been downloaded successfully.'),
                        ]);
                    }

                    return response()->json([
                        'message' => __('Could not download updated file. Please check your license or your internet network.'),
                    ], 422);

                case 2:
                    if ($core->updateFilesAndDatabase($version)) {
                        return response()->json([
                            'message' => __('Your files and database have been updated successfully.'),
                        ]);
                    }

                    return response()->json([
                        'message' => __('Could not update files & database.'),
                    ], 422);

                case 3:
                    $core->publishUpdateAssets();

                    return response()->json([
                        'message' => __('Your asset files have been published successfully.'),
                    ]);
                case 4:
                    $core->cleanUpUpdate();

                    event(new UpdatedEvent());

                    return response()->json([
                        'message' => __('Your system have been cleaned up successfully.'),
                    ]);
            }
        } catch (Throwable $exception) {
            $core->logError($exception);

            return response()->json([
                'message' => $exception->getMessage() . ' - ' . $exception->getFile() . ':' . $exception->getLine(),
            ], 422);
        }

        return response()->json([
            'message' => __('Something went wrong.'),
        ], 422);
    }

    public function getCleanup(
        Request $request,
        BaseHttpResponse $response,
        CleanDatabaseService $cleanDatabaseService
    ): BaseHttpResponse|View {
        PageTitle::setTitle(trans('core/base::system.cleanup.title'));

        Assets::addScriptsDirectly('vendor/core/core/base/js/cleanup.js');

        try {
            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        } catch (Throwable) {
            $tables = [];
        }

        $disabledTables = [
            'disabled' => $cleanDatabaseService->getIgnoreTables(),
            'checked' => [],
        ];

        if ($request->isMethod('POST')) {
            if (! config('core.base.general.enabled_cleanup_database', false)) {
                return $response
                    ->setCode(401)
                    ->setError()
                    ->setMessage(strip_tags(trans('core/base::system.cleanup.not_enabled_yet')));
            }

            $request->validate(['tables' => 'array']);

            $cleanDatabaseService->execute($request->input('tables', []));

            return $response->setMessage(trans('core/base::system.cleanup.success_message'));
        }

        return view('core/base::system.cleanup', compact('tables', 'disabledTables'));
    }
}
