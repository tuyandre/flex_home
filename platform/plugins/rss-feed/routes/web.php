<?php

use Botble\RssFeed\Http\Controllers\RssFeedController;
use Illuminate\Support\Facades\Route;

Route::group(['controller' => RssFeedController::class, 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        Route::get('feed/posts', 'getPostFeeds')->name('feeds.posts');
    });
});
