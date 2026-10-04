@extends ('layouts.admin-default')

@section ('page_title', 'Match '. $match->id)

@section ('content')

<div class="row">
	<div class="col-lg-12">
	<h3 class="pb-2 mt-4 mb-4 border-bottom">Match {{ $match->id }}</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item active">
				Matchmaking
			</li>
		</ol>
	</div>
</div>

<div class="row">

	<div class="col-lg-8">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-th-list fa-fw"></i> Teams:
			</div>

			@foreach ($match->teams as $team)
				<div class="card-body">
					<div class="row">
						<div class="col-sm">
							<h4>Team #{{ $team->id }}: {{ $team->name }} </h4>
						</div>
						<div class="col-sm mt-3">
							@if($team->match->status != "LIVE" && $team->match->status != "PENDING" && $team->match->status != "WAITFORPLAYERS" &&  $team->match->status != "COMPLETE")
								<a href="#" class="btn btn-warning btn-sm btn-block float-end" data-bs-toggle="modal" data-bs-target="#editTeamModal_{{ $team->id }}">Edit Team</a>

								@if($team->id != $team->match->oldestTeam->id)
									{{ Form::open(array('url'=>'/admin/matchmaking/' . $match->id . '/team/'. $team->id . '/delete', 'onsubmit' => 'return ConfirmDelete()')) }}
									{{ Form::hidden('_method', 'DELETE') }}
									<button type="submit" class="btn btn-danger btn-sm btn-block float-end">Delete Team</button>
									{{ Form::close() }}
								@endif


							@endif
						</div>
					</div>
					@if($team->match->status != "LIVE" && $team->match->status != "PENDING" && $team->match->status != "WAITFORPLAYERS" &&  $team->match->status != "COMPLETE")
						<div class="row">

							<div class="col-sm">
								<p class="mb-0 mt-2">Invite Url </p>
							</div>
							<div class="col-sm">
								<div class="input-group mb-3 mt-0" style="width: 100%">
									<input class="form-control" id="teaminviteurl_{{$team->id}}" type="text" readonly value="{{ URL::to('/') }}/matchmaking/invite/?url={{ $team->team_invite_tag }}">
									<button class="btn btn-primary" type="button" onclick="copyToClipBoard('teaminviteurl_{{$team->id}}')"><i class="far fa-clipboard"></i></button>
								</div>
							</div>
						</div>
					@endif
					<div class="dataTable_wrapper">
						<table width="100%" class="table table-striped table-hover" id="dataTables-example">
							<thead>
								<tr>
									<th></th>
									<th>User</th>
									<th>Name</th>
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
											@if (isset($teamplayer->user->username))
												{{ $teamplayer->user->username }}
											@endif

											@if ($teamplayer->user->steamid)
												- <span class="text-muted"><small>Steam: {{ $teamplayer->user->steamname }}</small></span>
											@endif
										</td>
										<td>
											{{ $teamplayer->user->firstname }} {{ $teamplayer->user->surname }}

										</td>

										<td width="15%">
											@if ($teamplayer->user->id != $team->team_owner_id)
												@if($team->match->status != "LIVE" && $team->match->status != "PENDING" && $team->match->status != "WAITFORPLAYERS" &&  $team->match->status != "COMPLETE")
													{{ Form::open(array('url'=>'/admin/matchmaking/' . $match->id . '/team/'. $team->id . '/teamplayer/'. $teamplayer->id .'/delete', 'onsubmit' => 'return ConfirmDelete()')) }}
														{{ Form::hidden('_method', 'DELETE') }}
														<button type="submit" class="btn btn-danger btn-sm btn-block">Remove from Match</button>
													{{ Form::close() }}
												@endif
											@else
												Teamowner
											@endif
										</td>
									</tr>
								@endforeach

							</tbody>
						</table>
					</div>
					@if(($team->match->status != "LIVE" && $team->match->status != "PENDING" && $team->match->status != "WAITFORPLAYERS" &&  $team->match->status != "COMPLETE") && $team->players->count() < $match->team_size)
					{{ Form::open(array('url'=>'/admin/matchmaking/'.$match->id.'/team/'. $team->id .'/teamplayer/add' )) }}
					<div class="row">
						<div class="col-sm">
							{{ Form::label('userid','Add User',array('id'=>'','class'=>'')) }}
								{{
									Form::select(
										'userid',
										$availableUsers,
										NULL ,
										array(
											'id'    => 'userid',
											'class' => 'form-control'
										)
									)
								}}
						</div>
						<div class="col-sm mt-4">
							<button type="submit" class="btn btn-success btn-block">Add</button>

						</div>

					</div>
					{{ Form::close() }}
					@endif
				</div>
			@endforeach


		</div>
	</div>


	<div class="col-lg-4">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-info-circle fa-fw"></i> Match Control
			</div>
			<div class="card-body">
				<div class="list-group">
					<p>Current Status: {{$match->status}}</p>
					@if(isset($match->game) && isset($match->matchMakingServer) && ($match->status == "LIVE" || $match->status == "WAITFORPLAYERS" ))
					<p>Current Server: {{ $match->matchMakingServer->gameServer->name}}</p>
					@endif
					@if($match->status == "DRAFT")
						<div class="mb-3">
						{{ Form::open(array('url'=>'/admin/matchmaking/'.$match->id.'/open' )) }}
							<button type="submit" class="btn btn-success btn-block">Open Match</button>
						{{ Form::close() }}
						</div>
					@endif
					@if($match->status == "OPEN")
						<p class="mb-0">Match Invite Url</p>
						<div class="input-group mb-3 mt-0" style="width: 100%">
							<input class="form-control" id="matchinviteurl" type="text" readonly value="{{ URL::to('/') }}/matchmaking/invite/?url={{ $match->invite_tag }}">
							<button class="btn btn-primary" type="button" onclick="copyToClipBoard('matchinviteurl')"><i class="far fa-clipboard"></i></button>
						</div>

						<div class="mb-3">
						{{ Form::open(array('url'=>'/admin/matchmaking/'.$match->id.'/start' )) }}
							<button type="submit" class="btn btn-success btn-block">Start Match</button>
						{{ Form::close() }}
						</div>
					@endif
					@if($match->status == "PENDING" && isset($match->game) )
						<div class="mb-3">
							<button class="btn btn-primary btn-sm btn-block" data-bs-toggle="modal" data-bs-target="#selectServerModal{{ $match->id }}">Select Server</button>
						</div>
					@endif
					@if($match->status == "PENDING" && !isset($match->game) )
						<div class="mb-3">
							{{ Form::open(array('url'=>'/admin/matchmaking/'.$match->id.'/start' )) }}
								<button type="submit" class="btn btn-success btn-block"><i class="fas fa-play"></i> @lang('matchmaking.startmatch')</button>
							{{ Form::close() }}
						</div>
					@endif
					@if($match->status == "LIVE" || $match->status == "WAITFORPLAYERS")
						@if(isset($match->game) && isset($match->matchMakingServer))
							<div class="mb-3">
								<button class="btn btn-primary btn-sm btn-block" data-bs-toggle="modal" data-bs-target="#executeServerCommandModal{{ $match->id }}">Execute Command</button>
							</div>
						@endif
							@if(isset($match->game) && isset($match->matchMakingServer))
								@if (isset($match->game->matchmaking_autoapi) && $match->game->matchmaking_autoapi)
									<button class="btn btn-danger btn-sm btn-block" data-bs-toggle="modal" data-bs-target="#selectServerModal{{ $match->id }}">Change Server</button>
								@else
									<button class="btn btn-primary btn-sm btn-block" data-bs-toggle="modal" data-bs-target="#selectServerModal{{ $match->id }}">Change Server</button>
								@endif
							@endif
							@if(isset($match->game) && !isset($match->matchMakingServer))
							<button class="btn btn-primary btn-sm btn-block" data-bs-toggle="modal" data-bs-target="#selectServerModal{{ $match->id }}">Select Server</button>
							@endif
						<div class="mb-3">
						{{ Form::open(array('url'=>'/admin/matchmaking/'.$match->id.'/finalize' )) }}
						@foreach ($match->teams as $team)

							{{ Form::label('teamscore_'. $team->id, 'Score of '.$team->name ,array('id'=>'','class'=>'')) }}
							{{ Form::number('teamscore_'. $team->id, $team->team_score, array('id'=>'teamscore_'. $team->id,'class'=>'form-control mb-3')) }}
						@endforeach

						@if (isset($match->game->matchmaking_autoapi) && $match->game->matchmaking_autoapi)
							<button type="submit" class="btn btn-danger btn-block ">Finalize Match Manually</button>
							<small style="color: red">This does not end the match remotely on the assigned server if it is still active. You have to make sure that its manually ended and the server is free before finalizing the match manually.</small>
						@else
							<button type="submit" class="btn btn-success btn-block ">Finalize Match</button>
						@endif
						{{ Form::close() }}
						</div>
					@endif

					@if($match->status == "COMPLETE")
					@foreach ($match->teams as $team)
						<p>{{$team->name}} Score: {{$team->team_score}}</p>
					@endforeach
					@endif
					
					{{ Form::open(array('url'=>'/admin/matchmaking/' . $match->id, 'onsubmit' => 'return ConfirmDelete()')) }}
					{{ Form::hidden('_method', 'DELETE') }}
						<button type="submit" class="btn btn-danger btn-sm btn-block">Delete</button>
					{{ Form::close() }}



				</div>
			</div>
		</div>
		@if($match->status != "LIVE" && $team->match->status != "PENDING" && $team->match->status != "WAITFORPLAYERS" &&  $match->status != "COMPLETE")
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-pencil fa-fw"></i> Edit Match
				</div>
				<div class="card-body">
					<div class="list-group">
						{{ Form::open(array('url'=>'/admin/matchmaking/'.$match->id.'/update' )) }}
							<div class="mb-3">
								{{ Form::label('game_id','Game',array('id'=>'','class'=>'')) }}
								{{
									Form::select(
										'game_id',
										Helpers::getMatchmakingGameSelectArray(),
										$match->game_id,
										array(
											'id'    => 'game_id',
											'class' => 'form-control'
										)
									)
								}}
							</div>
							<div class="mb-3">
								{{ Form::label('team_size','Team Size',array('id'=>'','class'=>'')) }}
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
										$match->team_size . "v" . $match->team_size ,
										array(
											'id'    => 'team_size',
											'class' => 'form-control'
										)
									)
								}}
							</div>
							<div class="mb-3">
								{{ Form::label('team_count','Team count',array('id'=>'','class'=>'')) }}
								{{
									Form::number('team_count',
										$match->team_count,
										array(
											'id'    => 'team_size',
											'class' => 'form-control'
										))
								}}
							</div>
							<div class="mb-3">
								{{ Form::label('ownerid','Match Owner',array('id'=>'','class'=>'')) }}
								{{
									Form::select(
										'ownerid',
										$users,
										$match->owner_id,
										array(
											'id'    => 'ownerid',
											'class' => 'form-control'
										)
									)
								}}
							</div>
							<div class="mb-3">
								<div class="form-check">
										<label class="form-check-label">
											{{ Form::checkbox('ispublic', null, $match->ispublic, array('id'=>'ispublic')) }} is public (show match publicly for signup)
										</label>
								</div>
							</div>

							<button type="submit" class="btn btn-success btn-block">Submit</button>
						{{ Form::close() }}
					</div>
				</div>
			</div>



			@if ( $match->team_count == 0 || $match->team_count != $match->teams->count() )
				<div class="card mb-3">
					<div class="card-header">
						<i class="fa fa-plus fa-fw"></i> Add Team
					</div>
					<div class="card-body">
						<div class="list-group">
							{{ Form::open(array('url'=>'/admin/matchmaking/'.$match->id.'/team/add' )) }}
								<div class="mb-3">
									{{ Form::label('teamname','Team Name',array('id'=>'','class'=>'')) }}
									{{ Form::text('teamname',NULL,array('id'=>'teamname','class'=>'form-control')) }}
								</div>
								<div class="mb-3">
									{{ Form::label('teamowner','Team Owner',array('id'=>'','class'=>'')) }}
									{{
										Form::select(
											'teamowner',
											$availableUsers,
											NULL ,
											array(
												'id'    => 'teamowner',
												'class' => 'form-control'
											)
										)
									}}
								</div>


								<button type="submit" class="btn btn-success btn-block">Add</button>
							{{ Form::close() }}
						</div>
					</div>
				</div>
			@endif

		@endif



	@if (isset($match->matchReplays) && count($match->matchReplays) > 0)

			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-info-circle fa-fw"></i> Replays
				</div>
				<div class="card-body">



					<table width="100%" class="table table-striped table-hover" id="dataTables-example">
						<thead>
							<tr>
								<th>Name</th>
								<th>Size</th>
								<th>Created</th>
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

									<td width="15%">
												{{ Form::open(array('url'=>'/admin/replays/'. $matchReplay->id , 'onsubmit' => 'return ConfirmDelete()')) }}
													{{ Form::hidden('_method', 'DELETE') }}
													<button type="submit" class="btn btn-danger btn-sm btn-block">Remove</button>
												{{ Form::close() }}

									</td>
								</tr>
							@endforeach

						</tbody>
					</table>

				</div>
			</div>
		
	@endif
	</div>	


