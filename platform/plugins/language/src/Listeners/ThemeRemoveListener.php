<?php

namespace Botble\Language\Listeners;

use Botble\Setting\Repositories\Interfaces\SettingInterface;
use Botble\Theme\Events\ThemeRemoveEvent;
use Botble\Theme\Facades\ThemeOption;
use Botble\Widget\Models\Widget;
use Botble\Widget\Repositories\Interfaces\WidgetInterface;
use Exception;
use Botble\Language\Facades\Language;

class ThemeRemoveListener
{
    public function __construct(
        protected WidgetInterface $widgetRepository,
        protected SettingInterface $settingRepository
    ) {
    }

    public function handle(ThemeRemoveEvent $event): void
    {
        try {
            $languages = Language::getActiveLanguage(['lang_code']);

            foreach ($languages as $language) {
                $this->widgetRepository->deleteBy(['theme' => Widget::getThemeName($language->lang_code)]);

                $this->settingRepository->deleteBy(['key', 'like', ThemeOption::getOptionKey('%', $language->lang_code)]);
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
