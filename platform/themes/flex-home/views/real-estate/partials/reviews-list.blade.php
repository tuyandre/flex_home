<div class="block__content">
    @foreach($reviews as $review)
        <div class="block--review" v-for="item in data" :key="item.id">
            <div class="block__header">
                <div class="block__image"><img src="{{ RvMedia::getImageUrl($review->author->avatar_url, 'thumb') }}" alt="{{ $review->author->name }}"  width="60" /></div>
                <div class="block__info">
                    @include(Theme::getThemeNamespace('views.real-estate.partials.review-star'), ['avgStar' => $review->star])
                    <div class="my-2">
                        <span class="d-block lh-1">
                            <strong>{{ $review->author->name }}</strong>
                        </span>
                        <small class="text-secondary lh-1">{{ $review->created_at?->diffForHumans() }}</small>
                    </div>

                    <div class="block__content">
                        <p>{{ $review->content }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{ $reviews->onEachSide(1)->links() }}
