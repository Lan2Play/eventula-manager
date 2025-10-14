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
						<button type="button" class="navbar-toggler collapsed" data-bs-toggle="collapse"
								data-bs-target="#navbar" aria-expanded="false" aria-controls="navbar">
							<span class="navbar-toggler-icon"></span>
						</button>
					</div>
					<div id="navbar" class="navbar-collapse collapse justify-content-md-center mb-3">
						<ul class="navbar-nav">
							<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link"
																							  href="#event">@lang('events.eventinfo')</a>
							</li>
							<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link"
																							  href="#purchaseTickets">@lang('events.tickets')</a>
							</li>
							@if (!$event->sponsors->isEmpty())
								<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link"
																								  href="#sponsors">@lang('events.sponsors')</a>
								</li>
							@endif
							@if (!$event->seatingPlans->isEmpty() && (in_array('PUBLISHED', $event->seatingPlans->pluck('status')->toArray()) || in_array('PREVIEW', $event->seatingPlans->pluck('status')->toArray())) )
								<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link"
																								  href="#seating">@lang('events.seating')</a>
								</li>
							@endif
							@if (!$event->private_participants || ($user && isset($userTickets) && !$userTickets->isEmpty()) )
								<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link"
																								  href="#attendees">@lang('events.attendees')</a>
								</li>
							@endif
							@if (!$event->tournaments->isEmpty() && config('challonge.api_key') != null)
								<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link"
																								  href="#tournaments">@lang('events.tournaments')</a>
								</li>
							@endif
							@if (!$event->timetables->isEmpty())
								<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link"
																								  href="#timetable">@lang('events.timetable')</a>
								</li>
							@endif
							<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link"
																							  href="#yourTickets">@lang('events.yourtickets')</a>
							</li>
							@if (!$event->polls->isEmpty())
								<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link"
																								  href="#polls">@lang('events.haveyoursay')</a>
								</li>
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
						<strong>{{ max($event->capacity - $event->tickets->count(), 0) }}
							/{{ $event->capacity }}</strong> @lang('events.ticketsavailable')
					</h3>
				</div>
				@if ($event->capacity > 0)
					<div class="col-12">
						<div class="progress">
							<div class="progress-bar bg-danger" role="progressbar"
								 aria-valuenow="{{ ($event->tickets->count() / $event->capacity) * 100}}"
								 aria-valuemin="0" aria-valuemax="100"
								 style="width: {{ ($event->tickets->count() / $event->capacity) * 100}}%;">
								@lang('events.purchased')
							</div>
							<div class="progress-bar bg-success"
								 style="width: {{ 100 - ($event->tickets->count() / $event->capacity) * 100}}%;">
								<span class="visually-hidden">@lang('events.available')</span>
								@lang('events.available')
							</div>
						</div>
					</div>
				@endif
			</div>

		</div>

		@include ('layouts._partials._events.information')
		<!-- TICKETS -->
		<div class="col-md-12">
			<!-- PURCHASE TICKETS -->
			@if (!$event->ticketTypes->isEmpty())
				<div class="pb-2 mt-4 mb-4 border-bottom">
					<a name="purchaseTickets"></a>
					<h3><i class="fas fa-ticket-alt me-3"></i>@lang('events.purchasetickets')</h3>
				</div>
				<div class="row card-deck">
                    @php
                    // Cache currency symbol to avoid repeated database calls
                    $currencySymbol = Settings::getCurrencySymbol();
                    $globalTicketTypeHidePolicy = Settings::getGlobalTicketTypeHidePolicy();
                    $eventTicketTypeHidePolicy = $event->tickettype_hide_policy;
                    
                    // Pre-calculate ticket counts for each ticket type to avoid N+1 queries
                    $ticketTypeCounts = [];
                    foreach ($event->ticketTypes as $tt) {
                        $ticketTypeCounts[$tt->id] = $tt->tickets->count();
                    }
                    
                    // Use eager-loaded ticketTypes and sort in memory instead of new query
                    $sortedTicketTypes = $event->ticketTypes->sortBy('event_ticket_group_id');
                    @endphp
					@foreach ($sortedTicketTypes as $ticketType)
                    @php
                    $ticketTypeHidePolicy = $ticketType->tickettype_hide_policy;
                    $effectivePolicy = -1;
                    if ($ticketTypeHidePolicy != -1) {
                        $effectivePolicy = $ticketTypeHidePolicy;
                    } elseif ($eventTicketTypeHidePolicy != -1) {
                        $effectivePolicy = $eventTicketTypeHidePolicy;
                    } else {
                        $effectivePolicy = $globalTicketTypeHidePolicy;
                    }
                    if ($ticketType->isHiddenByPolicy($effectivePolicy)) {
                        continue;
                    }
                        @endphp
						<div class="col-12 col-sm-4">
							<div class="card mb-3" disabled>
								<div class="card-body d-flex flex-column">
									<h3 class="card-title">{{$ticketType->name}} @if ($event->capacity <= $event->tickets->count())
											- <strong>@lang('events.soldout')</strong>
										@endif</h3>
									@if ($ticketType->quantity != 0)
										<small>
											@lang('events.limitedavailability')
										</small>
									@endif
									@if ($ticketType->hasTicketGroup())
										<small>@lang('events.ticketgroup', ['ticketgroup' => $ticketType->ticketGroup->name])</small>
									@endif
									<div class="row mt-auto">
										<div class="col-sm-12 col-12">
											<h3>{{ $currencySymbol }}{{$ticketType->price}}
												@if ($ticketType->quantity != 0)
													<small>
														{{ $ticketType->quantity - $ticketTypeCounts[$ticketType->id] }}
														/{{ $ticketType->quantity }} @lang('events.available')
													</small>
												@endif
											</h3>
											@if ($user)
												{{ Form::open(array('url'=>'/tickets/purchase/' . $ticketType->id)) }}
												@if (
                                                $event->capacity <= $event->tickets->count()
                                                    || ($ticketTypeCounts[$ticketType->id] >= $ticketType->quantity && $ticketType->quantity != 0)
                                                    )
													<div class="row">
														<div class="mb-3 col-sm-6 col-12">
															{{ Form::label('quantity','Quantity',array('id'=>'','class'=>'')) }}
															{{ Form::select('quantity', array(1 => 1), null, array('id'=>'quantity','class'=>'form-control', 'disabled' => true)) }}
														</div>
														<div class="mb-3 col-sm-6 col-12 d-flex">
															<button class="btn btn-md btn-primary btn-block mt-auto"
																	disabled>@lang('events.soldout')</button>
														</div>
													</div>
												@elseif($ticketType->sale_start && $ticketType->sale_start >= date('Y-m-d H:i:s'))
													<h5>
														@lang('events.availablefrom', ['time' => date('H:i', strtotime($ticketType->sale_start)), 'date'=> date ('d-m-Y', strtotime($ticketType->sale_start))])
													</h5>
												@elseif(
                                                $ticketType->sale_end && $ticketType->sale_end <= date('Y-m-d H:i:s') || date('Y-m-d H:i:s')>= $event->end
                                                    )
													<h5>
														@lang('events.ticketnolongavailable')
													</h5>
												@else
													<div class="row">
														<div class="mb-3 col-sm-6 col-12 ">
															{{ Form::label('quantity','Quantity',array('id'=>'','class'=>'')) }}
															{{ Form::select('quantity', Helpers::getTicketQuantitySelection($ticketType, $ticketType->quantity - $ticketTypeCounts[$ticketType->id]), null, array('id'=>'quantity','class'=>'form-control')) }}
														</div>
														<div class="mb-3 col-sm-6 col-12 d-flex">
															{{ Form::hidden('user_id', $user->id, array('id'=>'user_id','class'=>'form-control')) }}
															<button class="btn btn-md btn-primary btn-block mt-auto"><i
																		class="fas fa-shopping-cart"></i> @lang('events.buy')
															</button>
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

        @include ('layouts._partials._events.seating')


		<!-- VENUE INFORMATION -->
		<div class="pb-2 mt-4 mb-4 border-bottom">
			<a name="venue"></a>
			<h3><i class="fas fa-map-marked-alt me-3"></i>@lang('events.venueinformation')</h3>
		</div>
		<div class="row">
			<div class="col-lg-7">
				<address>
					<strong>{{ $event->venue->display_name }}</strong><br>
					@if (trim($event->venue->address_1) != '' || $event->venue->address_1 != null)
						{{ $event->venue->address_1 }}<br>
					@endif
					@if (trim($event->venue->address_2) != '' || $event->venue->address_2 != null)
						{{ $event->venue->address_2 }}<br>
					@endif
					@if (trim($event->venue->address_street) != '' || $event->venue->address_street != null)
						{{ $event->venue->address_street }}<br>
					@endif
					@if (trim($event->venue->address_city) != '' || $event->venue->address_city != null)
						{{ $event->venue->address_city }}<br>
					@endif
					@if (trim($event->venue->address_postcode) != '' || $event->venue->address_postcode != null)
						{{ $event->venue->address_postcode }}<br>
					@endif
					@if (trim($event->venue->address_country) != '' || $event->venue->address_country != null)
						{{ $event->venue->address_country }}<br>
					@endif
				</address>
			</div>
			<div class="col-lg-5">
				<div class="center-align slider-for">
					@include ('layouts._partials.slick_loader')

					@foreach ($event->venue->images as $image)
						<picture>
							<source srcset="{{ $image->path }}.webp" type="image/webp">
							<source srcset="{{ $image->path }}" type="image/jpeg">
							<img src="{{ $image->path }}" data-thumb="{{ $image->path }}"
								 alt="{{ $image->description ?? 'Image' }}" class="img-fluid">
						</picture>
					@endforeach
				</div>
			</div>
		</div>

		<!-- EVENT SPONSORS -->
		@include ('layouts._partials._sponsors.index')

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
										<img class="img-fluid rounded" alt="{{ $section->title }}"
											 src="{{ $section->image_path }}"/>
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
											<img class="img-fluid rounded" alt="{{ $section->title }}"
												 src="{{ $section->image_path }}"/>
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
										<img class="img-fluid rounded" alt="{{ $section->title }}"
											 src="{{ $section->image_path }}"/>
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
					{{ $timetable->created_at->toDateString() == now()->toDateString() ? $timetable->created_at->format('M d, H:i') : ($timetable->created_at->year == now()->year ? $timetable->created_at->format('M d') : $timetable->created_at->format('M d, Y')) }}
					,
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
									{{ date("D", strtotime($slot->start_time)) }}
									- {{ date("H:i", strtotime($slot->start_time)) }}
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
				@foreach ($user->getAllTickets($event->id) as $ticket)
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
							<a href="/events/{{ $event->slug }}/tournaments/{{ $tournament->slug }}"
							   class="link-unstyled">
								<div class="card card-hover mb-3">
									<div class="card-header ">
										@if ($tournament->game && $tournament->game->image_thumbnail_path)
											<picture>
												<source srcset="{{ $tournament->game->image_thumbnail_path }}.webp"
														type="image/webp">
												<source srcset="{{ $tournament->game->image_thumbnail_path }}"
														type="image/jpeg">
												<img class="img img-fluid rounded"
													 src="{{ $tournament->game->image_thumbnail_path }}"
													 alt="{{ $tournament->game->name }}">
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
																	<h2>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->eventTicket->user->username }}</h2>
																@else
																	<h2>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->name }}</h2>
																@endif
															@endif
															@if ($tournamentParticipant->final_rank == 2)
																@if ($tournament->team_size == '1v1')
																	<h3>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->eventTicket->user->username }}</h3>
																@else
																	<h3>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->name }}</h3>
																@endif
															@endif
															@if ($tournamentParticipant->final_rank != 2 && $tournamentParticipant->final_rank != 1)
																@if ($tournament->team_size == '1v1')
																	<h4>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->eventTicket->user->username }}</h4>
																@else
																	<h4>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->name }}</h4>
																@endif
															@endif
														@endforeach
													@endif
													@if ($tournament->team_size == '1v1')
														@foreach ($tournament->tournamentParticipants->sortBy('final_rank') as $tournamentParticipant)
															@if ($tournamentParticipant->final_rank == 1)
																@if ($tournament->team_size == '1v1')
																	<h2>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->eventTicket->user->username }}</h2>
																@else
																	<h2>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->name }}</h2>
																@endif
															@endif
															@if ($tournamentParticipant->final_rank == 2)
																@if ($tournament->team_size == '1v1')
																	<h3>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->eventTicket->user->username }}</h3>
																@else
																	<h3>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->name }}</h3>
																@endif
															@endif
															@if ($tournamentParticipant->final_rank != 2 && $tournamentParticipant->final_rank != 1)
																@if ($tournament->team_size == '1v1')
																	<h4>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->eventTicket->user->username }}</h4>
																@else
																	<h4>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }}
																		- {{ $tournamentParticipant->name }}</h4>
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

 	@include ('layouts._partials._events.attendees')
	</div>
@endsection