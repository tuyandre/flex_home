<?php

namespace Theme\FlexHome\Http\Controllers;

use Illuminate\Support\Facades\App;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\Theme\Http\Controllers\PublicController;
use Illuminate\Http\Request;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Theme\FlexHome\Http\Resources\AgentHTMLResource;
use Theme\FlexHome\Http\Resources\PostResource;
use Theme\FlexHome\Http\Resources\PropertyHTMLResource;
use Theme\FlexHome\Http\Resources\PropertyResource;

class FlexHomeController extends PublicController
{
    public function ajaxGetProperties(Request $request, BaseHttpResponse $response)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $request->validate([
            'limit' => 'nullable|numeric|min:1',
        ]);

        $properties = [];
        $with = RealEstateHelper::getPropertyRelationsQuery();

        $limit = $request->integer('limit') ?: 8;

        switch ($request->input('type')) {
            case 'related':
                $properties = app(PropertyInterface::class)
                    ->getRelatedProperties(
                        (int)$request->input('property_id'),
                        $limit,
                        $with
                    );

                break;
            case 'featured':
                $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                    [
                        're_properties.is_featured' => true,
                    ] + RealEstateHelper::getPropertyDisplayQueryConditions(),
                    $limit,
                    $with
                );

                break;
            case 'rent':
                $conditions = [
                        're_properties.type' => PropertyTypeEnum::RENT,
                    ] + RealEstateHelper::getPropertyDisplayQueryConditions();

                if ($request->input('featured', '1') == '1') {
                    $conditions['re_properties.is_featured'] = true;
                }

