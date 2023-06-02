<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\Invoice;
use Botble\RealEstate\Repositories\Interfaces\InvoiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Botble\Table\DataTables;
use Botble\Base\Facades\Html;

class InvoiceTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, InvoiceInterface $invoiceRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $invoiceRepository;

        if (Auth::check() && ! Auth::user()->hasAnyPermission(['invoice.edit', 'invoice.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function (Invoice $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('account_id', function (Invoice $item) {
                return Html::link(route('account.edit', $item->account), $item->account->name);
            })
            ->editColumn('amount', function (Invoice $item) {
                return format_price($item->amount);
            })
            ->editColumn('code', function (Invoice $item) {
                if (! Auth::user()->hasPermission('invoice.edit')) {
                    return $item->code;
                }

                return Html::link(route('invoices.show', $item->id), $item->code);
            })
            ->editColumn('created_at', function (Invoice $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Invoice $item) {
                return $item->status->toHtml();
            })
            ->addColumn('operations', function (Invoice $item) {
                return $this->getOperations('invoices.show', 'invoices.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->select([
                'id',
                'account_id',
                'code',
                'amount',
                'created_at',
                'status',
            ])
            ->with('account');

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'account_id' => [
                'title' => trans('plugins/real-estate::invoice.account'),
                'class' => 'text-start',
            ],
            'code' => [
                'title' => trans('plugins/real-estate::invoice.code'),
                'class' => 'text-start',
            ],
            'amount' => [
                'title' => trans('plugins/real-estate::invoice.amount'),
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

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('invoices.deletes'), 'invoices.destroy', parent::bulkActions());
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
