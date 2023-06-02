<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Str;
use Botble\Slug\Facades\SlugHelper;

class DuplicatePropertyController extends BaseController
{
    public function __invoke($id, PropertyInterface $propertyRepository, BaseHttpResponse $response)
    {
        $property = $propertyRepository->findOrFail($id);

        $categories = $property->categories->pluck('id')->all();
        $facilities = $property->facilities->pluck('id')->all();
        $features = $property->features->pluck('id')->all();

        $newProperty = $property->replicate();

        if ($newProperty->unique_id) {
            $newProperty->unique_id = $newProperty->unique_id . '-' . Str::random(5);
        }

        $newProperty->save();

        $newProperty->categories()->sync($categories);
        $newProperty->facilities()->sync($facilities);
        $newProperty->features()->sync($features);

        if (RealEstateHelper::isEnabledCustomFields()) {
            if ($property->customFields()->exists()) {
                $newProperty->customFields()->save($property->customFields()->first());
            }
        }

        Slug::query()->create([
            'reference_type' => Property::class,
            'reference_id' => $newProperty->id,
            'key' => Str::slug($newProperty->name),
            'prefix' => SlugHelper::getPrefix(Property::class),
        ]);

        $route = route('property.edit', $newProperty->id);

        return $response
            ->setData(['url' => $route])
            ->setMessage(trans('plugins/real-estate::property.duplicate_property_successfully'));
    }
}
