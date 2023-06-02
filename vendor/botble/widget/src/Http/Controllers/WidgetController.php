<?php

namespace Botble\Widget\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Widget\Repositories\Interfaces\WidgetInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Botble\Language\Facades\Language;
use Botble\Theme\Facades\Theme;
use Botble\Widget\Facades\WidgetGroup;

class WidgetController extends BaseController
{
    protected string|null $theme = null;

    public function __construct(protected WidgetInterface $widgetRepository)
    {
        $this->theme = Theme::getThemeName() . $this->getCurrentLocaleCode();
    }

    public function index()
    {
        PageTitle::setTitle(trans('packages/widget::widget.name'));

        Assets::addScripts(['sortable'])
            ->addScriptsDirectly('vendor/core/packages/widget/js/widget.js');

        $widgets = $this->widgetRepository->getByTheme($this->theme);

        $groups = WidgetGroup::getGroups();
        foreach ($widgets as $widget) {
            if (Arr::has($groups, $widget->sidebar_id)) {
                WidgetGroup::group($widget->sidebar_id)
                    ->position($widget->position)
                    ->addWidget($widget->widget_id, $widget->data);
            }
        }

        return view('packages/widget::list');
    }

    public function postSaveWidgetToSidebar(Request $request, BaseHttpResponse $response)
    {
        try {
            $sidebarId = $request->input('sidebar_id');
            $this->widgetRepository->deleteBy([
                'sidebar_id' => $sidebarId,
                'theme' => $this->theme,
            ]);
            foreach ($request->input('items', []) as $key => $item) {
                parse_str($item, $data);
                if (empty($data['id'])) {
                    continue;
                }

                $this->widgetRepository->createOrUpdate([
                    'sidebar_id' => $sidebarId,
                    'widget_id' => $data['id'],
                    'theme' => $this->theme,
                    'position' => $key,
                    'data' => $data,
                ]);
            }

            $widgetAreas = $this->widgetRepository->allBy([
                'sidebar_id' => $sidebarId,
                'theme' => $this->theme,
            ]);

            return $response
                ->setData(view('packages/widget::item', compact('widgetAreas'))->render())
                ->setMessage(trans('packages/widget::widget.save_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function postDelete(Request $request, BaseHttpResponse $response)
    {
        try {
            $this->widgetRepository->deleteBy([
                'theme' => $this->theme,
                'sidebar_id' => $request->input('sidebar_id'),
                'position' => $request->input('position'),
                'widget_id' => $request->input('widget_id'),
            ]);

            return $response->setMessage(trans('packages/widget::widget.delete_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    protected function getCurrentLocaleCode(): string|null
    {
        $languageCode = null;
        if (is_plugin_active('language')) {
            $currentLocale = is_in_admin() ? Language::getCurrentAdminLocaleCode() : Language::getCurrentLocaleCode();
            $languageCode = $currentLocale && $currentLocale != Language::getDefaultLocaleCode() ? '-' . $currentLocale : null;
        }

        return $languageCode;
    }
}
