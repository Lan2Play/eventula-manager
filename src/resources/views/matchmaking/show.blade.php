@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' - ' . __('matchmaking.match') . " " . $match->id)

@section ('content')

<div class="container pt-1">

	<div class="pb-2 mt-4 mb-4 border-bottom">

		<span>
			<h1 class="d-inline">@lang('matchmaking.match') {{ $match->id }} </h1>
			<div class="float-end">

				<div>
					@if (isset($match->matchReplays) && count($match->matchReplays) > 0)
						<span class="badge text-bg-primary">@lang('matchmaking.replayavailable')</span>
					@endif
					@if ($match->status == 'COMPLETE')
						<span class="badge text-bg-success">@lang('matchmaking.ended')</span>
					@endif
					@if ($match->status == 'OPEN')
						<span class="badge text-bg-success"><i class="fas fa-wifi"></i></span>
					@endif
					@if ($match->status == 'LIVE')
						<span class="badge text-bg-success">@lang('matchmaking.live')</span>
					@endif
					@if ($match->status == 'PENDING')
						<span class="badge text-bg-light">@lang('matchmaking.pending')</span>
					@endif
					@if ($match->status == 'WAITFORPLAYERS')
						<span class="badge text-bg-light">@lang('matchmaking.waitforplayers')</span>
					@endif
					@if ($match->status != 'COMPLETE' && !$match->getMatchTeamPlayer(Auth::id()))
						<span class="badge text-bg-danger">@lang('matchmaking.notsignedup')</span>
					@endif
					@if ($match->status != 'COMPLETE' && $match->getMatchTeamPlayer(Auth::id()))
						<span class="badge text-bg-success">@lang('matchmaking.signedup')</span>
					@endif
					@if ( $match->owner_id == Auth::id())
						<span class="badge text-bg-info">@lang('matchmaking.matchowner')</span>
					@endif
					@if ( $match->getMatchTeamOwner(Auth::id()))
						<span class="badge text-bg-info">@lang('matchmaking.teamowner')</span>
					@endif
				</div>
				<div class="form-check">
					<input class="form-check-input" type="checkbox" value="" id="autoReload">
					<label class="form-check-label" for="autoReload">
					  Auto Reload
					</label>
				</div>
			</div>

		</span>

	</div>


	@if($match->ispublic == 1 || $invite != null || $teamJoin != null || $match->owner_id == Auth::id() || $match->getMatchTeamPlayer(Auth::id()))
		@if ($match->owner_id == Auth::id())
				@if($match->status == "OPEN")
					{{-- Invite URL --}}
					<p class="mb-0">@lang('matchmaking.matchinviteurl')</p>
					<div class="input-group mb-3 mt-0" style="width: 100%">
						<input class="form-control" id="matchinviteurl" type="text" readonly value="{{ URL::to('/') }}/matchmaking/invite/?url={{ $match->invite_tag }}">
						<button class="btn btn-primary" type="button" onclick="copyToClipBoard('matchinviteurl')"><i class="far fa-clipboard"></i></button>
					</div>
				@endif
				<div class="row">
				{{-- Open, Start, Finalize --}}
				@if($match->status == "DRAFT")
					<div class="col">
						<div class="mb-3">
						{!! Html::form('POST', '/matchmaking/'.$match->id.'/open') !!}
							<button type="submit" class="btn btn-success btn-block"><i class="fas fa-wifi"></i> @lang('matchmaking.openmatch')</button>
						{!! Html::form()->close() !!}
						</div>
					</div>
				@endif
				@if($match->status == "OPEN")
					<div class="col">
						<div class="mb-3">
						{!! Html::form('POST', '/matchmaking/'.$match->id.'/start') !!}
							<button type="submit" class="btn btn-success btn-block"><i class="fas fa-play"></i> @lang('matchmaking.startmatch')</button>
						{!! Html::form()->close() !!}
						</div>
					</div>
				@endif
				@if($match->status == "LIVE")
					<div class="col">
						{!! Html::form('POST', '/matchmaking/'.$match->id.'/finalize') !!}
							<div class="row">
								@foreach ($match->teams as $team)
									<div class="col">
										<div class="mb-3">
											{!! Html::label('Score of '.$team->name, 'teamscore_'. $team->id) !!}
											@if ($match->game->matchmaking_autoapi)
												{!! Html::number('teamscore_'. $team->id, $team->team_score)->id('teamscore_'. $team->id)->class('form-control mb-3')->disabled() !!}
											@else
												{!! Html::number('teamscore_'. $team->id, $team->team_score)->id('teamscore_'. $team->id)->class('form-control mb-3') !!}
											@endif
										</div>
									</div>
								@endforeach
							</div>
							@if (!$match->game->matchmaking_autoapi)
								<button type="submit" class="btn btn-success btn-block ">@lang('matchmaking.finalizematch')</button>
							@endif
						{!! Html::form()->close() !!}
					</div>
				@endif
				{{-- Edit, Delete --}}
				@if ($match->status != "LIVE" && $match->status != "COMPLETE" && $match->status != "PENDING" && $match->status != "WAITFORPLAYERS")
					<div class="col">
						{!! Html::form('POST', '/matchmaking/'.$match->id.'/scramble') !!}
							<button type="submit" class="btn btn-warning btn-block mb-3 text-nowrap"><i class="fas fa-dice"></i> @lang('matchmaking.scramble')</button>
						{!! Html::form()->close() !!}
					</div>
					<div class="col">
						<a href="#" class="btn btn-warning btn-block mb-3 text-nowrap" data-bs-toggle="modal" data-bs-target="#editMatchModal"><i class="fas fa-edit"></i> @lang('matchmaking.editmatch')</a>
					</div>
					<div class="col">
						{!! Html::form('POST', '/matchmaking/' . $match->id)->attribute('onsubmit', 'return ConfirmDelete()') !!}
						{!! Html::hidden('_method', 'DELETE') !!}
						<button type="submit" class="btn btn-danger btn-block  mb-3 text-nowrap"><i class="fas fa-trash"></i> @lang('matchmaking.deletematch')</button>
						{!! Html::form()->close() !!}
					</div>
				@endif
			</div>
			<hr>
		@endif


		@if ( $match->getMatchTeamPlayer(Auth::id()) && !$match->getMatchTeamOwner(Auth::id()) && Auth::id() != $match->owner_id && $match->status != "LIVE" && $match->status != "COMPLETE" && $match->status != "PENDING" && $match->status != "WAITFORPLAYERS")
			{!! Html::form('POST', '/matchmaking/' . $match->id . '/team/'. $match->getMatchTeamPlayer(Auth::id())->team->id . '/teamplayer/'. $match->getMatchTeamPlayer(Auth::id())->id .'/delete')->attribute('onsubmit', 'return ConfirmDelete()') !!}
				{!! Html::hidden('_method', 'DELETE') !!}
				<button type="submit" class="btn btn-danger btn-sm btn-block mb-3">@lang('matchmaking.leavematch')</button>
			{!! Html::form()->close() !!}

		@endif

		@if (($match->status == 'LIVE' || $match->status == "WAITFORPLAYERS")  && isset($match->matchMakingServer) && isset($match->matchMakingServer->gameServer) )
		<script>
			function updateStatus(id ,serverStatus){

				if(serverStatus.info == false)
				{
					jQuery(id + "_map").html( "-" );
					jQuery(id + "_players").html( "-" );
				}else
				{
					jQuery(id + "_map").html( serverStatus.info.Map );
					jQuery(id + "_players").html( serverStatus.info.Players );
				}
			}
		</script>
		<div class="row">
			<div class="col-sm">
			@php
			$availableParameters = new \stdClass();
			$availableParameters->game = $match->matchMakingServer->gameServer->game;
			$availableParameters->gameServer = $match->matchMakingServer->gameServer;
			@endphp
					<h6  class="mt-0">{{ $match->matchMakingServer->gameServer->name }}</h6>
					<script>

						document.addEventListener("DOMContentLoaded", function(event) {

							$.get( '/games/{{ $match->matchMakingServer->gameServer->game->slug }}/gameservers/{{ $match->matchMakingServer->gameServer->slug }}/status', function( data ) {
								var serverStatus = JSON.parse(data);
								updateStatus('#serverstatus_{{ $match->matchMakingServer->gameServer->id }}', serverStatus);
							});
							var start = new Date;

							setInterval(function() {
								$.get( '/games/{{ $match->matchMakingServer->gameServer->game->slug }}/gameservers/{{ $match->matchMakingServer->gameServer->slug }}/status', function( data ) {
									var serverStatus = JSON.parse(data);
									updateStatus('#serverstatus_{{ $match->matchMakingServer->gameServer->id }}', serverStatus);
								});
							}, 30000);
						});
					</script>

					<div class="mb-3" id="serverstatus_{{ $match->matchMakingServer->gameServer->id }}">
						<div><i class="fas fa-map-marked-alt"></i><strong > Map: </strong><span id="serverstatus_{{ $match->matchMakingServer->gameServer->id }}_map"></span></div>
						<div><i class="fas fa-users"></i><span class ="ms-2"><strong class ="ms-2" > Players: </strong></span><span id="serverstatus_{{ $match->matchMakingServer->gameServer->id }}_players"></span></div>
					</div>
		</div>
		<div class="col-sm">
					@if($match->matchMakingServer->gameServer->game->connect_stream_url && $match->matchMakingServer->gameServer->stream_port != 0)
					<a class="btn btn-primary btn-block" href="{{ Helpers::resolveServerCommandParameters($match->matchMakingServer->gameServer->game->connect_stream_url, NULL, $availableParameters) }}" role="button">Join Stream</a>
					@endif
					@if($match->matchMakingServer->gameServer->game->connect_game_url && $match->getMatchTeamPlayer(Auth::id()))
					<a class="btn btn-primary btn-block " id="connectGameUrl" href="{{ Helpers::resolveServerCommandParameters($match->matchMakingServer->gameServer->game->connect_game_url, NULL, $availableParameters) }}" role="button">Join Game</a>
					@endif
					@if($match->matchMakingServer->gameServer->game->connect_game_command && $match->getMatchTeamPlayer(Auth::id()))
						<div class="input-group mt-2 mb-3">
							<input class="form-control" id="connectGameCommand{{ $availableParameters->gameServer->id }}" type="text" readonly value="{{ Helpers::resolveServerCommandParameters($match->matchMakingServer->gameServer->game->connect_game_command, NULL, $availableParameters) }}">
							<span class="input-group-btn">
							<button class="btn btn-primary " type="button" onclick="copyToClipBoard('connectGameCommand{{$availableParameters->gameServer->id}}')"><i class="fas fa-external-link-alt"></i></button>
						</div>
					@endif

		</div>

		</div>
	@endif


		{{-- TODO --}}
		@if ( !$match->getMatchTeamPlayer(Auth::id()) && $match->teams()->count() < $match->team_count )

			<div class="card mb-3">
				<div class="card-header @if(Colors::isBodyDarkMode()) border-light @endif">
					<i class="fa fa-plus fa-fw"></i> @lang('matchmaking.addteam')
				</div>
				<div class="card-body">
					<div class="list-group">
						{!! Html::form('POST', '/matchmaking/'.$match->id.'/team/add') !!}
							<div class="mb-3">
								{!! Html::label(__('matchmaking.teamname'), 'teamname') !!}
								{!! Html::text('teamname', null)->id('teamname')->class('form-control') !!}
							</div>

							<button type="submit" class="btn btn-success btn-block">@lang('matchmaking.add')</button>
						{!! Html::form()->close() !!}
					</div>
				</div>
			</div>

		@endif

		<div class="row card-deck">

			@php

				$winnerTeam;
				foreach ($match->teams as $team)
				{
					if(isset($winnerTeam))
					{
						if($winnerTeam->team_score < $team->team_score)
						{
							$winnerTeam = $team;
						}
					}
					else
					{
						$winnerTeam = $team;
					}
				}
			@endphp

			@foreach ($match->teams as $team)
				<div class="col">
					<div class="card me-0 ms-0 mb-3">
						<div class="card-header @if(Colors::isBodyDarkMode()) border-light  @endif">
							<div class="row">
								<div class="col">
									<h4>@lang('matchmaking.team') #{{ $loop->iteration }}: {{ $team->name }}</h4> <small>{{ $team->players->count() }}/ {{ $match->team_size }}</small>
								</div>
								<div class="col">
									@if($team->match->status != "LIVE" && $team->match->status != "COMPLETE" && $team->match->status != "PENDING" && $team->match->status != "WAITFORPLAYERS" && ($team->match->owner_id == Auth::id() || $team->team_owner_id == Auth::id()))
										<div class="row">
											<div class="col">
												<button class="btn btn-warning btn-sm btn-block float-end text-nowrap" data-bs-toggle="modal" data-bs-target="#editTeamModal_{{ $team->id }}"><i class="fas fa-user-edit"></i> @lang('matchmaking.editteam')</button>
											</div>

											@if($team->id != $team->match->oldestTeam->id )
												<div class="col">
													{!! Html::form('POST', '/matchmaking/' . $match->id . '/team/'. $team->id . '/delete')->attribute('onsubmit', 'return ConfirmDelete()') !!}
													{!! Html::hidden('_method', 'DELETE') !!}
													<button type="submit" class="btn btn-danger btn-sm btn-block float-end text-nowrap"><i class="fas fa-trash"></i> @lang('matchmaking.deleteteam')</button>
													{!! Html::form()->close() !!}
												</div>
											@endif
										</div>
									@endif
									@if (!$team->match->getMatchTeamPlayer(Auth::id()) && $team->players->count() < $match->team_size )

										{!! Html::form('POST', '/matchmaking/'.$match->id.'/team/'. $team->id .'/teamplayer/add') !!}
										<button type="submit" class="btn btn-success btn-sm btn-block float-end"><i class="fas fa-user-plus"></i> @lang('matchmaking.jointeam')</button>
										{!! Html::form()->close() !!}

									@endif
									@if ($team->match->getMatchTeamPlayer(Auth::id()) && !$match->getMatchTeamOwner(Auth::id()) && $team->players->count() < $match->team_size && $team->match->getMatchTeamPlayer(Auth::id())->team->id != $team->id)
									{!! Html::form('POST', '/matchmaking/'.$match->id.'/team/'. $team->id .'/teamplayer/'. $team->match->getMatchTeamPlayer(Auth::id())->id .'/change') !!}
										<button type="submit" class="btn btn-success btn-sm btn-block float-end"><i class="fas fa-user-plus"></i> @lang('matchmaking.switchteam')</button>
									{!! Html::form()->close() !!}
									@endif
									@if($match->status == "COMPLETE" || ($match->status == "LIVE" && isset($match->game) && isset ($match->game->gamematchapihandler) && $match->game->matchmaking_autoapi))
										<div class="text-center float-end">
											<h4 style="width:50px;" class="border p-2 @if($winnerTeam->id == $team->id) border-success bg-success-light text-success @else border-danger bg-danger-light text-danger @endif">{{ $team->team_score }}</h4>
										</div>
									@endif
								</div>
							</div>
						</div>
						<div class="card-body">
							@if($team->match->status != "LIVE" &&  $team->match->status != "COMPLETE" &&  $team->match->status != "PENDING" && $team->match->status != "WAITFORPLAYERS" &&  $team->match->status != "DRAFT" && ($team->match->owner_id == Auth::id() || $team->team_owner_id == Auth::id()))
								<div>
									<p class="mb-0 mt-2">@lang('matchmaking.inviteurl')</p>
									<div class="input-group mb-3 mt-0" style="width: 100%">
										<input class="form-control" id="teaminviteurl_{{$team->id}}" type="text" readonly value="{{ URL::to('/') }}/matchmaking/invite/?url={{ $team->team_invite_tag }}">
										<button class="btn btn-primary" type="button" onclick="copyToClipBoard('teaminviteurl_{{$team->id}}')"><i class="far fa-clipboard"></i></button>
									</div>
								</div>
							@endif
							<div class="dataTable_wrapper">
								<table width="100%" class="table table-striped table-hover" id="dataTables-example">
									<thead>
										<tr>
											<th></th>
											<th>@lang('matchmaking.user')</th>
											<th>@lang('matchmaking.name')</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach ($team->players as $teamplayer)
											<tr>
												<td>
													<img class="img-fluid rounded" style="max-width: 20%;" src="{{ $teamplayer->user->avatar }}">
												</td>
												<td>
													{{ $teamplayer->user->username }}
													@if ($teamplayer->user->steamid)
														- <span class="text-muted"><small>Steam: {{ $teamplayer->user->steamname }}</small></span>
													@endif
													@if ($teamplayer->user->id == $team->team_owner_id)
														<i class="fas fa-chess-king text-primary"></i>
													@endif
													@if ($teamplayer->user->id == $match->owner_id)
														<i class="fas fa-chess-queen text-success"></i>
													@endif
												</td>
												<td>
													{{ $teamplayer->user->firstname }}

												</td>

												<td width="15%">
													@if ($teamplayer->user->id == $team->team_owner_id)
														@lang('matchmaking.teamowner')
													@endif
													@if($teamplayer->user->id == $match->owner_id)
														@lang('matchmaking.matchowner')
													@endif
													@if($teamplayer->user->id != $team->team_owner_id && $teamplayer->user->id != $match->owner_id && $team->match->status != "LIVE" && $team->match->status != "WAITFORPLAYERS" &&  $team->match->status != "COMPLETE"&&  $team->match->status != "PENDING" && ($team->match->owner_id == Auth::id() || $team->team_owner_id == Auth::id()) )
														{!! Html::form('POST', '/matchmaking/' . $match->id . '/team/'. $team->id . '/teamplayer/'. $teamplayer->id .'/delete')->attribute('onsubmit', 'return ConfirmDelete()') !!}
															{!! Html::hidden('_method', 'DELETE') !!}
															<button type="submit" class="btn btn-danger btn-sm btn-block">@lang('matchmaking.removefrommatch')</button>
														{!! Html::form()->close() !!}
													@endif
												</td>
											</tr>
										@endforeach

									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	@else
		<script>
			window.addEventListener("load", function(){
			$("#showMatchErrorModal").modal()
		});
		</script>

	@endif

	@if (isset($match->matchReplays) && count($match->matchReplays) > 0)

			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-info-circle fa-fw"></i> @lang('matchmaking.Replays')
				</div>
				<div class="card-body">



					<table width="100%" class="table table-striped table-hover" id="dataTables-example">
						<thead>
							<tr>
								<th>@lang('matchmaking.ReplayName')</th>
								<th>@lang('matchmaking.ReplaySize')</th>
								<th>@lang('matchmaking.ReplayCreated')</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($match->matchReplays as $matchReplay)
								<tr>
									<td>
										<a href="/storage{{ App\MatchReplay::getReplayPath($match->game, $matchReplay->name) }}"> {{$matchReplay->name}}</a>
									</td>

									<td>
										{{ App\MatchReplay::getReplaySize($match->game, $matchReplay->name) }}
									</td>
									<td>
										{{ $matchReplay->created_at }}
									</td>
								</tr>
							@endforeach

						</tbody>
					</table>

				</div>
			</div>

	@endif

