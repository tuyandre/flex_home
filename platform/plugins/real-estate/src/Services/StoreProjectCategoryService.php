<?php

namespace Botble\RealEstate\Services;

use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Services\Abstracts\StoreProjectCategoryServiceAbstract;
use Illuminate\Http\Request;

class StoreProjectCategoryService extends StoreProjectCategoryServiceAbstract
{
    public function execute(Request $request, Project $project): void
    {
        $categories = $request->input('categories', []);
        if (is_array($categories)) {
            if ($categories) {
                $project->categories()->sync($categories);
            } else {
                $project->categories()->detach();
            }
        }
    }
}
