<?php

namespace Botble\Translation\Tables;

use Botble\Base\Supports\Language;
use Illuminate\Http\JsonResponse;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Translation\Manager;
use Botble\Base\Facades\Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Arr;
use Botble\Table\DataTables;

class ThemeTranslationTable extends TableAbstract
{
    protected $view = 'core/table::simple-table';

    protected $hasCheckbox = false;

    protected $hasOperations = false;

    protected int $pageLength = 100;

    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        protected Manager $manager,
        protected string $locale = 'en'
    ) {
        parent::__construct($table, $urlGenerator);
    }

    public function ajax(): JsonResponse
    {
        $table = $this->table
            ->of($this->manager->getTranslationData($this->locale))
            ->editColumn('key', fn (array $item) => $this->formatKeyAndValue($item['key']))
            ->editColumn(
                $this->locale,
                fn (array $item) => Html::link('#edit', $this->formatKeyAndValue($item['value']), [
                    'class' => 'editable',
                    'data-locale' => $this->locale,
                    'data-name' => $item['key'],
                    'data-type' => 'textarea',
                    'data-pk' => $this->locale,
                    'data-title' => trans('plugins/translation::translation.edit_title'),
                    'data-url' => route('translations.theme-translations.post'),
                ])
            );

        return $this->toJson($table);
    }

    public function columns(): array
    {
        return [
            'key' => [
                'class' => 'text-start',
            ],
            $this->locale => [
                'title' => Arr::get(Language::getAvailableLocales(), $this->locale . '.name', $this->locale),
                'class' => 'text-start',
            ],
        ];
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    protected function formatKeyAndValue(string|null $value): string|null
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    public function htmlDrawCallbackFunction(): string|null
    {
        return parent::htmlDrawCallbackFunction() . '$(".editable").editable({mode: "inline"});';
    }
}
