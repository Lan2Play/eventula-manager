@extends ('layouts.admin-default')

@section ('page_title', 'Users - View '. $userShow->username)

@section ('content')
<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">{{ $userShow->username }}</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/users/">Users</a>
			</li>
			<li class="breadcrumb-item active">
				{{ $userShow->username }}
			</li>
		</ol>
	</div>
</div>

<div class="row">
	<div class="col-sm-12 col-lg-6">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-users fa-fw"></i> User
			</div>
			<div class="card-body">
				@if ($userShow->banned)
					<div class="alert alert-danger">This User has been banned!</div>
				@endif
				<div class="d-flex">
  					<div class="d-flex-left">
						<img class="d-flex-object" src="{{ $userShow->avatar }}">
			  		</div>
  					<div class="flex-grow-1">
						<ul class="list-group">
							<li class="list-group-item">Username: {{ $userShow->username }}</li>
							@if ($userShow->steamid) <li class="list-group-item">Steam: {{ $userShow->steamname }}</li> @endif
							<li class="list-group-item">Name: {{ $userShow->firstname }} {{ $userShow->surname }}</li>
							<li class="list-group-item">
								Admin: @if ($userShow->admin) Yes @else No @endif
							</li>
							<li class="list-group-item">
								Email Verified: @if ($userShow->email_verified_at) {{ $userShow->email_verified_at }} @else Not verified @endif
							</li>
							@if ($userShow->email != null)
								<li class="list-group-item">Email: {{ $userShow->email }}</li>
							@endif
							<li class="list-group-item" @if (!Settings::isUserLocaleEnabled()) style="opacity: .5;" @endif >
								User Locale:  {{ $userShow->locale }} @if (!Settings::isUserLocaleEnabled()) (User Locale is disabled systemwide) @endif
							</li>
						</ul>
  					</div>
  				</div>
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-header">
				<h3 class="card-title">Purchases</h3>
			</div>
			<div class="card-body">
				@if (count($userShow->purchases))
					<table class="table table-striped">
						<thead>
							<tr>
								<th>
									ID
								</th>
								<th>
									Method
								</th>
								<th>
									Time
								</th>
								<th>
									Basket
								</th>
								<th>
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($userShow->purchases as $purchase)
								<tr>
									<td>
										{{ $purchase->id }}
									</td>
									<td>
										{{ $purchase->getPurchaseType() }}
									</td>
									<td>
										{{  date('d-m-y H:i', strtotime($purchase->created_at)) }}
									</td>
									<td>
										@if ($purchase->getPurchaseContentType() == 'eventTickets')
											@foreach ($purchase->participants as $participant)
											    {{ $participant->event->display_name }} - 
											    @php
											        $labels = [];
											        if ($participant->ticket) {
											            $labels[] = $participant->ticket->name;
											        }
											        if ($participant->free == 1) {
											            $labels[] = 'Freebie';
											        }
											        if ($participant->staff == 1) {
											            $labels[] = 'Staff';
											        }
											        if (empty($labels)) {
											            $labels[] = 'No Ticket!';
											        }
											    @endphp
											    {{ implode(', ', $labels) }}
											    @if (!$loop->last)
											        <hr>
											    @endif
											@endforeach
										@elseif ($purchase->getPurchaseContentType() == 'shopOrder')
											@foreach ($purchase->order->items as $item)
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
												@if (!$loop->last)
													<hr>
												@endif
											@endforeach
										@endif
									</td>
									<td>
										<a href="/admin/purchases/{{ $purchase->id }}">
											<button class="btn btn-sm btn-block btn-success">View</button>
										</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{{ $purchases->links() }}
				@else
					User has no purchases
				@endif
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-lg-6">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-users fa-fw"></i> Options
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-12 col-sm-6">
						@if ($userShow->admin)
							{{ Form::open(array('url'=>'/admin/users/' . $userShow->id . '/admin')) }}
								{{ Form::hidden('_method', 'DELETE') }}
								<button type="submit" class="btn btn-block btn-info">Remove Admin</button>
							{{ Form::close() }}
						@else
							{{ Form::open(array('url'=>'/admin/users/' . $userShow->id . '/admin')) }}
								<button type="submit" class="btn btn-block btn-success">Make Admin</button>
							{{ Form::close() }}
						@endif
						<small>This will add or remove access to this admin panel. This means they can access everything! BE CAREFUL!</small>
					</div>
					@if ($userShow->email != null && $userShow->password != null)
						<div class="col-12 col-sm-6">
							{{ Form::open(array('url'=>'/login/forgot')) }}
								<input type="hidden" name="email" value="{{ $userShow->email }}">
								<button type="submit" class="btn btn-block btn-success">Reset Password</button>
							{{ Form::close() }}
							<small>This will reset the users password and sent a verification link to their email. If they are using a 3rd party Login this will do nothing.</small>
						</div>
					@endif
					{{-- @if ($userShow->email != null && $userShow->password == null)
					<div class="col-12 col-sm-6">
						{{ Form::open(array('url'=>'/login/forgot')) }}
							<input type="hidden" name="email" value="{{ $userShow->email }}">
							<button type="submit" class="btn btn-block btn-success">Send password set link</button>
						{{ Form::close() }}
						<small>This will send a verification link to the users email so a password can be setted.</small>
					</div>
					@endif --}}

				</div>
				<br>
				<h4>Danger Zone</h4>
				<hr>
				<div class="row">
					<div class="col-12 col-sm-6 mb-4">
						@if (!$userShow->banned)
							{{ Form::open(array('url'=>'/admin/users/' . $userShow->id . '/ban')) }}
								<button type="submit" class="btn btn-block btn-danger">Ban</button>
							{{ Form::close() }}
						@else
							{{ Form::open(array('url'=>'/admin/users/' . $userShow->id . '/unban')) }}
								<button type="submit" class="btn btn-block btn-success">Un-Ban</button>
							{{ Form::close() }}
						@endif
					</div>
					<div class="col-12 col-sm-6 mb-4">
						@if ($userShow->banned)
							{{ Form::open(array('url'=>'/admin/users/' . $userShow->id, 'onsubmit' => 'return ConfirmDelete()')) }}
								{{ Form::hidden('_method', 'DELETE') }}
								<button type="submit" class="btn btn-block btn-danger">Delete</button>
							{{ Form::close() }}
						@else
							<button type="submit" class="btn btn-block btn-danger" disabled="true">Delete</button>
						@endif
					</div>
					<div class="col-12 col-sm-6 mb-4">
						@if ($userShow->email_verified_at)
							{{ Form::open(array('url'=>'/admin/users/' . $userShow->id . '/removemailverification')) }}
								<button type="submit" class="btn btn-block btn-danger">Remove Email Verification</button>
							{{ Form::close() }}
						@else
							{{ Form::open(array('url'=>'/admin/users/' . $userShow->id . '/setemailverification')) }}
								<button type="submit" class="btn btn-block btn-success">Set Email Verification</button>
							{{ Form::close() }}
						@endif
					</div>
					@if ($userShow->steamname != null || $userShow->steamid != null)
					<div class="col-12 col-sm-6 mb-4">
						{{ Form::open(array('url'=>'/admin/users/' . $userShow->id . '/unauthorizeThirdparty/steam')) }}
							<button type="submit" class="btn btn-block btn-danger">Remove Steam account</button>
						{{ Form::close() }}
						<small>This will remove the Steam account of the user.</small>
					</div>
					@endif
				</div>
			</div>
		</div>
		@if ($creditLogs)
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-users fa-fw"></i> Add Credit
				</div>
				<div class="card-body">
					{{ Form::open(array('url'=>'/admin/credit/edit')) }}
						<div class="mb-3">
							{{ Form::hidden('user_id', $userShow->id) }}
							{{ Form::label('amount','Amount',array('id'=>'','class'=>'')) }}
							{{ Form::number('amount', '',array('id'=>'amount','class'=>'form-control')) }}
						</div>
						<button type="submit" class="btn btn-block btn-success">Submit</button>
					{{ Form::close() }}
				</div>
			</div>
			<div class="card mb-3">
				<div class="card-header">
					<h3 class="card-title">Credit - {{ $userShow->credit_total }}</h3>
				</div>
				<div class="card-body">
					<table width="100%" class="table table-striped table-hover" id="dataTables-example">
						<thead>
							<tr>
								<th>Action</th>
								<th>Amount</th>
								<th>Item</th>
								<th>Reason</th>
								<th>Timestamp</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($userShow->creditLogs->reverse() as $creditLog)
							<tr class="table-row" class="odd gradeX">
								<td>{{ $creditLog->action }}</td>
								<td>{{ $creditLog->amount }}</td>
								<td>
									@if (strtolower($creditLog->action) == 'buy')
										@if (!$creditLog->purchase->getPurchaseContentType() == 'eventTickets')
											@foreach ($creditLog->purchase->participants as $participant)
												{{ $participant->event->display_name }} - {{ $participant->ticket->name }}
												@if (!$loop->last)
													<hr>
												@endif
											@endforeach
										@elseif ($creditLog->purchase->getPurchaseContentType() == 'shopOrder')
											@foreach ($creditLog->purchase->order->items as $item)
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
												@if (!$loop->last)
													<hr>
												@endif
											@endforeach
										@endif
									@endif
								</td>
								<td>{{ $creditLog->reason }}</td>
								<td>
									{{ $creditLog->updated_at }}
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					{{ $creditLogs->links() }}
				</div>
			</div>
		@endif
	</div>
</div>

@endsection