@extends ('layouts.admin-default')

@section ('page_title', 'Users')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Users</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item active">
				Users
			</li>
		</ol>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-users fa-fw"></i> Users

					<div class="row">
					  <div class="col-sm">
					  </div>
					  <div class="col-sm">
						
					  </div>
					  <div class="col-sm">
							{{ Form::open(array('url'=>'/admin/users/')) }}
							{{ Form::hidden('_method', 'GET') }}
							<div class="mb-3">
								{{ Form::text('searchquery', request()->get('searchquery') ?? null , array('id'=>'searchquery','class'=>'form-control')) }}
								{{ Form::submit('Search', array('class' => 'btn float-right btn-primary btn-sm btn-block', 'style' => 'margin-top:-2px;')) }}
							</div>
							{{ Form::close() }}
					  </div>
					</div>


			</div>
			<div class="card-body">
				<table class="table table-striped table-hover table-responsive">
					<thead>
						<tr>
							<th>Avatar</th>
							<th>User</th>
							<th>Name</th>
							<th>Contact</th>
							@if (Settings::isCreditEnabled())
							<th>Credit</th>
							@endif
							<th>Admin</th>
							<th>Email Verified</th>
							<th>Edit</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($users as $user)
						<tr>
							<td width="3%">
								<picture>
									<img class="img-fluid rounded" src="{{ $user->avatar }}">
								</picture>
							</td>
							<td>
								{{ $user->username }}
								@if ($user->steamid)
								- <span class="text-muted"><small>Steam: {{ $user->steamname }}</small></span>
								@endif
							</td>
							<td>{{ $user->firstname }} {{ $user->surname }}</td>
							<td>{{ $user->email }} @if (isset($user->phonenumber) && !empty($user->phonenumber)) <br /><a href="tel:{{ $user->phonenumber }}">{{ $user->phonenumber }}</a>@endif</td>
							@if (Settings::isCreditEnabled())
							<td>
								{{ $user->credit_total }}
							</td>
							@endif
							<td>
								@if ($user->admin)
								Yes
								@else
								No
								@endif
							</td>
							<td>
								@if ($user->email_verified_at)
								{{ $user->email_verified_at }}
								@else
								Not verified
								@endif
							</td>
							<td>
								<a href="/admin/users/{{ $user->id }}">
									<button class="btn btn-primary btn-sm btn-block">Edit</button>
								</a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				{{ $users->links() }}
			</div>
		</div>

	</div>
</div>

@endsection