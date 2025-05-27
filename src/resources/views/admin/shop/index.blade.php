@extends ('layouts.admin-default')

@section ('page_title', 'Shop')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Shop</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item active">
				Shop
			</li>
		</ol>
	</div>
</div>

<div class="row">
	@if (!$isShopEnabled)
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-info-circle fa-fw"></i> Shop is Currently Disabled...
				</div>
				<div class="card-body">
					<p>The Shop can be used for buying merch, consumables etc. It is not recommended you do event ticket sales through this system.</p>
						{!! Html::form('POST', '/admin/settings/shop/enable') !!}
							<button type="submit" class="btn btn-block btn-success">Enable</button>
						{!! Html::form()->close() !!}
				</div>
			</div>
		</div>
	@else
		<div class="col-12">

		</div>
		<div class="col-12 col-sm-10">
			<div class="row">
				<div class="col-12 col-sm-6">
					<div class="card mb-3">
						<div class="card-header">
							<i class="fa fa-plus fa-fw"></i> Add Item
						</div>
						<div class="card-body">
							<div class="list-group">
								{!! Html::form('POST', '/admin/shop/item') !!}
										<div class="mb-3">
											{!! Html::label('Name', 'name') !!}
											{!! Html::text('name', null)->id('name')->class('form-control') !!}
										</div>
									<div class="row">
										<div class="mb-3 col-12 col-sm-6">
											{!! Html::label('Stock', 'stock') !!}
											{!! Html::number('stock', null)->id('stock')->class('form-control') !!}
										</div>
										<div class="mb-3 col-12 col-sm-6">
											{!! Html::label('Category', 'category_id') !!}
											<select name="category_id" id="category_id" class="form-control">
												@foreach(Helpers::getShopCategoriesSelectArray() as $value => $label)
													<option value="{{ $value }}">{{ $label }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="row">
										<div class="mb-3 col-12 col-sm-6">
											{!! Html::label('Price (Real)', 'price') !!}
											{!! Html::text('price', null)->id('price')->class('form-control') !!}
										</div>
										<div class="mb-3 col-12 col-sm-6">
											{!! Html::label('Price Credit', 'price_credit') !!}
											{!! Html::text('price_credit', null)->id('price_credit')->class('form-control') !!}
										</div>
									</div>
									<div class="mb-3">
										{!! Html::label('Description', 'description') !!}
										{!! Html::textarea('description', null)->id('description')->class('form-control wysiwyg-editor')->rows(2) !!}
									</div>
									<button type="submit" class="btn btn-block btn-success">Submit</button>
								{!! Html::form()->close() !!}
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-6">
					<div class="card mb-3">
						<div class="card-header">
							<i class="fa fa-th-list fa-fw"></i> Categories
						</div>
						<div class="card-body">
							<div class="dataTable_wrapper">
								<table width="100%" class="table table-striped table-hover" id="dataTables-example">
									<thead>
										<tr>
											<th>Name</th>
											<th>No. of Items</th>
											<th>Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach ($categories as $category)
											<tr class="table-row" class="odd gradeX">
												<td>{{ $category->name }}</td>
												<td>{{ $category->getItemTotal() }}</td>
												<td>{{ $category->status }}</td>
												<td>
													<a href="/admin/shop/{{ $category->slug }}">
														<button class="btn btn-sm btn-block btn-success">Edit</button>
													</a>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
								{{ $categories->links() }}
							</div>
						</div>
					</div>
					
				</div>
			</div>
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-th-list fa-fw"></i> Items
				</div>
				<div class="card-body">
					{{ $items->links() }}
					<div class="row">
						@foreach ($items as $item)
							<div class="col-12 col-sm-4 col-md-3">
								@include ('layouts._partials._shop.item-preview', ['admin' => true])
							</div>
						@endforeach
					</div>
					{{ $items->links() }}
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-2">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-plus fa-fw"></i> Add Category
				</div>
				<div class="card-body">
					<div class="list-group">
						{!! Html::form('POST', '/admin/shop/category') !!}
							<div class="mb-3">
								{!! Html::label('Name', 'name') !!}
								{!! Html::text('name', null)->id('name')->class('form-control') !!}
							</div>
							<button type="submit" class="btn btn-block btn-success">Submit</button>
						{!! Html::form()->close() !!}
					</div>
				</div>
			</div>
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-info-circle fa-fw"></i> Settings
				</div>
				<div class="card-body">
					<p>The Shop settings has ben moved to the <a href="/admin/settings/systems">Opt System settings</a> </p>
				</div>
			</div>
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-info-circle fa-fw"></i> Enable/Disable
				</div>
				<div class="card-body">
					<p>The Shop can be used for buying merch, consumables etc. It is not recommended you do event ticket sales through this system.</p>
						{!! Html::form('POST', '/admin/settings/shop/disable') !!}
							<button type="submit" class="btn btn-block btn-danger">Disable</button>
						{!! Html::form()->close() !!}
				</div>
			</div>
		</div>
	@endif
</div>

@endsection
