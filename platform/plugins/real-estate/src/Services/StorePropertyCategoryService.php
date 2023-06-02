<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\Abstracts\StorePropertyCategoryServiceAbstract;
use Illuminate\Http\Request;

class StorePropertyCategoryService extends StorePropertyCategoryServiceAbstract
{
    public function execute(Request $request, Property $property): void
    {
        $categories = $request->input('categories', []);
        if (is_array($categories)) {
            if ($categories) {
                $property->categories()->sync($categories);
            } else {
                $property->categories()->detach();
            }
        }
    }
}
