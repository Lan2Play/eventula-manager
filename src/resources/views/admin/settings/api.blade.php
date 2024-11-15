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

@endsection