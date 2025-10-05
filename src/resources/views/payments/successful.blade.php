@extends ('layouts.default')

@section ('page_title', __('payments.payment_successful'))

@section ('content')

<div class="container pt-1">
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h1>
			@lang('payments.thank_you')
		</h1>
	</div>
	<div class="row">
		@if ($type == 'tickets')
			<div class="col-12 col-md-8">
				<h3>@lang('payments.tickets_active')</h3>
				<h4>@lang('payments.payment_successful_text1') <a href="/events/{{ $purchase->tickets[0]->event->slug }}/#seating">@lang('payments.payment_successful_linktext1')</a></h4>
				<h4>@lang('payments.payment_successful_text2') <a href="/events/{{ $purchase->tickets[0]->event->slug }}/#tournaments">@lang('payments.payment_successful_linktext2')</a></h4>
				<p><strong>@lang('payments.purchase_id')</strong> {{ $purchase->id }}</p>
				<p><strong>@lang('payments.payment_method')</strong> {{ $purchase->getPurchaseType() }}</p>
				<h3>@lang('payments.tickets')</h3>
				<hr>
				<div class="row">
					@foreach ($purchase->tickets as $ticket)
						<div class="col-lg-4 col-sm-6 col-12 text-center">
							<h5>{{ $ticket->event->display_name }}</h5>
							<h5>{{ $ticket->ticketType->name }}</h5>
							<img class="img img-fluid" src="/{{ $ticket->qrcode }}"/>
						</div>
					@endforeach
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="card mb-3">
					<div class="card-header ">
						<h3 class="card-title">@lang('payments.order_details')</h3>
					</div>
					<div class="card-body">
						<table class="table table-striped table-responsive">
							<tbody>
								@php ($total = 0)
								@foreach ($basket as $item)
									<tr>
										<td>
											<strong>{{ $item->name }}</strong>
										</td>
										<td>
											x {{ $item->quantity }}
										</td>
										<td>
											{{ Settings::getCurrencySymbol() }}{{ $item->price }}
										</td>
									</tr>
								@endforeach
								<tr>
									<td></td>
									<td>
										<strong>Total:</strong>
									</td>
									<td>
										{{ Settings::getCurrencySymbol() }}{{ $basket->total }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		@elseif ($type == 'shop')
			<div class="col-12 col-md-12">
				<h3>@lang('payments.order_successful')</h3>
				<h4></h4>
				<p><strong>@lang('payments.purchase_id')</strong> {{ $purchase->id }}</p>
				<p><strong>@lang('payments.payment_method')</strong> {{ $purchase->getPurchaseType() }}</p>
				<h3>@lang('payments.items')</h3>
				<hr>
				<div class="row">
					@foreach ($basket as $item)
						<div class="col-lg-3 col-sm-4 col-12 text-center">
							<h5>{{ $item->name }}</h5>
							<h5>@lang('payments.quantity') {{ $item->quantity }}</h5>
							<h5>
								@if ($item->price != null && $item->price != 0)
									{{ Settings::getCurrencySymbol() }}{{ $item->price * $item->quantity }}
									@if ($item->price_credit != null && Settings::isCreditEnabled() && $item->price_credit != 0)
										/
									@endif
								@endif
								@if ($item->price_credit != null && Settings::isCreditEnabled() && $item->price_credit != 0)
									{{ $item->price_credit * $item->quantity }} @lang('payments.credits')
								@endif
							</h5>
							<picture>
								<source srcset="{{ $item->getDefaultImageUrl() }}.webp" type="image/webp">
								<source srcset="{{ $item->getDefaultImageUrl() }}" type="image/jpeg">
								<img class="img img-fluid rounded" src="{{ $item->getDefaultImageUrl() }}"/>
							</picture>
						</div>
					@endforeach
				</div>
			</div>
		@endif
	</div>
</div>

@endsection