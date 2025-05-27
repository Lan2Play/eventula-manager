@extends ('layouts.admin-default')

@section ('page_title', 'Polls')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">
			Polls - {{ $poll->name }}
			@if ($poll->hasEnded())
				<small> - Ended</small>
			@endif
		</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/polls/">Polls</a>
			</li>
			<li class="breadcrumb-item active">
				{{ $poll->name }}
			</li>
		</ol>
	</div>
</div>

<div class="row">
	<div class="col-12 col-sm-8">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-users fa-fw"></i> Options
			</div>
			<div class="card-body">
				<table width="100%" class="table table-striped table-hover" id="dataTables-example">
					<thead>
						<tr>
							<th>Name</th>
							<th>Votes</th>
							<th>Percentage %</th>
							<th>Added By</th>
						</tr>
					</thead>
					<tbody>
						@if (!$poll->options->isEmpty())
							@foreach ($poll->options->reverse() as $option)
								<tr class="table-row odd gradeX">
									<td width="30%">{{ $option->name }}</td>
									<td width="5%">{{ $option->getTotalVotes() }}</td>
									<td width="40%">
										<div class="progress-bar" role="progressbar" aria-valuenow="{{ $option->getPercentage() }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $option->getPercentage() }}%;">
											{{ $option->getPercentage() }}%
										</div>
									</td>
									<td width="25%"><small>{{ $option->user->username }}</small></td>
									<td>
										@if ($option->getTotalVotes() <= 0)
											{!! Html::form('POST', '/admin/polls/' . $poll->slug . '/options/' . $option->id)->attribute('onsubmit', 'return ConfirmDelete()') !!}
												{!! Html::hidden('_method', 'DELETE') !!}
												<button type="submit" class="btn btn-danger btn-sm btn-block">Delete</button>
											{!! Html::form()->close() !!}
										@endif
									</td>
								</tr>
							@endforeach
						@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-4">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-pencil fa-fw"></i> Edit {{ $poll->name }}
			</div>
			<div class="card-body">
					{!! Html::label('Poll Link:', 'name') !!}
				<a href="{{ $_SERVER['REQUEST_SCHEME'] }}://{{ $_SERVER['HTTP_HOST'] }}/polls/{{ $poll->slug }}">
					{{ $_SERVER['REQUEST_SCHEME'] }}://{{ $_SERVER['HTTP_HOST'] }}/polls/{{ $poll->slug }}
				</a>
				{!! Html::form('POST', '/admin/polls/' . $poll->slug)->acceptsFiles() !!}
					<div class="mb-3">
						{!! Html::label('Name', 'name') !!}
						{!! Html::text('name', $poll->name)->id('name')->class('form-control') !!}
					</div>
					<div class="mb-3">
						{!! Html::label('Description', 'description') !!}
						{!! Html::textarea('description', $poll->description)->class('form-control')->rows(3) !!}
					</div>
					<div class="mb-3">
						{!! Html::label('Status', 'status') !!}
						<select id="status" name="status" class="form-control">
							<option value="draft" @if(strtolower($poll->status) == 'draft') selected @endif>Draft</option>
							<option value="preview" @if(strtolower($poll->status) == 'preview') selected @endif>Preview</option>
							<option value="published" @if(strtolower($poll->status) == 'published') selected @endif>Published</option>
						</select>
					</div>
					<div class="mb-3">
						{!! Html::label('Link to Event', 'event_id') !!}
						<select id="event_id" name="event_id" class="form-control">
							@foreach(Helpers::getEventNames('DESC', 0, true) as $key => $value)
								<option value="{{ $key }}" @if($poll->event_id == $key) selected @endif>{{ $value }}</option>
							@endforeach
						</select>
					</div>
					<div class="mb-3">
						{!! Html::label('Allow User to Add Options', 'allow_options_users') !!} @if ($poll->allow_options_user) True @else False @endif
						<br>
						{!! Html::label('Allow User to Select Multiple Options', 'allow_options_multi') !!} @if ($poll->allow_options_multi) True @else False @endif
					</div>
					<div class="mb-3">
						<button type="submit" class="btn btn-success btn-block">Submit</button>
					</div>
				{!! Html::form()->close() !!}
				@if (!$poll->hasEnded())
					<div class="mb-3">
						{!! Html::form('POST', '/admin/polls/' . $poll->slug . '/end') !!}
							<button type="submit" class="btn btn-primary btn-block">End Poll</button>
						{!! Html::form()->close() !!}
					</div>
				@endif
				<hr>
				{!! Html::form('POST', '/admin/polls/' . $poll->slug)->attribute('onsubmit', 'return ConfirmDelete()') !!}
					{!! Html::hidden('_method', 'DELETE') !!}
					<button type="submit" class="btn btn-danger btn-block">Delete</button>
				{!! Html::form()->close() !!}
			</div>
		</div>
		@if (!$poll->hasEnded())
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-plus fa-fw"></i> Add Options
				</div>
				<div class="card-body">
					{!! Html::form('POST', '/admin/polls/' . $poll->slug . '/options')->acceptsFiles() !!}
						<div class="mb-3">
							@include ('layouts._partials._polls.add-options')
						</div>
						<button type="submit" class="btn btn-secondary btn-block">Submit</button>
					{!! Html::form()->close() !!}
				</div>
			</div>
		@endif
	</div>
</div>

@endsection