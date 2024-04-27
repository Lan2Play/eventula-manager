@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' - ' . $event->display_name)

@section ('content')

<div class="container pt-1">

	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h1>
			{{$event->display_name}}
			@if ($event->status != 'PUBLISHED')
			<small> - {{ $event->status }}</small>
			@endif
		</h1>
		<h4>{!! $event->desc_short !!}</h4>
	</div>
	<div class="text-center">
		<nav class="subnavbar navbar navbar-expand-md bg-primary navbar-events" style="z-index: 1;">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggler collapsed" data-bs-toggle="collapse" data-bs-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="navbar-toggler-icon"></span>
					</button>
				</div>
				<div id="navbar" class="navbar-collapse collapse justify-content-md-center mb-3">
					<ul class="navbar-nav">
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#event">@lang('events.eventinfo')</a></li>
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#purchaseTickets">@lang('events.tickets')</a></li>
						@if (!$event->sponsors->isEmpty())
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#sponsors">@lang('events.sponsors')</a></li>
						@endif
						@if (!$event->seatingPlans->isEmpty() && (in_array('PUBLISHED', $event->seatingPlans->pluck('status')->toArray()) || in_array('PREVIEW', $event->seatingPlans->pluck('status')->toArray())) )
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#seating">@lang('events.seating')</a></li>
						@endif
						@if (!$event->private_participants || ($user && !$user->getAllTickets($event->id)->isEmpty()) )
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#attendees">@lang('events.attendees')</a></li>
						@endif
						@if (!$event->tournaments->isEmpty() && config('challonge.api_key') != null)
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#tournaments">@lang('events.tournaments')</a></li>
						@endif
						@if (!$event->timetables->isEmpty())
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#timetable">@lang('events.timetable')</a></li>
						@endif
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#yourTickets">@lang('events.yourtickets')</a></li>
						@if (!$event->polls->isEmpty())
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#polls">@lang('events.haveyoursay')</a></li>
						@endif

					</ul>
				</div>
				<!--/.nav-collapse -->
			</div>
			<!--/.container-fluid -->
		</nav>
		<div class="row">
			<div class="col-12">
				<h3>
					<strong>{{ max($event->capacity - $event->eventParticipants->count(), 0) }}/{{ $event->capacity }}</strong> @lang('events.ticketsavailable')
				</h3>
			</div>
			@if ($event->capacity > 0)
			<div class="col-12">
				<div class="progress">
					<div class="progress-bar bg-danger" role="progressbar" aria-valuenow="{{ ($event->eventParticipants->count() / $event->capacity) * 100}}" aria-valuemin="0" aria-valuemax="100" style="width: {{ ($event->eventParticipants->count() / $event->capacity) * 100}}%;">
						@lang('events.purchased')
					</div>
					<div class="progress-bar bg-success" style="width: {{ 100 - ($event->eventParticipants->count() / $event->capacity) * 100}}%;">
						<span class="visually-hidden">@lang('events.available')</span>
						@lang('events.available')
					</div>
				</div>
			</div>
			@endif
		</div>

	</div>

	<div class="row">
		<!-- EVENT INFORMATION -->
		<div class="col-md-12">
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<a name="event"></a>
				<h3><i class="fas fa-info me-3"></i>@lang('events.eventinfo')</h3>
			</div>
			<div class="row">
				<div class="col-12 col-sm-5">
					<p class="bg-success-light text-success padding">@lang('events.start'): {{ date('H:i d-m-Y', strtotime($event->start)) }}</p>
					<p class="bg-danger-light text-danger padding">@lang('events.end'): {{ date('H:i d-m-Y', strtotime($event->end)) }}</p>
					<p class="bg-info-light text-info padding">@if ($event->getSeatingCapacity() == 0) @lang('events.capacity'): {{ $event->capacity }} @endif @if ($event->getSeatingCapacity() != 0) @lang('events.seatingcapacity'): {{ $event->getSeatingCapacity() }} @endif</p>
				</div>
				<div class="col-12 col-sm-7">
					<p>{!! $event->desc_long !!}</p>
				</div>
			</div>
		</div>

		<!-- TICKETS -->
		<div class="col-md-12">
			<!-- PURCHASE TICKETS -->
			@if (!$event->tickets->isEmpty())
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<a name="purchaseTickets"></a>
				<h3><i class="fas fa-ticket-alt me-3"></i>@lang('events.purchasetickets')</h3>
			</div>
			<div class="row card-deck">
				@foreach ($event->tickets()->orderBy('event_ticket_group_id')->get() as $ticket)
				<div class="col-12 col-sm-4">
					<div class="card mb-3" disabled>
						<div class="card-body d-flex flex-column">
							<h3 class="card-title">{{$ticket->name}} @if ($event->capacity <= $event->eventParticipants->count()) - <strong>@lang('events.soldout')</strong> @endif</h3>
							@if ($ticket->quantity != 0)
							<small>
								@lang('events.limitedavailability')
							</small>
							@endif
							@if ($ticket->hasTicketGroup())
								<small>@lang('events.ticketgroup', ['ticketgroup' => $ticket->ticketGroup->name])</small>
							@endif
							<div class="row mt-auto">
								<div class="col-sm-12 col-12">
									<h3>{{ Settings::getCurrencySymbol() }}{{$ticket->price}}
										@if ($ticket->quantity != 0)
										<small>
											{{ $ticket->quantity - $ticket->participants()->count() }}/{{ $ticket->quantity }} @lang('events.available')
										</small>
										@endif
									</h3>
									@if ($user)
									{{ Form::open(array('url'=>'/tickets/purchase/' . $ticket->id)) }}
									@if (
									$event->capacity <= $event->eventParticipants->count()
										|| ($ticket->participants()->count() >= $ticket->quantity && $ticket->quantity != 0)
										)
										<div class="row">
											<div class="mb-3 col-sm-6 col-12">
												{{ Form::label('quantity','Quantity',array('id'=>'','class'=>'')) }}
												{{ Form::select('quantity', array(1 => 1), null, array('id'=>'quantity','class'=>'form-control', 'disabled' => true)) }}
											</div>
											<div class="mb-3 col-sm-6 col-12 d-flex">
												<button class="btn btn-md btn-primary btn-block mt-auto" disabled>@lang('events.soldout')</button>
											</div>
										</div>
										@elseif($ticket->sale_start && $ticket->sale_start >= date('Y-m-d H:i:s'))
										<h5>
											@lang('events.availablefrom', ['time' => date('H:i', strtotime($ticket->sale_start)), 'date'=> date ('d-m-Y', strtotime($ticket->sale_start))])
										</h5>
										@elseif(
										$ticket->sale_end && $ticket->sale_end <= date('Y-m-d H:i:s') || date('Y-m-d H:i:s')>= $event->end
											)
											<h5>
												@lang('events.ticketnolongavailable')
											</h5>
											@else
											<div class="row">
												<div class="mb-3 col-sm-6 col-12 ">
													{{ Form::label('quantity','Quantity',array('id'=>'','class'=>'')) }}
													{{ Form::select('quantity', Helpers::getTicketQuantitySelection($ticket, $ticket->quantity - $ticket->participants()->count()), null, array('id'=>'quantity','class'=>'form-control')) }}
												</div>
												<div class="mb-3 col-sm-6 col-12 d-flex">
													{{ Form::hidden('user_id', $user->id, array('id'=>'user_id','class'=>'form-control')) }}
													<button class="btn btn-md btn-primary btn-block mt-auto"><i class="fas fa-shopping-cart"></i> @lang('events.buy')</button>
												</div>
											</div>
											@endif
											{{ Form::close() }}
											@else
											<div class="alert alert-info">
												<h5>@lang('events.plslogintopurchaseticket')</h5>
											</div>
											@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>
			@endif
		</div>
	</div>

	<!-- SEATING -->
	@if (!$event->online_event &&
	!$event->seatingPlans->isEmpty() &&
	(
	in_array('PUBLISHED', $event->seatingPlans->pluck('status')->toArray()) ||
	in_array('PREVIEW', $event->seatingPlans->pluck('status')->toArray())
	)
	)
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<a name="seating"></a>
		<h3><i class="fas fa-chair me-3"></i>@lang('events.seatingplans') <small>- {{ $event->getSeatingCapacity() - $event->getSeatedCount() }} / {{ $event->getSeatingCapacity() }} @lang('events.seatsremaining')</small></h3>
	</div>
	<div class="card-group" id="accordion" role="tablist" aria-multiselectable="true">
		@foreach ($event->seatingPlans as $seatingPlan)
		@if ($seatingPlan->status != 'DRAFT')
		<div class="card mb-3">
			<a class="collapsed" role="button" data-bs-toggle="collapse" data-parent="#accordion" href="#collapse_{{ $seatingPlan->slug }}" aria-expanded="true" aria-controls="collapse_{{ $seatingPlan->slug }}">
				<div class="card-header  bg-success-light" role="tab" id="headingOne">
					<h4 class="card-title m-0">
						{{ $seatingPlan->name }} <small>- {{ $seatingPlan->getSeatingCapacity() - $seatingPlan->getSeatedCount() }} / {{ $seatingPlan->getSeatingCapacity() }} @lang('events.available')</small>
						@if ($seatingPlan->status != 'PUBLISHED')
						<small> - {{ $seatingPlan->status }}</small>
						@endif
					</h4>
				</div>
			</a>
			<div id="collapse_{{ $seatingPlan->slug }}" class="collapse @if ($loop->first) in @endif" role="tabpanel" aria-labelledby="collaspe_{{ $seatingPlan->slug }}">
				<div class="card-body">
					<div class="table-responsive text-center">
						<table class="table">

							<tbody>
								@for ($row = 1; $row <= $seatingPlan->rows; $row++)
									<tr>
										<td>
											<h4><strong>{{ Helpers::getLatinAlphabetUpperLetterByIndex($row) }}</strong></h4>
										</td>
										@for ($column = 1; $column <= $seatingPlan->columns; $column++)

											<td style="padding-top:14px;">
												@if ($event->getSeat($seatingPlan->id, $column, $row))
												@if($event->getSeat($seatingPlan->id, $column, $row)->status == 'ACTIVE')
												@if ($seatingPlan->locked)
												<button class="btn btn-success btn-sm" disabled>
													{{ Helpers::getLatinAlphabetUpperLetterByIndex($row) . $column }} - {{ $event->getSeat($seatingPlan->id, $column, $row)->eventParticipant->user->username }}
												</button>
												@else
												<button class="btn btn-success btn-sm" disabled>
													{{ Helpers::getLatinAlphabetUpperLetterByIndex($row) . $column }} - {{ $event->getSeat($seatingPlan->id, $column, $row)->eventParticipant->user->username }}
												</button>
												@endif
												@endif
												@else
												@if ($seatingPlan->locked)
												<button class="btn btn-primary btn-sm" disabled>
													{{ Helpers::getLatinAlphabetUpperLetterByIndex($row) . $column }} - @lang('events.empty')
												</button>
												@else
												@if (Auth::user() 
														&& $event->getEventParticipant() 
														&& ($event->getEventParticipant()->staff 
															|| $event->getEventParticipant()->free 
															|| $event->getEventParticipant()->ticket->seatable
														)
													)
												<button class="btn btn-primary btn-sm" onclick="pickSeat(
																				'{{ $seatingPlan->slug }}',
																				'{{ $column }}',
																				'{{ $row }}',
																				'{{ Helpers::getLatinAlphabetUpperLetterByIndex($row) . $column }}'
																			)" data-bs-toggle="modal" data-bs-target="#pickSeatModal">
													{{ Helpers::getLatinAlphabetUpperLetterByIndex($row) . $column }} - @lang('events.empty')
												</button>
												@else
												<button class="btn btn-primary btn-sm" disabled>
													{{ Helpers::getLatinAlphabetUpperLetterByIndex($row) . $column }} - @lang('events.empty')
												</button>
												@endif
												@endif
												@endif
											</td>
											@endfor
									</tr>
									@endfor
							</tbody>
						</table>
						@if ($seatingPlan->locked)
						<p class="text-center"><strong> @lang('events.seatingplanlocked')</strong></p>
						@endif
					</div>
				</div>
				<div class="card-footer">
					<div class="row" style="display: flex; align-items: center;">
						<div class="col-12 col-md-8">
							<img class="img-fluid" alt="{{ $seatingPlan->name }}" src="{{$seatingPlan->image_path}}" />
						</div>
						<div class="col-12 col-md-4">
							@if ($user && !$user->getAllTickets($event->id)->isEmpty() && $user->hasSeatableTicket($event->id))
							<h5>@lang('events.yourseats')</h5>
							@foreach ($user->getAllTickets($event->id) as $participant)
							@if ($participant->seat && $participant->seat->event_seating_plan_id == $seatingPlan->id)
							{{ Form::open(array('url'=>'/events/' . $event->slug . '/seating/' . $seatingPlan->slug)) }}
							{{ Form::hidden('_method', 'DELETE') }}
							{{ Form::hidden('user_id', $user->id, array('id'=>'user_id','class'=>'form-control')) }}
							{{ Form::hidden('participant_id', $participant->id, array('id'=>'participant_id','class'=>'form-control')) }}
							{{ Form::hidden('seat_column_delete', $participant->seat->column, array('id'=>'seat_column_delete','class'=>'form-control')) }}
							{{ Form::hidden('seat_row_delete', $participant->seat->row, array('id'=>'seat_row_delete','class'=>'form-control')) }}

							<h5>
								<button class="btn btn-success btn-block">
									@lang('events.remove') - {{ $participant->seat->getName() }}
								</button>
							</h5>
							{{ Form::close() }}
							@endif
							@endforeach
							@elseif($user && !$user->hasSeatableTicket($event->id))
							<div class="alert alert-info">
								<h5>@lang('events.noseatableticket')</h5>
							</div>
							@elseif(Auth::user())
							<div class="alert alert-info">
								<h5>@lang('events.plspurchaseticket')</h5>
							</div>
							@else
							<div class="alert alert-info">
								<h5>@lang('events.plslogintopurchaseticket')</h5>
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
		@endif
		@endforeach
	</div>
	@endif

	<!-- VENUE INFORMATION -->
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<a name="venue"></a>
		<h3><i class="fas fa-map-marked-alt me-3"></i>@lang('events.venueinformation')</h3>
	</div>
	<div class="row">
		<div class="col-lg-7">
			<address>
				<strong>{{ $event->venue->display_name }}</strong><br>
				@if (trim($event->venue->address_1) != '' || $event->venue->address_1 != null) {{ $event->venue->address_1 }}<br> @endif
				@if (trim($event->venue->address_2) != '' || $event->venue->address_2 != null) {{ $event->venue->address_2 }}<br> @endif
				@if (trim($event->venue->address_street) != '' || $event->venue->address_street != null) {{ $event->venue->address_street }}<br> @endif
				@if (trim($event->venue->address_city) != '' || $event->venue->address_city != null) {{ $event->venue->address_city }}<br> @endif
				@if (trim($event->venue->address_postcode) != '' || $event->venue->address_postcode != null) {{ $event->venue->address_postcode }}<br> @endif
				@if (trim($event->venue->address_country) != '' || $event->venue->address_country != null) {{ $event->venue->address_country }}<br> @endif
			</address>
		</div>
		<div class="col-lg-5">
			@foreach ($event->venue->images as $image)
			<picture>
				<source srcset="{{ $image->path }}.webp" type="image/webp">
				<source srcset="{{ $image->path }}" type="image/jpeg">
				<img class="img-fluid rounded" alt="{{ $event->venue->display_name }}" src="{{ $image->path }}" />
			</picture>
			@endforeach
		</div>
	</div>

	<!-- EVENT SPONSORS -->
	@if (!$event->sponsors->isEmpty())
		<div class="pb-2 mt-4 mb-4 border-bottom">
			<a name="sponsors"></a>
			<h3><i class="fas fa-running me-3"></i>@lang('events.eventsponsoredby', ['event' => $event->display_name])</h3>
		</div>
		@foreach ($event->sponsors as $sponsor)
			<a href="{{ $sponsor->website }}">
				<picture>
					<source srcset="{{ $sponsor->image_path }}.webp" type="image/webp">
					<source srcset="{{ $sponsor->image_path }}" type="image/jpeg">
					<img class="img-fluid rounded" src="{{ $sponsor->image_path }}" alt="{{ $sponsor->website}}" />
				</picture>
			</a>
		@endforeach
	@endif

	<!-- EVENT INFORMATION SECTIONS -->
	@if (!empty($event->information))
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h3><i class="fas fa-angle-double-right me-3"></i>@lang('events.therismore')</h3>
	</div>
	@php($x = 0)
	@foreach ($event->information as $section)
	<div class="row">
		@if ($x % 2 == 0)
		@if (isset($section->image_path))
		<div class="col-sm-4 d-block d-sm-none">
			<h4>{{$section->title}}</h4>
			<center>
				<picture>
					<source srcset="{{ $section->image_path }}.webp" type="image/webp">
					<source srcset="{{ $section->image_path }}" type="image/jpeg">
					<img class="img-fluid rounded" alt="{{ $section->title }}" src="{{ $section->image_path }}" />
				</picture>
			</center>
		</div>
		<div class="col-sm-8">
			<h4 class="d-none d-sm-block">{{$section->title}}</h4>
			<p>{!! $section->text !!}</p>
		</div>
		<div class="col-sm-4 d-none d-sm-block">
			@if (isset($section->image_path))
			<center>
				<picture>
					<source srcset="{{ $section->image_path }}.webp" type="image/webp">
					<source srcset="{{ $section->image_path }}" type="image/jpeg">
					<img class="img-fluid rounded" alt="{{ $section->title }}" src="{{ $section->image_path }}" />
				</picture>
			</center>
			@endif
		</div>
		@else
		<div class="col-sm-12">
			<h4>{{$section->title}}</h4>
			<p>{!! $section->text !!}</p>
		</div>
		@endif
		@else
		@if (isset($section->image_path))
		<div class="col-sm-4">
			<h4 class="d-block d-sm-none">{{$section->title}}</h4>
			<center>
				<picture>
					<source srcset="{{ $section->image_path }}.webp" type="image/webp">
					<source srcset="{{ $section->image_path }}" type="image/jpeg">
					<img class="img-fluid rounded" alt="{{ $section->title }}" src="{{ $section->image_path }}" />
				</picture>
			</center>
		</div>
		<div class="col-sm-8">
			<h4 class="d-none d-sm-block">{{$section->title}}</h4>
			<p>{!! $section->text !!}</p>
		</div>
		@else
		<div class="col-sm-12">
			<h4>{{$section->title}}</h4>
			<p>{!! $section->text !!}</p>
		</div>
		@endif
		@endif
	</div>
	<hr>
	@php($x++)
	@endforeach
	@endif

	<!-- TIMETABLE -->
	@if (!$event->timetables->isEmpty())
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<a name="timetable"></a>
		<h3><i class="fas fa-calendar-alt me-3"></i>@lang('events.timetable')</h3>
	</div>
	@foreach ($event->timetables->sortByDesc('primary') as $timetable)
	@if (strtoupper($timetable->status) == 'DRAFT')
	<h4>DRAFT</h4>
	@endif
	@if ($timetable->primary == '1')
    <div class="d-flex align-items-center">
        <h4 class="mb-1">{{ $timetable->name }} </h4>
        <span class="badge bg-primary ms-3">@lang('events.timetable-primary-pill')</span>
    </div>