</div>


<!-- Modals -->
@foreach ($match->teams as $team)

	<div class="modal fade" id="editTeamModal_{{ $team->id }}" tabindex="-1" role="dialog" aria-labelledby="editTeamModalLabel_{{ $team->id }}" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="editTeamModalLabel_{{ $team->id }}"><i class="fas fa-user-edit"></i> @lang('matchmaking.editteam')</h4>
					<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
				</div>
				<div class="modal-body">
					{!! Html::form('POST', '/matchmaking/'.$match->id.'/team/'.$team->id.'/update') !!}
					<div class="mb-3">
						{!! Html::label(__('matchmaking.teamname'), 'editteamname') !!}
						{!! Html::text('editteamname', $team->name)->id('editteamname')->class('form-control') !!}
					</div>
					<button type="submit" class="btn btn-success btn-block">@lang('matchmaking.submit')</button>
					{!! Html::form()->close() !!}
				</div>
			</div>
		</div>
	</div>
@endforeach

<div class="modal fade" id="editMatchModal" tabindex="-1" role="dialog" aria-labelledby="editMatchModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="editMatchModalLabel">@lang('matchmaking.editmatch')</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			<div class="modal-body">
				{!! Html::form('POST', '/matchmaking/'.$match->id.'/update') !!}
							<div class="mb-3">
								{!! Html::label(__('matchmaking.games'), 'game_id') !!}
								<select name="game_id" id="game_id" class="form-control">
									@foreach(Helpers::getMatchmakingGameSelectArray() as $value => $text)
										<option value="{{ $value }}" {{ $match->game_id == $value ? 'selected' : '' }}>{{ $text }}</option>
									@endforeach
								</select>
							</div>
							<div class="mb-3">
								{!! Html::label(__('matchmaking.teamsize'), 'team_size') !!}
								<select name="team_size" id="team_size" class="form-control">
									<option value="1v1" {{ $match->team_size . "v" . $match->team_size == '1v1' ? 'selected' : '' }}>1v1</option>
									<option value="2v2" {{ $match->team_size . "v" . $match->team_size == '2v2' ? 'selected' : '' }}>2v2</option>
									<option value="3v3" {{ $match->team_size . "v" . $match->team_size == '3v3' ? 'selected' : '' }}>3v3</option>
									<option value="4v4" {{ $match->team_size . "v" . $match->team_size == '4v4' ? 'selected' : '' }}>4v4</option>
									<option value="5v5" {{ $match->team_size . "v" . $match->team_size == '5v5' ? 'selected' : '' }}>5v5</option>
									<option value="6v6" {{ $match->team_size . "v" . $match->team_size == '6v6' ? 'selected' : '' }}>6v6</option>
								</select>
							</div>
							<div class="mb-3">
								{!! Html::label(__('matchmaking.teamcounts'), 'team_count') !!}
								{!! Html::number('team_count', $match->team_count)->id('team_size')->class('form-control') !!}
							</div>
							<div class="mb-3">
								<div class="form-check">
										<label class="form-check-label">
											{!! Html::checkbox('ispublic', null, $match->ispublic)->id('ispublic') !!} is public (show match publicly for signup)
										</label>
								</div>
							</div>

							<button type="submit" class="btn btn-success btn-block">Submit</button>
						{!! Html::form()->close() !!}
			</div>
		</div>
	</div>
