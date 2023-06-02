<?php

namespace Botble\RealEstate\Listeners;

use Botble\Location\Events\ImportedCityEvent;
use Illuminate\Support\Arr;
use Botble\RealEstate\Facades\RealEstateHelper;

class AddExtraFieldsWhenImportingCityListener
{
    public function handle(ImportedCityEvent $event): void
    {
        $slug = Arr::get($event->row, 'slug') ?: Arr::get($event->row, 'name');
        $slug = RealEstateHelper::createCitySlug($slug, $event->city);

        $event->city->slug = $slug;
        $event->city->save();
    }
}
