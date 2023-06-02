<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Base\Facades\Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Botble\Media\Facades\RvMedia;
use Botble\Table\DataTables;

class AccountTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AccountInterface $accountRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $accountRepository;

        if (! Auth::user()->hasAnyPermission(['account.edit', 'account.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('first_name', function (Account $item) {
                if (! Auth::user()->hasPermission('account.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('account.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('avatar_id', function (Account $item) {
                return Html::image(
                    RvMedia::getImageUrl($item->avatar->url, 'thumb', false, RvMedia::getDefaultImage()),
                    BaseHelper::clean($item->name),
                    ['width' => 50]
                );
            })
            ->editColumn('checkbox', function (Account $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function (Account $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('phone', function (Account $item) {
                return BaseHelper::clean($item->phone ?: '&mdash;');
            })
            ->editColumn('updated_at', function (Account $item) {
                return $item->properties_count;
            })
            ->addColumn('operations', function (Account $item) {
                return $this->getOperations('account.edit', 'account.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository
            ->getModel()
            ->select([
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'created_at',
                'credits',
                'avatar_id',
            ])
            ->with(['avatar'])
            ->withCount(['properties']);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'avatar_id' => [
                'title' => trans('core/base::tables.image'),
                'width' => '70px',
            ],
            'first_name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'email' => [
                'title' => trans('core/base::tables.email'),
                'class' => 'text-start',
            ],
            'phone' => [
                'title' => trans('plugins/real-estate::account.phone'),
                'class' => 'text-start',
            ],
            'credits' => [
                'title' => trans('plugins/real-estate::account.credits'),
                'class' => 'text-start',
            ],
            'updated_at' => [
                'title' => trans('plugins/real-estate::account.number_of_properties'),
                'width' => '100px',
                'class' => 'no-sort',
                'orderable' => false,
                'searchable' => false,
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('account.create'), 'account.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('account.deletes'), 'account.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'first_name' => [
                'title' => trans('plugins/real-estate::account.first_name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'last_name' => [
                'title' => trans('plugins/real-estate::account.last_name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'email' => [
                'title' => trans('core/base::tables.email'),
                'type' => 'text',
                'validate' => 'required|max:120|email',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
