@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' Polls')

@section ('content')

<div class="container pt-1">
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h1>
			Polls
		</h1>
	</div>
	@foreach ($activePolls as $poll)
		<div class="card mb-3">
			<div class="card-header ">
				<strong><a href="/polls/{{ $poll->slug }}">{{ $poll->name }}</a></strong>
				@if ($poll->status != 'PUBLISHED')
					<small> - {{ $poll->status }}</small>
				@endif
			</div>
			<div class="card-body">
				@if (!empty($poll->description))
					<p class="bg-info  padding">{{ $poll->description }}</p>
				@endif
				<p class="bg-info  padding">Options: {{ $poll->options->count() }}</p>
				<p class="bg-info  padding">Votes: {{ $poll->getTotalVotes() }}</p>
			</div>
		</div>
	@endforeach
	{{ $activePolls->links() }}
	<hr>
	<div class="pb-2 mt-4 mb-4 border-bottom">
		<h3>
			Previous Polls
		</h3>
	</div>
	@foreach ($endedPolls as $poll)
		<div class="card mb-3">
			<div class="card-header ">
				<strong><a href="/polls/{{ $poll->slug }}">{{ $poll->name }}</a></strong>
				@if ($poll->status != 'PUBLISHED')
					<small> - {{ $poll->status }}</small>
				@endif
			</div>
			<div class="card-body">
				@if (!empty($poll->description))
					<p class="bg-info  padding">{{ $poll->description }}</p>
				@endif
				<p class="bg-success  padding">Start: {{ date(__('date.time_date_numeric'), strtotime($poll->start)) }}</p>
				<p class="bg-danger  padding">End: {{ date(__('date.time_date_numeric'), strtotime($poll->end)) }}</p>
				<p class="bg-info  padding">Options: {{ $poll->options->count() }}</p>
				<p class="bg-info  padding">Votes: {{ $poll->getTotalVotes() }}</p>
			</div>
		</div>
	@endforeach
	{{ $endedPolls->links() }}
</div>

@endsection
