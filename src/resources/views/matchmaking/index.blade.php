@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' - ' . __('matchmaking.matchmaking'))

@section ('content')
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
<div class="container pt-1">

	<div class="pb-2 mb-4 border-bottom">
		<div class="row">
			<div class="col-sm">
				<h1>
				@lang('matchmaking.matchmaking')
				</h1>
			</div>
			<div class="col-sm mt-4">
				@if(Settings::getSystemsMatchMakingMaxopenperuser() == 0 || count($currentUserOpenLivePendingDraftMatches) < Settings::getSystemsMatchMakingMaxopenperuser())
				<a href="/matchmaking/" class="btn btn-success btn-sm btn-block float-end" data-bs-toggle="modal" data-bs-target="#addMatchModal">@lang('matchmaking.addmatch')</a>
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
				<div class="col">
					@php
						$match = $team->match;
					@endphp
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



</div>


<!-- Modals -->

	<div class="modal fade" id="addMatchModal" tabindex="-1" role="dialog" aria-labelledby="addMatchModal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="addMatchModal">@lang('matchmaking.addmatch')</h4>
					<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
				</div>
				<div class="modal-body">
					{!! Html::form('POST', '/matchmaking/') !!}
					<div class="mb-3">
						{!! Html::label(__('matchmaking.game').':', 'game_id') !!}
						<select id="game_id" name="game_id" class="form-control">
							@foreach(Helpers::getMatchmakingGameSelectArray() as $key => $value)
								<option value="{{ $key }}">{{ $value }}</option>
							@endforeach
						</select>
					</div>
					<div class="mb-3">
						{!! Html::label(__('matchmaking.firstteamname'), 'team1name') !!}
						{!! Html::text('team1name', null)->id('team1name')->class('form-control') !!}
						<small>@lang('matchmaking.thisisyourteam')</small>
					</div>
					<div class="mb-3">
						{!! Html::label(__('matchmaking.teamsize'), 'team_size') !!}
						<select id="team_size" name="team_size" class="form-control">
							<option value="1v1">1v1</option>
							<option value="2v2">2v2</option>
							<option value="3v3">3v3</option>
							<option value="4v4">4v4</option>
							<option value="5v5">5v5</option>
							<option value="6v6">6v6</option>
						</select>
					</div>
					<div class="mb-3">
						{!! Html::label(__('matchmaking.teamcounts'), 'team_count') !!}
						{!! Html::number('team_count', 2)->id('team_count')->class('form-control') !!}
					</div>
					<div class="mb-3">
						<div class="form-check">
								<label class="form-check-label">
									{!! Html::checkbox('ispublic', true, false)->id('ispublic') !!} @lang('matchmaking.ispublic')
								</label>
						</div>
					</div>
					<button type="submit" class="btn btn-success btn-block">@lang('matchmaking.submit')</button>
				{!! Html::form()->close() !!}
				</div>
			</div>
		</div>
	</div>


@endsection
