<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Base\Facades\Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\Table\DataTables;

class PropertyTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        PropertyInterface $propertyRepository
    ) {
        parent::__construct($table, $urlGenerator);

        $this->repository = $propertyRepository;
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (Property $item) {
                return Html::link(route('property.edit', $item->id), $item->name);
            })
            ->editColumn('views', function (Property $item) {
                return number_format($item->views);
            })
            ->editColumn('image', function (Property $item) {
                return $this->displayThumbnail($item->image);
            })
            ->editColumn('unique_id', function (Property $item) {
                return BaseHelper::clean($item->unique_id ?: '&mdash;');
            })
            ->editColumn('checkbox', function (Property $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function (Property $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Property $item) {
                return BaseHelper::clean($item->status->toHtml());
            })
            ->editColumn('moderation_status', function (Property $item) {
                return BaseHelper::clean($item->moderation_status->toHtml());
            })
            ->addColumn('operations', function (Property $item) {
                return $this->getOperations('property.edit', 'property.destroy', $item);
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
            'moderation_status',
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
                'width' => '50px',
                'class' => 'no-sort',
                'orderable' => false,
                'searchable' => false,
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'views' => [
                'title' => trans('plugins/real-estate::property.views'),
            ],
            'unique_id' => [
                'title' => trans('plugins/real-estate::property.unique_id'),
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
            'moderation_status' => [
                'title' => trans('plugins/real-estate::property.moderation_status'),
                'width' => '150px',
            ],
        ];
    }

    public function buttons(): array
    {
        $buttons = $this->addCreateButton(route('property.create'), 'property.create');

        if (Auth::user()->hasPermission('import-properties.index')) {
            $buttons['import'] = [
                'link' => route('import-properties.index'),
                'text' => '<i class="fas fa-cloud-upload-alt"></i> ' . trans('plugins/real-estate::property.import_properties'),
                'class' => 'btn-warning',
            ];
        }

        if (Auth::user()->hasPermission('export-properties.index')) {
            $buttons['export'] = [
                'link' => route('export-properties.index'),
                'text' => '<i class="fas fa-cloud-download-alt"></i> ' . trans('plugins/real-estate::property.export_properties'),
                'class' => 'btn-warning',
            ];
        }

        return $buttons;
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('property.deletes'), 'property.destroy', parent::bulkActions());
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
                'choices' => PropertyStatusEnum::labels(),
                'validate' => 'required|' . Rule::in(PropertyStatusEnum::values()),
            ],
            'moderation_status' => [
                'title' => trans('plugins/real-estate::property.moderation_status'),
                'type' => 'select',
                'choices' => ModerationStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', ModerationStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }

    public function applyFilterCondition(EloquentBuilder|QueryBuilder|EloquentRelation $query, string $key, string $operator, ?string $value): EloquentRelation|EloquentBuilder|QueryBuilder
    {
        if ($key == 'status') {
            switch ($value) {
                case 'expired':
                    // @phpstan-ignore-next-line
                    return $query->expired();
                case 'active':
                    // @phpstan-ignore-next-line
                    return $query
                        ->notExpired()
                        ->where(RealEstateHelper::getPropertyDisplayQueryConditions());
            }
        }

        return parent::applyFilterCondition($query, $key, $operator, $value);
    }
}
