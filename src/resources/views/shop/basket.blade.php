@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' Shop | Cart')

@section ('content')

<div class="container pt-1">
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h1>
			Shop - Cart
		</h1>
	</div>
	@include ('layouts._partials._shop.navigation')
	<div class="row">
		<div class="col-12 col-md-12">
			@if (isset($basket) && strtolower($basket) != 'empty')
				<table class="table table-striped table-responsive">
					<tbody>
						@foreach ($basket as $item)
							<tr>
								<td>
									<strong>{{ $item->name }}</strong>
								</td>
								<td class="text-end">
									x {{ $item->quantity }}
								</td>
								<td class="text-end">
									@if ($item->price != null && $item->price != 0)
										{{ Settings::getCurrencySymbol() }}{{ number_format($item->price, 2) }}
										@if ($item->price_credit != null && Settings::isCreditEnabled() && $item->price_credit != 0)
											/
										@endif
									@endif
									@if ($item->price_credit != null && Settings::isCreditEnabled() && $item->price_credit != 0)
										{{ number_format($item->price_credit, 2) }} Credits
									@endif
									Each
								</td>
								<td class="text-end">
									@if ($item->price != null && $item->price != 0)
										{{ Settings::getCurrencySymbol() }}{{ number_format($item->price * $item->quantity, 2) }}
										@if ($item->price_credit != null && Settings::isCreditEnabled() && $item->price_credit != 0)
											/
										@endif
									@endif
									@if ($item->price_credit != null && Settings::isCreditEnabled() && $item->price_credit != 0)
										{{ number_format($item->price_credit * $item->quantity, 2) }} Credits
									@endif
								</td>
								<td>
									{{ Form::open(array('url'=>'/shop/basket')) }}
										{{ Form::hidden('shop_item_id', $item->id) }}
										{{ Form::hidden('action', 'remove') }}
										<button type="submit" class="btn btn-primary">X</button>
									{{ Form::close() }}
								</td>
							</tr>
						@endforeach
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td class="text-end">
								<strong>Total:</strong>
								@if ($basket->total != 0)
									{{ Settings::getCurrencySymbol() }}{{ number_format($basket->total, 2) }}
									@if ($basket->total_credit != 0 && Settings::isCreditEnabled())
										/
									@endif
								@endif
								@if ($basket->total_credit != 0 && Settings::isCreditEnabled())
									{{ number_format($basket->total_credit, 2) }} Credits
								@endif
							</td>
							<td>
							</td>
						</tr>
					</tbody>
				</table>
				<a href="/payment/checkout">
					<button type="button" class="btn btn-primary btn-block">Checkout</button>
				</a>
			@else
				<p>Your cart is empty</p>
			@endif
		</div>
	</div>
</div>

@endsection
