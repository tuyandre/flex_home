<?php

namespace Botble\RealEstate\Facades;

use Botble\RealEstate\Supports\RealEstateHelper as RealEstateHelperSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isRegisterEnabled()
 * @method static bool isLoginEnabled()
 * @method static int propertyExpiredDays()
 * @method static array getPropertyRelationsQuery()
 * @method static array getProjectRelationsQuery()
 * @method static bool isEnabledCreditsSystem()
 * @method static string getThousandSeparatorForInputMask()
 * @method static string getDecimalSeparatorForInputMask()
 * @method static array getPropertyDisplayQueryConditions()
 * @method static array getProjectDisplayQueryConditions()
 * @method static array exceptedPropertyStatuses()
 * @method static array exceptedProjectsStatuses()
 * @method static bool isEnabledWishlist()
 * @method static string|null getPropertiesListPageUrl()
 * @method static string|null getProjectsListPageUrl()
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection getPropertiesFilter(int|null $perPage = 12, array $extra = [])
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection getProjectsFilter(int|null $perPage = 12, array $extra = [])
 * @method static array getPropertiesPerPageList()
 * @method static array getProjectsPerPageList()
 * @method static array getSortByList()
 * @method static array getReviewExtraData()
 * @method static bool isEnabledReview()
 * @method static string|int createCitySlug(string|null $slug, \Botble\Location\Models\City $city)
 * @method static array getMapCenterLatLng()
 * @method static bool isEnabledCustomFields()
 * @method static array getSquareUnits()
 * @method static int maxFilesizeUploadByAgent()
 * @method static int maxPropertyImagesUploadByAgent()
 *
 * @see \Botble\RealEstate\Supports\RealEstateHelper
 */
class RealEstateHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RealEstateHelperSupport::class;
    }
}
