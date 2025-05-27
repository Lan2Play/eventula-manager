@extends ('layouts.admin-default')

@section ('page_title', 'Polls')

@section ('content')
<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Polls</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item active">
				Polls
			</li>
		</ol>
	</div>
</div>

<div class="row">
	<div class="col-12 col-sm-8">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-users fa-fw"></i> Polls
			</div>
			<div class="card-body">
				<table width="100%" class="table table-striped table-hover">
					<thead>
						<tr>
							<th>Name</th>
							<th>Status</th>
							<th>Votes</th>
							<th>Options</th>
							<th>Created By</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($polls as $poll)
							<tr class="table-row odd gradeX" data-href="/admin/polls/{{ $poll->slug }}">
								<td>{{ $poll->name }}</td>
								<td>
									@if (!$poll->hasEnded())
										{{ $poll->status }}
									@else
										ENDED
									@endif
								</td>
								<td>{{ $poll->getTotalVotes() }}</td>
								<td>
									{{ $poll->options->count() }}
								</td>
								<td><small>{{ $poll->user->username }}</small></td>
								<td width="15%">
									<a href="/admin/polls/{{ $poll->slug }}">
										<button type="button" class="btn btn-primary btn-sm btn-block">Edit</button>
									</a>
								</td>
								<td width="15%">
									{!! Html::form('POST', '/admin/polls/' . $poll->slug)->attribute('onsubmit', 'return ConfirmDelete()') !!}
										{!! Html::hidden('_method', 'DELETE') !!}
										<button type="submit" class="btn btn-danger btn-sm btn-block">Delete</button>
									{!! Html::form()->close() !!}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				{{ $polls->links() }}
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-4">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-plus fa-fw"></i> Create Poll
			</div>
			<div class="card-body">
				{!! Html::form('POST', '/admin/polls/')->acceptsFiles() !!}
					<div class="mb-3">
						{!! Html::label('Name', 'name') !!}
						{!! Html::text('name', null)->id('name')->class('form-control') !!}
					</div>
					<div class="mb-3">
						{!! Html::label('Description', 'description') !!}
						{!! Html::textarea('description', '')->class('form-control')->rows(3) !!}
					</div>
					<div class="mb-3">
						{!! Html::label('Link to Event', 'event_id') !!}
						<select id="event_id" name="event_id" class="form-control">
							@foreach(Helpers::getEventNames('DESC', 0, true) as $key => $value)
								<option value="{{ $key }}">{{ $value }}</option>
							@endforeach
						</select>
					</div>
					{!! Html::label('Options', 'options') !!}
					@include ('layouts._partials._polls.add-options')
					<div class="row mt-3">
						<div class="col-lg-6 col-sm-12 mb-3">
							<div class="form-check">
								<label class="form-check-label">
									{!! Html::checkbox('allow_options_user', true, true)->id('allow_options_user') !!} Allow users to add their own options?
								</label>
							</div>
						</div>
						<div class="col-lg-6 col-sm-12 mb-3">
							<div class="form-check">
								<label class="form-check-label">
									{!! Html::checkbox('allow_options_multi', true, true)->id('allow_options_multi') !!} Allow multiple options?
								</label>
							</div>
						</div>
					</div>
					<button type="submit" class="btn btn-success btn-block">Submit</button>
				{!! Html::form()->close() !!}
			</div>
		</div>
	</div>
</div>

@endsection