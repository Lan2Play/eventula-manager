@extends ('layouts.default')

@section ('page_title', $event->display_name . ' - ' . $tournament->display_name)

@section ('content')
	<div class="container pt-1">
		<div class="row">
			<div class="card mb-3">
				<div class="row">
					<div class="col-sm-2 col-12">
						<picture>
							<source srcset="{{ $tournament->game->image_thumbnail_path }}.webp" type="image/webp">
							<source srcset="{{ $tournament->game->image_thumbnail_path }}" type="image/jpeg">
							<img src="{{ $tournament->game->image_thumbnail_path }}" class="img-fluid img-rounded img-thumbnail">
						</picture>
					</div>
					<div class="col-sm-10 col-12" >
						<div class="block">
							<h2>{{ $tournament->display_name }}</h2>
							<h4>{{ $tournament->description }}</h4>
							<dl>
								<dt>
									Team Sizes
								</dt>
								<dd>
									{{ $tournament->team_size }}
								</dd>
								<dt>
									Format:
								</dt>
								<dd>
									{{ $tournament->format }}
								</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
		</div>

		<dl>
			<dt>
				Status:
			</dt>
			<dd>
				@if ($tournament->status == 'COMPLETE')
					<h4>Complete!</h4>
					<div class="progress">
						<div class="progress-bar bg-success progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
							<span class="visually-hidden">Complete!</span>
						</div>
					</div>
				@endif
				@if ($tournament->status == 'OPEN')
					<h4>Signups Open!</h4>
					<div class="progress">
						<div class="progress-bar bg-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
							<span class="visually-hidden">Signups Open!</span>
						</div>
					</div>
				@endif
				@if ($tournament->status == 'LIVE')
					<h4>Live!</h4>
					<div class="progress">
						<div class="progress-bar bg-primary progress-bar-striped" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width: 10%">
							<span class="visually-hidden">LIVE!</span>
						</div>
					</div>
				@endif
			</dd>
		</dl>
		@if ($tournament->status == 'OPEN' && !$tournament->getParticipant($user->active_event_participant->id) && $tournament->team_size == '1v1')
			{{ Form::open(array('url'=>'/events/' . $event->slug . '/tournaments/' . $tournament->slug . '/register', 'files' => true )) }}
				<input type="hidden" name="event_participant_id" value="{{ $user->active_event_participant->id }}">
				<button type="submit" name="action" value="sign_up" class="btn btn-secondary">Signup</button>
			{{ Form::close() }}
		@endif

		@if ($tournament->status == 'OPEN' && !$tournament->getParticipant($user->active_event_participant->id) && $tournament->team_size != '1v1')
			{{ Form::open(array('url'=>'/events/' . $event->slug . '/tournaments/' . $tournament->slug . '/register/pug', 'files' => true )) }}
				<input type="hidden" name="event_participant_id" value="{{ $user->active_event_participant->id }}">
				<button type="submit" name="action" value="sign_up" class="btn btn-secondary">PUG</button>
			{{ Form::close() }}
			{{ Form::open(array('url'=>'/events/' . $event->slug . '/tournaments/' . $tournament->slug . '/register/team', 'files' => true )) }}
				<div class="mb-3">
					{{ Form::label('team_name','Team Name',array('id'=>'','class'=>'')) }}
					{{ Form::text('team_name', '',array('id'=>'team_name','class'=>'form-control', 'required' => 'required')) }}
				</div>
				<input type="hidden" name="event_participant_id" value="{{ $user->active_event_participant->id }}">
				<button type="submit" name="action" value="sign_up" class="btn btn-secondary">Create Team</button>
			{{ Form::close() }}
		@endif

		@if ($tournament->status == 'OPEN' && $tournament->getParticipant($user->active_event_participant->id))
			{{ Form::open(array('url'=>'/events/' . $event->slug . '/tournaments/' . $tournament->slug . '/register/remove', 'files' => true )) }}
				<input type="hidden" name="event_participant_id" value="{{ $user->active_event_participant->id }}">
				<button type="submit" name="action" value="remove_sign_up" class="btn btn-secondary">Remove Signup</button>
			{{ Form::close() }}
		@endif

		@if ($tournament->status == 'LIVE' && isset($tournament->matches))
			<iframe src="https://challonge.com/{{ $tournament->challonge_tournament_url }}/module?multiplier=1.0&amp;match_width_multiplier=1.0&amp;show_final_results=0&amp;show_standings=0&amp;theme=1&amp;subdomain=" width="100%" height="480" frameborder="0" scrolling="auto" allowtransparency="true"></iframe>
		@endif

		@if ($tournament->status == 'COMPLETE' && isset($tournament->challonge_participants))
			<div class="col">
				<div class="alert alert-success text-center">
					@foreach ($tournament->challonge_participants as $challongeParticipants)
						<h2>{{ Helpers::getChallongeRankFormat($challongeParticipants->final_rank) }} - {{ $challongeParticipants->name }}</h2>
					@endforeach
				</div>
			</div>
		@endif
		<div class="row">
				@if ($tournament->team_size == '1v1')
					<table class="table table-responsive">
						 <thead>
							<tr>
								<th>
									Name
								</th>
								<th>
									Seat
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($tournament->tournamentParticipants as $tournamentParticipant)
								<tr>
									<td>
										<p style="padding-top:7px;"><img class="rounded" style="max-width: 4%;" src="{{$tournamentParticipant->eventTicket->user->avatar}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $tournamentParticipant->eventTicket->user->username }}</p>
									</td>
									<td>
										<p style="padding-top:15px;">
											@if ($tournamentParticipant->eventTicket->seat)
												{{ $tournamentParticipant->eventTicket->seat->seat }}
											@else
												Not Seated
											@endif
										</p>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				@endif
				@if ($tournament->team_size != '1v1')
					<div class="col-sm-6 col-12">
						<h3>Pugs</h3>
						<table class="table table-responsive">
							 <thead>
								<tr>
									<th>
										Player name
									</th>
									<th>
										Seat
									</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($tournament->tournamentParticipants as $tournamentParticipant)
									@if ($tournamentParticipant->pug == 'Y')
										<tr>
											<td>
												<p><img class="rounded" style="max-width: 6%;" src="{{$tournamentParticipant->eventTicket->user->avatar}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $tournamentParticipant->eventTicket->user->username }}</p>
											</td>
											<td>
												<p>
													@if ($tournamentParticipant->eventTicket->seat)
														{{ $tournamentParticipant->eventTicket->seat->seat }}
													@else
														Not Seated
													@endif
												</p>
											</td>
										</tr>
									@endif
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="col-sm-6 col-12">
						<h3>Teams</h3>
						<table class="table table-responsive">
							 <thead>
								<tr>
									<th>
										Team Name
									</th>
									<th>
									</th>
									<th>
									</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($tournament->tournamentTeams as $tournamentTeam)
									<tr>
										<td>
											<h4>{{ $tournamentTeam->name }}</h4>
										</td>
										<td>
											<table class="table">
												@foreach ($tournamentTeam->tournamentParticipants as $participant)
													<tr>
														<td>
															<img class="rounded" style="max-width: 8%;" src="{{$participant->eventParticipant->user->avatar}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $participant->eventParticipant->user->username }}
														</td>
														<td>
															@if ($participant->eventParticipant->seat)
																{{ $participant->eventParticipant->seat->seat }}
															@else
																Not Seated
															@endif
														</td>
													</tr>
												@endforeach
											</table>
										</td>
										<td>
											@if (!$tournament->getParticipant($user->active_event_participant->id))
												{{ Form::open(array('url'=>'/events/' . $event->slug . '/tournaments/' . $tournament->slug . '/register', 'files' => true )) }}
													<input type="hidden" name="event_participant_id" value="{{ $user->active_event_participant->id }}">
													<input type="hidden" name="event_tournament_team_id" value="{{ $tournamentTeam->id }}">
													<button type="submit" name="action" value="sign_up" class="btn btn-secondary">Join Team</button>
												{{ Form::close() }}
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
		</div>

	</div>


@endsection
