<?php

namespace Database\Seeders;

use Botble\Language\Facades\Language;
use Botble\Language\Models\LanguageMeta;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Property;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $items = LanguageMeta::query()->where('reference_type', Property::class)
            ->where('lang_meta_code', '!=', Language::getDefaultLocaleCode())
            ->get();

        foreach ($items as $item) {
            $originalItem = Property::query()->find($item->reference_id);

            if (! $originalItem) {
                continue;
            }

            $originalId = LanguageMeta::query()->where('lang_meta_origin', $item->lang_meta_origin)
                ->where('lang_meta_code', Language::getDefaultLocaleCode())
                ->value('reference_id');

            if (! $originalId) {
                continue;
            }

            DB::table('re_properties_translations')->insert([
                're_properties_id' => $originalId,
                'lang_code' => $item->lang_meta_code,
                'name' => $originalItem->name,
                'description' => $originalItem->description,
                'content' => $originalItem->content,
                'location' => $originalItem->location,
            ]);

            $originalItem->delete();

            $item->delete();
        }

        Property::query()->update(['expire_date' => Carbon::now()->addDays(RealEstateHelper::propertyExpiredDays())]);

        DB::statement('UPDATE re_properties SET views = FLOOR(rand() * 10000) + 1;');
    }
}