                $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                    $conditions,
                    $limit,
                    $with
                );

                break;
            case 'sale':
                $conditions = [
                        're_properties.type' => PropertyTypeEnum::SALE,
                    ] + RealEstateHelper::getPropertyDisplayQueryConditions();

                if ($request->input('featured', '1') == '1') {
                    $conditions['re_properties.is_featured'] = true;
                }

                $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                    $conditions,
                    $limit,
                    $with
                );

                break;
            case 'project-properties-for-sell':
                $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                    [
                        're_properties.project_id' => $request->input('project_id'),
                        're_properties.type' => PropertyTypeEnum::SALE,
                    ] + RealEstateHelper::getPropertyDisplayQueryConditions(),
                    $limit,
                    $with
                );

                break;
            case 'project-properties-for-rent':
                $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                    [
                        're_properties.project_id' => $request->input('project_id'),
                        're_properties.type' => PropertyTypeEnum::RENT,
                    ] + RealEstateHelper::getPropertyDisplayQueryConditions(),
                    $limit,
                    $with
                );

                break;
            case 'recently-viewed-properties':
                $cookieName = App::getLocale() . '_recently_viewed_properties';
                $jsonRecentViewProduct = null;

                if (isset($_COOKIE[$cookieName])) {
                    $jsonRecentViewProduct = $_COOKIE[$cookieName];
                }

                if (! empty($jsonRecentViewProduct)) {
                    $ids = collect(json_decode($jsonRecentViewProduct, true))->flatten()->all();

                    $properties = app(PropertyInterface::class)->getPropertiesByConditions(
                        [
                            ['re_properties.id', 'IN', $ids],
                        ] + RealEstateHelper::getPropertyDisplayQueryConditions(),
                        $limit,
                        $with
                    );

                    $reversed = array_reverse($ids);

                    $properties = $properties->sortBy(function ($model) use ($reversed) {
                        return array_search($model->id, $reversed);
                    });
                }

                break;
        }

        return $response
            ->setData(PropertyHTMLResource::collection($properties))
            ->toApiResponse();
    }

    public function ajaxGetPropertiesForMap(Request $request, BaseHttpResponse $response)
    {
        $filters = [
            'keyword' => $request->input('k'),
            'type' => $request->input('type'),
            'bedroom' => $request->input('bedroom'),
            'bathroom' => $request->input('bathroom'),
            'floor' => $request->input('floor'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'min_square' => $request->input('min_square'),
            'max_square' => $request->input('max_square'),
            'project' => $request->input('project'),
            'category_id' => $request->input('category_id'),
            'city' => $request->input('city'),
            'city_id' => $request->input('city_id'),
            'location' => $request->input('location'),
        ];

        $params = [
            'with' => RealEstateHelper::getPropertyRelationsQuery(),
            'paginate' => [
                'per_page' => 20,
                'current_paged' => (int)$request->input('page', 1),
            ],
        ];

        $properties = app(PropertyInterface::class)->getProperties($filters, $params);

        return $response
            ->setData(PropertyResource::collection($properties))
            ->toApiResponse();
    }

    public function ajaxGetPosts(Request $request, BaseHttpResponse $response)
    {
        if (! $request->ajax() || ! $request->wantsJson()) {
            abort(404);
        }

        $request->validate([
            'limit' => 'nullable|numeric|min:1',
        ]);

        $posts = app(PostInterface::class)->getFeatured($request->integer('limit') ?: 4, ['slugable', 'categories', 'categories.slugable']);

        return $response
            ->setData(PostResource::collection($posts))
            ->toApiResponse();
    }

    public function ajaxGetCities(Request $request, CityInterface $cityRepository, BaseHttpResponse $response)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $keyword = $request->input('k');

        $cities = $cityRepository->filters($keyword);

        return $response->setData(Theme::partial('city-suggestion', ['items' => $cities]));
    }

    public function getWishlist(Request $request, PropertyInterface $propertyRepository)
    {
        if (! RealEstateHelper::isEnabledWishlist()) {
            abort(404);
        }

        SeoHelper::setTitle(__('Wishlist'))
            ->setDescription(__('Wishlist'));

        $cookieName = 'wishlist';
        $jsonWishlist = null;
        if (isset($_COOKIE[$cookieName])) {
            $jsonWishlist = $_COOKIE[$cookieName];
        }

        $properties = collect();

        if (! empty($jsonWishlist)) {
            $arrValue = collect(json_decode($jsonWishlist, true))->flatten()->all();
            $properties = $propertyRepository->advancedGet([
                'condition' => [
                    ['re_properties.id', 'IN', $arrValue],
                ],
                'order_by' => [
                    're_properties.id' => 'DESC',
                ],
                'paginate' => [
                    'per_page' => (int)theme_option('number_of_properties_per_page', 12),
                    'current_paged' => (int)$request->input('page', 1),
                ],
                'with' => RealEstateHelper::getPropertyRelationsQuery(),
            ]);
        }

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Wishlist'));

        return Theme::scope('real-estate.wishlist', compact('properties'))->render();
    }

    public function ajaxGetFeaturedAgents(
        Request $request,
        BaseHttpResponse $response,
        AccountInterface $accountRepository
    ) {
        if (! $request->ajax()) {
            abort(404);
        }

        $accounts = $accountRepository->advancedGet([
            'condition' => [
                're_accounts.is_featured' => true,
            ],
            'order_by' => [
                're_accounts.id' => 'DESC',
            ],
            'take' => $request->integer('limit') ?: 4,
            'withCount' => [
                'properties' => function ($query) {
                    return RepositoryHelper::applyBeforeExecuteQuery($query, $query->getModel());
                },
            ],
        ]);

        return $response
            ->setData(AgentHTMLResource::collection($accounts))
            ->toApiResponse();
    }

    public function ajaxGetProjectsFilter(Request $request, BaseHttpResponse $response, ProjectInterface $projectRepository)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $request->validate([
            'project' => 'nullable|string',
        ]);

        $keyword = $request->input('project');

        $projects = $projectRepository->advancedGet([
            'condition' => [
                ['name', 'LIKE', '%' . $keyword . '%'],
            ],
            'select' => ['id', 'name'],
            'take' => 10,
            'order_by' => ['name' => 'ASC'],
        ]);

        return $response->setData(Theme::partial('real-estate.filters.projects-suggestion', compact('projects')));
    }
}
