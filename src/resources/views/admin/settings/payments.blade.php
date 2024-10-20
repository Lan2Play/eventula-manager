@extends ('layouts.admin-default')

@section ('page_title', 'Settings')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Payments</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/settings">Settings</a>
			</li>
			<li class="breadcrumb-item active">
				Payments
			</li>
		</ol>
	</div>
</div>

@include ('layouts._partials._admin._settings.dashMini', ['active' => 'payments'])

<div class="row">
	<div class="col-lg-6 col-12">
		<!-- Payment Gateways -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-info-circle fa-fw"></i> Payment Gateways
			</div>
			<div class="card-body">
				<div class="row">
					@foreach ($supportedPaymentGateways as $gateway)
						<div class="col-6">
							<h4>{{ ucwords(str_replace('-', ' ', (str_replace('_', ' ' , $gateway)))) }}</h4>
							@if (in_array($gateway, $activePaymentGateways))
								{{ Form::open(array('url'=>'/admin/settings/payments/' . $gateway . '/disable')) }}
									<button type="submit" class="btn btn-block btn-danger">Disable</button>
								{{ Form::close() }}
							@else
								{{ Form::open(array('url'=>'/admin/settings/payments/' . $gateway . '/enable')) }}
									<button type="submit" class="btn btn-block btn-success">Enable</button>
								{{ Form::close() }}
							@endif
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-12">
		<!-- Currency -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-money fa-fw"></i> Currency
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/settings/', 'onsubmit' => 'return ConfirmSubmit()')) }}
					<div class="mb-3">
						{{ Form::select('currency', ['GBP' => 'GBP', 'USD' => 'USD', 'EUR' => 'EUR', 'SEK' => 'SEK', 'DKK' => 'DKK'], Settings::getCurrency(), array('id'=>'venue','class'=>'form-control')) }}
					</div>
					<button type="submit" class="btn btn-success btn-block">Submit</button>
				{{ Form::close() }}
			</div>
		</div>
	</div>
	<div class="col-12">
		<!-- Terms & Conditions -->
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-info-circle fa-fw"></i> Terms and Conditions
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/settings/', 'onsubmit' => 'return ConfirmSubmit()')) }}
					<div class="mb-3">
						{{ Form::label('purchase_terms_and_conditions','Purchase',array('id'=>'','class'=>'')) }}
						{{ Form::textarea('purchase_terms_and_conditions', Settings::getPurchaseTermsAndConditions() ,array('id'=>'','class'=>'form-control wysiwyg-editor')) }}
					</div>
					<button type="submit" class="btn btn-success btn-block">Submit</button>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>

@endsection