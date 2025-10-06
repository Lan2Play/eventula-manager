@extends ('layouts.default')

@section ('page_title', '403 - Forbidden')

@section ('content')

<div class="container pt-1">

    <div class="pb-2 mt-4 mb-4 border-bottom">
        <h1>
            @lang('errorPages.403.header')
        </h1>
    </div>
    <div class="row">
        <div class="col-12 col-md-8">
           @lang('errorPages.403.message')
        </div>
    </div>
</div>

@endsection