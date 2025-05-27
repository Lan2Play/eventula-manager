@extends ('layouts.admin-default')

@section ('page_title', 'Settings')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Authentication</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/settings">Settings</a>
			</li>
			<li class="breadcrumb-item active">
				Authentication
			</li>
		</ol>
	</div>
</div>

@include ('layouts._partials._admin._settings.dashMini', ['active' => 'auth'])

<div class="row">
	<div class="col-12">
		<!-- Login Methods -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Login Methods
			</div>
			<div class="card-body">
				<div class="row">
					@foreach ($supportedLoginMethods as $method)
					<div class="col">
						<h4>{{ ucwords(str_replace('-', ' ', (str_replace('_', ' ' , $method)))) }}</h4>
						@if (in_array($method, $activeLoginMethods))
						{!! Html::form('POST', '/admin/settings/login/' . $method . '/disable') !!}
						<button type="submit" class="btn btn-block btn-danger">Disable</button>
						{!! Html::form()->close() !!}
						@else
						{!! Html::form('POST', '/admin/settings/login/' . $method . '/enable') !!}
						<button type="submit" class="btn btn-block btn-success">Enable</button>
						{!! Html::form()->close() !!}
						@endif
					</div>
					@endforeach
				</div>
			</div>
		</div>


		<!-- global settings -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Global Authentication settings
			</div>
			<div class="card-body">
				{!! Html::form('POST', '/admin/settings/auth/general')->acceptsFiles() !!}
				<div class="row">
					<div class="col-12 col-md-6">
						<div class="mb-3">
							<div class="form-check">
								<label class="form-check-label">
									{!! Html::checkbox('auth_allow_email_change', null, $isAuthAllowEmailChangeEnabled)->id('auth_allow_email_change') !!} Allow changing the Email Adress after registration
								</label>
							</div>
						</div>
					</div>

					<div class="col-12 col-md-6">
						<div class="mb-3">
							<div class="form-check">
								<label class="form-check-label">
									{!! Html::checkbox('auth_require_phonenumber', null, $isAuthRequirePhonenumberEnabled)->id('auth_require_phonenumber') !!} Require Phonenumber (all registered users will be forced to set it on the next login)
								</label>
							</div>
						</div>
					</div>
				</div>
				<button type="submit" class="btn btn-success btn-block">Submit</button>
				{!! Html::form()->close() !!}

			</div>
		</div>

		@if (in_array("steam", $activeLoginMethods))
		<!-- Steam settings -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Steam Authentication settings
			</div>
			<div class="card-body">
				{!! Html::form('POST', '/admin/settings/auth/steam')->attribute('onsubmit', 'return ConfirmSubmit()')->acceptsFiles() !!}
				<div class="row">
					<div class="col-12 col-md-6">
						<div class="mb-3">
							<div class="form-check">
								<label class="form-check-label">
									{!! Html::checkbox('auth_steam_require_email', null, $isAuthSteamRequireEmailEnabled)->id('auth_steam_require_email') !!} Require Email Adress on Steam registration (all registered users will be forced to set it on the next login)
								</label>
							</div>
						</div>

						<button type="submit" class="btn btn-success btn-block">Submit</button>
					</div>

				</div>
				{!! Html::form()->close() !!}
			</div>
		</div>
		@endif


		<!-- Terms & Conditions -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-info-circle fa-fw"></i> Terms and Conditions
			</div>
			<div class="card-body">
				{!! Html::form('POST', '/admin/settings/')->attribute('onsubmit', 'return ConfirmSubmit()') !!}
				<div class="mb-3">
					{!! Html::label('Registration', 'registration_terms_and_conditions') !!}
					{!! Html::textarea('registration_terms_and_conditions', Settings::getRegistrationTermsAndConditions())->class('form-control wysiwyg-editor') !!}
				</div>
				<button type="submit" class="btn btn-success btn-block">Submit</button>
				{!! Html::form()->close() !!}
			</div>
		</div>
	</div>
</div>

@endsection