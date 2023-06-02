<?php

namespace Botble\Language\Traits;

use Botble\Language\LanguageManager;
use Botble\Language\Facades\Language;

trait TranslatedRouteCommandContext
{
    protected function isSupportedLocale(string|null $locale): bool
    {
        return in_array($locale, $this->getSupportedLocales());
    }

    protected function getSupportedLocales(): array
    {
        return $this->getLocalization()->getSupportedLanguagesKeys();
    }

    protected function getLocalization()
    {
        return $this->laravel->make(LanguageManager::class);
    }

    protected function getBootstrapPath(): string
    {
        return $this->laravel->bootstrapPath();
    }

    protected function makeLocaleRoutesPath(string|null $locale = ''): string
    {
        $path = $this->laravel->getCachedRoutesPath();

        if (! $locale || (Language::hideDefaultLocaleInURL() && $locale == Language::getDefaultLocale())) {
            return $path;
        }

        return substr($path, 0, -4) . '_' . $locale . '.php';
    }
}
