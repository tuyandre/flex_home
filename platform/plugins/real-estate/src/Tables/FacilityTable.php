<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\Facility;
use Illuminate\Support\Facades\Auth;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Repositories\Interfaces\FacilityInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Botble\Table\DataTables;
use Botble\Base\Facades\Html;

class FacilityTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, FacilityInterface $facilityRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $facilityRepository;

        if (! Auth::user()->hasAnyPermission(['facility.edit', 'facility.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (Facility $item) {
                if (! Auth::user()->hasPermission('facility.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('facility.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('checkbox', function (Facility $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function (Facility $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Facility $item) {
                return BaseHelper::clean($item->status->toHtml());
            })
            ->addColumn('operations', function (Facility $item) {
                return $this->getOperations('facility.edit', 'facility.destroy', $item);
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
        return $this->addCreateButton(route('facility.create'), 'facility.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('facility.deletes'), 'facility.destroy', parent::bulkActions());
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
