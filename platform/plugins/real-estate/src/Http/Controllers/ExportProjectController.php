<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Exports\ProjectsExport;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Maatwebsite\Excel\Excel;
use Botble\Base\Facades\PageTitle;

class ExportProjectController extends BaseController
{
    public function index(ProjectInterface $projectRepository)
    {
        PageTitle::setTitle(trans('plugins/real-estate::export.projects.name'));

        Assets::addScriptsDirectly(['vendor/core/plugins/real-estate/js/export.js']);

        $totalProjects = $projectRepository->count();

        return view('plugins/real-estate::export.projects', compact('totalProjects'));
    }

    public function export(ProjectsExport $projectsExport)
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        return $projectsExport->download('export_projects.csv', Excel::CSV, ['Content-Type' => 'text/csv']);
    }
}
