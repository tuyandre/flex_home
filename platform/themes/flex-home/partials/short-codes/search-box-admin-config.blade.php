<div class="form-group mb-3">
    <label class="control-label">{{ __('Title') }}</label>
    <input name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control">
</div>

<div class="form-group mb-3">
    <label class="control-label">{{ __('Background Image') }}</label>
    {!! Form::mediaImage('background_image', Arr::get($attributes, 'background_image')) !!}
</div>

<div class="form-group mb-3">
    <label class="control-label">{{ __('Enable search projects on homepage search box?') }}</label>
    {!! Form::customSelect('enable_search_projects_on_homepage_search', [
                            'yes' => trans('core/base::base.yes'),
                            'no' => trans('core/base::base.no'),
                        ], Arr::get($attributes, 'enable_search_projects_on_homepage_search', 'yes')) !!}
</div>

<div class="form-group mb-3">
    <label class="control-label">{{ __('Default search type on homepage search box?') }}</label>
    {!! Form::customSelect('default_home_search_type', [
                            'project' => __('Projects'),
                            'sale' => __('Properties for sale'),
                            'rent' => __('Properties for rent'),
                        ], Arr::get($attributes, 'default_home_search_type', 'project')) !!}
</div>
