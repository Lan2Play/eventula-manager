@extends ('layouts.admin-default')

@section ('page_title', 'Tickets - ' . $event->display_name)

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Ticket Type - {{ $ticket->name }}</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/events/">Events</a>
			</li>
			<li class="breadcrumb-item">
				<a href="/admin/events/{{ $event->slug }}">{{ $event->display_name }}</a>
			</li>
			<li class="breadcrumb-item">
				<a href="/admin/events/{{ $event->slug }}/tickets">Tickets</a>
			</li>
			<li class="breadcrumb-item active">
				{{ $ticket->name }}
			</li>
		</ol>
	</div>
</div>

@include ('layouts._partials._admin._event.dashMini')

<div class="row">
	<div class="col-lg-6">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-pencil fa-fw"></i> Edit
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/tickets/' . $ticket->id)) }}
					@if (isset($ticket) && !$ticket->tickets->isEmpty()) @php $priceLock = true; @endphp @endif

					@include ('layouts._partials._admin._event._tickets.form')
				{{ Form::close() }}
			</div>
		</div>

	</div>
	<div class="col-lg-6">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-bar-chart-o fa-fw"></i> Statistics
			</div>
			<div class="card-body">
				<div class="list-group">
					Chart me
					<h4>Money Made</h4>
					<p>{{ Settings::getCurrencySymbol() }}{{ $ticket->tickets()->count() * $ticket->price }}</p>
					<h4>Purchases</h4>
					<p>{{ $ticket->tickets()->count() }}</p>
					@if ($ticket->quantity > 0)
						<h4>Quantity</h4>
						<p>{{ $ticket->quantity }}</p>
					@endif
				</div>
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-user fa-fw"></i> Purchases
			</div>
			<div class="card-body">
				<div class="list-group">
					@foreach ($event->eventParticipants as $participant)
						@if ($participant->ticket_id == $ticket->id)
							<a href="/admin/events/{{ $event->slug }}/participants/{{ $participant->id }}" -actionclass="list-group-item">
								<i class="fa fa-comment fa-fw"></i> {{ $participant->user->username }}
								@if ($participant->user->steamid)
									- <span class="text-muted"><small>Steam: {{ $participant->user->steamname }}</small></span>
								@endif
								<span class="float-end text-muted small">
									<em>
										{{ date('d-m-Y H:i', strtotime($participant->created_at)) }}
									</em>
								</span>
							</a>
						@endif
					@endforeach
				</div>
			</div>
		</div>

	</div>
</div>

@endsection