</div>

<!-- Modals -->
@if(isset($match->game) && isset($match->matchMakingServer))
<!-- execute Command Modal -->
<div class="modal fade" id="executeServerCommandModal{{ $match->id }}" tabindex="-1" role="dialog" aria-labelledby="executeServerCommandModalLabel{{ $match->id }}" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="executeServerCommandModalLabel{{ $match->id }}">Execute Server Command for Match #{{ $match->id }}</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			<div class="modal-body">
				@if (isset($match->game->matchmaking_autoapi) && $match->game->matchmaking_autoapi)
					<small style="color: orange">You have the matchmaking autoapi enabled on this game. This means, you should never have to execute commands here if everything works like intended. Use this with caution!</small>
				@endif
				<div class="row row-seperator">
					<div class="col-12 col-md-3">
						{{ Form::label("Command", NULL, array('id'=>'','class'=>'')) }}
					</div>
					<div class="col-12 col-md-6">
						{{ Form::label("parameter", NULL, array('id'=>'','class'=>'')) }}
					</div>
					<div class="col-12 col-md-3">
						{{ Form::label("execute", NULL, array('id'=>'','class'=>'')) }}
					</div>
				</div>
				@foreach ($match->game->getMatchCommands() as $matchCommand)
					{{ Form::open(array('url'=>'/admin/games/' . $match->game->slug . '/gameservercommands/execute/' . $match->matchMakingServer->gameServer->slug .'/matchmaking/' . $match->id, 'id'=>'executeServerCommandModal')) }}
						{{ Form::hidden('command', $matchCommand->id) }}
						{{ Form::hidden('match_id', $match->game->id) }}
						match_id
						<div class="row row-seperator">
							<div class="col-12 col-md-3">
								<h4>{{ $matchCommand->name }}</h4>
							</div>
							<div class="col-12 col-md-6">
								<div class="row">
									@foreach(App\GameServerCommandParameter::getParameters($matchCommand->command) as $gameServerCommandParameter)
										<div class="mb-3 col-sm-12  col-md-6">
											{{ Form::label($gameServerCommandParameter->slug, $gameServerCommandParameter->name, array('id'=>'','class'=>'')) }}
											{{ Form::select($gameServerCommandParameter->slug, $gameServerCommandParameter->getParameterSelectArray(), null, array('id'=>$gameServerCommandParameter->slug,'class'=>'form-control')) }}
										</div>
									@endforeach
								</div>
							</div>
							<div class="col-12 col-md-3">
								<button type="submit" class="btn btn-success">Execute</button>
							</div>
						</div>
					{{ Form::close() }}
				@endforeach
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
@endif
@if(isset($match->game))
			<!-- Select Server Modal -->
			<div class="modal fade" id="selectServerModal{{ $match->id }}" tabindex="-1" role="dialog" aria-labelledby="selectServerModalLabel{{ $match->id }}" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="selectServerModalLabel{{ $match->id }}">Select Server for Match #{{ $match->id }}</h4>
							<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
						</div>
						{{ Form::open(array('url'=>'/admin/matchmaking/' . $match->id . ((isset($match->matchMakingServer)) ? '/serverupdate':'/serverstore') , 'id'=>'selectServerModal')) }}



						<div class="modal-body">
							@if (isset($match->game->matchmaking_autoapi) && $match->game->matchmaking_autoapi)
								<small style="color: red">This does not end the match remotely on the currently assigned server and does not load it on the new assigned Server. You have to manually execute the necessary commands on your server with the Execute Command button.</small>
							@endif
							@if (isset($match->matchMakingServer))
							<br><br><p><small style="color: red">If you need to delete the current assignment, you can do that on the <a href="/admin/games/{{$match->game->slug}}/gameservers/{{$match->matchMakingServer->gameServer->slug}}">gameservers detail page</a></small></p>
							@endif
								<div class="mb-3">
									{{ Form::label('gameServer','Server',array('id'=>'','class'=>'')) }}
									{{ Form::select('gameServer', $match->game->getGameServerSelectArray(), null, array('id'=>'gameServer','class'=>'form-control')) }}
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success">Select</button>
								<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
							</div>
						{{ Form::close() }}
					</div>
				</div>

			</div>
		@endif
@foreach ($match->teams as $team)

	<div class="modal fade" id="editTeamModal_{{ $team->id }}" tabindex="-1" role="dialog" aria-labelledby="editTeamModalLabel_{{ $team->id }}" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="editTeamModalLabel_{{ $team->id }}">Edit Team</h4>
					<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
				</div>
				<div class="modal-body">
					{{ Form::open(array('url'=>'/admin/matchmaking/'.$match->id.'/team/'.$team->id.'/update' )) }}
					<div class="mb-3">
						{{ Form::label('teamname','Team Name',array('id'=>'','class'=>'')) }}
						{{ Form::text('teamname',$team->name,array('id'=>'teamname','class'=>'form-control')) }}
					</div>
					<div class="mb-3">
						{{ Form::label('teamowner','Team Owner',array('id'=>'','class'=>'')) }}
						{{
							Form::select(
								'teamowner',
								$users,
								$team->team_owner_id ,
								array(
									'id'    => 'teamowner',
									'class' => 'form-control'
								)
							)
						}}
					</div>
					<button type="submit" class="btn btn-success btn-block">Submit</button>
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
@endforeach


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

@endsection
