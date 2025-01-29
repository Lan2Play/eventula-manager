@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' Shop | Order #' . $order->id)

@section ('content')

<div class="container pt-1">
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h1>
			Shop - Order # {{ $order->id }}
		</h1>
	</div>
	@include ('layouts._partials._shop.navigation')
	<table width="100%" class="table table-striped table-hover">
		<thead>
			<tr>
				<th>#</th>
				<th>User</th>
				<th>Name</th>
				<th>Status</th>
				<th>Payment Type</th>
				<th>Ordered At</th>
			</tr>
		</thead>
		<tbody class="table-row odd gradeX">
				@php
					$statusColor = '';
					if ($order->status == 'CANCELLED') {
						$statusColor = 'warning';
					}elseif($order->status == 'EVENT') {
						$statusColor = 'warning';
					}elseif($order->status == 'ERROR') {
						$statusColor = 'danger';
					}elseif($order->status == 'COMPLETE') {
						$statusColor = 'success';
					}
				@endphp
				<tr @if($statusColor != '') class="table-{{ $statusColor }} text-{{ $statusColor }}" @endif>
					<td>{{ $order->id }}</td>
					<td>{{ $order->purchase->user->username }}</td>
					<td>{{ $order->purchase->user->firstname }} {{ $order->purchase->user->surname }}</td>
					<td>{{ $order->status }}</td>
					<td>{{ $order->purchase->type }}</td>
					<td>{{ $order->created_at }}</td>
				</tr>
		</tbody>
	</table>
	@if ($order->shipping_tracking || $order->shipping_note)
		<div class="alert alert-info">
			@if ($order->shipping_tracking)
				<strong>Tracking Number: {{ $order->shipping_tracking }} </strong>
			@endif
			@if ($order->shipping_tracking && $order->shipping_note)
				<br>
			@endif
			@if ($order->shipping_note)
				Note: {{ $order->shipping_note }}
			@endif
		</div>
	@endif
	<div class="row">
		<div class="col-12 col-sm-6">
			<div class="card mb-3">
				<div class="card-header ">
					<h3 class="card-title">Basket</h3>
				</div>
				<div class="card-body">
					@if ($order->purchase->order != null)
						@foreach ($order->purchase->order->items as $item)
							@if ($item->item)
								{{ $item->item->name }}
							@endif
							 - x {{ $item->quantity }}
							 <br>
						 	@if ($item->price != null)
								{{ Settings::getCurrencySymbol() }}{{ $item->price * $item->quantity }}
								@if ($item->price_credit != null && Settings::isCreditEnabled())
									/
								@endif
							@endif
							@if ($item->price_credit != null && Settings::isCreditEnabled())
								{{ $item->price_credit * $item->quantity }} Credits
							@endif
								<hr>
							
						@endforeach
					@endif
					<div>
						<strong>Total:</strong>
								@if ($order->getTotalPrice() != 0)
									{{ Settings::getCurrencySymbol() }}{{ number_format($order->getTotalPrice(), 2) }}
									@if ($order->getTotalCreditPrice() != 0 && Settings::isCreditEnabled())
										/
									@endif
								@endif
								@if ($order->getTotalCreditPrice() != 0 && Settings::isCreditEnabled())
									{{ number_format($order->getTotalCreditPrice(), 2) }} Credits
								@endif
					</div>

				</div>
			</div>
		</div>
		<div class="col-12 col-sm-6">
			<div class="card mb-3">
				<div class="card-header ">
					<h3 class="card-title">Order Details</h3>
				</div>
				<div class="card-body">
					@if ($order->hasShipping())
						<h4>Shipping Details</h4>
						<address>
							<strong>{{ $order->shipping_first_name}} {{ $order->shipping_last_name }}</strong><br>
							{{ $order->shipping_address_1 }}<br>
							@if (trim($order->shipping_address_2) != '')
								{{ $order->shipping_address_2 }}<br>
							@endif
							@if (trim($order->shipping_country) != '')
								{{ $order->shipping_country }}<br>
							@endif
							@if (trim($order->shipping_state) != '')
								{{ $order->shipping_state }}<br>
							@endif
							{{ $order->shipping_postcode }}
						</address>
					@endif
					<ul class="list-group">
						@php
							$statusColor = 'info';
							if ($order->status == 'CANCELLED') {
								$statusColor = 'warning';
							}elseif($order->status == 'EVENT') {
								$statusColor = 'warning';
							}elseif($order->status == 'ERROR') {
								$statusColor = 'danger';
							}elseif($order->status == 'COMPLETE') {
								$statusColor = 'success';
							}
						@endphp
						<li class="list-group-item list-group-item-{{ $statusColor }}"><strong>Order Status: <span class="float-end">{{ $order->status }}</span></strong></li>
						<li class="list-group-item @if (strtolower($order->purchase->status) != 'success') list-group-item-danger @else list-group-item-success @endif"><strong>Payment Status: <span class="float-end">{{ $order->purchase->status }}</span></strong></li>
						<li class="list-group-item list-group-item-info"><strong>Order ID: <span class="float-end">{{ $order->id }}</span></strong></li>
						<li class="list-group-item list-group-item-info"><strong>Transaction ID: <span class="float-end">{{ $order->purchase->transaction_id }}</span></strong></li>
						<li class="list-group-item list-group-item-info"><strong>Purchase ID: <span class="float-end">{{ $order->purchase->id }}</span></strong></li>
						@if ($order->purchase->paypal_email != null)
							<li class="list-group-item list-group-item-info">
								<strong>Paypal Email: <span class="float-end">{{ $order->purchase->paypal_email }}</span></strong>
							</li>
						@endif
						<li class="list-group-item list-group-item-info"><strong>Payment Type: <span class="float-end">{{ $order->purchase->type }}</span></strong></li>
						<li class="list-group-item list-group-item-info"><strong>Ordered at: <span class="float-end">{{ $order->created_at }}</span></strong></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
