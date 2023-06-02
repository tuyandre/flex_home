<?php

namespace Botble\Theme\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Botble\Theme\Facades\Theme;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Move base routes to a service provider to make sure all filters & actions can hook to base routes
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $routeFilePath = theme_path(Theme::getThemeName() . '/routes/web.php');

            if ($this->app['files']->exists($routeFilePath)) {
                $this->loadRoutesFrom($routeFilePath);
            }
        });
    }
}
