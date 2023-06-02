<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\Shortcode\Compilers\Shortcode;
use Botble\Theme\Supports\ThemeSupport;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\App;

app()->booted(function () {
    ThemeSupport::registerGoogleMapsShortcode();
    ThemeSupport::registerYoutubeShortcode();

    if (is_plugin_active('real-estate')) {
        add_shortcode('featured-projects', __('Featured projects'), __('Featured projects'), function ($shortcode) {
            $projects = collect();

            if (is_plugin_active('real-estate')) {
                $projects = app(ProjectInterface::class)->advancedGet([
                    'condition' => [
                            're_projects.is_featured' => true,
                        ] + RealEstateHelper::getProjectDisplayQueryConditions(),
                    'take' => (int) $shortcode->limit ?: (int)theme_option('number_of_featured_projects', 4),
                    'with' => RealEstateHelper::getProjectRelationsQuery(),
                    'order_by' => ['re_projects.created_at' => 'DESC'],
                ]);
            }

            if (! $projects->count()) {
                return null;
            }

            return Theme::partial('short-codes.featured-projects', [
                'title' => $shortcode->title,
                'subtitle' => $shortcode->subtitle,
                'projects' => $projects,
            ]);
        });

        shortcode()->setAdminConfig('featured-projects', function ($attributes, $content) {
            return Theme::partial('short-codes.featured-projects-admin-config', compact('attributes', 'content'));
        });

        add_shortcode('projects-by-locations', __('Projects by locations'), __('Projects by locations'), function ($shortcode) {
            $cities = collect();

            if (is_plugin_active('location')) {
                $cities = app(CityInterface::class)->advancedGet([
                    'condition' => [
                        'cities.is_featured' => true,
                        'cities.status' => BaseStatusEnum::PUBLISHED,
                    ],
                    'take' => (int) $shortcode->limit ?: 10,
                    'select' => ['cities.id', 'cities.name', 'cities.image', 'cities.slug'],
                    'order_by' => ['order' => 'ASC', 'name' => 'ASC'],
                ]);
            }

            if (! $cities->count()) {
                return null;
            }

            return Theme::partial('short-codes.projects-by-locations', [
                'title' => $shortcode->title,
                'subtitle' => $shortcode->subtitle,
                'limit' => (int) $shortcode->limit ?: 10,
                'cities' => $cities,
            ]);
        });

        shortcode()->setAdminConfig('projects-by-locations', function ($attributes, $content) {
            return Theme::partial('short-codes.projects-by-locations-admin-config', compact('attributes', 'content'));
        });

        add_shortcode(
            'properties-by-locations',
            __('Properties by locations'),
            __('Properties by locations'),
            function ($shortcode) {
                $cities = collect();

                if (is_plugin_active('location')) {
                    $cities = app(CityInterface::class)->advancedGet([
                        'condition' => [
                            'cities.is_featured' => true,
                            'cities.status' => BaseStatusEnum::PUBLISHED,
                        ],
                        'take' => (int) $shortcode->limit ?: 10,
                        'select' => ['cities.id', 'cities.name', 'cities.image', 'cities.slug'],
                        'order_by' => ['order' => 'ASC', 'name' => 'ASC'],
                    ]);
                }

                if (! $cities->count()) {
                    return null;
                }

                return Theme::partial('short-codes.properties-by-locations', [
                    'title' => $shortcode->title,
                    'subtitle' => $shortcode->subtitle,
                    'cities' => $cities,
                ]);
            }
        );

        shortcode()->setAdminConfig('properties-by-locations', function ($attributes, $content) {
            return Theme::partial('short-codes.properties-by-locations-admin-config', compact('attributes', 'content'));
        });

        add_shortcode('featured-properties', __('Featured properties'), __('Featured properties'), function ($shortcode) {
            return Theme::partial('short-codes.featured-properties', [
                'title' => $shortcode->title,
                'subtitle' => $shortcode->subtitle,
                'limit' => (int)$shortcode->limit,
            ]);
        });

        shortcode()->setAdminConfig('featured-properties', function ($attributes, $content) {
            return Theme::partial('short-codes.featured-properties-admin-config', compact('attributes', 'content'));
        });

        add_shortcode('properties-for-sale', __('Properties for sale'), __('Properties for sale'), function ($shortcode) {
            return Theme::partial('short-codes.properties-for-sale', [
                'title' => $shortcode->title,
                'subtitle' => $shortcode->subtitle,
                'limit' => (int)$shortcode->limit,
            ]);
        });

        shortcode()->setAdminConfig('properties-for-sale', function ($attributes, $content) {
            return Theme::partial('short-codes.properties-for-sale-admin-config', compact('attributes', 'content'));
        });

        add_shortcode('properties-for-rent', __('Properties for rent'), __('Properties for rent'), function ($shortcode) {
            return Theme::partial('short-codes.properties-for-rent', [
                'title' => $shortcode->title,
                'subtitle' => $shortcode->subtitle,
                'limit' => (int)$shortcode->limit,
            ]);
        });

        shortcode()->setAdminConfig('properties-for-rent', function ($attributes, $content) {
            return Theme::partial('short-codes.properties-for-rent-admin-config', compact('attributes', 'content'));
        });

        add_shortcode(
            'recently-viewed-properties',
            __('Recent Viewed Properties'),
            __('Recently Viewed Properties'),
            function ($shortcode) {
                $cookieName = App::getLocale() . '_recently_viewed_properties';

                $jsonRecentlyViewedProperties = null;
                if (isset($_COOKIE[$cookieName])) {
                    $jsonRecentlyViewedProperties = $_COOKIE[$cookieName];
                }
                $arrValue = collect(json_decode((string)$jsonRecentlyViewedProperties, true))->flatten()->all();

                if (count($arrValue) > 0) {
                    return Theme::partial('short-codes.recently-viewed-properties', [
                        'title' => $shortcode->title,
                        'description' => $shortcode->description,
                        'subtitle' => $shortcode->subtitle,
                    ]);
                }

                return null;
            }
        );

        shortcode()->setAdminConfig('recently-viewed-properties', function ($attributes, $content) {
            return Theme::partial('short-codes.recently-viewed-properties-admin-config', compact('attributes', 'content'));
        });

        add_shortcode(
            'featured-agents',
            __('Featured Agents'),
            __('Featured Agents'),
            function ($shortcode) {
                return Theme::partial('short-codes.featured-agents', [
                    'title' => $shortcode->title,
                    'description' => $shortcode->description,
                    'subtitle' => $shortcode->subtitle,
                    'limit' => (int)$shortcode->limit,
                ]);
            }
        );

        shortcode()->setAdminConfig('featured-agents', function ($attributes, $content) {
            return Theme::partial('short-codes.featured-agents-admin-config', compact('attributes', 'content'));
        });

        add_shortcode(
            'search-box',
            __('Search box'),
            __('Search box'),
            function ($shortcode) {
                return Theme::partial('short-codes.search-box', compact('shortcode'));
            }
        );

        shortcode()->setAdminConfig('search-box', function ($attributes, $content) {
            return Theme::partial('short-codes.search-box-admin-config', compact('attributes', 'content'));
        });

        add_shortcode('properties-list', __('Properties List'), __('Properties List'), function (Shortcode $shortcode) {
            $properties = RealEstateHelper::getPropertiesFilter((int)$shortcode->per_page ?: 12);

            return Theme::partial('short-codes.properties-list', [
                'title' => $shortcode->title,
                'description' => $shortcode->description,
                'properties' => $properties,
            ]);
        });

        shortcode()->setAdminConfig('properties-list', function (array $attributes) {
            return Theme::partial('short-codes.properties-list-admin-config', compact('attributes'));
        });

        add_shortcode('projects-list', __('Projects List'), __('Projects List'), function (Shortcode $shortcode) {
            Theme::asset()->container('footer')->usePath()->add('project-filter', 'js/project-filter.js');

            $projects = RealEstateHelper::getProjectsFilter((int)$shortcode->per_page ?: 12);

            return Theme::partial('short-codes.projects-list', [
                'title' => $shortcode->title,
                'description' => $shortcode->description,
                'projects' => $projects,
            ]);
        });

        shortcode()->setAdminConfig('projects-list', function (array $attributes) {
            return Theme::partial('short-codes.properties-list-admin-config', compact('attributes'));
        });
    }

    if (is_plugin_active('blog')) {
        add_shortcode('latest-news', __('Latest news'), __('Latest news'), function ($shortcode) {
            return Theme::partial('short-codes.latest-news', [
                'title' => $shortcode->title,
                'subtitle' => $shortcode->subtitle,
                'limit' => (int)$shortcode->limit ?: 4,
            ]);
        });

        shortcode()->setAdminConfig('latest-news', function ($attributes, $content) {
            return Theme::partial('short-codes.latest-news-admin-config', compact('attributes', 'content'));
        });
    }

    if (is_plugin_active('contact')) {
        add_filter(CONTACT_FORM_TEMPLATE_VIEW, function () {
            return Theme::getThemeNamespace() . '::partials.short-codes.contact-form';
        }, 120);
    }
});
