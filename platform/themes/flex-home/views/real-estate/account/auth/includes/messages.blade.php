@if (session()->has('status'))
    <div class="alert alert-success">
        <span>{!! BaseHelper::clean(session('status')) !!}</span>
    </div>
@endif

@if (session()->has('success_msg'))
    <div class="alert alert-success">
        <span>{!! BaseHelper::clean(session('success_msg')) !!}</span>
    </div>
@endif

@if (session()->has('error_msg'))
    <div class="alert alert-danger">
        <span>{!! BaseHelper::clean(session('error_msg')) !!}</span>
    </div>
@endif

@if (isset($errors) && $errors->count() > 0)
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p @if ($loop->last) class="mb-0" @endif>{!! BaseHelper::clean($error) !!}</p>
        @endforeach
    </div>
@endif
