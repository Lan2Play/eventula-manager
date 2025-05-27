@extends ('layouts.admin-default')

@section ('page_title', 'Matchmaking')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Matchmaking</h3>
	</div>
</div>

@if ($isMatchMakingEnabled)
	<div class="row">
		<div class="col-12">

			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-th-list fa-fw"></i> Pending Matches
				</div>
				<div class="card-body">
					<div class="dataTable_wrapper">

						<table width="100%" class="table table-striped table-hover" id="dataTables-example">
							<thead>
								<tr>
									<th>ID</th>
									<th>Team1 Name</th>
									<th>Team1 Owner</th>
									<th>Match Owner</th>
									<th>Teamcount</th>
									<th>Teamsize</th>
									<th>Status</th>
									<th>Updatetime</th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($pendingMatches as $match)
									<tr>
										<td>{{ $match->id }}</td>
										<td>
											{{ $match->oldestTeam->name }}
										</td>
										<td>				
											@if (isset($match->oldestTeam->owner->username))
												{{ $match->oldestTeam->owner->username }}
											@else
												Teams randomized
											@endif
										</td>
										<td>
											@if (isset($match->owner->username))
												{{ $match->owner->username }}
											@endif
										</td>
										<td>
											{{ $match->teams->count() }}
											@if(isset($match->team_count) && $match->team_count > 0)
											/ {{ $match->team_count }}
											@endif
										</td>
										<td>{{ $match->team_size }}v{{ $match->team_size }}</td>
										<td>{{ $match->status }}</td>
										<td>{{ $match->updated_at }}</td>
										<td width="15%">
											@if(isset($match->game))
												<button class="btn btn-primary btn-sm btn-block" data-bs-toggle="modal" data-bs-target="#selectServerModal{{ $match->id }}">Select Server</button>
											@else
												{!! Html::form('POST', '/admin/matchmaking/'.$match->id.'/start') !!}
												<button type="submit" class="btn btn-primary btn-sm btn-block">Start Match</button>
												{!! Html::form()->close() !!}
											@endif
										</td>
										<td>
											<a href="/admin/matchmaking/{{ $match->id }}">
												<button type="button" class="btn btn-primary btn-sm btn-block">Edit</button>
											</a>
										</td>
										<td>
											{!! Html::form('POST', '/admin/matchmaking/' . $match->id)->attribute('onsubmit', 'return ConfirmDelete()') !!}
												{!! Html::hidden('_method', 'DELETE') !!}
												<button type="submit" class="btn btn-danger btn-sm btn-block">Delete</button>
											{!! Html::form()->close() !!}
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-th-list fa-fw"></i> Live Matches
				</div>
				<div class="card-body">
					<div class="dataTable_wrapper">

						<table width="100%" class="table table-striped table-hover" id="dataTables-example">
							<thead>
								<tr>
									<th>ID</th>
									<th>Team1 Name</th>
									<th>Team1 Owner</th>
									<th>Match Owner</th>
									<th>Teamcount</th>
									<th>Teamsize</th>
									<th>Status</th>
									<th>Updatetime</th>
									<th>Selected Server</th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($liveMatches as $match)
									<tr>
										<td>{{ $match->id }}</td>
										<td>
											{{ $match->oldestTeam->name }}
										</td>
										<td>				
											@if (isset($match->oldestTeam->owner->username))
												{{ $match->oldestTeam->owner->username }}
											@endif
										</td>
										<td>
											@if (isset($match->owner->username))
												{{ $match->owner->username }}
											@endif
										</td>
										<td>
											{{ $match->teams->count() }}
											@if(isset($match->team_count) && $match->team_count > 0)
											/ {{ $match->team_count }}
											@endif
										</td>
										<td>{{ $match->team_size }}v{{ $match->team_size }}</td>
										<td>{{ $match->status }}</td>
										<td>{{ $match->updated_at }}</td>
										<td>
											@if(isset($match->game) && isset($match->matchMakingServer))
												{{ $match->matchMakingServer->gameServer->name}}
											@else
												not set
											@endif
										</td>
										<td>
											@if(isset($match->game) && isset($match->matchMakingServer))
												<button class="btn btn-primary btn-sm btn-block" data-bs-toggle="modal" data-bs-target="#executeServerCommandModal{{ $match->id }}">Execute Command</button>
											@endif
										</td>									
										<td>
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

										</td>
										<td>
											@if (isset($match->game->matchmaking_autoapi) && $match->game->matchmaking_autoapi)
												<button class="btn btn-danger btn-sm btn-block" data-bs-toggle="modal" data-bs-target="#finalizeModal{{ $match->id }}">finalize Match Manually</button>
											@else
												<button class="btn btn-primary btn-sm btn-block" data-bs-toggle="modal" data-bs-target="#finalizeModal{{ $match->id }}">finalize Match</button>
											@endif
										</td>
										<td>
											<a href="/admin/matchmaking/{{ $match->id }}">
												<button type="button" class="btn btn-primary btn-sm btn-block">Edit</button>
											</a>
										</td>
										<td>
											{!! Html::form('POST', '/admin/matchmaking/' . $match->id)->attribute('onsubmit', 'return ConfirmDelete()') !!}
												{!! Html::hidden('_method', 'DELETE') !!}
												<button type="submit" class="btn btn-danger btn-sm btn-block">Delete</button>
											{!! Html::form()->close() !!}
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
		@foreach ($pendingMatches as $match)
		@if(isset($match->game))
			<!-- Select Server Modal -->
			<div class="modal fade" id="selectServerModal{{ $match->id }}" tabindex="-1" role="dialog" aria-labelledby="selectServerModalLabel{{ $match->id }}" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="selectServerModalLabel{{ $match->id }}">Select Server for Match #{{ $match->id }}</h4>
							<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
						</div>
						{!! Html::form('POST', '/admin/matchmaking/' . $match->id . ((isset($match->matchMakingServer)) ? '/serverupdate':'/serverstore'))->id('selectServerModal') !!}

						<div class="modal-body">
							@if (isset($match->game->matchmaking_autoapi) && $match->game->matchmaking_autoapi)
								<small style="color: red">This does not start the match remotely on the assigned server. You have to manually execute the nessecary commands on your server with the Execute Command button.</small>
							@endif
							@if (isset($match->matchMakingServer))
							<br><br><p><small style="color: red">If you need to delete the current assignment, you can do that on the <a href="/admin/games/{{$match->game->slug}}/gameservers/{{$match->matchMakingServer->gameServer->slug}}">gameservers detail page</a></small></p>
							@endif
								<div class="mb-3">
									{!! Html::label('Server', 'gameServer') !!}
									<select name="gameServer" id="gameServer" class="form-control">
										@foreach($match->game->getGameServerSelectArray() as $value => $name)
											<option value="{{ $value }}">{{ $name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success">Select</button>
								<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
							</div>
						{!! Html::form()->close() !!}
					</div>
				</div>

			</div>
		@endif
	@endforeach
	@foreach ($liveMatches as $match)
		@if(isset($match->game))
			<!-- Update Server Modal -->
			<div class="modal fade" id="selectServerModal{{ $match->id }}" tabindex="-1" role="dialog" aria-labelledby="selectServerModalLabel{{ $match->id }}" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="selectServerModalLabel{{ $match->id }}">Select Server for Match #{{ $match->id }}</h4>
							<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
						</div>
						{!! Html::form('POST', '/admin/matchmaking/' . $match->id . ((isset($match->matchMakingServer)) ? '/serverupdate':'/serverstore'))->id('selectServerModal') !!}

						<div class="modal-body">

							@if (isset($match->game->matchmaking_autoapi) && $match->game->matchmaking_autoapi)
								<small style="color: red">This does not end the match remotely on the currently assigned server and does not load it on the new assigned Server. You have to manually execute the nessecary commands on your server with the Execute Command button.</small>
							@endif
							@if (isset($match->matchMakingServer))
							<br><br><p><small style="color: red">If you need to delete the current assignment, you can do that on the <a href="/admin/games/{{$match->game->slug}}/gameservers/{{$match->matchMakingServer->gameServer->slug}}">gameservers detail page</a></small></p>
							@endif
								<div class="mb-3">
									{!! Html::label('Server', 'gameServer') !!}
									<select name="gameServer" id="gameServer" class="form-control">
										@foreach($match->game->getGameServerSelectArray() as $value => $name)
											<option value="{{ $value }}">{{ $name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success">Select</button>
								<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
							</div>
						{!! Html::form()->close() !!}
					</div>
				</div>

			</div>
		@endif
	<!-- Finalize Modal -->
	<div class="modal fade" id="finalizeModal{{ $match->id }}" tabindex="-1" role="dialog" aria-labelledby="finalizeModalLabel{{ $match->id }}" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="finalizeModalLabel{{ $match->id }}">Select Server for Match #{{ $match->id }}</h4>
					<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						{!! Html::form('POST', '/admin/matchmaking/'.$match->id.'/finalize') !!}
						@foreach ($match->teams as $team)
		
							{!! Html::label('Score of '.$team->name, 'teamscore_'. $team->id) !!}
							{!! Html::number('teamscore_'. $team->id, $team->team_score)->id('teamscore_'. $team->id)->class('form-control mb-3') !!}
		
						@endforeach
					</div>
				</div>
				<div class="modal-footer">
					@if (isset($match->game->matchmaking_autoapi) && $match->game->matchmaking_autoapi)
							<button type="submit" class="btn btn-danger btn-block ">Finalize Match Manually</button>
							<small style="color: red">This does not end the match remotely on the assigned server if it is still active. You have to make sure that its manually ended and the server is free before finalizing the match manually.</small>
						@else
							<button type="submit" class="btn btn-success btn-block ">Finalize Match</button>
					@endif
					{!! Html::form()->close() !!}
					
				</div>

			</div>
		</div>

	</div>
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
								{!! Html::label('Command') !!}
							</div>
							<div class="col-12 col-md-6">
								{!! Html::label('parameter') !!}
							</div>
							<div class="col-12 col-md-3">
								{!! Html::label('execute') !!}
							</div>
						</div>
						@foreach ($match->game->getMatchCommands() as $matchCommand)
							{!! Html::form('POST', '/admin/games/' . $match->game->slug . '/gameservercommands/execute/' . $match->matchMakingServer->gameServer->slug .'/matchmaking/' . $match->id)->id('executeServerCommandModal') !!}
								{!! Html::hidden('command', $matchCommand->id) !!}
								<div class="row row-seperator">
									<div class="col-12 col-md-3">
										<h4>{{ $matchCommand->name }}</h4>
									</div>
									<div class="col-12 col-md-6">
										<div class="row">
											@foreach(App\GameServerCommandParameter::getParameters($matchCommand->command) as $gameServerCommandParameter)
												<div class="mb-3 col-sm-12  col-md-6">
													{!! Html::label($gameServerCommandParameter->name, $gameServerCommandParameter->slug) !!}
													<select name="{{ $gameServerCommandParameter->slug }}" id="{{ $gameServerCommandParameter->slug }}" class="form-control">
														@foreach($gameServerCommandParameter->getParameterSelectArray() as $key => $value)
															<option value="{{ $key }}">{{ $value }}</option>
														@endforeach
													</select>
												</div>
											@endforeach
										</div>
									</div>
									<div class="col-12 col-md-3">
										<button type="submit" class="btn btn-success">Execute</button>
									</div>
								</div>
							{!! Html::form()->close() !!}
						@endforeach
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
					</div>
				</div>
			</div>
		</div>
	@endif


	@endforeach
@endif
<div class="row">
	@if (!$isMatchMakingEnabled)
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-info-circle fa-fw"></i> MatchMaking is Currently Disabled...
				</div>
				<div class="card-body">
					<p>The Matchmaking feature can be used to make matches by admins or users without the need of an event tournament.</p>
						{!! Html::form('POST', '/admin/settings/matchmaking/enable') !!}
							<button type="submit" class="btn btn-block btn-success">Enable</button>
						{!! Html::form()->close() !!}
				</div>
			</div>
		</div>
	@else
	<div class="col-lg-8">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-th-list fa-fw"></i> All Matches
			</div>
			<div class="card-body">
				<div class="dataTable_wrapper">

					<table width="100%" class="table table-striped table-hover" id="dataTables-example">
						<thead>
							<tr>
								<th>ID</th>
								<th>Team1 Name</th>
								<th>Team1 Owner</th>
								<th>Match Owner</th>
								<th>Teamcount</th>
								<th>Teamsize</th>
								<th>Status</th>
								<th>Replay(s)</th>
								<th></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($matches as $match)



								<tr @if ($match->status == 'COMPLETE' && isset($match->matchMakingServer)) style="border:2px solid red;" @endif >
									<td>{{ $match->id }}</td>
									<td>
										{{ $match->oldestTeam->name }}
									</td>
									<td>				
										@if (isset($match->oldestTeam->owner->username))
											{{ $match->oldestTeam->owner->username }}
										@endif
									</td>
									<td>
										@if (isset($match->owner->username))
											{{ $match->owner->username }}
										@endif
									</td>
									<td>
										{{ $match->teams->count() }}
										@if(isset($match->team_count) && $match->team_count > 0)
										/ {{ $match->team_count }}
										@endif
									</td>
									<td>{{ $match->team_size }}v{{ $match->team_size }}</td>
									<td>{{ $match->status }}
										@if ($match->status == 'COMPLETE' && isset($match->matchMakingServer))
											<p><small style="color: red">It seems like the match is finished, but the server is still assigned. Depending on your gameservers config it might take some time to free the servers. If it should already be free you can delete the assignment on the <a href="/admin/games/{{$match->game->slug}}/gameservers/{{$match->matchMakingServer->gameServer->slug}}">gameservers detail page</a></small></p>
										@endif
									</td>
									<td>
										@if (isset($match->matchReplays) && count($match->matchReplays) > 0)
										<i class="fa fa-check-circle-o fa-1x" style="color:green"></i>
										@else
										<i class="fa fa-times-circle-o fa-1x" style="color:red"></i>
										@endif
									</td>
									<td width="15%">
										<a href="/admin/matchmaking/{{ $match->id }}">
											<button type="button" class="btn btn-primary btn-sm btn-block">Edit</button>
										</a>
									</td>
									<td width="15%">
										{!! Html::form('DELETE', '/admin/matchmaking/' . $match->id)->attribute('onsubmit', 'return ConfirmDelete()') !!}
											{!! Html::hidden('_method', 'DELETE') !!}
											<button type="submit" class="btn btn-danger btn-sm btn-block">Delete</button>
										{!! Html::form()->close() !!}
									</td>		
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-plus fa-fw"></i> Add Match
			</div>
			<div class="card-body">
				<div class="list-group">
					{!! Html::form('POST', '/admin/matchmaking/') !!}
						<div class="mb-3">
							{!! Html::label('Game', 'game_id') !!}
							<select name="game_id" id="game_id" class="form-control">
								@foreach(Helpers::getMatchmakingGameSelectArray() as $key => $value)
									<option value="{{ $key }}">{{ $value }}</option>
								@endforeach
							</select>
						</div>
						<div class="mb-3">
							{!! Html::label('Team 1 Name', 'team1name') !!}
							{!! Html::text('team1name')->id('team1name')->class('form-control') !!}
						</div>
						<div class="mb-3">
							{!! Html::label('Team 1 Owner', 'team1owner') !!}
							<select name="team1owner" id="team1owner" class="form-control">
								@foreach($users as $key => $value)
									<option value="{{ $key }}">{{ $value }}</option>
								@endforeach
							</select>
						</div>
						<div class="mb-3">
							{!! Html::label('Team Size', 'team_size') !!}
							<select name="team_size" id="team_size" class="form-control">
								<option value="1v1">1v1</option>
								<option value="2v2">2v2</option>
								<option value="3v3">3v3</option>
								<option value="4v4">4v4</option>
								<option value="5v5">5v5</option>
								<option value="6v6">6v6</option>
							</select>
						</div>
						<div class="mb-3">
							{!! Html::label('Team count', 'team_count') !!}
							{!! Html::number('team_count', '2')->id('team_count')->class('form-control') !!}
						</div>
						<div class="mb-3">
							{!! Html::label('Match Owner', 'ownerid') !!}
							<select name="ownerid" id="ownerid" class="form-control">
								@foreach($users as $key => $value)
									<option value="{{ $key }}">{{ $value }}</option>
								@endforeach
							</select>
						</div>
						<div class="mb-3">
							<div class="form-check">
									<label class="form-check-label">
										{!! Html::checkbox('ispublic')->id('ispublic') !!} is public (show match publicly for signup)
									</label>
							</div>
						</div>

						<button type="submit" class="btn btn-success btn-block">Submit</button>
					{!! Html::form()->close() !!}
				</div>
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-info-circle fa-fw"></i> Settings
			</div>
			<div class="card-body">
				<p>The Matchmaking settings has ben moved to the <a href="/admin/settings/systems">Opt System settings</a> </p>
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-info-circle fa-fw"></i> Enable/Disable
			</div>
			<div class="card-body">
				<p>The Matchmaking feature can be used to make matches by admins or users without the need of an event tournament.</p>
					{{ Form::open(array('url'=>'/admin/settings/matchmaking/disable')) }}
						<button type="submit" class="btn btn-block btn-danger">Disable</button>
					{{ Form::close() }}
			</div>
		</div>
	</div>
	@endif
</div>


@endsection
