<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Base\Facades\Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Botble\Table\DataTables;

class ProjectTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ProjectInterface $projectRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $projectRepository;
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (Project $item) {
                return Html::link(route('project.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('views', function (Project $item) {
                return number_format($item->views);
            })
            ->editColumn('image', function (Project $item) {
                return $this->displayThumbnail($item->image);
            })
            ->editColumn('unique_id', function (Project $item) {
                return BaseHelper::clean($item->unique_id ?: '&mdash;');
            })
            ->editColumn('checkbox', function (Project $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function (Project $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Project $item) {
                return BaseHelper::clean($item->status->toHtml());
            })
            ->addColumn('operations', function (Project $item) {
                return $this->getOperations('project.edit', 'project.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'name',
            'images',
            'views',
            'status',
            'created_at',
            'unique_id',
        ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'image' => [
                'title' => trans('core/base::tables.image'),
                'width' => '60px',
                'class' => 'no-sort',
                'orderable' => false,
                'searchable' => false,
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'views' => [
                'title' => trans('plugins/real-estate::project.views'),
            ],
            'unique_id' => [
                'title' => trans('plugins/real-estate::project.unique_id'),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-start',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons(): array
    {
        $buttons = $this->addCreateButton(route('project.create'), 'project.create');

        if (Auth::user()->hasPermission('import-projects.index')) {
            $buttons['import'] = [
                'link' => route('import-projects.index'),
                'text' => '<i class="fas fa-cloud-upload-alt"></i> ' . trans('plugins/real-estate::project.import_projects'),
                'class' => 'btn-warning',
            ];
        }

        if (Auth::user()->hasPermission('export-projects.index')) {
            $buttons['export'] = [
                'link' => route('export-projects.index'),
                'text' => '<i class="fas fa-cloud-download-alt"></i> ' . trans('plugins/real-estate::project.export_projects'),
                'class' => 'btn-warning',
            ];
        }

        return $buttons;
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('project.deletes'), 'project.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => ProjectStatusEnum::labels(),
                'validate' => 'required|' . Rule::in(ProjectStatusEnum::values()),
            ],
        ];
    }
}
