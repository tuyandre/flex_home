<?php

use Theme\FlexHome\Http\Controllers\FlexHomeController;

Route::group(['controller' => FlexHomeController::class, 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::get('wishlist', 'getWishlist')->name('public.wishlist');
        Route::get('ajax/cities', 'ajaxGetCities')->name('public.ajax.cities');
        Route::get('ajax/properties', 'ajaxGetProperties')->name('public.ajax.properties');
        Route::get('ajax/posts', 'ajaxGetPosts')->name('public.ajax.posts');
        Route::get('ajax/properties/map', 'ajaxGetPropertiesForMap')->name('public.ajax.properties.map');
        Route::get('ajax/agents/featured', 'ajaxGetFeaturedAgents')->name('public.ajax.featured-agents');
        Route::get('ajax/projects-filter', 'ajaxGetProjectsFilter')->name('public.ajax.projects-filter');
    });
});

Theme::routes();
