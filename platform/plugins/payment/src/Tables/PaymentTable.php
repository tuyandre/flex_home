<?php

namespace Botble\Payment\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Base\Facades\Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Botble\Table\DataTables;

class PaymentTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, PaymentInterface $paymentRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $paymentRepository;

        if (! Auth::user()->hasAnyPermission(['payment.show', 'payment.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('charge_id', function (Payment $item) {
                return Html::link(route('payment.show', $item->id), Str::limit($item->charge_id, 20));
            })
            ->editColumn('checkbox', function (Payment $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('customer_id', function (Payment $item) {
                if ($item->customer_id && $item->customer_type && class_exists($item->customer_type)) {
                    return $item->customer->name;
                }

                if ($item->order && $item->order->address) {
                    return $item->order->address->name;
                }

                return '&mdash;';
            })
            ->editColumn('payment_channel', function (Payment $item) {
                return $item->payment_channel->label();
            })
            ->editColumn('amount', function (Payment $item) {
                return $item->amount . ' ' . $item->currency;
            })
            ->editColumn('created_at', function (Payment $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Payment $item) {
                return $item->status->toHtml();
            })
            ->addColumn('operations', function (Payment $item) {
                return $this->getOperations('payment.show', 'payment.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'charge_id',
            'amount',
            'currency',
            'payment_channel',
            'created_at',
            'status',
            'order_id',
            'customer_id',
            'customer_type',
        ])->with(['customer']);

        if (method_exists($query->getModel(), 'order')) {
            $query->with(['customer', 'order']);
        }

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'charge_id' => [
                'title' => trans('plugins/payment::payment.charge_id'),
                'class' => 'text-start',
            ],
            'customer_id' => [
                'title' => trans('plugins/payment::payment.payer_name'),
                'class' => 'text-start',
            ],
            'amount' => [
                'title' => trans('plugins/payment::payment.amount'),
                'class' => 'text-start',
            ],
            'payment_channel' => [
                'title' => trans('plugins/payment::payment.payment_channel'),
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
        return $this->addDeleteAction(route('payment.deletes'), 'payment.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'customSelect',
                'choices' => PaymentStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', PaymentStatusEnum::values()),
            ],
            'charge_id' => [
                'title' => trans('plugins/payment::payment.charge_id'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }

    public function saveBulkChangeItem(Model|Payment $item, string $inputKey, ?string $inputValue): Model|bool
    {
        if ($inputKey === 'status') {
            $request = request();

            $request->merge(['status' => $inputValue]);

            do_action(ACTION_AFTER_UPDATE_PAYMENT, $request, $item);
        }

        return parent::saveBulkChangeItem($item, $inputKey, $inputValue);
    }
}