@else
    <h4>{{ $timetable->name }}</h4>
@endif

	<p>
    @lang('events.timetable-created-at')
    {{ $timetable->created_at->toDateString() == now()->toDateString() ? $timetable->created_at->format('M d, H:i') : ($timetable->created_at->year == now()->year ? $timetable->created_at->format('M d') : $timetable->created_at->format('M d, Y')) }},
    @lang('events.timetable-updated-at')
    {{ $timetable->updated_at->toDateString() == now()->toDateString() ? $timetable->updated_at->format('M d, H:i') : ($timetable->updated_at->year == now()->year ? $timetable->updated_at->format('M d') : $timetable->updated_at->format('M d, Y')) }}
	</p>

	<table class="table table-striped">
		<thead>
			<th>
				@lang('events.time')
			</th>
			<th>
				@lang('events.game')
			</th>
			<th>
				@lang('events.description')
			</th>
		</thead>
		<tbody>
			@foreach ($timetable->data as $slot)
			@if ($slot->name != NULL && $slot->desc != NULL)
			<tr>
				<td>
					{{ date("D", strtotime($slot->start_time)) }} - {{ date("H:i", strtotime($slot->start_time)) }}
				</td>
				<td>
					{{ $slot->name }}
				</td>
				<td>
					{{ $slot->desc }}
				</td>
			</tr>
			@endif
			@endforeach
		</tbody>
	</table>
	@endforeach
	@endif

	<!-- POLLS-->
	@if ($event->polls->count() > 0)
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<a name="polls"></a>
		<h3><i class="fas fa-poll me-3"></i>@lang('events.haveyoursay')</h3>
	</div>
	@foreach ($event->polls as $poll)
	<h4>
		{{ $poll->name }}
		@if ($poll->status != 'PUBLISHED')
		<small> - {{ $poll->status }}</small>
		@endif
		@if ($poll->hasEnded())
		<small> - @lang('events.ended')</small>
		@endif
	</h4>
	@if (!empty($poll->description))
	<p>{{ $poll->description }}</p>
	@endif
	@include ('layouts._partials._polls.votes')
	@endforeach
	@endif

	<!-- MY TICKETS -->
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<a name="yourTickets"></a>
		<h3><i class="fas fa-ticket-alt me-3"></i>@lang('events.mytickets')</h3>
	</div>
	@if (Auth::user())
		@if (!$user->getAllTickets($event->id)->isEmpty())
			@foreach ($user->getAllTickets($event->id) as $participant)
				@include('layouts._partials._tickets.index')
			@endforeach
		@else
			<div class="alert alert-info">@lang('events.purchaseticketopickseat')</div>
		@endif
	@else
		<div class="alert alert-info">@lang('events.plslogintopurchaseticket')</div>
	@endif

	<!-- TOURNAMENTS -->
	@if (!$event->tournaments->isEmpty() && config('challonge.api_key') != null)
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<a name="tournaments"></a>
		<h3><i class="fas fa-trophy me-3"></i>@lang('events.tournaments')</h3>
	</div>
	<div class="row">
		@foreach ($event->tournaments as $tournament)
		@if ($tournament->status != 'DRAFT')
		<div class="col-12 col-sm-6 col-md-3">
			<a href="/events/{{ $event->slug }}/tournaments/{{ $tournament->slug }}" class="link-unstyled">
				<div class="card card-hover mb-3">
					<div class="card-header ">
						@if ($tournament->game && $tournament->game->image_thumbnail_path)
						<picture>
							<source srcset="{{ $tournament->game->image_thumbnail_path }}.webp" type="image/webp">
							<source srcset="{{ $tournament->game->image_thumbnail_path }}" type="image/jpeg">
							<img class="img img-fluid rounded" src="{{ $tournament->game->image_thumbnail_path }}" alt="{{ $tournament->game->name }}">
						</picture>
						@endif
						<h3 class="text-primary">{{ $tournament->name }}</h3>
					</div>
					<div class="card-body">
						<div class="thumbnail">
							<div class="caption">
								<span class="small">
									@if ($tournament->status == 'COMPLETE')
									<span class="badge text-bg-success">@lang('events.ended')</span>
									@endif
									@if ($tournament->status == 'LIVE')
									<span class="badge text-bg-success">@lang('events.live')</span>
									@endif
									@if ($tournament->status != 'COMPLETE' && $user && $user->active_event_participant && !$tournament->getParticipant($user->active_event_participant->id))
									<span class="badge text-bg-danger">@lang('events.notsignedup')</span>
									@endif
									@if ($tournament->status != 'COMPLETE' && $user && $user->active_event_participant && $tournament->getParticipant($user->active_event_participant->id))
									<span class="badge text-bg-success">@lang('events.signedup')</span>
									@endif
									@if ($tournament->status != 'COMPLETE' && $user && !$user->active_event_participant && $user->getAllTickets($event->id)->isEmpty())
									<span class="badge text-bg-info">@lang('events.purchaseticketosignup')</span>
									@else
									@if ($tournament->status != 'COMPLETE' && $user && !$user->active_event_participant && !$event->online_event)
									<span class="badge text-bg-info">@lang('events.signuponlywhenlive')</span>
									@endif
									@endif
								</span>
								@if ($tournament->status != 'COMPLETE')
								<dl>
									<dt>
										@lang('events.teamsizes'):
									</dt>
									<dd>
										{{ $tournament->team_size }}
									</dd>
									@if ($tournament->game)
									<dt>
										@lang('events.game'):
									</dt>
									<dd>
										{{ $tournament->game->name }}
									</dd>
									@endif
									<dt>
										@lang('events.format'):
									</dt>
									<dd>
										{{ $tournament->format }}
									</dd>
								</dl>
								@endif
								<!-- // TODO - refactor & add order on rank-->
								@if ($tournament->status == 'COMPLETE' && $tournament->format != 'list')
								@if ($tournament->team_size != '1v1')
								@foreach ($tournament->tournamentTeams->sortBy('final_rank') as $tournamentParticipant)
								@if ($tournamentParticipant->final_rank == 1)
								@if ($tournament->team_size == '1v1')
								<h2>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->eventParticipant->user->username }}</h2>
								@else
								<h2>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->name }}</h2>
								@endif
								@endif
								@if ($tournamentParticipant->final_rank == 2)
								@if ($tournament->team_size == '1v1')
								<h3>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->eventParticipant->user->username }}</h3>
								@else
								<h3>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->name }}</h3>
								@endif
								@endif
								@if ($tournamentParticipant->final_rank != 2 && $tournamentParticipant->final_rank != 1)
								@if ($tournament->team_size == '1v1')
								<h4>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->eventParticipant->user->username }}</h4>
								@else
								<h4>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->name }}</h4>
								@endif
								@endif
								@endforeach
								@endif
								@if ($tournament->team_size == '1v1')
								@foreach ($tournament->tournamentParticipants->sortBy('final_rank') as $tournamentParticipant)
								@if ($tournamentParticipant->final_rank == 1)
								@if ($tournament->team_size == '1v1')
								<h2>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->eventParticipant->user->username }}</h2>
								@else
								<h2>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->name }}</h2>
								@endif
								@endif
								@if ($tournamentParticipant->final_rank == 2)
								@if ($tournament->team_size == '1v1')
								<h3>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->eventParticipant->user->username }}</h3>
								@else
								<h3>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->name }}</h3>
								@endif
								@endif
								@if ($tournamentParticipant->final_rank != 2 && $tournamentParticipant->final_rank != 1)
								@if ($tournament->team_size == '1v1')
								<h4>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->eventParticipant->user->username }}</h4>
								@else
								<h4>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->name }}</h4>
								@endif
								@endif
								@endforeach
								@endif
								@endif
								<strong>
									{{ $tournament->tournamentParticipants->count() }} @lang('events.signups')
								</strong>
							</div>
						</div>
					</div>
				</div>
			</a>
		</div>
		@endif
		@endforeach
	</div>
	@endif

	@if (!$event->private_participants || ($user && !$user->getAllTickets($event->id)->isEmpty() ))
		<!-- ATTENDEES -->
		<div class="pb-2 mt-4 mb-4 border-bottom">
			<a name="attendees"></a>
			<h3><i class="fas fa-users me-3"></i>@lang('events.attendees')</h3>
		</div>
		<table class="table table-striped">
			<thead>
				<th width="15%">
				</th>
				<th>
					@lang('events.user')
				</th>
				<th>
					@lang('events.name')
				</th>
				<th>
					@lang('events.seat')
				</th>
			</thead>
			<tbody>
				@foreach ($event->eventParticipants as $participant)
				<tr>
					<td>
						<img class="img-fluid rounded img-small" style="max-width: 30%;" alt="{{ $participant->user->username}}'s Avatar" src="{{ $participant->user->avatar }}">
					</td>
					<td style="vertical-align: middle;">
						{{ $participant->user->username }}
						@if ($participant->user->steamid)
						- <span class="text-muted"><small>Steam: {{ $participant->user->steamname }}</small></span>
						@endif
					</td>
					<td style="vertical-align: middle;">
						{{$participant->user->firstname}}
					</td>
					<td style="vertical-align: middle;">
						@if ($participant->user->hasSeatableTicket($event->id))
						@if ($participant->seat)
						{{ $participant->seat->seatingPlan->getShortName() }} | {{ $participant->seat->getName() }}
						@else
						@lang('events.notseated')
						@endif
						@else
						@lang('events.noseatableticketlist')
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	@endif


