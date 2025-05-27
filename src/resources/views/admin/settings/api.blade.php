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
				{!! Html::form('POST', '/admin/settings/api')->attribute('onsubmit', 'return ConfirmSubmit()')->acceptsFiles() !!}
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="mb-3">
								{!! Html::label('API Key', 'challonge_api_key') !!}
								{!! Html::text('challonge_api_key', $challongeApiKey)->id('challonge_api_key')->class('form-control') !!}
							</div>
							<button type="submit" class="btn btn-success btn-block">Submit</button>
						</div>
						<div class="col-12 col-md-6">
							<p>Challonge API Documentation</p>
							<p>Without this key Tournaments will be disabled</p>
							<p>https://challonge.com/settings/developer</p>
						</div>
					</div>
				{!! Html::form()->close() !!}
			</div>
		</div>
		<!-- Steam API Key -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Steam API Key
			</div>
			<div class="card-body">
				{!! Html::form('POST', '/admin/settings/api')->attribute('onsubmit', 'return ConfirmSubmit()')->acceptsFiles() !!}
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="mb-3">
								{!! Html::label('API Key', 'steam_api_key') !!}
								{!! Html::text('steam_api_key', $steamApiKey)->id('steam_api_key')->class('form-control') !!}
							</div>
							<button type="submit" class="btn btn-success btn-block">Submit</button>
						</div>
						<div class="col-12 col-md-6">
							<p>Steam API Documentation</p>
							<p>Without this key Steam Login will be disabled</p>
							<p>https://steamcommunity.com/dev/apikey</p>
						</div>
					</div>
				{!! Html::form()->close() !!}
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
				{!! Html::form('POST', '/admin/settings/api')->attribute('onsubmit', 'return ConfirmSubmit()')->acceptsFiles() !!}
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="mb-3">
								{!! Html::label('Username', 'paypal_username') !!}
								{!! Html::text('paypal_username', $paypalUsername)->id('paypal_username')->class('form-control') !!}
							</div>
							<div class="mb-3">
								{!! Html::label('Password', 'paypal_password') !!}
								{!! Html::text('paypal_password', $paypalPassword)->id('paypal_password')->class('form-control') !!}
							</div>
							<div class="mb-3">
								{!! Html::label('Signature', 'paypal_signature') !!}
								{!! Html::text('paypal_signature', $paypalSignature)->id('paypal_signature')->class('form-control') !!}
							</div>
							<button type="submit" class="btn btn-success btn-block">Submit</button>
						</div>
						<div class="col-12 col-md-6">
							<p>Paypal API Documentation</p>
							<p>Without this key Paypal Payments will be disabled</p>
						</div>
					</div>
				{!! Html::form()->close() !!}
			</div>
		</div>
		<!-- Stripe -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Stripe
			</div>
			<div class="card-body">
				{!! Html::form('POST', '/admin/settings/api')->attribute('onsubmit', 'return ConfirmSubmit()')->acceptsFiles() !!}
					<div class="row">
						<div class="col-12 col-md-6">
							<div class="mb-3">
								{!! Html::label('Public Key', 'stripe_public_key') !!}
								{!! Html::text('stripe_public_key', $stripePublicKey)->id('stripe_public_key')->class('form-control') !!}
							</div>
							<div class="mb-3">
								{!! Html::label('Secret Key', 'stripe_secret_key') !!}
								{!! Html::text('stripe_secret_key', $stripeSecretKey)->id('stripe_secret_key')->class('form-control') !!}
							</div>
							<button type="submit" class="btn btn-success btn-block">Submit</button>
						</div>
						<div class="col-12 col-md-6">
							<p>Stripe API Documentation</p>
							<p>Without this key Card Payments will be disabled</p>
						</div>
					</div>
				{!! Html::form()->close() !!}
			</div>
		</div>
	</div>
</div>

@endsection