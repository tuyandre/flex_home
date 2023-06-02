<?php

use Botble\Theme\Facades\Theme;
use Illuminate\Database\Migrations\Migration;
use Botble\Setting\Models\Setting as SettingModel;

return new class () extends Migration {
    public function up(): void
    {
        $socialLinks = [
            [
                [
                    'key'   => 'social-name',
                    'value' => 'Facebook',
                ],
                [
                    'key'   => 'social-icon',
                    'value' => 'fab fa-facebook-f',
                ],
                [
                    'key'   => 'social-url',
                    'value' => theme_option('facebook'),
                ],
            ],
            [
                [
                    'key'   => 'social-name',
                    'value' => 'Twitter',
                ],
                [
                    'key'   => 'social-icon',
                    'value' => 'fab fa-twitter',
                ],
                [
                    'key'   => 'social-url',
                    'value' => theme_option('twitter'),
                ],
            ],
            [
                [
                    'key'   => 'social-name',
                    'value' => 'Youtube',
                ],
                [
                    'key'   => 'social-icon',
                    'value' => 'fab fa-youtube',
                ],
                [
                    'key'   => 'social-url',
                    'value' => theme_option('youtube'),
                ],
            ],
        ];

        SettingModel::query()->insertOrIgnore([
            'key'   => 'theme-' . Theme::getThemeName() . '-social_links',
            'value' => json_encode($socialLinks),
        ]);
    }
};