</div>

<!-- Seat Modal -->
<div class="modal fade" id="pickSeatModal" tabindex="-1" role="dialog" aria-labelledby="editSeatingModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="pickSeatModalLabel"></h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			@if (Auth::user())
			{{ Form::open(array('url'=>'/events/' . $event->slug . '/seating/', 'id'=>'pickSeatFormModal')) }}
			<div class="modal-body">
				<div class="mb-3">
					<h4>@lang('events.wichtickettoseat')</h4>
					{{
								Form::select(
									'participant_id',
									$user->getTickets($event->id),
									null,
									array(
										'id'    => 'format',
										'class' => 'form-control'
									)
								)
							}}
					<p class="pt-2">@lang('events.wantthisseat')</p>
					<p>@lang('events.removeitanytime')</p>
				</div>
			</div>
			{{ Form::hidden('user_id', $user->id, array('id'=>'user_id','class'=>'form-control')) }}
			{{ Form::hidden('seat_column', null, array('id'=>'seat_column','class'=>'form-control')) }}
			{{ Form::hidden('seat_row', null, array('id'=>'seat_row','class'=>'form-control')) }}
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">@lang('events.yes')</button>
				<button type="button" class="btn btn-danger" data-bs-dismiss="modal">@lang('events.no')</button>
			</div>
			{{ Form::close() }}
			@endif
		</div>
	</div>
</div>

<script>
	function pickSeat(seating_plan_slug, seatColumn, seatRow, seatDisplay) {
		jQuery("#seat_column").val(seatColumn);
		jQuery("#seat_row").val(seatRow);
		jQuery("#seat_column_delete").val(seatColumn);
		jQuery("#seat_row_delete").val(seatRow);
		jQuery("#seat_number_modal").val(seatDisplay);
		jQuery("#pickSeatModalLabel").html('Do you what to choose seat ' + seatDisplay);
		jQuery("#pickSeatFormModal").prop('action', '/events/{{ $event->slug }}/seating/' + seating_plan_slug);
	}
</script>

@endsection