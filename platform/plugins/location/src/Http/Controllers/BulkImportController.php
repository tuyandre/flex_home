<?php

namespace Botble\Location\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Helper;
use Botble\Location\Exports\TemplateLocationExport;
use Botble\Location\Http\Requests\BulkImportRequest;
use Botble\Location\Http\Requests\LocationImportRequest;
use Botble\Location\Imports\LocationImport;
use Botble\Location\Imports\ValidateLocationImport;
use Botble\Location\Location;
use Botble\Location\Repositories\Interfaces\CountryInterface;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class BulkImportController extends BaseController
{
    public function __construct(protected LocationImport $locationImport, protected ValidateLocationImport $validateLocationImport)
    {
    }

    public function index()
    {
        PageTitle::setTitle(trans('plugins/location::bulk-import.name'));

        Assets::addScriptsDirectly(['vendor/core/plugins/location/js/bulk-import.js']);

        return view('plugins/location::bulk-import.index');
    }

    public function postImport(BulkImportRequest $request, BaseHttpResponse $response)
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        $file = $request->file('file');

        $this->validateLocationImport
            ->setValidatorClass(new LocationImportRequest())
            ->setImportType($request->input('type'))
            ->import($file);

        if ($this->validateLocationImport->failures()->count()) {
            $data = [
                'total_failed' => $this->validateLocationImport->failures()->count(),
                'total_error' => $this->validateLocationImport->errors()->count(),
                'failures' => $this->validateLocationImport->failures(),
            ];

            $message = trans('plugins/location::bulk-import.import_failed_description');

            return $response
                ->setError()
                ->setData($data)
                ->setMessage($message);
        }

        $this->locationImport
            ->setValidatorClass(new LocationImportRequest())
            ->setImportType($request->input('type'))
            ->import($file);

        $data = [
            'total_success' => $this->locationImport->successes()->count(),
            'total_failed' => $this->locationImport->failures()->count(),
            'total_error' => $this->locationImport->errors()->count(),
            'failures' => $this->locationImport->failures(),
            'successes' => $this->locationImport->successes(),
        ];

        $message = trans('plugins/location::bulk-import.imported_successfully');

        $result = trans('plugins/location::bulk-import.results', [
            'success' => $data['total_success'],
            'failed' => $data['total_failed'],
        ]);

        return $response->setData($data)->setMessage($message . ' ' . $result);
    }

    public function downloadTemplate(Request $request)
    {
        $extension = $request->input('extension');
        $extension = $extension == 'csv' ? $extension : Excel::XLSX;
        $writeType = $extension == 'csv' ? Excel::CSV : Excel::XLSX;
        $contentType = $extension == 'csv' ? ['Content-Type' => 'text/csv'] : ['Content-Type' => 'text/xlsx'];
        $fileName = 'template_locations_import.' . $extension;

        return (new TemplateLocationExport($extension))->download($fileName, $writeType, $contentType);
    }

    public function ajaxGetAvailableRemoteLocations(Location $location, BaseHttpResponse $response, CountryInterface $countryRepository)
    {
        $remoteLocations = $location->getRemoteAvailableLocations();

        $availableLocations = $countryRepository->pluck('code');

        $listCountries = Helper::countries();

        $locations = [];

        foreach ($remoteLocations as $location) {
            $location = strtoupper($location);

            if (in_array($location, $availableLocations)) {
                continue;
            }

            foreach ($listCountries as $key => $country) {
                if ($location === strtoupper($key)) {
                    $locations[$location] = $country;
                }
            }
        }

        $locations = array_unique($locations);

        return $response
            ->setData(view('plugins/location::partials.available-remote-locations', compact('locations'))->render());
    }

    public function importLocationData(string $countryCode, Location $location, BaseHttpResponse $response)
    {
        $result = $location->downloadRemoteLocation($countryCode);

        return $response
            ->setError($result['error'])
            ->setMessage($result['message']);
    }
}
