@extends ('layouts.admin-default')

@section ('page_title', 'Tickets - ' . $event->display_name)

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Tickets</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/events/">Events</a>
			</li>
			<li class="breadcrumb-item">
				<a href="/admin/events/{{ $event->slug }}">{{ $event->display_name }}</a>
			</li>
			<li class="breadcrumb-item active">
				Tickets
			</li>
		</ol>
	</div>
</div>

@include ('layouts._partials._admin._event.dashMini')

<div class="row">
	<div class="col-lg-8">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-bar-chart-o fa-fw"></i> Ticket Breakdown
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-6 col-12">
						<h4>Purchase Breakdown</h4>
						<div id="ticket-purchase-breakdown"></div>
					</div>
					<div class="col-sm-6 col-12">
						<h4>Income Breakdown</h4>
						<div id="ticket-income-breakdown"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-layer-group"></i> Ticket Groups
			</div>
			<div class="card-body table-responsive">
				<table class="table table-striped table-hover">
					<thead>
					<tr>
						<th>Name</th>
						<th>Tickets in group</th>
						<th>No. tickets per user</th>
						<th colspan="2">Actions</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td><i>ungrouped</i></td>
						<td>{{ $event->getUngroupedTickets()->count() }}</td>
						<td>N/A</td>
						<td colspan="2"></td>
					</tr>
					@foreach($event->ticketGroups as $group)
						<tr>
							<td>{{ $group->name }}</td>
							<td>{{ $group->tickets->count() }}</td>
							<td>{{ $group->tickets_per_user }}</td>
							<td>
								<a class="btn btn-primary btn-sm"
								   href="/admin/events/{{ $event->slug }}/ticketgroups/{{ $group->id }}">Edit</a>
							</td>
							<td>
								{{ Form::open([
									'url' => "/admin/events/{$event->slug}/ticketgroups/{$group->id}",
									'onsubmit' => 'return ConfirmSubmit()'
								]) }}
								{{ Form::hidden('_method', 'DELETE') }}
								<button type="submit" class="btn btn-danger btn-sm">Delete</button>
								{{ Form::close() }}
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>

    {{-- TODO: Replace Morris --}}
		{{-- <script>
			Morris.Donut({
				element: 'ticket-purchase-breakdown',
				data: [
					@foreach ($event->tickets as $ticket)
						{label: '{{ $ticket->name }}', value: {{ $ticket->participants()->count() }} },
					@endforeach
				]
			});
			Morris.Bar({
				element: 'ticket-income-breakdown',
				data: [
					@foreach ($event->tickets as $ticket)
						{ y: '{{ $ticket->name }}', a: {{ $ticket->price * $ticket->participants()->count() }} },
					@endforeach
				],
				xkey: 'y',
				ykeys: ['a'],
				labels: ['Pounds']
			});
		</script> --}}
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-ticket fa-fw"></i> Tickets
			</div>
			<div class="card-body table-responsive">
				<table width="100%" class="table table-striped table-hover" id="dataTables-example">
					<thead>
						<tr>
							<th>Name</th>
							<th>Type</th>
							<th>Ticket group</th>
							<th>Price</th>
							<th>Quantity</th>
							<th>Purchased</th>
							<th>Purchase Period</th>
							<th>Seatable</th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($event->tickets as $ticket)
							<tr class="table-row odd gradeX" data-href="/admin/events/{{ $event->slug }}/tickets/{{ $ticket->id }}">
								<td>
									{{ $ticket->name }}
								</td>
								<td>
									{{ $ticket->type }}
								</td>
								<td>
									@if ($ticket->ticketGroup)
										{{ $ticket->ticketGroup->name }}
									@else
										<i>ungrouped</i>
									@endif
								</td>
								<td>
									{{ $ticket->price }}
								</td>
								<td>
									@if ($ticket->quantity == 0)
										N/A
									@else
										{{ $ticket->quantity }}
									@endif
								</td>
								<td>
									{{ $ticket->participants()->count() }}
								</td>
								<td>
									Start:
									@if ($ticket->sale_start)
										{{ date('H:i d-m-Y', strtotime($ticket->sale_start)) }}
									@else
										N/A
									@endif
									-
									End:
									@if ($ticket->sale_end)
										{{ date('H:i d-m-Y', strtotime($ticket->sale_end)) }}
									@else
										N/A
									@endif
								</td>
								<td>
									@if ($ticket->seatable)
										Yes
									@else
										No
									@endif
								</td>
								<td width="15%">
									<a href="/admin/events/{{ $event->slug }}/tickets/{{ $ticket->id }}">
										<button type="button" class="btn btn-primary btn-sm btn-block">Edit</button>
									</a>
								</td>
								<td width="15%">
									{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/tickets/' . $ticket->id, 'onsubmit' => 'return ConfirmDelete()')) }}
										{{ Form::hidden('_method', 'DELETE') }}
										<button type="submit" class="btn btn-danger btn-sm btn-block" data-confirm="Are you sure to delete this Ticket?">Delete</button>
									{{ Form::close() }}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-gift fa-fw"></i> Freebies
				<a name="freebies"></a>
			</div>
			<div class="card-body table-responsive">
				<table width="100%" class="table table-striped table-hover" id="dataTables-example">
					<thead>
						<tr>
							<th>Name</th>
							<th>Free Tickets</th>
							<th>Staff Tickets</th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ( $users as $user )
							<tr class="table-row odd gradeX">
								<td>
									{{ $user->username }}
									@if ($user->steamid)
										<br><span class="text-muted"><small>Steam: {{ $user->steamname }}</small></span>
									@endif
								</td>
								<td>
									{{ $user->getFreeTickets($event->id)->count() }}
								</td>
								<td>
									{{ $user->getStaffTickets($event->id)->count() }}
								</td>
								<td width="15%">
									{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/freebies/gift')) }}
										<input type="hidden" name="user_id" value="{{ $user->id }}" />
										<button type="submit" name="action" class="btn btn-success btn-sm btn-block">Free Ticket</button>
									{{ Form::close() }}
								</td>
								<td width="15%">
									{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/freebies/admin')) }}
										<input type="hidden" name="user_id" value="{{ $user->id }}" />
										<button type="submit" name="action" class="btn btn-success btn-sm btn-block">Admin Ticket</button>
									{{ Form::close() }}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-4">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-plus fa-fw"></i> Add Ticket Group
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/ticketgroups')) }}
				@include('layouts._partials._admin._event._tickets.ticket-group-form')
				{{ Form::close() }}
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-plus fa-fw"></i> Add Tickets
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/tickets')) }}
					@include('layouts._partials._admin._event._tickets.form', ['empty' => true])
				{{ Form::close() }}
			</div>
		</div>

	</div>
</div>

@endsection