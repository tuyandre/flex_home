<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Exports\ProjectTemplateExport;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Http\Requests\BulkImportRequest;
use Botble\RealEstate\Http\Requests\ImportProjectRequest;
use Botble\RealEstate\Imports\ProjectsImport;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Http\Request;
use Botble\Base\Facades\PageTitle;

class ProjectImportController extends BaseController
{
    public function index(ProjectTemplateExport $export)
    {
        PageTitle::setTitle(trans('plugins/real-estate::project.import_projects'));

        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/project-import.js');

        $projects = $export->collection();
        $headings = $export->headings();
        $rules = $export->rules();

        return view('plugins/real-estate::project-import.index', compact('projects', 'headings', 'rules'));
    }

    public function store(BulkImportRequest $request, BaseHttpResponse $response, ProjectsImport $projectsImport)
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        try {
            $projectsImport->validator(ImportProjectRequest::class)->import($request->file('file'));

            $message = trans('plugins/real-estate::bulk-import.import_success_message');

            return $response
                ->setData([
                    'message' => $message . ' ' . trans('plugins/real-estate::bulk-import.results', [
                        'success' => $projectsImport->successes()->count(),
                        'failed' => $projectsImport->failures()->count(),
                    ]),
                ])
                ->setMessage($message);
        } catch (ValidationException $e) {
            return $response
                ->setError()
                ->setData($e->failures())
                ->setMessage(trans('plugins/real-estate::bulk-import.import_failed_message'));
        }
    }

    public function downloadTemplate(Request $request, ProjectTemplateExport $export)
    {
        $request->validate([
            'extension' => 'required|in:csv,xlsx',
        ]);

        $extension = Excel::XLSX;
        $contentType = 'text/xlsx';

        if ($request->input('extension') === 'csv') {
            $extension = Excel::CSV;
            $contentType = 'text/csv';
        }

        $fileName = 'template_properties_import.' . $extension;

        return $export->download($fileName, $extension, ['Content-Type' => $contentType]);
    }
}
