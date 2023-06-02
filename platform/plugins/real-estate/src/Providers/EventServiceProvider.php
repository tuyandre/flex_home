<?php

namespace Botble\RealEstate\Providers;

use Botble\Location\Events\ImportedCityEvent;
use Botble\RealEstate\Listeners\AddExtraFieldsWhenImportingCityListener;
use Botble\RealEstate\Listeners\AddSitemapListener;
use Botble\RealEstate\Listeners\UpdatedContentListener;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UpdatedContentEvent::class => [
            UpdatedContentListener::class,
        ],
        RenderingSiteMapEvent::class => [
            AddSitemapListener::class,
        ],
        ImportedCityEvent::class => [
            AddExtraFieldsWhenImportingCityListener::class,
        ],
    ];
}
