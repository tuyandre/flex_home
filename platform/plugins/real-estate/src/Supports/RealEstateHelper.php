<?php

namespace Botble\RealEstate\Supports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Location\Models\City;
use Botble\Page\Models\Page;
use Botble\Page\Repositories\Interfaces\PageInterface;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\ReviewStatusEnum;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Botble\Slug\Facades\SlugHelper;

class RealEstateHelper
{
    public function isRegisterEnabled(): bool
    {
        return setting('real_estate_enabled_register', true) && $this->isLoginEnabled();
    }

    public function isLoginEnabled(): bool
    {
        return setting('real_estate_enabled_login', true);
    }

    public function propertyExpiredDays(): int
    {
        $days = (int)setting('property_expired_after_days');

        if ($days > 0) {
            return $days;
        }

        return config('plugins.real-estate.real-estate.property_expired_after_x_days');
    }

    public function getPropertyRelationsQuery(): array
    {
        return [
            'slugable:id,key,prefix,reference_id',
            'state:id,name',
            'city:id,name',
            'currency:id,is_default,exchange_rate,symbol,title,is_prefix_symbol',
            'categories' => function (Builder $query) {
                return $query->where('status', BaseStatusEnum::PUBLISHED)
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('is_default', 'DESC')
                    ->orderBy('order', 'ASC')
                    ->select('re_categories.id', 're_categories.name');
            },
        ];
    }

    public function getProjectRelationsQuery(): array
    {
        return [
            'slugable:id,key,prefix,reference_id',
            'state:id,name',
            'city:id,name',
            'categories' => function (Builder $query) {
                return $query->where('status', BaseStatusEnum::PUBLISHED)
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('is_default', 'DESC')
                    ->orderBy('order', 'ASC')
                    ->select('re_categories.id', 're_categories.name');
            },
        ];
    }

    public function isEnabledCreditsSystem(): bool
    {
        return setting('real_estate_enable_credits_system', 1) == 1;
    }

    public function getThousandSeparatorForInputMask(): string
    {
        return ',';
    }

    public function getDecimalSeparatorForInputMask(): string
    {
        return '.';
    }

    public function getPropertyDisplayQueryConditions(): array
    {
        $conditions = [
            're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
        ];

        foreach ($this->exceptedPropertyStatuses() as $status) {
            $conditions[] = ['re_properties.status', '!=', $status];
        }

        return $conditions;
    }

    public function getProjectDisplayQueryConditions(): array
    {
        $conditions = [];

        foreach ($this->exceptedProjectsStatuses() as $status) {
            $conditions[] = ['re_projects.status', '!=', $status];
        }

        return $conditions;
    }

    public function exceptedPropertyStatuses(): array
    {
        $statuses = setting('real_estate_hide_properties_in_statuses');

        if ($statuses) {
            return json_decode($statuses, true);
        }

        return [PropertyStatusEnum::NOT_AVAILABLE];
    }

    public function exceptedProjectsStatuses(): array
    {
        $statuses = setting('real_estate_hide_projects_in_statuses');

        if ($statuses) {
            return json_decode($statuses, true);
        }

        return [ProjectStatusEnum::NOT_AVAILABLE];
    }

    public function isEnabledWishlist(): bool
    {
        return (int)setting('real_estate_enable_wishlist', '1') == 1;
    }

    protected function getPage($pageId): ?Page
    {
        if (! $pageId) {
            return null;
        }

        return app(PageInterface::class)->getFirstBy(
            ['id' => $pageId, 'status' => BaseStatusEnum::PUBLISHED],
            ['id', 'name'],
            ['slugable']
        );
    }

    public function getPropertiesListPageUrl(): ?string
    {
        $pageId = theme_option('properties_list_page_id');

        if (! $pageId) {
            return route('public.properties');
        }

        $page = $this->getPage($pageId);

        return $page ? $page->url : route('public.properties');
    }

    public function getProjectsListPageUrl(): ?string
    {
        $pageId = theme_option('projects_list_page_id');

        if (! $pageId) {
            return route('public.projects');
        }

        $page = $this->getPage($pageId);

        return $page ? $page->url : route('public.projects');
    }

    public function getPropertiesFilter(int|null $perPage = 12, array $extra = []): LengthAwarePaginator|Collection
    {
        $request = request();

        $perPage = $request->input('per_page') ?: ($perPage ?? 12);

        $filters = $request->validate([
            'keyword' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'city_id' => 'nullable|numeric',
            'type' => 'nullable|string',
            'bedroom' => 'nullable|numeric',
            'bathroom' => 'nullable|numeric',
            'floor' => 'nullable|numeric',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'min_square' => 'nullable|numeric',
            'max_square' => 'nullable|numeric',
            'project' => 'nullable|string',
            'project_id' => 'nullable|string',
            'category_id' => 'nullable|numeric',
            'city' => 'nullable|string',
            'sort_by' => 'nullable|string',
            'locations' => 'nullable|array',
            'category_ids' => 'nullable|array',
        ]);

        $filters['keyword'] = $request->input('k');

        $params = array_merge([
            'paginate' => [
                'per_page' => (int)$perPage,
                'current_paged' => (int)$request->input('page', 1),
            ],
            'order_by' => ['re_properties.created_at' => 'DESC'],
            'with' => RealEstateHelper::getPropertyRelationsQuery(),
        ], $extra);

        return app(PropertyInterface::class)->getProperties($filters, $params);
    }

