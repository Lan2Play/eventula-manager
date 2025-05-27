@extends ('layouts.defa							@if (Set							@if (Settings::isAuthRequirePhonenumberEnabled())
							<div class="mb-3">
								{!! Html::label(__('accounts.phonenumber'), 'phonenumber') !!}
								<input type="phonenumber" class="form-control" name="phonenumber" id="phonenumber @error('phonenumber') is-invalid @enderror" aria-describedby="phonenumber" value="{{ $user->phonenumber }}" placeholder="@lang('accounts.phonenumber')">"s::isAuthAllowEmailChangeEnabled() || (!$user->email && Settings::isAuthSteamRequireEmailEnabled()))
							<div class="mb-3">
								{!! Html::label(__('accounts.email'), 'email') !!}
								<input type="email" class="form-control" name="email" id="email @error('email') is-invalid @enderror" aria-describedby="email" value="{{ $user->email }}" placeholder="@lang('accounts.email')">)

@section ('page_title', __('accounts.accounts_title'))

@section ('content')

<div class="container pt-1">

	<div class="row">

		<div class="col-12  col-lg-12 mt-3 mb-3">
			<div class="card mb-3">
				<div class="card-header ">
					<h3 class="card-title">@lang('accounts.contactdetails')</h3>
				</div>
				<div class="card-body">
					{!! Html::form('POST', '/account/email') !!}
					<div class="row" style="display: flex; align-items: center;">
						<div class="col-md-2 col-sm-12">
							@lang('accounts.emailmessage')
						</div>
						<div class="col-md-10 col-sm-12">

							@if (Settings::isAuthAllowEmailChangeEnabled() || (!$user->email && Settings::isAuthSteamRequireEmailEnabled()))
							<div class="mb-3">
								{!! Html::label(__('accounts.email'), 'email') !!}
								<input type="email" class="form-control" name="email" id="email @error('email') is-invalid @enderror" aria-describedby="email" value="{{ $user->email }}" placeholder="@lang('accounts.email')">
								@error('email')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
								@enderror
							</div>
							@endif

							@if (Settings::isAuthRequirePhonenumberEnabled())
							<div class="mb-3">
								{!! Html::label(__('accounts.phonenumber'), 'phonenumber') !!}
								<input type="phonenumber" class="form-control" name="phonenumber" id="phonenumber @error('phonenumber') is-invalid @enderror" aria-describedby="phonenumber" value="{{ $user->phonenumber }}" placeholder="@lang('accounts.phonenumber')">
								@error('phonenumber')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
								@enderror
							</div>
							@endif


							@if (!$user->email || Settings::isAuthAllowEmailChangeEnabled())
							<button type="submit" class="btn btn-primary btn-block">@lang('accounts.submit')</button>
							@endif
						</div>
					</div>
					{!! Html::form()->close() !!}
				</div>
			</div>
		</div>
	</div>


	@endsection