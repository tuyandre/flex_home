<div class="container-fluid w90">
    <div class="padtop70">
        <div class="areahome">
            <div class="row">
                <div class="col-12">
                    <h2>{!! BaseHelper::clean($title) !!}</h2>
                    @if ($subtitle)
                        <p>{!! BaseHelper::clean($subtitle) !!}</p>
                    @endif
                </div>
            </div>
            <div style="position:relative;">
                <div class="owl-carousel" id="cityslide">
                    @foreach($cities as $city)
                        <div class="item itemarea">
                            <a href="{{ route('public.properties-by-city', $city->slug) }}">
                                <img src="{{ RvMedia::getImageUrl($city->image, 'small', false, RvMedia::getDefaultImage()) }}" alt="{{ $city->name }}">
                                <h4>{{ $city->name }}</h4>
                            </a>
                        </div>
                    @endforeach
                </div>
                <i class="am-next"><img src="{{ Theme::asset()->url('images/aright.png') }}" alt="pre"></i>
                <i class="am-prev"><img src="{{ Theme::asset()->url('images/aleft.png') }}" alt="next"></i>
            </div>
        </div>
    </div>
</div>
