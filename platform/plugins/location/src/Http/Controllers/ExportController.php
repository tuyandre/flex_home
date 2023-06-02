<?php

namespace Botble\Location\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Location\Exports\CsvLocationExport;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Location\Repositories\Interfaces\CountryInterface;
use Botble\Location\Repositories\Interfaces\StateInterface;
use Maatwebsite\Excel\Excel;

class ExportController extends BaseController
{
    public function index(
        CountryInterface $countryRepository,
        StateInterface $stateRepository,
        CityInterface $cityRepository
    ) {
        PageTitle::setTitle(trans('plugins/location::location.export_location'));

        Assets::addScriptsDirectly(['vendor/core/plugins/location/js/export.js']);

        $countryCount = $countryRepository->count();
        $stateCount = $stateRepository->count();
        $cityCount = $cityRepository->count();

        return view('plugins/location::export.index', compact('countryCount', 'stateCount', 'cityCount'));
    }

    public function export()
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        return (new CsvLocationExport())->download('exported_location.csv', Excel::CSV, ['Content-Type' => 'text/csv']);
    }
}