    public function getProjectsFilter(int|null $perPage = 12, array $extra = []): LengthAwarePaginator|Collection
    {
        $request = request();

        $perPage = $request->input('per_page') ?: ($perPage ?? 12);

        $filters = $request->validate([
            'keyword' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'city_id' => 'nullable|numeric',
            'city' => 'nullable|string',
            'category_id' => 'nullable|numeric',
            'sort_by' => 'nullable|string',
            'blocks' => 'nullable|numeric',
            'min_floor' => 'nullable|numeric',
            'max_floor' => 'nullable|numeric',
            'min_flat' => 'nullable|numeric',
            'max_flat' => 'nullable|numeric',
            'locations' => 'nullable|array',
            'category_ids' => 'nullable|array',
        ]);

        $filters['keyword'] = $request->input('k');

        $params = array_merge([
            'paginate' => [
                'per_page' => (int)$perPage,
                'current_paged' => (int)$request->input('page', 1),
            ],
            'order_by' => ['re_projects.created_at' => 'DESC'],
            'with' => self::getProjectRelationsQuery(),
        ], $extra);

        return app(ProjectInterface::class)->getProjects($filters, $params);
    }

    public function getPropertiesPerPageList(): array
    {
        return apply_filters(PROPERTIES_PER_PAGE_LIST, [
            9 => 9,
            12 => 12,
            15 => 15,
            30 => 30,
            45 => 45,
            60 => 60,
            120 => 120,
        ]);
    }

    public function getProjectsPerPageList(): array
    {
        return apply_filters(PROJECTS_PER_PAGE_LIST, [
            9 => 9,
            12 => 12,
            15 => 15,
            30 => 30,
            45 => 45,
            60 => 60,
            120 => 120,
        ]);
    }

    public function getSortByList(): array
    {
        return [
            'date_asc' => __('Oldest'),
            'date_desc' => __('Newest'),
            'price_asc' => __('Price (low to high)'),
            'price_desc' => __('Price (high to low)'),
            'name_asc' => __('Name (A-Z)'),
            'name_desc' => __('Name (Z-A)'),
        ];
    }

    public function getReviewExtraData(): array
    {
        if (! $this->isEnabledReview()) {
            return [];
        }

        return [
            'withCount' => [
                'reviews' => function ($query) {
                    $query->where('status', ReviewStatusEnum::APPROVED);
                },
            ],
            'withAvg' => ['reviews', 'star'],
        ];
    }

    public function isEnabledReview(): bool
    {
        return (bool)setting('real_estate_enable_review_feature', true);
    }

    public function createCitySlug(?string $slug, City $city): int|string
    {
        if (! $slug) {
            $slug = $city->name;
        }

        $language = ! SlugHelper::turnOffAutomaticUrlTranslationIntoLatin() ? 'en' : false;

        $slug = Str::slug($slug, '-', $language);

        $index = 1;
        $baseSlug = $slug;

        $cityModel = $city->getModel();

        while ($cityModel->where('slug', $slug)->where('id', '!=', $city->id)->exists()) {
            if ($slug == $baseSlug) {
                $slug = $baseSlug . '-' . Str::slug($city->state->name, '-', $language);
            } else {
                $slug = $baseSlug . '-' . $index++;
            }
        }

        if (empty($slug)) {
            $slug = time();
        }

        return $slug;
    }

    public function getMapCenterLatLng(): array
    {
        $center = theme_option('latitude_longitude_center_on_properties_page', '');
        $latLng = [];
        if ($center) {
            $center = explode(',', $center);
            if (count($center) == 2) {
                $latLng = [trim($center[0]), trim($center[1])];
            }
        }

        if (! $latLng) {
            $latLng = [43.615134, -76.393186];
        }

        return $latLng;
    }

    public function isEnabledCustomFields(): bool
    {
        return (bool)setting('real_estate_enabled_custom_fields_feature', true);
    }

    public function getSquareUnits(): array
    {
        return [
            'm²' => __('m²'),
            'ft2' => __('ft2'),
            'yd2' => __('yd2'),
        ];
    }

    public function maxFilesizeUploadByAgent(): int
    {
        $size = setting('real_estate_max_filesize_upload_by_agent');

        if (! $size) {
            $size = setting('max_upload_filesize') ?: 10;
        }

        return (int)$size;
    }

    public function maxPropertyImagesUploadByAgent(): int
    {
        return (int)setting('real_estate_max_property_images_upload_by_agent', 20);
    }
}
