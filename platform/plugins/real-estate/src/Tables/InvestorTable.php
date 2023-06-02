<?php

namespace Botble\RealEstate\Tables;

use Illuminate\Support\Facades\Auth;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Models\Investor;
use Botble\RealEstate\Repositories\Interfaces\InvestorInterface;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Base\Facades\Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Botble\Table\DataTables;

class InvestorTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, InvestorInterface $investorRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $investorRepository;

        if (! Auth::user()->hasAnyPermission(['investor.edit', 'investor.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (Investor $item) {
                if (! Auth::user()->hasPermission('investor.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('investor.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('checkbox', function (Investor $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function (Investor $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Investor $item) {
                return $item->status->toHtml();
            })
            ->addColumn('operations', function (Investor $item) {
                return $this->getOperations('investor.edit', 'investor.destroy', $item);
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
        return $this->addCreateButton(route('investor.create'), 'investor.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('investor.deletes'), 'investor.destroy', parent::bulkActions());
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
