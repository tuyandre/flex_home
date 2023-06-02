@php
    Theme::asset()->usePath()->add('jquery-bar-rating-css', 'libraries/jquery-bar-rating/css-stars.css');
    Theme::asset()->container('footer')->usePath()->add('jquery-bar-rating-js', 'libraries/jquery-bar-rating/jquery.barrating.min.js');
    Theme::asset()->container('footer')->usePath()->add('review-js', 'js/review.js');
@endphp

@if(RealEstateHelper::isEnabledReview())
    @php($canReview = auth('account')->check() && auth('account')->user()->canReview($model))
    <div>
        <h4>{{ __('Write a review') }}</h4>
        <form action="{{ route('public.ajax.review.store', $model->slug) }}" method="post" class="space-y-3 review-form">
            @csrf
            <input type="hidden" name="reviewable_type" value="{{ get_class($model) }}">
            <div class="form-group">
                <select name="star" id="select-star">
                    @foreach(range(1, 5) as $i)
                        <option value="{{ $i }}" @selected(old('score', 5) === $i)>{{ $i }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <textarea name="content" id="content" class="form-control" placeholder="{{ __('Enter your message') }}" @disabled(! $canReview)>{{ old('content') }}</textarea>
            </div>
            @guest('account')
                <p class="text-danger">{{ __('Please log in to write review!') }}</p>
            @endguest
            <button @class(['btn btn-primary']) @disabled(! $canReview)>
                {{ __('Submit review') }}
            </button>
        </form>
    </div>
    <br>
    <hr>
    <br>
    <div>
        @if($model->reviews_count)
            <div class="row">
                <div class="col-md-6">
                    <h5><span class="reviews-count">{{ __(':count Review(s)', ['count' => $model->reviews_count]) }}</span></h5>
                </div>
                <div class="col-md-6 text-right">
                    @include(Theme::getThemeNamespace('views.real-estate.partials.review-star'), ['avgStar' => $model->reviews_avg_star, 'count' => $model->reviews_count, 'style' => 2])
                </div>
            </div>
        @endif
        <div @class(['reviews-list space-y-7', 'mt-10' => $model->reviews_count]) data-url="{{ route('public.ajax.review.index', $model->slug) }}?reviewable_type={{ get_class($model) }}"></div>
    </div>
@endif
