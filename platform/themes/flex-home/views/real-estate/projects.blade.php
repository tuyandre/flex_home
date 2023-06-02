{!! Theme::partial('short-codes.projects-list', [
    'title' => __('Discover our projects'),
    'description' => theme_option('home_project_description'),
    'projects' => $projects,
    'ajaxUrl' => $ajaxUrl ?? null,
    'actionUrl' => $actionUrl ?? null,
]) !!}
