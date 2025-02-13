@extends ('layouts.default')

@section ('page_title', __('payments.payment_details'))

@section ('content')

<div class="container pt-1">
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h1>
		@lang('payments.payment_details')
		</h1>
	</div>
	<div class="row">
		<div class="col-12 col-md-8">
			@if ($paymentGateway == 'stripe')

				{{ Form::open(array('url'=>'/payment/post', 'id'=>'payment-form')) }}
					<div class="row">
						<div class="mb-3 col-sm-6 col-12">
							{{ Form::label('card_first_name', __('payments.firstname').' *' , array('id'=>'','class'=>'')) }}
							{{ Form::text('card_first_name', '', array('id'=>'card_first_name','class'=>'form-control')) }}
						</div>
						<div class="mb-3 col-sm-6 col-12">
							{{ Form::label('card_last_name', __('payments.lastname').' *' , array('id'=>'','class'=>'')) }}
							{{ Form::text('card_last_name', '', array('id'=>'card_last_name','class'=>'form-control')) }}
						</div>
					</div>
					{{ Form::label('card_number', __('payments.card_details'), array('id'=>'','class'=>'')) }}
					<div class="form-control" id="card-element"></div>
			  		<div id="card-errors" role="alert"></div>
					<p><small>@lang('payments.delivery_required_fields')</small></p>
					{{ Form::hidden('gateway', $paymentGateway) }}
					<button type="button" id="checkout_btn" class="btn btn-primary btn-block">@lang('payments.confirm_order')</button>
				{{ Form::close() }}
			@else ($paymentGateway == 'credit' && Settings::isCreditEnabled())
				<h5>Credit: {{ $user->credit_total }}</h5>
				<h5>Credit After Purchase: {{ $user->credit_total - $basket->total_credit }}</h5>
				@if ($user->checkCredit(-1 * abs($basket->total_credit)) && $basket->allow_credit)
					<hr>
					<div class="alert alert-warning">
						<h5>@lang('payments.enough_credits')</h5>
					</div>
					{{ Form::open(array('url'=>'/payment/post')) }}
					{{ Form::hidden('gateway', $paymentGateway) }}
					{{ Form::hidden('confirm', true) }}
						<button class="btn btn-primary btn-block">@lang('payments.confirm_order')</button>
					{{ Form::close() }}
				@elseif (!$basket->allow_credit)
					<hr>
					<div class="alert alert-warning">
						<h5>@lang('payments.cannot_use_credit_1') <a href="/payment/checkout">@lang('payments.back')</a> @lang('payments.cannot_use_credit_2')</h5>
					</div>
				@else
					<hr>
					<div class="alert alert-warning">
						<h5>@lang('payments.not_enough_credits_1') <a href="/payment/checkout">@lang('payments.back')</a> @lang('payments.not_enough_credits_1')</h5>
					</div>
				@endif
			@endif
		</div>
		<div class="col-12 col-md-4">
			<div class="card mb-3">
				<div class="card-header ">
					<h3 class="card-title">@lang('payments.order_details')</h3>
				</div>
				<div class="card-body">
					@include ('layouts._partials._shop.basket-preview')
				</div>
			</div>
			@if ($delivery && $deliveryDetails)
				<div class="card mb-3">
					<div class="card-header ">








						<h3 class="card-title">@lang('payments.delivery_details')</h3>
					</div>
					<div class="card-body">
						@if ($deliveryDetails && $deliveryDetails['type'] == 'shipping')
							<address>
								<strong>{{ $deliveryDetails['shipping_first_name']}} {{ $deliveryDetails['shipping_last_name'] }}</strong><br>
								{{ $deliveryDetails['shipping_address_1'] }}<br>
								@if (trim($deliveryDetails['shipping_address_2']) != '')
									{{ $deliveryDetails['shipping_address_2'] }}<br>
								@endif
								@if (trim($deliveryDetails['shipping_country']) != '')
									{{ $deliveryDetails['shipping_country'] }}<br>
								@endif
								@if (trim($deliveryDetails['shipping_state']) != '')
									{{ $deliveryDetails['shipping_state'] }}<br>
								@endif
								{{ $deliveryDetails['shipping_postcode'] }}
							</address>
						@else
							<strong>@lang('payments.delivery_next_event')</strong>
						@endif
					</div>
				</div>
			@endif
		</div>

	</div>
</div>

@if ($paymentGateway == 'stripe')
	<script src="https://js.stripe.com/v3/"></script>
	<script>

        var style = {
            base: {
			  	lineHeight: '1.429'
            }
        };

	    const stripe = Stripe( "{!! config('laravel-omnipay.gateways.stripe.credentials.public') !!}" );

	    const elements = stripe.elements();
	    const cardElement = elements.create('card', {style: style});

	    cardElement.mount('#card-element');

	    const cardHolderName = document.getElementById('card_first_name') + document.getElementById('card_last_name');
		const cardButton = document.getElementById('checkout_btn');
		const clientSecret = cardButton.dataset.secret;
		const form = document.getElementById('payment-form');
		cardButton.addEventListener('click', async (e) => {
		    stripe.createPaymentMethod('card', cardElement, {
                billing_details: {name: cardHolderName.value }
            }).then(function(result) {

                if (result.error) {
                    cardButton.disabled = false;
                    alert(result.error.message);
                } else {
					var hiddenInput = document.createElement('input');
					hiddenInput.setAttribute('type', 'hidden');
					hiddenInput.setAttribute('name', 'stripe_token');
					hiddenInput.setAttribute('value', result.paymentMethod.id);
					form.appendChild(hiddenInput);
					console.log(result.paymentMethod.id);
					form.submit();
                }
            });
		});
	</script>
@endif

@endsection