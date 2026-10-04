@extends ('layouts.admin-default')

@section ('page_title', 'Gameserver: '. $gameServer->name )

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Gameserver: {{ $gameServer->name }} </h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/games/">Games</a>
			</li>

			<li class="breadcrumb-item">
				<a href="/admin/games/{{ $gameServer->game->slug }}">{{ $gameServer->game->name }}</a>
			</li>		
			<li class="breadcrumb-item active">
				Gameservers
			</li>			
			<li class="breadcrumb-item active">
				{{ $gameServer->name }}
			</li>
		</ol>
	</div>
</div>

	<div class="row">
		<div class="col-12">

			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-th-list fa-fw"></i> Assigned Matches

				</div>
				<div class="card-body">
					<small style="color: red">You should only delete the assignment(s) here, if they are really orphaned and your server is free again. If you do this on a running match, this will most likely break the match/tournament automation</small>
					@if (isset($gameServer->matchMakingServer) || isset($gameServer->eventTournamentMatchServer))
					
					<div class="dataTable_wrapper">

						<table width="100%" class="table table-striped table-hover" id="dataTables-example">
							<thead>
								<tr>
									<th>Matchserver ID</th>
									<th>Match</th>
									<th>Event</th>
									<th>Status</th>
									<th></th>
								</tr>
							</thead>
							<tbody>

								@if (isset($gameServer->matchMakingServer))
									<tr>
										<td>{{ $gameServer->matchMakingServer->id }}</td>
										<td><a href="/admin/matchmaking/{{ $gameServer->matchMakingServer->match->id }}">{{ $gameServer->matchMakingServer->match->id }}</a></td>
										<td>-</td>
										
										<td>{{ $gameServer->matchMakingServer->match->status }}</td>

										<td>
											{{ Form::open(array('url'=>'/admin/matchmaking/' . $gameServer->matchMakingServer->match->id . '/serverdelete', 'onsubmit' => 'return ConfirmDelete()')) }}
												{{ Form::hidden('_method', 'DELETE') }}
												<button type="submit" class="btn btn-danger btn-sm btn-block">Delete Assignment</button>
											{{ Form::close() }}
										</td>
									</tr>
								@endif

								@if (isset($gameServer->eventTournamentMatchServer))
									<tr>
										<td>{{ $gameServer->eventTournamentMatchServer->id }}</td>
										<td>										
											<a href="/admin/events/{{ $gameServer->eventTournamentMatchServer->eventTournament->event->slug }}/tournaments/{{ $gameServer->eventTournamentMatchServer->eventTournament->slug }}/">  
												
												@php 
													$matchCounter = 1;
													$matches = $gameServer->eventTournamentMatchServer->eventTournament->getMatches();
												@endphp

												@foreach ($matches as $roundNumber => $round)
													@foreach ($round as $match)

														
														@if ($match->id == $gameServer->getAssignedMatchServer()["match"]->challonge_match_id)

															{{ $matchCounter }}

														@endif
															@php 
																$matchCounter++
															@endphp

													@endforeach

												@endforeach
											
											
												<small>({{ $gameServer->getAssignedMatchServer()["match"]->challonge_match_id }})</small>
											</a>
										
										
										
										
										
										</td>
										<td>
											<a href="/admin/events/{{ $gameServer->eventTournamentMatchServer->eventTournament->event->slug }}/tournaments/{{ $gameServer->eventTournamentMatchServer->eventTournament->slug }}/">
												{{ $gameServer->eventTournamentMatchServer->eventTournament->name }} 
											</a>
										</td>
										<td>{{ $gameServer->eventTournamentMatchServer->eventTournament->getMatch($gameServer->eventTournamentMatchServer->challonge_match_id)->state }}</td>

										<td>
											{{ Form::open(array('url'=>'/admin/events/' . $gameServer->eventTournamentMatchServer->eventTournament->event->slug . '/tournaments/' . $gameServer->eventTournamentMatchServer->eventTournament->slug . '/match/'. $gameServer->eventTournamentMatchServer->challonge_match_id . '/delete', 'onsubmit' => 'return ConfirmDelete()')) }}
												{{ Form::hidden('_method', 'DELETE') }}
												<button type="submit" class="btn btn-danger btn-sm btn-block">Delete Assignment</button>
											{{ Form::close() }}
										</td>
									</tr>
								@endif


								
							</tbody>
						</table>
					</div>
					@endif
				</div>
			</div>			
		</div>
	</div>
		
	


@endsection
