<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Botble\Slug\Facades\SlugHelper;

class CategorySeeder extends BaseSeeder
{
    public function run(): void
    {
        Category::query()->truncate();

        $categories = [
            [
                'name' => 'Apartment',
                'is_default' => true,
                'order' => 0,
            ],
            [
                'name' => 'Villa',
                'is_default' => false,
                'order' => 1,
            ],
            [
                'name' => 'Condo',
                'is_default' => false,
                'order' => 2,
            ],
            [
                'name' => 'House',
                'is_default' => false,
                'order' => 3,
            ],
            [
                'name' => 'Land',
                'is_default' => false,
                'order' => 4,
            ],
            [
                'name' => 'Commercial property',
                'is_default' => false,
                'order' => 5,
            ],
        ];

        Category::query()->truncate();
        DB::table('re_categories_translations')->truncate();
        Slug::query()->where('reference_type', Category::class)->delete();
        LanguageMeta::query()->where('reference_type', Category::class)->delete();

        foreach ($categories as $item) {
            $category = Category::query()->create($item);

            Slug::query()->create([
                'reference_type' => Category::class,
                'reference_id' => $category->id,
                'key' => Str::slug($category->name),
                'prefix' => SlugHelper::getPrefix(Category::class),
            ]);
        }

        $properties = Property::query()->get();

        foreach ($properties as $property) {
            $property->categories()->sync([Category::query()->inRandomOrder()->value('id')]);
            $property->save();
        }

        $projects = Project::query()->get();

        foreach ($projects as $project) {
            $project->categories()->sync([Category::query()->inRandomOrder()->value('id')]);
            $project->save();
        }

        $translations = [
            [
                'name' => 'Căn hộ',
            ],
            [
                'name' => 'Biệt thự',
            ],
            [
                'name' => 'Condo',
            ],
            [
                'name' => 'Nhà ở',
            ],
            [
                'name' => 'Đất',
            ],
            [
                'name' => 'Căn hộ thương mại',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['re_categories_id'] = $index + 9;

            DB::table('re_categories_translations')->insert($item);
        }
    }
}
