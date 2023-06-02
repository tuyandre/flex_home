<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Enums\ReviewStatusEnum;
use Botble\RealEstate\Http\Requests\ReviewRequest;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Botble\RealEstate\Facades\RealEstateHelper;

class ReviewController
{
    public function __construct(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        if (! RealEstateHelper::isEnabledReview()) {
            abort(404);
        }
    }

    public function index(string $key, Request $request, BaseHttpResponse $response)
    {
        $request->validate([
            'reviewable_type' => Rule::in([Property::class, Project::class]),
        ]);

        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix($request->query('reviewable_type')));

        if (! $slug) {
            abort(404);
        }

        $reviewable = $slug->reference;

        if (! $reviewable) {
            abort(404);
        }

        $reviews = $reviewable
            ->reviews()
            ->where('status', ReviewStatusEnum::APPROVED)
            ->with('author')
            ->latest()
            ->paginate(setting('real_estate_reviews_per_page'));

        return $response->setData(
            view(Theme::getThemeNamespace('views.real-estate.partials.reviews-list'), [
                'reviews' => $reviews,
            ])->render()
        );
    }
    public function store(string $key, ReviewRequest $request, BaseHttpResponse $response)
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix($request->input('reviewable_type')));

        if (! $slug) {
            abort(404);
        }

        $reviewable = $slug->reference;

        if (! $reviewable) {
            abort(404);
        }

        if (! auth('account')->check() && auth('account')->user()->canReview($reviewable)) {
            return $response
                ->setCode(422)
                ->setMessage(__('You have already submitted a review.'));
        }

        $review = $reviewable->reviews()->create(
            array_merge($request->validated(), [
                'account_id' => auth('account')->id(),
            ])
        );

        event(new CreatedContentEvent(REVIEW_MODULE_SCREEN_NAME, $request, $review));

        $viewsCount = $reviewable->reviews->count();

        return $response->setData([
            'count' => $viewsCount,
            'message' => __('Your review has been submitted!'),
        ]);
    }
}
