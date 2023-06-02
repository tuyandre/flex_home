<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Models\Invoice;
use Botble\Base\Facades\Html;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class AccountInvoiceTable extends InvoiceTable
{
    protected $hasActions = false;

    protected $hasFilter = false;

    protected $hasCheckbox = false;

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function (Invoice $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('amount', function (Invoice $item) {
                return format_price($item->amount);
            })
            ->editColumn('code', function (Invoice $item) {
                return Html::link(route('public.account.invoices.show', $item->id), $item->code);
            })
            ->editColumn('created_at', function (Invoice $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Invoice $item) {
                return $item->status->toHtml();
            })
            ->addColumn('operations', function (Invoice $item) {
                $extra = view('plugins/real-estate::account.table.view-invoice-button', compact('item'))->render();

                return view('plugins/real-estate::account.table.actions', ['edit' => null, 'delete' => null, 'item' => $item, 'extra' => $extra])->render();
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->select([
                'id',
                'code',
                'amount',
                'created_at',
                'status',
            ])
            ->where('account_id', auth('account')->id());

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
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
}
