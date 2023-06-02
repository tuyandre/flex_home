<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Enums\ReviewStatusEnum;
use Botble\RealEstate\Models\Review;
use Botble\RealEstate\Repositories\Interfaces\ReviewInterface;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Base\Facades\Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Botble\Table\DataTables;

class ReviewTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ReviewInterface $reviewRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $reviewRepository;

        if (! Auth::user()->hasAnyPermission(['review.edit', 'review.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('account_id', function (Review $item) {
                return Html::link(route('account.edit', $item->author->id), BaseHelper::clean($item->author->name))->toHtml();
            })
            ->editColumn('reviewable', function (Review $item) {
                return Html::link($item->reviewable->url, $item->reviewable->name, ['target' => '_blank']);
            })
            ->editColumn('star', function (Review $item) {
                return view('plugins/real-estate::partials.review-star', ['star' => $item->star])->render();
            })
            ->editColumn('checkbox', function (Review $item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('content', function (Review $item) {
                return BaseHelper::clean($item->content);
            })
            ->editColumn('status', function (Review $item) {
                return BaseHelper::clean($item->status->toHtml());
            })
            ->editColumn('created_at', function (Review $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function (Review $item) {
                return $this->getOperations(null, 'review.destroy', $item);
            })
            ->filter(function ($query) {
                $keyword = $this->request->input('search.value');
                if ($keyword) {
                    return $query
                        ->whereHas('reviewable', function ($subQuery) use ($keyword) {
                            return $subQuery->where('name', 'LIKE', '%' . $keyword . '%');
                        })
                        ->orWhereHas('author', function ($subQuery) use ($keyword) {
                            return $subQuery
                                ->where('first_name', 'LIKE', '%' . $keyword . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
                                ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . $keyword . '%');
                        });
                }

                return $query;
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->select([
                'id',
                'reviewable_type',
                'reviewable_id',
                'star',
                'content',
                'account_id',
                'status',
                'created_at',
            ])
            ->with(['author', 'reviewable']);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-start',
            ],
            'account_id' => [
                'title' => trans('plugins/real-estate::review.author'),
                'class' => 'text-start',
            ],
            'reviewable' => [
                'title' => trans('plugins/real-estate::review.reviewable'),
                'class' => 'text-start',
                'searchable' => false,
                'sortable' => false,
            ],
            'star' => [
                'title' => trans('plugins/real-estate::review.star'),
                'class' => 'text-center',
            ],
            'content' => [
                'title' => trans('plugins/real-estate::review.content'),
                'class' => 'text-start',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'class' => 'text-center',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '70px',
                'class' => 'text-start',
            ],
        ];
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('review.deletes'), 'review.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => ReviewStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', ReviewStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
