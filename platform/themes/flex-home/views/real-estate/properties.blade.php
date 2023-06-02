{!! Theme::partial('short-codes.properties-list', [
    'title' => __('Discover our properties'),
    'description' => theme_option('properties_description'),
    'properties' => $properties,
    'ajaxUrl' => $ajaxUrl ?? null,
    'actionUrl' => $actionUrl ?? null,
]) !!}
