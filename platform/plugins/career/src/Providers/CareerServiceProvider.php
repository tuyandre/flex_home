<?php

namespace Botble\Career\Providers;

use Botble\Career\Models\Career;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Botble\Career\Repositories\Caches\CareerCacheDecorator;
use Botble\Career\Repositories\Eloquent\CareerRepository;
use Botble\Career\Repositories\Interfaces\CareerInterface;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
use Botble\Language\Facades\Language;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\SiteMapManager;
use Botble\Slug\Facades\SlugHelper;

class CareerServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(CareerInterface::class, function () {
            return new CareerCacheDecorator(new CareerRepository(new Career()));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot(): void
    {
        SlugHelper::registerModule(Career::class, 'Careers');
        SlugHelper::setPrefix(Career::class, 'careers');

        $this->setNamespace('plugins/career')
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadRoutes();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                'id' => 'cms-plugins-career',
                'priority' => 5,
                'parent_id' => null,
                'name' => 'plugins/career::career.name',
                'icon' => 'far fa-newspaper',
                'url' => route('career.index'),
                'permissions' => ['career.index'],
            ]);
        });

        $useLanguageV2 = $this->app['config']->get('plugins.career.general.use_language_v2', false) &&
            defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME');

        if (defined('LANGUAGE_MODULE_SCREEN_NAME') && $useLanguageV2) {
            LanguageAdvancedManager::registerModule(Career::class, [
                'name',
                'location',
                'salary',
                'description',
            ]);
        }

        $this->app->booted(function () use ($useLanguageV2) {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME') && ! $useLanguageV2) {
                Language::registerModule([Career::class]);
            }
        });

        $this->app->booted(function () {
            SeoHelper::registerModule([Career::class]);
        });

        $this->app->register(EventServiceProvider::class);

        SiteMapManager::registerKey(['careers']);
    }
}
