use Debugbar;

@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' - ' . $event->display_name)

@section ('content')

<div class="container pt-1">

	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h1>Welcome to {{ $event->display_name }}!</h1>
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
						<!--<li style="font-size:15px; font-weight:bold;"><a href="#food">Food Orders</a></li>-->
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#event">@lang('events.eventinfo')</a></li>
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#server">@lang('events.server')</a></li>
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#seating">@lang('events.seating')</a></li>
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#attendees">@lang('events.attendees')</a></li>
						@if (!$event->tournaments->isEmpty())
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#tournaments">@lang('events.tournaments')</a></li>
						@endif
						@if ($event->matchmaking_enabled && $isMatchMakingEnabled)
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#matchmaking">@lang('matchmaking.matchmaking')</a></li>
						@endif
						<li class="nav-item" style="font-size:15px; font-weight:bold;"><a class="nav-link" href="#information">@lang('events.essentialinfo')</a></li>
					</ul>
				</div>
			</div>
		</nav>
	</div>
	<!-- SIGN IN TO EVENT -->
	@if (!$signedIn)
	@lang('events.plssignin')
	@endif

	

	@include ('layouts._partials._sponsors.index')
<!-- EVENT SPONSORS 

	@if (!$event->sponsors->isEmpty())
		<div class="pb-2 mt-4 mb-4 border-bottom">
			<a name="sponsors"></a>
			<h3>@lang('events.eventsponsoredby', ['event' => $event->display_name])</h3>
		</div>
		@foreach ($event->sponsors as $sponsor)
			<a href="{{$sponsor->website}}">
				<picture>
					<source srcset="{{ $sponsor->image_path }}.webp" type="image/webp">
					<source srcset="{{ $sponsor->image_path }}" type="image/jpeg">
					<img alt="{{ $sponsor->website}}" class="img-fluid rounded" src="{{ $sponsor->image_path }}"/>
				</picture>
			</a>
		@endforeach
	@endif
	-->

	<!-- ESSENTIAL INFORMATION -->
	<div class="row">
		<div class="col-lg-6 col-md-6 col-12">
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<a name="information"></a>
				<h3>@lang('events.essentialinfo')</h3>
			</div>
			{!! $event->event_live_info !!}
		</div>
		<div class="col-lg-6 col-md-6 col-12">
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<a name="announcements"></a>
				<h3>@lang('events.announcements')</h3>
			</div>
			@if ($event->announcements->isEmpty())
			<div class="alert alert-info"><strong>@lang('events.noannouncements')</strong></div>
			@else
			@foreach ($event->announcements as $announcement)
    		<div class="alert alert-info" style="position: relative; padding-right: 150px;">
	        	{{ $announcement->message }}
       			<span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
    	        	{{ $announcement->created_at->format('M d, h:s A') }}
        		</span>
    		</div>
			@endforeach
			@endif
		</div>
	</div>

	<!-- TIMETABLE -->
	@if (!$event->timetables->isEmpty())
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<a name="timetable"></a>
		<h3>@lang('events.timetable')</h3>
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
					@if ($slot->desc != NULL)
					{{ $slot->desc }}
					@endif
				</td>
			</tr>
			@endif
			@endforeach
		</tbody>
	</table>
	@endforeach
	@endif

	<!-- Server -->
	@if(Settings::getTeamspeakLink() != "" || Settings::getMumbleLink() != "" || Settings::getFacebookLink() != "" || Settings::getDiscordLink() != "" || Settings::getSteamLink() != "" || Settings::getRedditLink() != "" || Settings::getTwitterLink() != "" || !empty($gameServerList))
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<a name="server"></a>
		<h3>@lang('events.server')</h3>
	</div>
	<div class="row">
		@if (Settings::getTeamspeakLink() != "")
		<div class="col-6 col-lg-4">
			<a href="ts3server://{{ Settings::getTeamspeakLink() }}?nickname={{ $user->username }}"><i class="fab fa-teamspeak fa-2x margin"></i>@lang('home.servers_teamspeak')</a>
		</div>
		@endif

		@if (Settings::getMumbleLink() != "")
		<div class="col-6 col-lg-4">
			<a href="mumble://{{ $user->username }}{{ chr(64) }}{{ Settings::getMumbleLink() }}" width="100%">
				<img class="margin" src="https://www.mumble.info/css/mumble.svg" alt="Mumble Logo" width="28" height="28">@lang('home.servers_mumble')
			</a>
		</div>
		@endif
		@if (Settings::getFacebookLink() != "")
		<div class="col-6 col-lg-4">
			<a target="_blank" rel="noreferrer" href="{{ Settings::getFacebookLink() }}"><i class="fab fa-facebook fa-2x margin"></i>@lang('home.servers_facebook')</a>
		</div>
		@endif
		@if (Settings::getDiscordLink() != "")
		<div class="col-6 col-lg-4">
			<a target="_blank" rel="noreferrer" href="{{ Settings::getDiscordLink() }}"><i class="fab fa-discord fa-2x margin"></i>@lang('home.servers_discord')</a>
		</div>
		@endif
		@if (Settings::getSteamLink() != "")
		<div class="col-6 col-lg-4">
			<a target="_blank" rel="noreferrer" href="{{ Settings::getSteamLink() }}"><i class="fab fa-steam fa-2x margin"></i>@lang('home.servers_steam')</a>
		</div>
		@endif
		@if (Settings::getRedditLink() != "")
		<div class="col-6 col-lg-4">
			<a target="_blank" rel="noreferrer" href="{{ Settings::getRedditLink() }}"><i class="fab fa-reddit fa-2x margin"></i>@lang('home.servers_reddit')</a>
		</div>
		@endif
		@if (Settings::getTwitterLink() != "")
		<div class="col-6 col-lg-4">
			<a target="_blank" rel="noreferrer" href="{{ Settings::getTwitterLink() }}"><i class="fab fa-twitter fa-2x margin"></i>@lang('home.servers_twitter')</a>
		</div>
		@endif
	</div>

	@if ( !empty($gameServerList) )
	<script>
		function updateStatus(id, serverStatus) {

			if (serverStatus.info == false) {
				jQuery(id + "_map").html("-");
				jQuery(id + "_players").html("-");
			} else {
				jQuery(id + "_map").html(serverStatus.info.Map);
				jQuery(id + "_players").html(serverStatus.info.Players);
			}
		}
	</script>
	<div class="row top30">
		@foreach ($gameServerList as $game => $gameServers)
		<div class="col-12 col-sm-6 col-md-4">
			<div class="card mb-3">
				<div class="card-header ">
					<div class="row text-center block-center">
						<picture>
							<source srcset="{{ $gameServers[0]->game->image_thumbnail_path }}.webp" type="image/webp">
							<source srcset="{{ $gameServers[0]->game->image_thumbnail_path }}" type="image/jpeg">
							<img src="{{ $gameServers[0]->game->image_thumbnail_path }}" class="img img-fluid rounded img-contain margin-top margin-bottom width=100em" height="100em">
						</picture>
						<strong class="margin">{{ $gameServers[0]->game->name }}</strong>
					</div>
				</div>
				<div class="card-body">
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

					<h3>#{{$counter}} - {{ $gameServer->name }}</h3>
					<script>
						document.addEventListener("DOMContentLoaded", function(event) {

							$.get('/games/{{ $gameServer->game->slug }}/gameservers/{{ $gameServer->slug }}/status', function(data) {
								var serverStatus = JSON.parse(data);
								updateStatus('#serverstatus_{{ $gameServer->id }}', serverStatus);
							});
							var start = new Date;

							setInterval(function() {
								$.get('/games/{{ $gameServer->game->slug }}/gameservers/{{ $gameServer->slug }}/status', function(data) {
									var serverStatus = JSON.parse(data);
									updateStatus('#serverstatus_{{ $gameServer->id }}', serverStatus);
								});
							}, 30000);
						});
					</script>
					@if($gameServer->game->gamecommandhandler != "0")
					<div id="serverstatus_{{ $gameServer->id }}">
						<div><i class="fas fa-map-marked-alt margin"></i><strong>Map: </strong><span id="serverstatus_{{ $gameServer->id }}_map">Waiting for status</span></div>
						<div><i class="fas fa-users margin"></i><strong>Players: </strong><span id="serverstatus_{{ $gameServer->id }}_players">Waiting for status</span></div>
					</div>
					@else
					<div id="serverstatus_{{ $gameServer->id }}">
						<div><span id="serverstatus_{{ $gameServer->id }}_nostats">No Status</span></div>
					</div>
					@endif


					@if($gameServer->game->connect_game_url || $gameServer->game->connect_game_command )
					@if($gameServer->game->connect_game_url)
					<a class="btn btn-primary btn-block" id="connectGameUrl" href="{{ Helpers::resolveServerCommandParameters($gameServer->game->connect_game_url, NULL, $availableParameters) }}" role="button">Join Game</a>
					@endif
					@if($gameServer->game->connect_game_command)
					<div class="input-group" style="width: 100%">
						<input class="form-control" id="connectGameCommand{{ $availableParameters->gameServer->id }}" type="text" readonly value="{{ Helpers::resolveServerCommandParameters($gameServer->game->connect_game_command, NULL, $availableParameters) }}">
						<span class="input-group-btn">
							<button class="btn btn-primary" type="button" onclick="copyToClipBoard('connectGameCommand{{$availableParameters->gameServer->id}}')"><i class="fas fa-external-link-alt"></i></button>
					</div>
					@endif
					@else
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
					<a class="btn btn-primary btn-block" href="{{ Helpers::resolveServerCommandParameters($gameServer->game->connect_stream_url, NULL, $availableParameters) }}" role="button">Join Stream</a>
					@endif
					@endforeach
				</div>
			</div>
		</div>
		@endforeach
	</div>

	<script>
		function copyToClipBoard(inputId) {
			/* Get the text field */
			var copyText = document.getElementById(inputId);

			/* Select the text field */
			copyText.select();
			copyText.setSelectionRange(0, 99999); /*For mobile devices*/

			/* Copy the text inside the text field */
			document.execCommand("copy");
		}
	</script>

	@endif
	@endif

	<!-- TOURNAMENTS -->
	@if (!$event->tournaments->isEmpty())
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<a name="tournaments"></a>
		<h3>@lang('events.tournaments')</h3>
	</div>
	<div class="row">
		@foreach ($event->tournaments as $tournament)
		@if ($tournament->status != 'DRAFT')
		<div class="col-12 col-sm-6 col-md-3">
			<a href="/events/{{ $event->slug }}/tournaments/{{ $tournament->slug }}" class="link-unstyled">
				<div class="card card-hover mb-3">
					@if ($tournament->game && $tournament->game->image_thumbnail_path)
					<picture>
						<source srcset="{{ $tournament->game->image_thumbnail_path }}.webp" type="image/webp">
						<source srcset="{{ $tournament->game->image_thumbnail_path }}" type="image/jpeg">
						<img class="img img-fluid rounded" src="{{ $tournament->game->image_thumbnail_path }}" alt="{{ $tournament->game->name }}" class="card-img-top img-fluid">
					</picture>
					@endif
					<div class="card-body">
						<div class="card-title">
						<h2 class="text-primary">{{ $tournament->name }}</h2>
						</div>
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
					<div class="card-footer">
						@if ($tournament->status == 'COMPLETE')
						<span class="badge badge-success">@lang('events.ended')</span>
						@endif
						@if ($tournament->status == 'LIVE')
						<span class="badge badge-success">@lang('events.live')</span>
						@endif
						@if ($tournament->status != 'COMPLETE' && $user && $user->active_event_participant && !$tournament->getParticipant($user->active_event_participant->id))
						<span class="badge badge-danger">@lang('events.notsignedup')</span>
						@endif
						@if ($tournament->status != 'COMPLETE' && $user && $user->active_event_participant && $tournament->getParticipant($user->active_event_participant->id))
						<span class="badge badge-success">@lang('events.signedup')</span>
						@endif
						@if ($tournament->status != 'COMPLETE' && $user && !$user->active_event_participant && $user->getAllTickets($event->id)->isEmpty())
						<span class="badge badge-info">@lang('events.purchaseticketosignup')</span>
						@else
						@if ($tournament->status != 'COMPLETE' && $user && !$user->active_event_participant && !$event->online_event)
						<span class="badge badge-info">@lang('events.signuponlywhenlive')</span>
						@endif
						@endif
					</div>
				</div>
			</a>
		</div>
		@endif
		@endforeach
	</div>
	@endif


	<!-- Matchmaking -->
	@if ($event->matchmaking_enabled && $isMatchMakingEnabled)

	<div class="pb-2 mt-4 mb-4 border-bottom">
		<div class="row">
			<div class="col-sm">
				<a name="matchmaking"></a>
				<h3>
					@lang('matchmaking.matchmaking')
				</h3>
			</div>
			<div class="col-sm mt-4">
				@if(Settings::getSystemsMatchMakingMaxopenperuser() == 0 || count($currentUserOpenLivePendingDraftMatches) < Settings::getSystemsMatchMakingMaxopenperuser()) <a href="/matchmaking/" class="btn btn-success btn-sm btn-block float-end" data-bs-toggle="modal" data-bs-target="#addMatchModal">Add Match</a>
					@endif
			</div>
		</div>
	</div>

	<!-- owned matches -->
	@if (!$ownedMatches->isEmpty())
	@php
	$scope = "ownedmatches";
	@endphp
	<a name="ownedmatches"></a>
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h3>@lang('matchmaking.ownedmatches')</h3>
	</div>
	<div class="row card-deck">
		@foreach ($ownedMatches as $match)
			<div class="col">
				@include ('layouts._partials._matchmaking.card')
			</div>
		@endforeach

	</div>
	@if($ownedMatches->count())
	<div>
		{{ $ownedMatches->links() }}
	</div>
	@endif
	@endif

	<!-- owned teams -->
	@if (!$memberedTeams->isEmpty())
	@php
	$scope = "memberedteams";
	@endphp
	<a name="memberedmatches"></a>
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h3>@lang('matchmaking.ownedteams')</h3>
	</div>
	<div class="row card-deck">
		@foreach ($memberedTeams as $team)
		@php
		$match = $team->match;
		@endphp
		<div class="col">
			@include ('layouts._partials._matchmaking.card')
		</div>
		@endforeach
	</div>
	@if($memberedTeams->count())
	<div>
		{{ $memberedTeams->links() }}
	</div>
	@endif
	@endif

	<!-- open public matches -->
	@if (!$openPublicMatches->isEmpty())
	@php
	$scope = "openpublicmatches";
	@endphp
	<a name="openpubmatches"></a>
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h3>@lang('matchmaking.publicmatches')</h3>
	</div>
	<div class="row card-deck">
		@foreach ($openPublicMatches as $match)
			<div class="col">
				@include ('layouts._partials._matchmaking.card')
			</div>
		@endforeach
	</div>
	@if($openPublicMatches->count())
	<div>
		{{ $openPublicMatches->links() }}
	</div>
	@endif
	@endif

	<!-- live closed public matches -->
	@if (!$liveClosedPublicMatches->isEmpty())
	@php
	$scope = "liveclosedpublicmatches";
	@endphp
	<a name="closedpubmatches"></a>
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h3>@lang('matchmaking.closedpublicmatches')</h3>
	</div>
	<div class="row card-deck">
		@foreach ($liveClosedPublicMatches as $match)
			<div class="col">
				@include ('layouts._partials._matchmaking.card')
			</div>
		@endforeach
	</div>
	@if($liveClosedPublicMatches->count())
	<div>
		{{ $liveClosedPublicMatches->links() }}
	</div>
	@endif
	@endif



	<!-- Modal -->

	<div class="modal fade" id="addMatchModal" tabindex="-1" role="dialog" aria-labelledby="addMatchModal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="addMatchModal">Add Match</h4>
					<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
				</div>
				<div class="modal-body">
					{{ Form::open(array('url'=>'/matchmaking/' )) }}
					<div class="mb-3">
						{{ Form::label('game_id',__('matchmaking.game').':',array('id'=>'','class'=>'')) }}
						{{
							Form::select(
								'game_id',
								Helpers::getMatchmakingGameSelectArray(),
								null,
								array(
									'id'    => 'game_id',
									'class' => 'form-control'
								)
							)
						}}
					</div>
					<div class="mb-3">
						{{ Form::label('team1name',__('matchmaking.firstteamname'),array('id'=>'','class'=>'')) }}
						{{ Form::text('team1name',NULL,array('id'=>'team1name','class'=>'form-control')) }}
						<small>@lang('matchmaking.thisisyourteam')</small>
					</div>
					<div class="mb-3">
						{{ Form::label('team_size',__('matchmaking.teamsize'),array('id'=>'','class'=>'')) }}
						{{
							Form::select(
								'team_size',
								array(
									'1v1' => '1v1',
									'2v2' => '2v2',
									'3v3' => '3v3',
									'4v4' => '4v4',
									'5v5' => '5v5',
									'6v6' => '6v6'
								),
								null,
								array(
									'id'    => 'team_size',
									'class' => 'form-control'
								)
							)
						}}
					</div>
					<div class="mb-3">
						{{ Form::label('team_count',__('matchmaking.teamcounts'),array('id'=>'','class'=>'')) }}
						{{
							Form::number('team_count',
								2,
								array(
									'id'    => 'team_size',
									'class' => 'form-control'
								))
						}}
					</div>
					<div class="mb-3">
						<div class="form-check">
							<label class="form-check-label">
								{{ Form::checkbox('ispublic', null, null, array('id'=>'ispublic')) }} is public (show match publicly for signup)
							</label>
						</div>
					</div>
					<button type="submit" class="btn btn-success btn-block">Submit</button>
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>

	@endif

	<!-- ATTENDEES -->
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<a name="attendees"></a>
		<h3>@lang('events.attendees')</h3>
	</div>
	<table class="table table-striped">
		<thead>
			<th width="7%">
			</th>
			<th>
				@lang('events.steamname')
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
					<img class="img-fluid rounded" style="max-width: 70%;" alt="{{ $participant->user->username }}'s Avatar" src="{{ $participant->user->avatar }}">
				</td>
				<td style="vertical-align: middle;">
					{{ $participant->user->username }}
					@if ($participant->user->steamid)
					- <span class="text-muted"><small>Steam: {{ $participant->user->steamname }}</small></span>
					@endif
				</td>
				<td style="vertical-align: middle;">
					{{ $participant->user->firstname }}
				</td>
				<td style="vertical-align: middle;">
					@if ( $participant->user->hasSeatableTicket($event->id) )
					@if ( $participant->seat )
					{{ $participant->seat->getName() }}
					@else
					@lang( 'events.notseated' )
					@endif
					@else
					@lang( 'events.noseatableticketlist' )
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	@include ('layouts._partials._events.seating')



	<!-- Image Uploader -->
	<div class="pb-2 mt-4 mb-4 border-bottom d-none">
		<a name="image_uploader"></a>
		<h3>@lang('events.imageuploader')</h3>
	</div>
	<div class="row d-none">
	</div>

</div>

@endsection