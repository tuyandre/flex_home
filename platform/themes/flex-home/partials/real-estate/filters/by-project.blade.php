<div class="form-group project-filter-input" style="position: relative;">
    <input type="hidden" name="project_id">
    <label for="filter-by-project" class="control-label">{{ __('Project') }}</label>
    <div style="position: relative;">
        <div class="input-has-icon">
            <input class="form-control" id="filter-by-project"
                   data-url="{{ route('public.ajax.projects-filter') }}" value="{{ BaseHelper::stringify(request()->query('project')) }}" placeholder="{{ __('Project') }}"
                   autocomplete="off">
        </div>
        <div class="spinner-icon">
            <i class="fas fa-spin fa-spinner"></i>
        </div>
        <div class="suggestion">

        </div>
    </div>
</div>
