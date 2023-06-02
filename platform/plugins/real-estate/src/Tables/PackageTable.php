<?php

namespace Botble\RealEstate\Tables;

use Illuminate\Support\Facades\Auth;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Models\Package;
use Botble\RealEstate\Repositories\Interfaces\PackageInterface;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Base\Facades\Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Botble\Table\DataTables;

class PackageTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, PackageInterface $packageRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $packageRepository;

        if (! Auth::user()->hasAnyPermission(['package.edit', 'package.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (Package $item) {
                if (! Auth::user()->hasPermission('package.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('package.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('checkbox', function (Package $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function (Package $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Package $item) {
                return BaseHelper::clean($item->status->toHtml());
            })
            ->addColumn('operations', function (Package $item) {
                return $this->getOperations('package.edit', 'package.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'name',
            'created_at',
            'status',
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
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('package.create'), 'package.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('package.deletes'), 'package.destroy', parent::bulkActions());
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
                'choices' => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
