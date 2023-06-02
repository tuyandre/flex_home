<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Exports\PropertiesExport;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Maatwebsite\Excel\Excel;
use Botble\Base\Facades\PageTitle;

class ExportPropertyController extends BaseController
{
    public function index(PropertyInterface $propertyRepository)
    {
        PageTitle::setTitle(trans('plugins/real-estate::export.properties.name'));

        Assets::addScriptsDirectly(['vendor/core/plugins/real-estate/js/export.js']);

        $totalProperties = $propertyRepository->count();

        return view('plugins/real-estate::export.properties', compact('totalProperties'));
    }

    public function export(PropertiesExport $propertiesExport)
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        return $propertiesExport->download('export_properties.csv', Excel::CSV, ['Content-Type' => 'text/csv']);
    }
}
