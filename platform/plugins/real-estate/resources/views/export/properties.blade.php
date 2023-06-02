@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-6 col-xl-8 col-lg-10 col-12">
            <div class="widget meta-boxes">
                <div class="widget-title ps-2">
                    <h4>{{ trans('plugins/real-estate::export.properties.name') }}</h4>
                </div>
                <div class="widget-body">
                    <div class="row text-center py-5">
                        <div>
                            <h5>{{ trans('plugins/real-estate::export.properties.total_properties') }}</h5>
                            <h2 class="h1 text-primary font-bold">{{ $totalProperties }}</h2>
                        </div>
                    </div>

                    <div class="form-group mb-3 d-grid">
                        <button
                            type="button"
                            href="{{ route('export-properties.index.post') }}"
                            class="btn btn-info btn-export-data"
                            data-loading-text="{{ trans('plugins/real-estate::export.exporting') }}"
                            data-filename="export_properties.csv"
                        >{{ trans('plugins/real-estate::export.start_export') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
