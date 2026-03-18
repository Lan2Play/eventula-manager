@extends ('layouts.admin-default')

@section ('page_title', 'Settings')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">API</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/settings">Settings</a>
			</li>
			<li class="breadcrumb-item active">
				API
			</li>
		</ol>
	</div>
</div>

@include ('layouts._partials._admin._settings.dashMini', ['active' => 'auth'])

<div class="row">
	<div class="col-12">
		<div class="alert alert-info">
			Be careful! Changes these settings could break the site!
		</div>
	</div>
	<!-- Challonge -->
	<div class="col-12 col-md-6">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Challonge
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/settings/api', 'onsubmit' => 'return ConfirmSubmit()', 'files' => 'true')) }}
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="mb-3">
								{{ Form::label('challonge_api_key','API Key',array('id'=>'','class'=>'')) }}
								{{ Form::text('challonge_api_key', $challongeApiKey, array('id'=>'challonge_api_key','class'=>'form-control')) }}
							</div>
							<button type="submit" class="btn btn-success btn-block">Submit</button>
						</div>
						<div class="col-12 col-md-6">
							<p>Challonge API Documentation</p>
							<p>Without this key Tournaments will be disabled</p>
							<p>https://challonge.com/settings/developer</p>
						</div>
					</div>
				{{ Form::close() }}
			</div>
		</div>
		<!-- Steam API Key -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Steam API Key
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/settings/api', 'onsubmit' => 'return ConfirmSubmit()', 'files' => 'true')) }}
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="mb-3">
								{{ Form::label('steam_api_key','API Key',array('id'=>'','class'=>'')) }}
								{{ Form::text('steam_api_key', $steamApiKey, array('id'=>'steam_api_key','class'=>'form-control')) }}
							</div>
							<button type="submit" class="btn btn-success btn-block">Submit</button>
						</div>
						<div class="col-12 col-md-6">
							<p>Steam API Documentation</p>
							<p>Without this key Steam Login will be disabled</p>
							<p>https://steamcommunity.com/dev/apikey</p>
						</div>
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
	<!-- Paypal -->
	<div class="col-12 col-md-6">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Paypal
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/settings/api', 'onsubmit' => 'return ConfirmSubmit()', 'files' => 'true')) }}
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="mb-3">
								{{ Form::label('paypal_username','Username',array('id'=>'','class'=>'')) }}
								{{ Form::text('paypal_username', $paypalUsername, array('id'=>'paypal_username','class'=>'form-control')) }}
							</div>
							<div class="mb-3">
								{{ Form::label('paypal_password','Password',array('id'=>'','class'=>'')) }}
								{{ Form::text('paypal_password', $paypalPassword, array('id'=>'paypal_password','class'=>'form-control')) }}
							</div>
							<div class="mb-3">
								{{ Form::label('paypal_signature','Signature',array('id'=>'','class'=>'')) }}
								{{ Form::text('paypal_signature', $paypalSignature, array('id'=>'paypal_signature','class'=>'form-control')) }}
							</div>
							<button type="submit" class="btn btn-success btn-block">Submit</button>
						</div>
						<div class="col-12 col-md-6">
							<p>Paypal API Documentation</p>
							<p>Without this key Paypal Payments will be disabled</p>
						</div>
					</div>
				{{ Form::close() }}
			</div>
		</div>
		<!-- Stripe -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Stripe
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/settings/api', 'onsubmit' => 'return ConfirmSubmit()', 'files' => 'true')) }}
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="mb-3">
								{{ Form::label('stripe_public_key','Public Key',array('id'=>'','class'=>'')) }}
								{{ Form::text('stripe_public_key', $stripePublicKey, array('id'=>'stripe_public_key','class'=>'form-control')) }}
							</div>
							<div class="mb-3">
								{{ Form::label('stripe_secret_key','Secret Key',array('id'=>'','class'=>'')) }}
								{{ Form::text('stripe_secret_key', $stripeSecretKey, array('id'=>'stripe_secret_key','class'=>'form-control')) }}
							</div>
							<button type="submit" class="btn btn-success btn-block">Submit</button>
						</div>
						<div class="col-12 col-md-6">
							<p>Stripe API Documentation</p>
							<p>Without this key Card Payments will be disabled</p>
						</div>
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>

{{-- Plausible Analytics --}}
<div class="row mt-2">
	<div class="col-12">
		<h3 class="pb-2 mt-2 mb-4 border-bottom">Plausible Analytics</h3>
	</div>
	@if(!empty(env('PLAUSIBLE_ENABLE')))
	<div class="col-12">
		<div class="alert alert-warning">
			<strong>Note:</strong> <code>PLAUSIBLE_ENABLE</code> is set in the environment and overrides the toggle below.
		</div>
	</div>
	@endif
	<div class="col-12 col-md-6">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-chart-bar fa-fw"></i> Plausible Settings
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/settings/api', 'onsubmit' => 'return ConfirmSubmit()', 'files' => 'true')) }}
					<div class="mb-3 form-check form-switch">
						{{ Form::hidden('plausible_enabled', 'off') }}
						{{ Form::checkbox('plausible_enabled', 'on', $plausibleEnabled, array('id'=>'plausible_enabled','class'=>'form-check-input')) }}
						{{ Form::label('plausible_enabled', 'Enable Plausible Analytics', array('class'=>'form-check-label')) }}
					</div>
					<div class="mb-3">
						{{ Form::label('plausible_script_url','Script URL',array('class'=>'')) }}
						{{ Form::text('plausible_script_url', $plausibleScriptUrl, array('id'=>'plausible_script_url','class'=>'form-control','placeholder'=>'https://plausible.io/js/pa-XXXXX.js')) }}
						<div class="form-text">Personalized script URL from your Plausible site settings.</div>
					</div>
					<div class="mb-3">
						{{ Form::label('plausible_domain','Tracked Domain (optional)',array('class'=>'')) }}
						{{ Form::text('plausible_domain', $plausibleDomain, array('id'=>'plausible_domain','class'=>'form-control','placeholder'=>'yourdomain.com')) }}
						<div class="form-text">Leave empty to use <code>APP_URL</code>. Useful when the public domain differs from the internal app URL.</div>
					</div>
					<div class="mb-3">
						{{ Form::label('plausible_api_url','Events API URL',array('class'=>'')) }}
						{{ Form::text('plausible_api_url', $plausibleApiUrl, array('id'=>'plausible_api_url','class'=>'form-control','placeholder'=>'https://plausible.io/api/event')) }}
						<div class="form-text">Override for self-hosted Plausible instances, e.g. <code>https://plausible.yourdomain.com/api/event</code></div>
					</div>
					<button type="submit" class="btn btn-success btn-block">Save</button>
				{{ Form::close() }}
			</div>
		</div>
	</div>
	<div class="col-12 col-md-6">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-info-circle fa-fw"></i> About Plausible
			</div>
			<div class="card-body">
				<p>Plausible is a lightweight, privacy-friendly analytics tool. Events are proxied through this application to avoid ad blockers.</p>
				<p>The local proxy endpoints are:</p>
				<ul>
					<li><code>GET /js/script.js</code> — tracking script</li>
					<li><code>POST /api/event</code> — event collection</li>
				</ul>
				<p><a href="https://plausible.io/docs" target="_blank" rel="noopener">Plausible Documentation</a></p>
				<hr>
				<p class="text-muted small">To force-disable Plausible regardless of these settings, set <code>PLAUSIBLE_ENABLE=false</code> in your environment file.</p>
			</div>
		</div>
	</div>
</div>

@endsection