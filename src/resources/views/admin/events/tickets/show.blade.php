@extends ('layouts.admin-default')

@section ('page_title', 'Tickets - ' . $event->display_name)

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Ticket Type - {{ $ticketType->name }}</h3>
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
				{{ $ticketType->name }}
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
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/tickets/' . $ticketType->id)) }}
					@if (isset($ticketType) && !$ticketType->tickets->isEmpty()) @php $priceLock = true; @endphp @endif

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
					<p>{{ Settings::getCurrencySymbol() }}{{ $ticketType->tickets()->count() * $ticketType->price }}</p>
					<h4>Purchases</h4>
					<p>{{ $ticketType->tickets()->count() }}</p>
					@if ($ticketType->quantity > 0)
						<h4>Quantity</h4>
						<p>{{ $ticketType->quantity }}</p>
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
					@foreach ($event->tickets as $ticket)
						@if ($ticket->ticket_type_id == $ticketType->id)
							<a href="/admin/events/{{ $event->slug }}/participants/{{ $ticket->id }}" class="list-group-item">
								<i class="fa fa-comment fa-fw"></i> {{ $ticket->user->username }}
								@if ($ticket->user->steamid)
									- <span class="text-muted"><small>Steam: {{ $ticket->user->steamname }}</small></span>
								@endif
								<span class="float-end text-muted small">
									<em>
										{{ date('d-m-Y H:i', strtotime($ticket->created_at)) }}
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