</div>



@if($teamJoin != null)
	<div class="modal fade" id="jointeamModal" tabindex="-1" role="dialog" aria-labelledby="jointeamModallabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="jointeamModallabel">@lang('matchmaking.jointeam')</h4>
					<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
				</div>
				<div class="modal-body">
					@if(!$match->getMatchTeamPlayer(Auth::Id()))
						@lang('matchmaking.doyouwanttojointeam') <strong>{{ $teamJoin->name}} </strong>


						{!! Html::form('POST', '/matchmaking/'.$match->id.'/team/'. $teamJoin->id .'/teamplayer/add') !!}
						<button type="submit" class="btn btn-success btn-block ">@lang('matchmaking.jointeam')</button>
						{!! Html::form()->close() !!}
					@else
						@lang('matchmaking.cannotjoinyoualreadyareinateam')
					@endif

				</div>
			</div>
		</div>
	</div>




	<script>
		window.addEventListener("load", function(){
		$("#jointeamModal").modal()
	});
	</script>
@endif


<div class="modal fade" id="showMatchErrorModal" tabindex="-1" role="dialog" aria-labelledby="showMatchErrorModallabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="showMatchErrorModallabel">@lang('matchmaking.error')</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			<div class="modal-body">
					@lang('matchmaking.nopermissions')

					<a href="/matchmaking/">@lang('matchmaking.matchmakinghome')</a>

			</div>
		</div>
	</div>
</div>



@endsection

@section ('scripts')

<script language="javascript" type="text/javascript">

	function copyToClipBoard(inputId) {
		/* Get the text field */
		var copyText = document.getElementById(inputId);

		/* Select the text field */
		copyText.select();
		copyText.setSelectionRange(0, 99999); /*For mobile devices*/

		/* Copy the text inside the text field */
		document.execCommand("copy");
	}

	function isABootstrapModalOpen() {
		return $('.modal.show').length > 0;
	}

	function isAddTeamActive(){

		if($('#teamname').val() != undefined && ($('#teamname').val() != "" || $('#teamname').is(":focus")))
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	jQuery( function()
	{
		$('#autoReload').change(function()
		{
			localStorage.setItem('autoReload', $(this).prop('checked'));
		});

		if(localStorage.getItem('autoReload') != "false")
		{
			$('#autoReload').prop("checked", true);
		}

		setInterval(function()
		{
			if(!isABootstrapModalOpen() && !isAddTeamActive() && localStorage.getItem('autoReload') != "false")
			{
				location.reload();
			}
		}, 10000);
	});
</script>

@endsection
