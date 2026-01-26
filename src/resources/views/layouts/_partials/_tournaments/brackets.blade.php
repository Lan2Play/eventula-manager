<div class="progress">
	<div class="progress-bar" role="progressbar" aria-valuenow="{{ $tournament->getStandings(null, true)->progress }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $tournament->getStandings(null, true)->progress }}%;">
		{{ $tournament->getStandings(null, true)->progress }}%
	</div>
</div>
@php
	$matchCounter = 1;
@endphp
<div class="row">
	@php
		$matches = $tournament->getMatches();
		$isLoosersBracket = false;
		$prevIsLoosersBracket = false;

		$isFinalsBracket = false;
		$prevIsFinalsBracket = false;

		$matchNumbers = array();
	@endphp
	@foreach ($matches as $roundNumber => $round)

		@php
			$isFinal = false;

			$prevIsLoosersBracket = $isLoosersBracket;
			$isLoosersBracket = false;

			$prevIsFinalsBracket = $isFinalsBracket;
			$isFinalsBracket = false;

			$roundTitle = "Round $roundNumber";

			// Matches are grouped by round, therefor we can use the first match for checks
			$firstMatchInRound = reset($round);

			if($tournament->format == 'single elimination' || $tournament->format == 'double elimination')
			{
				if($tournament->isFinalMatch($firstMatchInRound->id))
				{
					$roundTitle = "Finals";
					$isFinalsBracket = true;
				}
				else if($tournament->isSemiFinalMatch($firstMatchInRound->id))
				{
					$roundTitle = "Semi-Finals";
					$isFinalsBracket = true;
				}
				else if($tournament->isThirdPlaceMatch($firstMatchInRound->id))
				{
					$roundTitle = "Match for 3rd place";
					$isFinalsBracket = true;
				}
				else if($tournament->isLoserBracketMatch($firstMatchInRound->id))
				{
					$roundTitle = "Losers Round " . abs($roundNumber);
					$isLoosersBracket = true;
				}
			}
		@endphp

		@if ($isLoosersBracket && !$prevIsLoosersBracket || $isFinalsBracket && !$prevIsFinalsBracket)
			</div>
			<div class="row">
		@endif

		<div class="col-12 col-sm-6 col-md-3">
			<h4 class="pb-2 mt-4 mb-4 border-bottom">
				{{ $roundTitle }}
			</h4>
			@foreach ($round as $match)
				@php
					$matchserver = App\EventTournamentMatchServer::getTournamentMatchServer($match->id);
					$matchNumbers[$match->id] = $matchCounter;
				@endphp
				<table class="table table-bordered table-sm">
					<tbody>
						@php
							$scores[0] = 0;
							$scores[1] = 0;
							if ($match->scores_csv != "") {
								$scores = explode("-", $match->scores_csv, 2);
							}
							$context[0] = 'active';
							$context[1] = 'active';
							if ($scores[0] > $scores[1]) {
								$context[0] = 'success';
								$context[1] = 'danger';
							}
							if ($scores[0] < $scores[1]) {
								$context[0] = 'danger';
								$context[1] = 'success';
							}
							if ($scores[0] == $scores[1]) {
								$context[0] = 'warning';
								$context[1] = 'warning';
							}
						@endphp
						<tr>
							<td rowspan="2" class="text-center" width="10%">
								{{ $matchCounter }}
								<p><small>({{ $match->id }})</small> </p>
								<p><small>{{ $match->state }}</small></p>
							</td>
							<td class="text-center " width="10%">
								1
							</td>
							<td class="table-{{ $context[0] }} text-{{ $context[0] }}">
								@if ($match->player1_id)
									@if ($tournament->team_size != '1v1')
										{{ ($tournament->getTeamByChallongeId($match->player1_id))->name }}
									@else
										{{ ($tournament->getParticipantByChallongeId($match->player1_id))->eventTicket->user->username }}
									@endif
									<span class="badge rounded-pill float-end">{{ $scores[0] }}</span>
								@endif
								@if ($match->player1_is_prereq_match_loser && !$match->player1_id)
									<small><i>Loser of {{ $matchNumbers[$match->player1_prereq_match_id] }}</i></small>
								@elseif (!$match->player1_is_prereq_match_loser && !$match->player1_id)
									<small><i>Winner of {{ $matchNumbers[$match->player1_prereq_match_id] }}</i></small>
								@endif
							</td>
							@if ( @$admin && $user->admin )
								<td rowspan="3" class="text-center p-0" width="10%">
									@if ($match->state == 'open' && ($match->player2_id != null && $match->player1_id != null))
										@if ($tournament->team_size != '1v1')
											<button
										 		class="btn btn-sm @if (isset($tournament->match_autoapi) && $tournament->match_autoapi && isset($tournament->game->gamematchapihandler) && $tournament->game->gamematchapihandler != 0 ) btn-danger @else btn-primary @endif rounded-0"
										 		onclick="submitScores(
										 			'{{ $match->id }}',
										 			'{{ ($tournament->getTeamByChallongeId($match->player1_id))->name }}',
										 			'{{ ($tournament->getTeamByChallongeId($match->player2_id))->name }}'
									 			)"
									 		 data-bs-toggle="modal"
									 		 data-bs-target="#submitScoresModal"
								 			>
								 				Submit Scores
								 			</button>
										@else
										 	<button
										 		class="btn btn-sm @if (isset($tournament->match_autoapi) && $tournament->match_autoapi && isset($tournament->game->gamematchapihandler) && $tournament->game->gamematchapihandler != 0 ) btn-danger @else btn-primary @endif rounded-0"
										 		onclick="submitScores(
										 			'{{ $match->id }}',
										 			'{{ ($tournament->getParticipantByChallongeId($match->player1_id))->eventTicket->user->username }}',
										 			'{{ ($tournament->getParticipantByChallongeId($match->player2_id))->eventTicket->user->username }}'
									 			)"
									 		 data-bs-toggle="modal"
									 		 data-bs-target="#submitScoresModal"
								 			>
								 				Submit Scores
								 			</button>
							 			@endif
										@if(isset($tournament->game))
											<button class="btn @if (isset($tournament->match_autoapi) && $tournament->match_autoapi && isset($tournament->game->gamematchapihandler) && $tournament->game->gamematchapihandler != 0 ) btn-danger @else btn-primary @endif btn-sm btn-block rounded-0" data-bs-toggle="modal" data-bs-target="#selectServerModal{{ $match->id }}">Select Server</button>
											<!-- Select Server Modal -->
											<div class="modal fade" id="selectServerModal{{ $match->id }}" tabindex="-1" role="dialog" aria-labelledby="selectServerModalLabel{{ $match->id }}" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<h4 class="modal-title" id="selectServerModalLabel{{ $match->id }}">Select Server for Match {{ $matchCounter }}</h4>
															<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
														</div>
														{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/tournaments/' . $tournament->slug .'/match/' . $match->id . ((isset($matchserver) && isset($matchserver->gameServer)) ? '/update':'') , 'id'=>'selectServerModal')) }}
														<div class="modal-body">
															@if (isset($tournament->match_autoapi) && $tournament->match_autoapi && isset($tournament->game->gamematchapihandler) && $tournament->game->gamematchapihandler != 0 )
																<small style="color: red">This does not end the match remotely on the currently assigned server and does not load it on the new assigned Server. You have to manually execute the nessecary commands on your server with the Execute Command button. Also if you're automation works as its intended, you should never have to assign a server here.</small>
															@endif
															@if (isset($matchserver) && isset($matchserver->gameServer))
																<br><br><p><small style="color: red">If you need to delete the current assignment, you can do that on the <a href="/admin/games/{{$matchserver->gameServer->game->slug}}/gameservers/{{$matchserver->gameServer->slug}}">gameservers detail page</a></small></p>
															@endif
																<div class="mb-3">
																	{{ Form::label('gameServer','Server',array('id'=>'','class'=>'')) }}
																	{{ Form::select('gameServer', $tournament->game->getGameServerSelectArray(), null, array('id'=>'gameServer','class'=>'form-control')) }}
																</div>
															</div>
															<div class="modal-footer">
																<button type="submit" class="btn btn-success">Select Server</button>
																<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
															</div>
														{{ Form::close() }}
													</div>
												</div>
											</div>
										@endif

										@if ( isset($matchserver) && isset($matchserver->gameServer) )
											<button class="btn btn-primary btn-sm btn-block rounded-0" data-bs-toggle="modal" data-bs-target="#executeServerCommandModal{{ $match->id }}">Commands</button>
											<!-- execute Command Modal -->
											<div class="modal fade" id="executeServerCommandModal{{ $match->id }}" tabindex="-1" role="dialog" aria-labelledby="executeServerCommandModalLabel{{ $match->id }}" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<h4 class="modal-title" id="executeServerCommandModalLabel{{ $match->id }}">Execute Server Command for Match {{ $matchCounter }}</h4>
															<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
														</div>
														<div class="modal-body">
															@if (isset($tournament->match_autoapi) && $tournament->match_autoapi && isset($tournament->game->gamematchapihandler) && $tournament->game->gamematchapihandler != 0 )
																<small style="color: orange">You have the tournament autoapi enabled on this tournament. This means, you should never have to execute commands here if everything works like intended. Use this with caution!</small>
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
															@foreach ($tournament->game->getMatchCommands() as $matchCommand)
																{{ Form::open(array('url'=>'/admin/games/' . $tournament->game->slug . '/gameservercommands/execute/' . $matchserver->gameServer->slug .'/tournament/' . $tournament->slug, 'id'=>'executeServerCommandModal')) }}
																	{{ Form::hidden('command', $matchCommand->id) }}
																	{{ Form::hidden('challonge_match_id', $matchserver->challonge_match_id) }}
																	challonge_match_id
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
								 	@endif
								</td>
							@endif
						</tr>
						<tr>
							<td class="text-center " width="10%">
								2
							</td>
							<td class="table-{{ $context[1] }} text-{{ $context[1] }}">
								@if ($match->player2_id)
									@if ($tournament->team_size != '1v1')
										{{ ($tournament->getTeamByChallongeId($match->player2_id))->name }}
									@else
										{{ ($tournament->getParticipantByChallongeId($match->player2_id))->eventTicket->user->username }}
									@endif
									<span class="badge rounded-pill float-end">{{ $scores[1] }}</span>
								@endif
								@if ($match->player2_is_prereq_match_loser && !$match->player2_id)
									<small><i>Loser of {{ $matchNumbers[$match->player2_prereq_match_id] }}</i></small>
								@elseif (!$match->player2_is_prereq_match_loser && !$match->player2_id)
									<small><i>Winner of {{ $matchNumbers[$match->player2_prereq_match_id] }}</i></small>
								@endif
							</td>
						</tr>

						{{-- Connection is only possible if a gameServer is assigned --}}
						@if( !isset($admin) && $match->state == 'open' && isset($matchserver) && isset($matchserver->gameServer) && ($tournament->game->connect_game_url || $tournament->game->connect_game_command))
							@php
								$availableParameters = new \stdClass();
								$availableParameters->game = $tournament->game;
								$availableParameters->event = $tournament->event;
								$availableParameters->tournament = $tournament;
								$availableParameters->gameServer = $matchserver->gameServer;
								$availableParameters->match = $tournament->getMatch($matchserver->challonge_match_id);
							@endphp
							<tr>
								<td colspan="3">
									@if ($user && Helpers::isMatchPlayer($tournament, $availableParameters->match, $user))
										@if($tournament->game->connect_game_url)
											<a class="btn btn-primary btn-block" id="connectGameUrl" href="{{ Helpers::resolveServerCommandParameters($tournament->game->connect_game_url, NULL, $availableParameters) }}" role="button">Join Game</a>
										@endif
										@if($tournament->game->connect_game_command)
											<div class="input-group" style="width: 100%">
												<input class="form-control" id="connectGameCommand{{ $availableParameters->match->id }}" type="text" readonly value="{{ Helpers::resolveServerCommandParameters($tournament->game->connect_game_command, NULL, $availableParameters) }}">
												<span class="input-group-btn">
													<button class="btn btn-primary" type="button" onclick="copyToClipBoard('connectGameCommand{{$availableParameters->match->id}}')"><i class="far fa-clipboard"></i></button>
												</div>
											</div>
										@endif
									@endif
									@if($tournament->game->connect_stream_url && $matchserver->gameserver->stream_port != 0)
										<a class="btn btn-primary btn-block" href="{{ Helpers::resolveServerCommandParameters($tournament->game->connect_stream_url, NULL, $availableParameters) }}" role="button">Join Stream</a>
									@endif
								</td>
							</tr>
						@endif
						@if ( @$admin && $user->admin && $matchserver)
							<tr>
								<td colspan="3">
									Selected Server: {{ $matchserver->gameServer->name }}
								</td>
							</tr>
							@if ($match->state == 'complete' && isset($matchserver))
								<tr>
									<td colspan="3">
										<p><small style="color: red">It seems like the match is finished, but the server is still assigned. Depending on your gameservers config it might take some time to free the servers. If it should already be free you can delete the assignment on the <a href="/admin/games/{{$matchserver->gameServer->game->slug}}/gameservers/{{$matchserver->gameServer->slug}}">gameservers detail page</a></small></p>
									</td>
								</tr>
							@endif
							@endif

							@php
								$matchReplays = $tournament->getMatchReplays($match->id);
							@endphp
							@if (isset($matchReplays) && count($matchReplays) > 0)

								<tr>
									<td colspan="3">

											Replays
											<table width="100%" class="table table-striped table-hover mt-3" id="dataTables-example">
												<thead>
													<tr>
														<th>Name</th>
														<th>Size</th>
														<th>Created</th>
														@if (isset($admin))
														<th></th>
														@endif
													</tr>
												</thead>
												<tbody>
													@foreach ($matchReplays as $matchReplay)
														<tr>
															<td>
																<a href="/storage{{ App\MatchReplay::getReplayPath($tournament->game, $matchReplay->name) }}"> {{$matchReplay->name}}</a>
															</td>

															<td>
																{{ App\MatchReplay::getReplaySize($tournament->game, $matchReplay->name) }}
															</td>
															<td>
																{{ $matchReplay->created_at }}
															</td>
															@if (isset($admin))
															<td width="15%">
																		{{ Form::open(array('url'=>'/admin/replays/'. $matchReplay->id , 'onsubmit' => 'return ConfirmDelete()')) }}
																			{{ Form::hidden('_method', 'DELETE') }}
																			{{ Form::hidden('game', $tournament->game->id) }}
																			<button type="submit" class="btn btn-danger btn-sm btn-block">Remove</button>
																		{{ Form::close() }}

															</td>
															@endif
														</tr>
													@endforeach

												</tbody>
											</table>
									</td>
								</tr>


							@endif

					</tbody>
				</table>
				@php
					$matchCounter++
				@endphp
			@endforeach
		</div>
	@endforeach
</div>

@if (@$admin && $user->admin)
	<!-- Modals -->
	<div class="modal fade" id="submitScoresModal" tabindex="-1" role="dialog" aria-labelledby="submitScoresModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="submitScoresModalLabel">Submit Scores</h4>
					<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
				</div>
				<div class="modal-body">
					@if (isset($tournament->match_autoapi) && $tournament->match_autoapi && isset($tournament->game->gamematchapihandler) && $tournament->game->gamematchapihandler != 0 )
					<small style="color: red">You have the tournament autoapi enabled on this tournament. This means, you should never have to finish the game here if everything works like intended. This also does not end the match remotely on the currently assigned server. You have to make sure that its manually ended and the server is free before finalizing the match manually.</small>
					@endif
					{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/tournaments/' . $tournament->slug . '/match', 'class'=>'form-horizontal')) }}
						<div class="mb-3">
							{{ Form::label('player1_score','',array('id'=>'player1_score_lbl','class'=>'col-6 col-sm-9 text-start')) }}
							<div class="col-6 col-sm-3 text-start">
								{{ Form::number('player1_score', 0, array('id'=>'player1_score','class'=>'form-control')) }}
							</div>
						</div>
						<hr>
						<div class="mb-3">
							{{ Form::label('player2_score','',array('id'=>'player2_score_lbl','class'=>'col-6 col-sm-9 text-start')) }}
							<div class="col-6 col-sm-3 text-start">
								{{ Form::number('player2_score', 0, array('id'=>'player2_score','class'=>'form-control')) }}
							</div>
						</div>
						<hr>
						<h4>Verify Winner</h4>
						<div class='form-check form-check-inline center-text'>
							<label class="form-check-label" id="player1_verify_lbl">
								<input class="form-check-input" type="radio" name="player_winner_verify" id="player1_verify" value="player1"><span id="player1_verify_span"></span>
							</label>
							<label class="form-check-label" id="player2_verify_lbl">
								<input class="form-check-input" type="radio" name="player_winner_verify" id="player2_verify" value="player2"><span id="player2_verify_span"></span>
							</label>
						</div>
						{{ Form::hidden('tournament_match_id', null, array('id'=>'tournament_match_id','class'=>'form-control')) }}
						<br>
						<button type="submit" class="btn btn-secondary">Submit</button>
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
	<!-- JavaScript-->
	<script>
		function submitScores(match_id, player1_name, player2_name)
		{
			jQuery("#tournament_match_id").val(match_id);
			jQuery('[id$=player1_score_lbl]').text(player1_name);
			jQuery('[id$=player1_verify_span]').text(player1_name);
			jQuery('[id$=player2_score_lbl]').text(player2_name);
			jQuery('[id$=player2_verify_span]').text(player2_name);
		}

	</script>
@endif

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
