<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Botble\Language\Facades\Language;
use Botble\RealEstate\Facades\RealEstateHelper;

class PropertyRepository extends RepositoriesAbstract implements PropertyInterface
{
    public function getRelatedProperties(int $propertyId, int $limit = 4, array $with = [], array $extra = []): Collection|LengthAwarePaginator
    {
        $limit = $limit > 1 ? $limit : 4;
        $currentProperty = $this->findById($propertyId, ['categories']);

        $this->model = $this->originalModel;

        // @phpstan-ignore-next-line
        $this->model = $this->model
            ->where('re_properties.id', '<>', $propertyId)
            ->notExpired();

        if ($currentProperty && $currentProperty->categories->count()) {
            $categoryIds = $currentProperty->categories->pluck('id')->toArray();

            $this->model
                ->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('category_id', $categoryIds);
                })
                ->where('type', $currentProperty->type);
        }

        $params = array_merge([
            'condition' => RealEstateHelper::getPropertyDisplayQueryConditions(),
            'order_by' => [
                'created_at' => 'DESC',
            ],
            'take' => $limit,
            'with' => $with,
        ], $extra);

        return $this->advancedGet($params);
    }

    public function getProperties(array $filters = [], array $params = []): Collection|LengthAwarePaginator
    {
        $filters = array_merge([
            'keyword' => null,
            'type' => null,
            'bedroom' => null,
            'bathroom' => null,
            'floor' => null,
            'min_square' => null,
            'max_square' => null,
            'min_price' => null,
            'max_price' => null,
            'project' => null,
            'project_id' => null,
            'category_id' => null,
            'city_id' => null,
            'city' => null,
            'location' => null,
            'sort_by' => null,
        ], $filters);

        $orderBy = match ($filters['sort_by']) {
            'date_asc' => [
                're_properties.created_at' => 'ASC',
            ],
            'price_asc' => [
                're_properties.price' => 'ASC',
            ],
            'price_desc' => [
                're_properties.price' => 'DESC',
            ],
            'name_asc' => [
                're_properties.name' => 'ASC',
            ],
            'name_desc' => [
                're_properties.name' => 'DESC',
            ],
            default => [
                're_properties.created_at' => 'DESC',
            ],
        };

        $params = array_merge([
            'condition' => RealEstateHelper::getPropertyDisplayQueryConditions(),
            'order_by' => [
                're_properties.created_at' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => 10,
                'current_paged' => 1,
            ],
            'select' => [
                're_properties.*',
            ],
            'with' => [],
        ], $params);

        $params['order_by'] = $orderBy;

        // @phpstan-ignore-next-line
        $this->model = $this->originalModel->notExpired();

        if ($filters['keyword'] !== null) {
            $keyword = $filters['keyword'];

            $this->model = $this->model
                ->where(function (Builder $query) use ($keyword) {
                    return $query
                        ->where('re_properties.name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('re_properties.location', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('re_properties.description', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('re_properties.unique_id', 'LIKE', '%' . $keyword . '%');
                });
        }

        if ($filters['type'] !== null) {
            if ($filters['type'] == PropertyTypeEnum::SALE) {
                $this->model = $this->model->where('re_properties.type', $filters['type']);
            } else {
                $this->model = $this->model->where('re_properties.type', $filters['type']);
            }
        }

        if ($filters['bedroom']) {
            if ($filters['bedroom'] < 5) {
                $this->model = $this->model->where('re_properties.number_bedroom', $filters['bedroom']);
            } else {
                $this->model = $this->model->where('re_properties.number_bedroom', '>=', $filters['bedroom']);
            }
        }

        if ($filters['bathroom']) {
            if ($filters['bathroom'] < 5) {
                $this->model = $this->model->where('re_properties.number_bathroom', $filters['bathroom']);
            } else {
                $this->model = $this->model->where('re_properties.number_bathroom', '>=', $filters['bathroom']);
            }
        }

        if ($filters['floor']) {
            if ($filters['floor'] < 5) {
                $this->model = $this->model->where('re_properties.number_floor', $filters['floor']);
            } else {
                $this->model = $this->model->where('re_properties.number_floor', '>=', $filters['floor']);
            }
        }

        if ($filters['min_square'] !== null || $filters['max_square'] !== null) {
            $this->model = $this->model
                ->where(function (Builder $query) use ($filters) {
                    $minSquare = Arr::get($filters, 'min_square');
                    $maxSquare = Arr::get($filters, 'max_square');

                    if ($minSquare !== null) {
                        $query = $query->where('re_properties.square', '>=', $minSquare);
                    }

                    if ($maxSquare !== null) {
                        $query = $query->where('re_properties.square', '<=', $maxSquare);
                    }

                    return $query;
                });
        }

        if ($filters['min_price'] !== null || $filters['max_price'] !== null) {
            $this->model = $this->model
                ->where(function (Builder $query) use ($filters) {
                    $minPrice = Arr::get($filters, 'min_price');
                    $maxPrice = Arr::get($filters, 'max_price');

                    if ($minPrice !== null) {
                        $query = $query->where('re_properties.price', '>=', $minPrice);
                    }

                    if ($maxPrice !== null) {
                        $query = $query->where('re_properties.price', '<=', $maxPrice);
                    }

                    return $query;
                });
        }

        if ($filters['city'] !== null) {
            $this->model = $this->model->whereHas('city', function ($query) use ($filters) {
                $query->where('slug', $filters['city']);
            });
        }

        if ($filters['project'] !== null) {
            $this->model = $this->model->where(function ($query) use ($filters) {
                $query
                    ->where('re_properties.project_id', $filters['project'])
                    ->orWhereHas('project', function ($query) use ($filters) {
                        $query->where('re_projects.name', 'LIKE', '%' . $filters['project'] . '%');
                    });
            });
        }

        if ($filters['project_id'] !== null) {
            $this->model = $this->model->where('re_properties.project_id', $filters['project_id']);
        }

        if ($filters['category_id'] !== null) {
            $categoryIds = get_property_categories_related_ids($filters['category_id']);
            $this->model = $this->model
                ->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('category_id', $categoryIds);
                });
        }

        if ($filters['city_id']) {
            $this->model = $this->model->where('re_properties.city_id', $filters['city_id']);
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
                            ->orWhere('re_properties.location', 'LIKE', '%' . $locationSearch . '%');
                    });
            } else {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_properties.city_id')
                    ->join('states', 'states.id', '=', 'cities.state_id')
                    ->where(function ($query) use ($locationSearch) {
                        return $query
                            ->where('cities.name', 'LIKE', '%' . $locationSearch . '%')
                            ->orWhere('states.name', 'LIKE', '%' . $locationSearch . '%')
                            ->orWhere('re_properties.location', 'LIKE', '%' . $locationSearch . '%');
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
                                    $query->orwhere('re_properties.location', 'like', '%' . $location . '%');
                                }
                            });
                    });
            } else {
                $this->model = $this->model
                    ->join('cities', 'cities.id', '=', 're_properties.city_id')
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
                                    $query->orwhere('re_properties.location', 'like', '%' . $location . '%');
                                }
                            });
                    });
            }
        }

        return $this->advancedGet($params);
    }

    public function getProperty(int $propertyId, array $with = [], array $extra = []): ?Property
    {
        $params = array_merge([
            'condition' => [
                'id' => $propertyId,
                'moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            'with' => $with,
            'take' => 1,
        ], $extra);

        // @phpstan-ignore-next-line
        $this->model = $this->originalModel->notExpired();

        return $this->advancedGet($params);
    }

    public function getPropertiesByConditions(array $condition, int $limit = 4, array $with = []): Collection|LengthAwarePaginator
    {
        $limit = $limit > 1 ? $limit : 4;

        // @phpstan-ignore-next-line
        $this->model = $this->originalModel->notExpired();

        $params = [
            'condition' => $condition,
            'with' => $with,
            'take' => $limit,
            'order_by' => ['created_at' => 'DESC'],
        ];

        return $this->advancedGet($params);
    }
}
