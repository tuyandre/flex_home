<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Media\Facades\RvMedia;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Botble\Base\Facades\Html;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class AccountPropertyTable extends PropertyTable
{
    protected $hasActions = false;

    protected $hasFilter = false;

    protected $hasCheckbox = false;

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (Property $item) {
                return Html::link(route('public.account.properties.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('image', function (Property $item) {
                return Html::image(
                    RvMedia::getImageUrl($item->image, 'thumb', false, RvMedia::getDefaultImage()),
                    BaseHelper::clean($item->name),
                    ['width' => 50]
                );
            })
            ->editColumn('checkbox', function (Property $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function (Property $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('unique_id', function (Property $item) {
                return BaseHelper::clean($item->unique_id ?: '&mdash;');
            })
            ->editColumn('expire_date', function (Property $item) {
                if ($item->never_expired) {
                    return trans('plugins/real-estate::property.never_expired_label');
                }

                if (! $item->expire_date) {
                    return '&mdash;';
                }

                if ($item->expire_date->isPast()) {
                    return Html::tag('span', $item->expire_date->toDateString(), ['class' => 'text-danger'])->toHtml();
                }

                if (now()->diffInDays($item->expire_date) < 3) {
                    return Html::tag('span', $item->expire_date->toDateString(), ['class' => 'text-warning'])->toHtml();
                }

                return $item->expire_date->toDateString();
            })
            ->editColumn('status', function (Property $item) {
                return BaseHelper::clean($item->status->toHtml());
            })
            ->editColumn('moderation_status', function (Property $item) {
                return BaseHelper::clean($item->moderation_status->toHtml());
            })
            ->addColumn('operations', function (Property $item) {
                $edit = 'public.account.properties.edit';
                $delete = 'public.account.properties.destroy';
                $extra = view('plugins/real-estate::account.table.property-renew-button', compact('item'))->render();

                return view('plugins/real-estate::account.table.actions', compact('edit', 'delete', 'item', 'extra'))->render();
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->select([
                'id',
                'name',
                'images',
                'created_at',
                'status',
                'moderation_status',
                'expire_date',
                'views',
                'unique_id',
            ])
            ->where([
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ]);

        return $this->applyScopes($query);
    }

    public function buttons(): array
    {
        $buttons = [];
        if (auth('account')->user()->canPost()) {
            $buttons = $this->addCreateButton(route('public.account.properties.create'));
        }

        return $buttons;
    }

    public function columns(): array
    {
        $columns = parent::columns();
        Arr::forget($columns, 'author_id');

        $columns['expire_date'] = [
            'name' => 'expire_date',
            'title' => trans('plugins/real-estate::property.expire_date'),
            'width' => '150px',
        ];

        return $columns;
    }

    public function getDefaultButtons(): array
    {
        return ['reload'];
    }
}
