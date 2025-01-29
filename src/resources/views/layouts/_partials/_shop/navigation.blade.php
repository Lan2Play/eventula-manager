<nav class="subnavbar navbar navbar-expand-md @if(Colors::isBodyDarkmode()) navbar-dark @else navbar-light @endif">
	<div class="container-fluid">
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="nav-item @if(Route::current()->uri == 'shop') active @endif"><a class="nav-link" href="/shop">All</a></li>
				@foreach ($allCategories as $c)
					@if (isset($category) && $c->id == $category->id)
						<li class="nav-item active"><a class="nav-link" href="/shop/{{ $c->slug }}">{{ $c->name }}</a></li>
					@else
						<li class="nav-item"><a class="nav-link" href="/shop/{{ $c->slug }}">{{ $c->name }}</a></li>
					@endif
				@endforeach
			</ul>
			</ul>
			<ul class="nav navbar-nav ms-auto">
			  <li class="nav-item"><a class="nav-link" href="/shop/basket">Basket</a></li>
			  <li class="nav-item"><a class="nav-link" href="/payment/checkout">Checkout</a></li>
			  <li class="nav-item"><a class="nav-link" href="/shop/orders">Orders</a></li>
			</ul>
		</div>
	</div>
</nav>
@if (Settings::getShopStatus() == 'CLOSED')
	<div class="row">
		<div class="col-12">
			<div class="alert alert-danger"><strong>{{ Settings::getShopClosedMessage() }}</strong></div>
		</div>
	</div>
@endif