<?php

namespace Database\Seeders;

use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\Language;
use Botble\Language\Models\LanguageMeta;
use Botble\Setting\Models\Setting;

class LanguageSeeder extends BaseSeeder
{
    public function run(): void
    {
        $languages = [
            [
                'lang_name' => 'English',
                'lang_locale' => 'en',
                'lang_is_default' => true,
                'lang_code' => 'en_US',
                'lang_is_rtl' => false,
                'lang_flag' => 'us',
                'lang_order' => 0,
            ],
            [
                'lang_name' => 'Tiếng Việt',
                'lang_locale' => 'vi',
                'lang_is_default' => false,
                'lang_code' => 'vi',
                'lang_is_rtl' => false,
                'lang_flag' => 'vn',
                'lang_order' => 1,
            ],
            [
                'lang_name' => 'Arabic',
                'lang_locale' => 'ar',
                'lang_is_default' => false,
                'lang_code' => 'ar',
                'lang_is_rtl' => true,
                'lang_flag' => 'ar',
                'lang_order' => 2,
            ],
        ];

        Language::query()->truncate();
        LanguageMeta::query()->truncate();

        foreach ($languages as $item) {
            Language::query()->create($item);
        }

        $settings = [
            [
                'key' => 'language_hide_default',
                'value' => '1',
            ],
            [
                'key' => 'language_switcher_display',
                'value' => 'list',
            ],
            [
                'key' => 'language_display',
                'value' => 'all',
            ],
            [
                'key' => 'language_hide_languages',
                'value' => '[]',
            ],
        ];

        Setting::query()->whereIn('key', collect($settings)->pluck('key')->all())->delete();

        if (BaseModel::determineIfUsingUuidsForId()) {
            $settings = array_map(function ($item) {
                $item['id'] = BaseModel::newUniqueId();

                return $item;
            }, $settings);
        }

        Setting::query()->insertOrIgnore($settings);
    }
}
