@if (is_plugin_active('blog'))
    <div class="box_shadow" style="margin-bottom: 0;padding-bottom: 80px;">
        <div class="container-fluid w90">
            <div class="discover">
                <div class="row">
                    <div class="col-12">
                        <h2>{!! BaseHelper::clean($title) !!}</h2>
                        @if ($subtitle)
                            <p>{!! BaseHelper::clean($subtitle) !!}</p>
                        @endif
                        <br>
                        <div class="blog-container">
                            <news-component url="{{ route('public.ajax.posts') }}?limit={{ $limit }}"></news-component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
