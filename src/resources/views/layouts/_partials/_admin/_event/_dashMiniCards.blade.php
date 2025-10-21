<div class="row card-deck">

	@if ($event->status == 'PUBLISHED')
		@php
			$eventStatusColor = 'success';
			$eventStatusIcon = 'fa-check-circle-o';
		@endphp
	@elseif ($event->status == 'REGISTEREDONLY')
		@php
			$eventStatusColor = 'info';
			$eventStatusIcon = 'fa-check-circle-o';
		@endphp
	@elseif ($event->status == 'DRAFT')
		@php
			$eventStatusColor = 'danger';
			$eventStatusIcon = 'fa-times-circle-o';
		@endphp
	@elseif ($event->status == 'PREVIEW' || $event->status == 'PRIVATE')
		@php
			$eventStatusColor = 'warning';
			$eventStatusIcon = 'fa-ban';
		@endphp
	@else
		@php
			$eventStatusColor = 'success';
			$eventStatusIcon = 'fa-question-circle ';
		@endphp
	@endif
    <div class="col-6 col-md-4 col-xl-2 d-flex align-items-stretch">
        <div class="card event-card mb-3 d-flex">
		<div class="card-header text-bg-{{ $eventStatusColor }} d-flex flex-column flex-md-row flex-wrap flex-grow-1">
			<div class="icon-large pe-0 mx-auto mx-md-0">
				<i class="fa {{ $eventStatusIcon }} fa-3x fa-md-4x fa-lg-5x fa-xl-7x"></i>
			</div>
			<div class="ms-md-auto text-center text-md-end align-self-end">
				<div class="huge ms-auto">
					@if ($event->status == 'PUBLISHED')
						<small>Live</small>
					@elseif ($event->status == 'REGISTEREDONLY')
						<small>Registered only</small>
					@elseif ($event->status == 'DRAFT')
						<small>Draft</small>
					@elseif ($event->status == 'PREVIEW')
						<small>Preview</small>
					@elseif ($event->status == 'PRIVATE')
						<small>Private</small>
					@else
						<small>{{ $event->status }}</small>
					@endif
				</div>
				<div>Event Status</div>
			</div>
		</div>
			<a href="/admin/events/{{ $event->slug }}" class="card-footer d-flex flex-nowrap">
				<span class="float-start text-nowrap">View Event Page</span>
				<span class="float-end ms-auto"><i class="fa fa-arrow-circle-right"></i></span>
				<div class="clearfix"></div>
			</a>
		</div>
	</div>
    <div class="col-6 col-md-4 col-xl-2 d-flex align-items-stretch">
        <div class="card event-card mb-3">
		<div class="card-header d-flex flex-column flex-md-row flex-wrap flex-grow-1">
			<div class="icon-large pe-0 mx-auto mx-md-0">
				<i class="fa fa-wheelchair fa-3x fa-md-4x fa-lg-5x fa-xl-7x"></i>
			</div>
			<div class="col-auto ms-md-auto text-center text-md-end ps-0 align-self-end">
				<div class="huge ms-auto">{{ $event->getSeatedCount() }}</div>
				<div>Seated Participants</div>
			</div>
		</div>
			<a href="/admin/events/{{ $event->slug }}/seating" class="card-footer d-flex flex-nowrap">
				<span class="float-start text-nowrap text-nowrap">View Seating Plans</span>
				<span class="float-end ms-auto"><i class="fa fa-arrow-circle-right"></i></span>
				<div class="clearfix"></div>
			</a>
		</div>
	</div>
    <div class="col-6 col-md-4 col-xl-2 d-flex align-items-stretch">
        <div class="card event-card mb-3">
		<div class="card-header d-flex flex-column flex-md-row flex-wrap flex-grow-1">
			<div class="icon-large pe-0 mx-auto mx-md-0">
				<i class="fa fa-list-ol fa-3x fa-md-4x fa-lg-5x fa-xl-7x"></i>
			</div>
			<div class="col-auto ms-md-auto text-center text-md-end ps-0 align-self-end">
				<div class="huge ms-auto">{{ $event->tournaments->count() }}</div>
				<div>Tournaments</div>
			</div>
		</div>
			<a href="/admin/events/{{ $event->slug }}/tournaments" class="card-footer d-flex flex-nowrap">
				<span class="float-start text-nowrap">View Tournaments</span>
				<span class="float-end ms-auto"><i class="fa fa-arrow-circle-right"></i></span>
				<div class="clearfix"></div>
			</a>
		</div>
	</div>
    <div class="col-6 col-md-4 col-xl-2 d-flex align-items-stretch">
        <div class="card event-card mb-3">
		<div class="card-header d-flex flex-column flex-md-row flex-wrap flex-grow-1">
			<div class="icon-large pe-0 mx-auto mx-md-0">
				<i class="fa fa-user fa-3x fa-md-4x fa-lg-5x fa-xl-7x"></i>
			</div>
			<div class="col-auto ms-md-auto text-center text-md-end ps-0 align-self-end">
				<div class="huge ms-auto">{{ $event->getUniqueUsersWithTickets() }}</div>
				<div>Attendees</div>
			</div>
		</div>
			<a href="/admin/events/{{ $event->slug }}/participants" class="card-footer d-flex flex-nowrap">
				<span class="float-start text-nowrap">View Attendees</span>
				<span class="float-end ms-auto"><i class="fa fa-arrow-circle-right"></i></span>
				<div class="clearfix"></div>
			</a>
		</div>
	</div>
    <div class="col-6 col-md-4 col-xl-2 d-flex align-items-stretch">
        <div class="card event-card mb-3">
		<div class="card-header d-flex flex-column flex-md-row flex-wrap flex-grow-1">
			<div class="icon-large pe-0 mx-auto mx-md-0">
				<i class="fa fa-ticket fa-3x fa-md-4x fa-lg-5x fa-xl-7x"></i>
			</div>
			<div class="col-auto ms-md-auto text-center text-md-end ps-0 align-self-end">
				<div class="huge ms-auto">{{ Settings::getCurrencySymbol() }}{{ $event->getTicketSalesCount() }}</div>
				<div>Ticket Sales</div>
			</div>
		</div>
			<a href="/admin/events/{{ $event->slug }}/tickets" class="card-footer d-flex flex-nowrap">
				<span class="float-start text-nowrap">View Tickets</span>
				<span class="float-end ms-auto"><i class="fa fa-arrow-circle-right"></i></span>
				<div class="clearfix"></div>
			</a>
		</div>
	</div>
    <div class="col-6 col-md-4 col-xl-2 d-flex align-items-stretch">
        <div class="card event-card mb-3">
		<div class="card-header d-flex flex-column flex-md-row flex-wrap flex-grow-1">
			<div class="icon-large pe-0 mx-auto mx-md-0">
				<i class="fa fa-calendar fa-3x fa-md-4x fa-lg-5x fa-xl-7x"></i>
			</div>
			<div class="col-auto ms-md-auto text-center text-md-end ps-0 align-self-end">
				<div class="huge ms-auto">{{ $event->getTimetableDataCount() }}</div>
				<div>Scheduled Slots</div>
			</div>
		</div>
			<a href="/admin/events/{{ $event->slug }}/timetables" class="card-footer d-flex flex-nowrap">
				<span class="float-start text-nowrap">View Timetables</span>
				<span class="float-end ms-auto"><i class="fa fa-arrow-circle-right"></i></span>
				<div class="clearfix"></div>
			</a>
		</div>
	</div>
</div>
