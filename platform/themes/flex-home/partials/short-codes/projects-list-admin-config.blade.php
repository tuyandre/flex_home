<div class="form-group mb-3">
    <label class="control-label">{{ __('Title') }}</label>
    <input name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control">
</div>

<div class="form-group mb-3">
    <label class="control-label">{{ __('Description') }}</label>
    <textarea name="description" class="form-control" rows="3">{{ Arr::get($attributes, 'description') }}</textarea>
</div>

<div class="form-group mb-3">
    <label class="control-label">{{ __('Number of projects per page') }}</label>
    {!! Form::customSelect('per_page', RealEstateHelper::getProjectsPerPageList(), Arr::get($attributes, 'per_page', 12)) !!}
</div>
