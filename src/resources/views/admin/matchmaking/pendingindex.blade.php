@extends ('layouts.admin-default')

@section ('page_title', 'Matchmaking')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Matchmaking</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item active">
				Matchmaking
			</li>
		</ol>
	</div>
</div>

<div class="row">
	@if (!$isMatchMakingEnabled)
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-info-circle fa-fw"></i> MatchMaking is Currently Disabled...
				</div>
				<div class="card-body">
					<p>The Matchmaking feature can be used to make matches by admins or users without the need of an event tournament.</p>
						{{ Form::open(array('url'=>'/admin/settings/matchmaking/enable')) }}
							<button type="submit" class="btn btn-block btn-success">Enable</button>
						{{ Form::close() }}
				</div>
			</div>
		</div>
	@else
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
							</tr>
						</thead>
						<tbody>
							@foreach ($matches as $match)
								<tr>
									<td>{{ $match->id }}</td>
									<td>
										{{ $match->oldestTeam->name }}
									</td>
									<td>				
										{{ $match->oldestTeam->owner->username }}
									</td>
									<td>
										{{ $match->owner->username }}
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
											<button class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#selectServerModal{{ $match->id }}">Select Server</button>
										@else
											{{ Form::open(array('url'=>'/admin/matchmaking/'.$match->id.'/start' )) }}
											<button type="submit" class="btn btn-primary btn-sm btn-block">Start Match</button>
											{{ Form::close() }}
										@endif
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
										{{ $match->oldestTeam->owner->username }}
									</td>
									<td>
										{{ $match->owner->username }}
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
											<button class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#executeServerCommandModal{{ $match->id }}">Execute Command</button>
										@endif
									</td>									
									<td>
										@if(isset($match->game) && isset($match->matchMakingServer))
											<button class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#selectServerModal{{ $match->id }}">Change Server</button>
										@endif
										@if(isset($match->game) && !isset($match->matchMakingServer))
										<button class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#selectServerModal{{ $match->id }}">Select Server</button>
										@endif

									</td>
									<td>
										<button class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#finalizeModal{{ $match->id }}">finalize Match</button>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	
	@endif
</div>
@foreach ($matches as $match)
	@if(isset($match->game))
		<!-- Select Server Modal -->
		<div class="modal fade" id="selectServerModal{{ $match->id }}" tabindex="-1" role="dialog" aria-labelledby="selectServerModalLabel{{ $match->id }}" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="selectServerModalLabel{{ $match->id }}">Select Server for Match #{{ $match->id }}</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					{{ Form::open(array('url'=>'/admin/matchmaking/' . $match->id . ((isset($match->matchMakingServer)) ? '/serverupdate':'/serverstore') , 'id'=>'selectServerModal')) }}



					<div class="modal-body">
							<div class="form-group">
								{{ Form::label('gameServer','Server',array('id'=>'','class'=>'')) }}
								{{ Form::select('gameServer', $match->game->getGameServerSelectArray(), null, array('id'=>'gameServer','class'=>'form-control')) }}
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-success">Select</button>
							<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
						</div>
					{{ Form::close() }}
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
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					{{ Form::open(array('url'=>'/admin/matchmaking/' . $match->id . ((isset($match->matchMakingServer)) ? '/serverupdate':'/serverstore') , 'id'=>'selectServerModal')) }}



					<div class="modal-body">
							<div class="form-group">
								{{ Form::label('gameServer','Server',array('id'=>'','class'=>'')) }}
								{{ Form::select('gameServer', $match->game->getGameServerSelectArray(), null, array('id'=>'gameServer','class'=>'form-control')) }}
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-success">Select</button>
							<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
						</div>
					{{ Form::close() }}
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
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					{{ Form::open(array('url'=>'/admin/matchmaking/'.$match->id.'/finalize' )) }}
					@foreach ($match->teams as $team)
	
						{{ Form::label('teamscore_'. $team->id, 'Score of '.$team->name ,array('id'=>'','class'=>'')) }}
						{{ Form::number('teamscore_'. $team->id, 0, array('id'=>'teamscore_'. $team->id,'class'=>'form-control mb-3')) }}
	
					@endforeach
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success btn-block ">Finalize Match</button>
				{{ Form::close() }}
				
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
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
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
											<div class="form-group col-sm-12  col-md-6">
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
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
@endif


@endforeach











@endsection
