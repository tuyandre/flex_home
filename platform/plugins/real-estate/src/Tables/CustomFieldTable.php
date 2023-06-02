<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Models\CustomField;
use Botble\RealEstate\Repositories\Interfaces\CustomFieldInterface;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Base\Facades\Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Botble\Table\DataTables;

class CustomFieldTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CustomFieldInterface $optionRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $optionRepository;

        if (! Auth::user()->hasAnyPermission(['real-estate.custom-fields.edit', 'real-estate.custom-fields.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'name',
            'created_at',
        ]);

        return $this->applyScopes($query);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (CustomField $item) {
                if (! Auth::user()->hasPermission('real-estate.custom-fields.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('real-estate.custom-fields.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('checkbox', function (CustomField $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function (CustomField $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function (CustomField $item) {
                return $this->getOperations('real-estate.custom-fields.edit', 'real-estate.custom-fields.destroy', $item);
            });

        return $this->toJson($data);
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
                'class' => 'text-center',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('real-estate.custom-fields.create'), 'real-estate.custom-fields.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(
            route('real-estate.custom-fields.deletes'),
            'real-estate.custom-fields.destroy',
            parent::bulkActions()
        );
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
