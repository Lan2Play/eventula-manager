@extends ('layouts.default')

@section ('content')

<div id="hero-carousel" class="carousel slide carousel-fade" style="margin-top: -20px;" data-bs-ride="carousel" data-bs-interval="8000">
	<!-- Wrapper for slides -->
	<div class="carousel-inner">
		@foreach ($sliderImages as $image)
			@if ($loop->first)
				<div class="carousel-item active">
					<picture>
						<source srcset="{{ $image->path }}.webp" type="image/webp">
						<source srcset="{{ $image->path }}" type="image/jpeg">
						<img class="hero-image" style="object-fit=cover" class="d-block w-100" alt="{{ Settings::getOrgName() }} Banner" src="{{ $image->path }}" width="100%" height="500">
					</picture>
				</div>
			@else
				<div class="carousel-item">
					<picture>
						<source data-lazy-load-srcset="{{ $image->path }}.webp" type="image/webp">
						<source data-lazy-load-srcset="{{ $image->path }}" type="image/jpeg">
						<img class="hero-image" alt="{{ Settings::getOrgName() }} Banner" data-lazy-load-src="{{ $image->path }}" width="100%"  height="500">
					</picture>
				</div>
			@endif
		@endforeach
	</div>
    <div class="hero-overlay">
        @if ($nextEvent)
				<div>
					<h3>@lang('messages.next_event')</h3>
					<h1>{{ $nextEvent->display_name }}</h1>
					<h5>
						@php
							$locale = app()->getLocale();
							$startDate = \Carbon\Carbon::parse($nextEvent->start)->locale($locale);
							$endDate = \Carbon\Carbon::parse($nextEvent->end)->locale($locale);
                            echo $startDate->translatedFormat(__('date.without_year')) . ' - ' . $endDate->translatedFormat(__('date.with_year'));
						@endphp
					</h5>
					<a href="/events/{{ $nextEvent->slug }}#tickets"><button class="btn btn-primary btn-lg">@lang('messages.book_now')</button></a>
				</div>
			@else
				<div>
					<h3>@lang('messages.next_event')</h3>
					<h1>@lang('home.comingsoon')</h1>
				</div>
			@endif
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-12 col-sm-12 col-md-12 col-lg-8">
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<h3>@lang('home.about') {{ Settings::getOrgName() }}</h3>
			</div>
			<p>{!! Settings::getAboutShort() !!}</p>
		</div>
		<div class="d-none d-xl-block col-lg-4">
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<h3>@lang('home.eventcalendar')</h3>
			</div>
			@if ( count($events) > 0 )
				<table class="table table-borderless">
					<tbody>
						@foreach ( $events as $event )
							@if ($event->start > \Carbon\Carbon::today() )
								<tr>
									<td>
										<a href="/events/{{ $event->slug }}">
											{{ $event->display_name }}
											@if ($event->status != 'PUBLISHED')
												- {{ $event->status }}
											@endif
										</a>
									</td>
									<td>
										<span class="float-end">
											@php
							                    $locale = app()->getLocale();
							                    $startDate = \Carbon\Carbon::parse($nextEvent->start)->locale($locale);
							                    $endDate = \Carbon\Carbon::parse($nextEvent->end)->locale($locale);
                                                echo $startDate->translatedFormat(__('date.without_year')) . ' - ' . $endDate->translatedFormat(__('date.with_year'));
						                    @endphp
										</span>
									</td>
								</tr>
							@endif
						@endforeach
					</tbody>
				</table>
			@else
				<div>@lang('home.comingsoon')...</div>
			@endif

			@if (count($gameServerList) > 0)
				<script>
					function updateStatus(id ,serverStatus){

						if(serverStatus.info == false)
						{
							$(id + "_map").html( "-" );
							$(id + "_players").html( "-" );
						}else
						{
							$(id + "_map").html( serverStatus.info.Map );
							$(id + "_players").html( serverStatus.info.Players );
						}
					}
				</script>
				<div class="page-header">
					<h3>@lang('home.publicserver')</h3>
				</div>
				<div class="panel-body">
					@foreach ($gameServerList as $game => $gameServers)
						@php
							$counter = 0;
						@endphp
						@foreach ($gameServers as $gameServer)
							@php
							$availableParameters = new \stdClass();
							$availableParameters->game = $gameServer->game;
							$availableParameters->gameServer = $gameServer;
							$counter++;
							@endphp
									@if ($counter > 1)
										<hr>
									@endif
									@if($gameServer->game->connect_game_url)
									<a id="connectGameUrl" href="{{ Helpers::resolveServerCommandParameters($gameServer->game->connect_game_url, NULL, $availableParameters) }}" role="button"><strong>#{{$counter}} - {{ $gameServer->name }}</strong></a>
									@else
										<strong>#{{$counter}} - {{ $gameServer->name }}</strong>
										@if($gameServer->address)
											<div><strong>IP: </strong><span id="serverip_{{ $gameServer->id }}">{{$gameServer->address}}</span></div>
										@endif
										@if($gameServer->game_port)
											<div><strong>Port: </strong><span id="serverip_{{ $gameServer->id }}">{{$gameServer->game_port}}</span></div>
										@endif
										@if($gameServer->game_password)
											<div><strong>Password: </strong><span id="serverpw_{{ $gameServer->id }}">{{$gameServer->game_password}}</span></div>
										@endif

									@endif
									@if($gameServer->game->connect_stream_url && $gameServer->stream_port != 0)
									<div><a href="{{ Helpers::resolveServerCommandParameters($gameServer->game->connect_stream_url, NULL, $availableParameters) }}" role="button">Join Stream</a></div>
														@endif
									<script>

										document.addEventListener("DOMContentLoaded", function(event) {

											$.get( '/games/{{ $gameServer->game->slug }}/gameservers/{{ $gameServer->slug }}/status', function( data ) {
												var serverStatus = JSON.parse(data);
												updateStatus('#serverstatus_{{ $gameServer->id }}', serverStatus);
											});
											var start = new Date;

											setInterval(function() {
												$.get( '/games/{{ $gameServer->game->slug }}/gameservers/{{ $gameServer->slug }}/status', function( data ) {
													var serverStatus = JSON.parse(data);
													updateStatus('#serverstatus_{{ $gameServer->id }}', serverStatus);
												});
											}, 30000);
										});
									</script>

									@if($gameServer->game->gamecommandhandler != "0")
										<div id="serverstatus_{{ $gameServer->id }}">
											<div><i class="fas fa-map-marked-alt"></i><strong>Map: </strong><span id="serverstatus_{{ $gameServer->id }}_map">Waiting for status</span></div>
											<div><i class="fas fa-users"></i><strong>Players: </strong><span id="serverstatus_{{ $gameServer->id }}_players">Waiting for status</span></div>
										</div>
									@else
										<div id="serverstatus_{{ $gameServer->id }}">
											<div><span id="serverstatus_{{ $gameServer->id }}_nostats">No Status</span></div>
										</div>
									@endif	


						@endforeach
						@endforeach
				</div>
			@endif



		</div>
		@if ($nextEvent)
			<div class="col-12">
				<div class="pb-2 mt-4 mb-4 border-bottom">
					<h3>
						{{ $nextEvent->display_name }}
						@if (count($nextEvent->seatingPlans) > 0)
							<small>{{ max($nextEvent->getSeatingCapacity() - $nextEvent->tickets->count(), 0) }} / {{ $nextEvent->getSeatingCapacity() }} @lang('home.seatsremaining')</small>
						@else
							<small>{{ max($nextEvent->capacity - $nextEvent->tickets->count(), 0) }} / {{ $nextEvent->capacity }} @lang('home.ticketsremaining')</small>
						@endif
					</h3>
				</div>
			</div>
			<div class="col-12 col-sm-9">
				<h4>{!! $nextEvent->desc_short !!}</h4>
				<p>{!! $nextEvent->essential_info !!}</p>
			</div>
			<div class="col-12 col-sm-3">
				<h4>@lang('home.when'):</h4>
				<h5>
                    @php
                        $locale = app()->getLocale();
                        $startDate = \Carbon\Carbon::parse($nextEvent->start)->locale($locale);
                        $endDate = \Carbon\Carbon::parse($nextEvent->end)->locale($locale);
                        echo $startDate->translatedFormat(__('date.without_year')) . ' - ' . $endDate->translatedFormat(__('date.with_year'));
                    @endphp
				</h5>
				<h4>@lang('home.where'):</h4>
				<h5>{{ $nextEvent->venue->display_name }}</h5>
				@if ($nextEvent->tickets && $user)
					<h4>@lang('home.price'):</h4>
					<h5>@lang('home.ticketsstartfrom') {{ Settings::getCurrencySymbol() }}{{ $nextEvent->getCheapestTicket() }}</h5>
				@endif
			</div>
		@endif
	</div>
