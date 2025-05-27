@extends ('layouts.admin-default')

@section ('page_title', 'Help')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Help</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item active">
				Help
			</li>
		</ol>
	</div>
</div>

<div class="row">
	@if (!$isHelpEnabled)
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-info-circle fa-fw"></i> Help is Currently Disabled...
				</div>
				<div class="card-body">
					<p>The Help System can be used to populate help articles.</p>
						{!! Html::form('POST', '/admin/settings/help/enable') !!}
							<button type="submit" class="btn btn-block btn-success">Enable</button>
						{!! Html::form()->close() !!}
				</div>
			</div>
		</div>
	@else
	<div class="col-lg-8">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-th-list fa-fw"></i> Categorys
			</div>
			<div class="card-body">
				<div class="dataTable_wrapper">
					<table width="100%" class="table table-striped table-hover" id="dataTables-example">
						<thead>
							<tr>
								<th>Name</th>
								<th>Description</th>
								<th>URL</th>
								<th>Status</th>
								<th># of Entrys</th>
								<th></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($helpCategorys as $helpCategory)
								<tr>
									<td>{{ $helpCategory->name }}</td>
									<td>{{ $helpCategory->description }}</td>
									<td>{{ $helpCategory->slug }}</td>
									<td>{{ $helpCategory->status }}</td>
									<td>{{ $helpCategory->entrys()->count() }}</td>
									<td width="15%">
										<a href="/admin/help/{{ $helpCategory->slug }}">
											<button type="button" class="btn btn-primary btn-sm btn-block">Edit</button>
										</a>
									</td>
									<td width="15%">
										{!! Html::form('POST', '/admin/help/' . $helpCategory->slug)->attribute('onsubmit', 'return ConfirmDelete()') !!}
											{!! Html::hidden('_method', 'DELETE') !!}
											<button type="submit" class="btn btn-danger btn-sm btn-block">Delete</button>
										{!! Html::form()->close() !!}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-plus fa-fw"></i> Add Category
			</div>
			<div class="card-body">
				<div class="list-group">
					{!! Html::form('POST', '/admin/help/') !!}
						<div class="mb-3">
							{!! Html::label('Category Name', 'name') !!}
							{!! Html::text('name', null)->id('name')->class('form-control') !!}
						</div>
						<div class="mb-3">
							{!! Html::label('Description', 'description') !!}
							{!! Html::textarea('description', null)->id('description')->class('form-control')->rows(2) !!}
						</div>
						<button type="submit" class="btn btn-success btn-block">Submit</button>
					{!! Html::form()->close() !!}
				</div>
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-info-circle fa-fw"></i> Enable/Disable
			</div>
			<div class="card-body">
				<p>The Help System can be used to populate help articles.</p>
					{!! Html::form('POST', '/admin/settings/help/disable') !!}
						<button type="submit" class="btn btn-block btn-danger">Disable</button>
					{!! Html::form()->close() !!}
			</div>
		</div>
	</div>
	@endif
</div>

<script type="text/javascript">
	jQuery( function() {
		jQuery( "#start_date" ).datepicker();
		jQuery( "#end_date" ).datepicker();
	});
</script>

@endsection
