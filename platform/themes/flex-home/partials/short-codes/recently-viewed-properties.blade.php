<div class="container-fluid w90">
    <div class="homehouse padtop30 projecthome">
        <div class="row">
            <div class="col-12">
                <h2>{!! BaseHelper::clean($title) !!}</h2>
                @if ($description)
                    <p>{!! BaseHelper::clean($description) !!}</p>
                @endif
                @if ($subtitle)
                    <p>{!! BaseHelper::clean($subtitle) !!}</p>
                @endif
            </div>
        </div>
        <property-component type="recently-viewed-properties" url="{{ route('public.ajax.properties') }}"></property-component>
    </div>
</div>
