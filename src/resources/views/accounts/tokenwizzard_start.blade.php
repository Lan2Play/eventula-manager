@extends ('layouts.default')
{{-- <?php
        Debugbar::addMessage("status: $status", 'wizzardblade');	
        Debugbar::addMessage("application: $application", 'wizzardblade');	
        Debugbar::addMessage("callbackurl: $callbackurl", 'wizzardblade');	
?> --}}
@if(isset($application))
	@section ('page_title', __('accounts.tokenwizzard') . " " . __('accounts.tokenwizzardfor') ." ". $application)
@else
	@section ('page_title', __('accounts.tokenwizzard'))
@endif

@section ('content')

	<div class="container pt-1">
		<div class="pb-2 mt-4 mb-4 border-bottom">
			<h1>				
				@if(isset($application))
					@lang('accounts.tokenwizzard') @lang('accounts.tokenwizzardfor') {{ $application }}
				@else
					@lang('accounts.tokenwizzard') 
				@endif
			</h1>

		</div>

		<div class="card mb-3">
			<div class="card-header ">
				<h3 class="card-title">
					@if(isset($application))
						@lang('accounts.tokenwizzard') @lang('accounts.tokenwizzardfor') {{ $application }}
					@else
						@lang('accounts.tokenwizzard') 
					@endif					
				</h3>
			</div>
			<div class="card-body">

			@if ($status ==  "no_application")
				<div class="alert alert-danger">
					@lang('accounts.new_token_wizzard_application_not_set')
				</div>
			@endif
			{!! Html::form('POST', '/account/tokens/wizzard/finish') !!}							

			@if ($status ==  "exists")
				<div class="alert alert-warning">
					@lang('accounts.new_token_wizzard_application_already_exists_1') <b>{{ $application }}</b> @lang('accounts.new_token_wizzard_application_already_exists_2') 
				</div>
			@endif
			@if ($status ==  "exists" || $status ==  "not_exists")
				@lang('accounts.new_token_wizzard_application_authenticate_1') <b>{{ $application }}</b> @lang('accounts.new_token_wizzard_application_authenticate_2')
				<br>
				<br>
				{!! Html::hidden('application', $application) !!}
				{!! Html::hidden('callbackurl', $callbackurl) !!}

				<button type="submit" class="btn btn-primary btn-block">@lang('accounts.add_token')</button>

							
			@endif

			{!! Html::form()->close() !!}

		</div>
		</div>
	</div>
			

@endsection