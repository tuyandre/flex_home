<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Botble\Language\Facades\Language;
use Botble\RealEstate\Facades\RealEstateHelper;

class ProjectRepository extends RepositoriesAbstract implements ProjectInterface
{
    public function getProjects($filters = [], $params = []): Collection|LengthAwarePaginator
    {
        $filters = array_merge([
            'keyword' => null,
            'city' => null,
            'min_floor' => null,
            'max_floor' => null,
            'blocks' => null,
            'min_flat' => null,
            'max_flat' => null,
            'category_id' => null,
            'city_id' => null,
            'location' => null,
            'sort_by' => null,
        ], $filters);

        $orderBy = match ($filters['sort_by']) {
            'date_asc' => [
                're_projects.created_at' => 'ASC',
            ],
            'price_asc' => [
                're_projects.price_from' => 'ASC',
            ],
            'price_desc' => [
                're_projects.price_from' => 'DESC',
            ],
            'name_asc' => [
                're_projects.name' => 'ASC',
            ],
            'name_desc' => [
                're_projects.name' => 'DESC',
            ],
            default => [
                're_projects.created_at' => 'DESC',
            ],
        };

        $params = array_merge([
            'condition' => RealEstateHelper::getProjectDisplayQueryConditions(),
            'order_by' => [
                're_projects.created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => 10,
                'current_paged' => 1,
            ],
            'select' => [
                're_projects.*',
            ],
            'with' => [],
        ], $params);

        $params['order_by'] = $orderBy;

        $this->model = $this->originalModel;

        if ($filters['keyword'] !== null) {
            $keyword = $filters['keyword'];

            $this->model = $this->model
                ->where(function (Builder $query) use ($keyword) {
                    return $query
                        ->where('re_projects.name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('re_projects.location', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('re_projects.description', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('re_projects.unique_id', 'LIKE', '%' . $keyword . '%');
                });
        }

        if ($filters['city'] !== null) {
            $this->model = $this->model->whereHas('city', function ($query) use ($filters) {
                $query->where('slug', $filters['city']);
            });
        }

        if ($filters['blocks']) {
            if ($filters['blocks'] < 5) {
                $this->model = $this->model->where('re_projects.number_block', $filters['blocks']);
            } else {
                $this->model = $this->model->where('re_projects.number_block', '>=', $filters['blocks']);
            }
        }

        if ($filters['min_floor'] !== null || $filters['max_floor'] !== null) {
            $this->model = $this->model
                ->where(function (Builder $query) use ($filters) {
                    $minFloor = Arr::get($filters, 'min_floor');
                    $maxFloor = Arr::get($filters, 'max_floor');

                    if ($minFloor !== null) {
                        $query = $query->where('re_projects.number_floor', '>=', $minFloor);
                    }

                    if ($maxFloor !== null) {
                        $query = $query->where('re_projects.number_floor', '<=', $maxFloor);
                    }

                    return $query;
                });
        }

        if ($filters['min_flat'] !== null || $filters['max_flat'] !== null) {
            $this->model = $this->model
                ->where(function (Builder $query) use ($filters) {
                    $minFlat = Arr::get($filters, 'min_flat');
                    $maxFlat = Arr::get($filters, 'max_flat');

                    if ($minFlat !== null) {
                        $query = $query->where('re_projects.number_flat', '>=', $minFlat);
                    }

                    if ($maxFlat !== null) {
                        $query = $query->where('re_projects.number_flat', '<=', $maxFlat);
                    }

                    return $query;
                });
        }

        if ($filters['category_id'] !== null) {
            $categoryIds = get_property_categories_related_ids($filters['category_id']);
            $this->model = $this->model
                ->whereHas('categories', function (Builder $query) use ($categoryIds) {
                    $query->whereIn('category_id', $categoryIds);
                });
        }

        if ($filters['city_id']) {
            $this->model = $this->model->where('re_projects.city_id', $filters['city_id']);
        } elseif ($filters['location']) {
            $locationData = explode(',', $filters['location']);

            if (count($locationData) > 1) {
                $locationSearch = trim($locationData[0]);
            } else {
                $locationSearch = trim($filters['location']);
            }

            if (is_plugin_active('language') && is_plugin_active('language-advanced') && Language::getCurrentLocale() != Language::getDefaultLocale()) {
                $this->model = $this->model
                    ->where(function (Builder $query) use ($locationSearch) {
                        return $query
                            ->whereHas('city.translations', function ($query) use ($locationSearch) {
                                $query->where('name', 'LIKE', '%' . $locationSearch . '%');
                            })
                            ->orWhereHas('city.state.translations', function ($query) use ($locationSearch) {
                                $query->where('name', 'LIKE', '%' . $locationSearch . '%');
                            })
                            ->orWhere('re_projects.location', 'LIKE', '%' . $locationSearch . '%');
                    });
            } else {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_projects.city_id')
                    ->join('states', 'states.id', '=', 'cities.state_id')
                    ->where(function ($query) use ($locationSearch) {
                        return $query
                            ->where('cities.name', 'LIKE', '%' . $locationSearch . '%')
                            ->orWhere('states.name', 'LIKE', '%' . $locationSearch . '%')
                            ->orWhere('re_projects.location', 'LIKE', '%' . $locationSearch . '%');
                    });
            }
        }

        if (count($filters['category_ids'] ?? [])) {
            $categoryIds = $filters['category_ids'];

            $this->model = $this->model
                ->whereHas('categories', function (Builder $query) use ($categoryIds) {
                    $query->whereIn('category_id', $categoryIds);
                });
        }

        if ($filters['locations'] ?? []) {
            $locationsSearch = $filters['locations'];

            if (is_plugin_active('language') && is_plugin_active('language-advanced') && Language::getCurrentLocale() != Language::getDefaultLocale()) {
                $this->model = $this->model
                    ->where(function (Builder $query) use ($locationsSearch) {
                        return $query
                            ->whereHas('city.translations', function (Builder $query) use ($locationsSearch) {
                                foreach ($locationsSearch as $location) {
                                    $query->where('name', 'LIKE', '%' . $location . '%');
                                }
                            })
                            ->orWhereHas('city.state.translations', function (Builder $query) use ($locationsSearch) {
                                foreach ($locationsSearch as $location) {
                                    $query->where('name', 'LIKE', '%' . $location . '%');
                                }
                            })
                            ->orWhere(function ($query) use ($locationsSearch) {
                                foreach ($locationsSearch as $location) {
                                    $query->orwhere('re_projects.location', 'like', '%' . $location . '%');
                                }
                            });
                    });
            } else {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_projects.city_id')
                    ->join('states', 'states.id', '=', 'cities.state_id')
                    ->where(function (Builder $query) use ($locationsSearch) {
                        return $query
                            ->where(function ($query) use ($locationsSearch) {
                                foreach ($locationsSearch as $location) {
                                    $query->orwhere('cities.name', 'like', '%' . $location . '%');
                                }
                            })
                            ->orWhere(function ($query) use ($locationsSearch) {
                                foreach ($locationsSearch as $location) {
                                    $query->orwhere('states.name', 'like', '%' . $location . '%');
                                }
                            })
                            ->orWhere(function ($query) use ($locationsSearch) {
                                foreach ($locationsSearch as $location) {
                                    $query->orwhere('re_projects.location', 'like', '%' . $location . '%');
                                }
                            });
                    });
            }
        }

        return $this->advancedGet($params);
    }

    public function getRelatedProjects(int $projectId, int $limit = 4, array $with = []): Collection|LengthAwarePaginator
    {
        $currentProject = $this->findById($projectId, ['categories']);

        $this->model = $this->originalModel;
        $this->model = $this->model
            ->where('re_projects.id', '<>', $projectId);

        if ($currentProject && $currentProject->categories->count()) {
            $categoryIds = $currentProject->categories->pluck('id')->toArray();

            $this->model
                ->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('re_project_categories.category_id', $categoryIds);
                });
        }

        $params = [
            'condition' => RealEstateHelper::getProjectDisplayQueryConditions(),
            'order_by' => [
                'created_at' => 'DESC',
            ],
            'take' => $limit,
            'with' => $with,
        ];

        return $this->advancedGet($params);
    }
}
