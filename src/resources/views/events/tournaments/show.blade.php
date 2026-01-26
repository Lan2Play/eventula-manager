@extends ('layouts.default')

@section ('page_title', $event->display_name . ' - ' . $tournament->display_name)

@section ('content')
	<div class="container pt-1">
		@if (isset($tournament->game) && $tournament->game->image_header_path != null)
			<picture>
				<source srcset="{{ $tournament->game->image_header_path }}.webp" type="image/webp">
				<source srcset="{{ $tournament->game->image_header_path }}" type="image/jpeg">
				<img class="img-fluid full-width top-image" width="100%" src="{{ $tournament->game->image_header_path }}">
			</picture>
		@endif
		<!-- HEADER -->
		<div class="pb-2 mt-4 mb-4 border-bottom">
			<h1>
				{{ $tournament->name }}
				<span class="float-end">
					<small>
						<span class="badge text-bg-success">{{ $tournament->status }}</span>
						@if ((!$user || !$user->active_event_participant || !$tournament->getParticipant($user->active_event_participant->id)) && $tournament->status != 'COMPLETE')
							<span class="badge text-bg-danger">@lang('events.notsignedup')</span>
						@endif
						@if ($user && $user->active_event_participant && $tournament->getParticipant($user->active_event_participant->id) && $tournament->status != 'COMPLETE')
							<span class="badge text-bg-success">@lang('events.signedup')</span>
						@endif
					</small>
				</span>
			</h1>
		</div>
		<div class="row">
			<div class="col-12 col-sm-4 col-md-3">
				<h4>
					{{ $tournament->description }}
				</h4>
				<dl>
					@if ($tournament->game)
						<dt>
						@lang('events.game')
						</dt>
						<dd>
							{{ $tournament->game->name }}
						</dd>
					@endif
					<dt>
						@lang('events.teamsizes')
					</dt>
					<dd>
						{{ $tournament->team_size }}
					</dd>
					<dt>
						@lang('events.format')
					</dt>
					<dd>
						{{ $tournament->format }}
					</dd>
				</dl>
			</div>
			<div class="col-12 col-sm-8 col-md-9">
				<!-- // TODO - refactor & add order on rank-->
				@if ($tournament->status == 'COMPLETE' && $tournament->format != 'list')
					<div class="row">
						<div class="col">
							<div class="alert alert-success text-center w-100">
								@php
									if ($tournament->team_size != '1v1') {
										$tournamentParticipants = $tournament->tournamentTeams;
									}
									if ($tournament->team_size == '1v1') {
										$tournamentParticipants = $tournament->tournamentParticipants;
									}
									$tournamentParticipants = $tournamentParticipants->sortBy('final_rank');
								@endphp
								@foreach ($tournamentParticipants as $tournamentParticipant)
									@if ($tournamentParticipant->final_rank == 1)
										@if ($tournament->team_size == '1v1')
											<h2>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->eventTicket->user->username }}</h2>
										@else
											<h2>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->name }}</h2>
										@endif
									@endif
									@if ($tournamentParticipant->final_rank == 2)
										@if ($tournament->team_size == '1v1')
											<h3>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->eventTicket->user->username }}</h3>
										@else
											<h3>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->name }}</h3>
										@endif
									@endif
									@if ($tournamentParticipant->final_rank != 2 && $tournamentParticipant->final_rank != 1)
										@if ($tournament->team_size == '1v1')
											<h4>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->eventTicket->user->username }}</h4>
										@else
											<h4>{{ Helpers::getChallongeRankFormat($tournamentParticipant->final_rank) }} - {{ $tournamentParticipant->name }}</h4>
										@endif
									@endif
								@endforeach
							</div>
						</div>
					</div>
				@endif

				<!-- PROGRESS -->
				@if ($tournament->status == 'LIVE' && $tournament->format != 'list')
					<h4 class="section-header">
						@lang('events.nextmatch')
					</h4>
					<div class="row">

						@foreach ($tournament->getNextMatches(2) as $match)
							<div class="col-12 col-sm-4">
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
												@endif
											</td>
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
												@endif
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						@endforeach
					</div>
				@endif

				<!-- REGISTRATION -->
				@if ($tournament->status == 'OPEN')
					@if ($user && $user->active_event_participant)

						<h4 class="section-header">
							@lang('events.registration')
						</h4>

				 		<!-- Team Registration -->
						@if ($tournament->team_size != '1v1' && !$tournament->getParticipant($user->active_event_participant->id))
							<div class="row border-between">
								<div class="col-12 col-sm-6">
									<label>@lang('events.join_a_team')</label>
									<div class="row">
										@foreach ($tournament->tournamentTeams as $tournamentTeam)
											<div class="col-6 col-sm-6">
												{{ Form::open(array('url'=>'/events/' . $event->slug . '/tournaments/' . $tournament->slug . '/register', 'files' => true )) }}
													<input type="hidden" name="ticket_id" value="{{ $user->active_event_participant->id }}">
													<input type="hidden" name="event_tournament_team_id" value="{{ $tournamentTeam->id }}">
													<button type="submit" name="action" value="sign_up" class="btn btn-secondary btn-block">{{ $tournamentTeam->name }}</button>
												{{ Form::close() }}
												<br>
											</div>
										@endforeach
									</div>
								</div>
								<div class="col-12 col-sm-6">
									@if (!$tournament->random_teams)
										<label>@lang('events.createateam')</label>
										{{ Form::open(array('url'=>'/events/' . $event->slug . '/tournaments/' . $tournament->slug . '/register/team', 'files' => true )) }}
											<div class="row">
												<div class="mb-3 col-sm-6 col-12">
													{{ Form::text('team_name', '',array('id'=>'team_name','class'=>'form-control', 'required' => 'required', 'placeholder' => 'Team Name')) }}
												</div>
												<div class="mb-3 col-sm-6 col-12">
													<button type="submit" class="btn btn-primary btn-block">@lang('events.createteam')</button>
												</div>
											</div>
											<input type="hidden" name="ticket_id" value="{{ $user->active_event_participant->id }}">
										{{ Form::close() }}
										<hr>
									@endif
									{{ Form::open(array('url'=>'/events/' . $event->slug . '/tournaments/' . $tournament->slug . '/register/pug', 'files' => true )) }}
										<input type="hidden" name="ticket_id" value="{{ $user->active_event_participant->id }}">
										<button type="submit" class="btn btn-primary btn-block">@lang('events.signinaspug')</button>
									{{ Form::close() }}
								</div>
							</div>
						@endif
						<!-- Singles Registration -->
						@if (!$tournament->getParticipant($user->active_event_participant->id) && $tournament->team_size == '1v1')
							<div class="row">
								<div class="col-6 col-sm-6">
									{{ Form::open(array('url'=>'/events/' . $event->slug . '/tournaments/' . $tournament->slug . '/register', 'files' => true )) }}
										<input type="hidden" name="ticket_id" value="{{ $user->active_event_participant->id }}">
										<button type="submit" class="btn btn-primary btn-block">@lang('events.signup')</button>
									{{ Form::close() }}
								</div>
							</div>
						@endif
						<!-- Signed up -->
						@if ($tournament->getParticipant($user->active_event_participant->id))
							{{ Form::open(array('url'=>'/events/' . $event->slug . '/tournaments/' . $tournament->slug . '/register/remove', 'files' => true )) }}
								<div class="row">
									<div class="col-12 col-md-6">
										@if ($tournament->team_size != '1v1')
											@if (($tournament->getParticipant($user->active_event_participant->id))->pug && !($tournament->getParticipant($user->active_event_participant->id))->tournamentTeam)
												<h4>@lang('events.signedupaspugteamassignedshortly')</h4>
											@else
												<h4>@lang('events.signedupwithteam', ['team' => ($tournament->getParticipant($user->active_event_participant->id))->tournamentTeam->name ])</h4>
											@endif
										@else
											<h4>@lang('events.signedup')</h4>
										@endif
										<input type="hidden" name="ticket_id" value="{{ $user->active_event_participant->id }}">
										<button type="submit" class="btn btn-danger btn-block">@lang('events.removesignup')</button>
									</div>
								</div>
							{{ Form::close() }}
						@endif
					@endif
				@endif
			</div>
		</div>

		<!-- BRACKETS & STANDINGS -->
		@if (($tournament->status == 'LIVE' || $tournament->status == 'COMPLETE') && $tournament->format != 'list')
			<div>
				<div class="pb-2 mt-4 mb-4 border-bottom">
					<h3>@lang('events.brackets')</h3>
				</div>
				@include ('layouts._partials._tournaments.brackets')
			</div>
			<div >
				<div class="pb-2 mt-4 mb-4 border-bottom">
					<h3>@lang('events.standings')</h3>
				</div>
				@include ('layouts._partials._tournaments.standings')
			</div>
		@endif

		<!-- PARTICIPANTS -->
		@if (($tournament->status != 'LIVE' && $tournament->status != 'COMPLETE') || $tournament->format == 'list')
			<div >
				<div class="pb-2 mt-4 mb-4 border-bottom">
					<h3>@lang('events.participants')</h3>
				</div>
				@php
					$participants_view = true;
					if ($tournament->team_size != '1v1') {
						$participants_view = false;
					}
				@endphp
				@include ('layouts._partials._tournaments.participants', ['all' => $participants_view])
			</div>
		@endif

		<div>
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<h3 id="rules">@lang('events.rules')</h3>
			</div>
			<p>{!! $tournament->rules !!}</p>
		</div>
	</div>

@endsection
