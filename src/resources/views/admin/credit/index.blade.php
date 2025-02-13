@extends ('layouts.admin-default')

@section ('page_title', 'Credit System')

@section ('content')
<div class="row">
	<div class="col-lg-12">
		<h1 class="pb-2 mt-4 mb-4 border-bottom">Credit System</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item active">
				Credit System
			</li>
		</ol>
	</div>
</div>

<div class="row">
	@if (!$isCreditEnabled)
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-info-circle fa-fw"></i> Credit is Currently Disabled...
				</div>
				<div class="card-body">
					<p>The Credit System is used to award participants with credit they can use to buy things from the shop and events. It can be award for buying tickets, attending events, participanting/winning tournaments or manually assigned.</p>
					@if ($isCreditEnabled)
						{{ Form::open(array('url'=>'/admin/settings/credit/disable')) }}
							<button type="submit" class="btn btn-block btn-danger">Disable</button>
						{{ Form::close() }}
					@else
						{{ Form::open(array('url'=>'/admin/settings/credit/enable')) }}
							<button type="submit" class="btn btn-block btn-success">Enable</button>
						{{ Form::close() }}
					@endif
				</div>
			</div>
		</div>
	@else
		<div class="col-12 col-sm-10">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-credit-card fa-fw"></i> Logs
				</div>
				<div class="card-body">
					<table width="100%" class="table table-striped table-hover" id="dataTables-example">
						<thead>
							<tr>
								<th>Action</th>
								<th>User</th>
								<th>Amount</th>
								<th>Item</th>
								<th>Reason</th>
								<th>Added By</th>
								<th>Timestamp</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($creditLogs->reverse() as $creditLog)
								<tr class="table-row" class="odd gradeX">
									<td>{{ $creditLog->action }}</td>
									<td>{{ $creditLog->user->username }}</td>
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
										@if ($creditLog->admin_id == null)
											System Automated
										@else
											{{ $creditLog->admin->username }}
										@endif
									</td>
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
		</div>
		<div class="col-12 col-sm-2">
			<div class="card mb-3" >
				<div class="card-header">
					<i class="fa fa-credit-card fa-fw"></i> Add/Subtract Credit
				</div>
				<div class="card-body">
					{{ Form::open(array('url'=>'/admin/credit/edit')) }}
						<div class="mb-3">
							{{ Form::label('user_id','User',array('id'=>'','class'=>'')) }}
							{{-- {{ Form::text('user', '',array('id'=>'user','class'=>'form-control typeahead')) }} --}}
							{!! Form::select('user_id', $users, null, ['multiple' => false, 'class' => 'form-control margin', 'id' => 'user_id']) !!}

						</div>
						<div class="mb-3">
							{{ Form::label('amount','Amount',array('id'=>'','class'=>'')) }}
							{{ Form::number('amount', '',array('id'=>'amount','class'=>'form-control')) }}
							<small>to subtract the amount, just use a negative number</small>
						</div>
						<button class="btn btn-success btn-block">Submit</button>
					{{ Form::close() }}
				</div>
			</div>
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-info-circle fa-fw"></i> Settings
				</div>
				<div class="card-body">
					<p>The Credit settings has ben moved to the <a href="/admin/settings/systems">Opt System settings</a> </p>
				</div>
			</div>
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-info-circle fa-fw"></i> Enable/Disable
				</div>
				<div class="card-body">
					<p>The Credit System is used to award participants with credit they can use to buy things from the shop and events. It can be award for buying tickets, attending events, participanting/winning tournaments or manually assigned.</p>
					@if ($isCreditEnabled)
						{{ Form::open(array('url'=>'/admin/settings/credit/disable')) }}
							<button type="submit" class="btn btn-block btn-danger">Disable</button>
						{{ Form::close() }}
					@else
						{{ Form::open(array('url'=>'/admin/settings/credit/enable')) }}
							<button type="submit" class="btn btn-block btn-success">Enable</button>
						{{ Form::close() }}
					@endif
				</div>
			</div>
		</div>
		
	@endif
</div>

@endsection