</div>
<div class="book-now  text-center d-none d-sm-block">
	<div class="container">
		<div class="row">
			<div class="col-12">
				@if ($nextEvent)
					<h3>@lang('home.wantgetinaction') <a href="/events/{{ $nextEvent->slug }}" class="text-info">@lang('messages.book_now')</a></h3>
				@else
					<h3>@lang('home.eventscomingsoon')!</h3>
				@endif
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-12 col-sm-9">
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<h3>@lang('home.latestnews')</h3>
			</div>
			@if (!$newsArticles->isEmpty())
				@foreach ($newsArticles as $newsArticle)
					@include ('layouts._partials._news.short')
				@endforeach
			@else
				<p>@lang('home.nothingtosee')...</p>
			@endif
		</div>
		<div class="col-12 col-sm-3">
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<h3>@lang('home.thefam', ['orgName' => Settings::getOrgName()])</h3>
			</div>
			@if (Settings::getDiscordId())
				<iframe class="d-md-none d-lg-block" src="https://discordapp.com/widget?id={{ Settings::getDiscordId() }}&theme=light" width="100%" height="500" allowtransparency="true" frameborder="0"></iframe>
			@endif
			@if (count($topAttendees) > 0)
				<div class="pb-2 mt-4 mb-4 border-bottom">
					<h5>@lang('home.top5attendees')</h5>
				</div>
				@foreach ($topAttendees as $attendee)
					<div class="row">
						<div class="col-12 col-sm-3">
							<img class="rounded img-fluid" alt={{ $attendee->username }}'s Avatar" src="{{ $attendee->avatar }}">
						</div>
						<div class="col-12 col-sm-9">
							<p>
								{{ $attendee->username }}<br>
								<small> {{ $attendee->unique_attended_event_count }} @lang('home.eventsattended')</small>
							</p>
						</div>
					</div>
				@endforeach
			@endif
			@if (count($topWinners) > 0)
				<div class="pb-2 mt-4 mb-4 border-bottom">
					<h5>@lang('home.top5winners')</h5>
				</div>
				@foreach ($topWinners as $winner)
					<div class="row">
						<div class="col-12 col-sm-3">
							<img class="rounded img-fluid" alt={{ $winner->username }}'s Avatar" src="{{ $winner->avatar }}">
						</div>
						<div class="col-12 col-sm-9">
							<p>
								{{ $winner->username }}<br>
								<small> {{ $winner->win_count }} @lang('home.wins')</small>
							</p>
						</div>
					</div>
				@endforeach
			@endif
		</div>
	</div>
</div>

<div class="about  section-padding  section-margin d-none">
	<div class="container">
		<div class="row">
			<div class="col-md-8  offset-md-2 text-center">
				<div class="text-center">
					<h2 class="section-heading  text-center">@lang('home.allaboutorg', ['orgName' => Settings::getOrgName() ])</h2>
				</div>
				{!! Settings::getAboutShort() !!}
			</div>
		</div>
	</div>
</div>

@endsection


@section ('scripts')

<script language="javascript" type="text/javascript">
	jQuery( function()
	{
		jQuery('#hero-carousel').on('slide.bs.carousel', function(e)
		{
			var lazy = jQuery(e.relatedTarget).find("img[data-lazy-load-src]");
			lazy.attr("src", lazy.data('lazy-load-src'));
			lazy.removeAttr("data-lazy-load-src");

			var lazySources = jQuery(e.relatedTarget).find("source[data-lazy-load-srcset]");
			lazySources.attr("srcset", lazySources.data('lazy-load-srcset'));
			lazySources.removeAttr("data-lazy-load-srcset");
		});
	});
</script>

@endsection
