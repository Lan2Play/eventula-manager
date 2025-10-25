@extends ('layouts.admin-default')

@section ('page_title', 'Events')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Events</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item active">
				Events
			</li>
		</ol>
	</div>
</div>

<div class="row">
	<div class="col12 col-lg-8">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-th-list fa-fw"></i> Events
			</div>
			<div class="card-body">
				<div class="table-responsive dataTable_wrapper">
					<table width="100%" class="table table-striped table-hover" id="dataTables-example">
						<thead>
							<tr>
								<th>Name</th>
								<th class="d-none d-sm-table-cell">Start</th>
								<th class="d-none d-sm-table-cell">End</th>
								<th class="d-none d-md-table-cell">Short Description</th>
								<th>Capacity</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($events as $event)
								<tr class="table-row" class="odd gradeX">
									<td>{{ $event->display_name }}</td>
									<td class="d-none d-sm-table-cell">
                                        {{ date('d-m-y H:i', strtotime($event->start)) }}
                                    </td>
									<td class="d-none d-sm-table-cell">
                                        {{ date('d-m-y H:i', strtotime($event->end)) }}
                                    </td>
									<td class="d-none d-md-table-cell">{!! $event->desc_short !!}</td>
                                    <td class="center">
                                        <span class="d-block">{{ $event->capacity }}</span>
                                        <small class="d-none d-md-inline">Seats:{{ $event->getSeatingCapacity() }}</small>
                                    </td>
									<td width="15%">
                                        <div class="d-flex flex-column flex-sm-row gap-2">
                                            <a href="/admin/events/{{ $event->slug }}"><button type="button" class="btn btn-primary btn-sm btn-block">View</button></a>
										{{ Form::open(array('url'=>'/admin/events/' . $event->slug, 'onsubmit' => 'return ConfirmDelete()')) }}
											{{ Form::hidden('_method', 'DELETE') }}
											<button type="submit" class="btn btn-danger btn-sm btn-block">Delete</button>
										{{ Form::close() }}
                                        </div>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{{ $events->links() }}
				</div>
			</div>
		</div>

	</div>
	<div class="col-12 col-lg-4">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-plus fa-fw"></i> Add Event
			</div>
			<div class="card-body">
				<div class="list-group">
					{{ Form::open(array('url'=>'/admin/events/')) }}
						<div class="mb-3">
							{{ Form::label('event_name','Event Name',array('id'=>'','class'=>'')) }}
							{{ Form::text('event_name', '',array('id'=>'event_name','class'=>'form-control')) }}
						</div>
						<div class="row">
							<div class="col-lg-6 col-sm-12 mb-3">
								{{ Form::label('start_date','Start Date',array('id'=>'','class'=>'')) }}
								{{ Form::text('start_date', '',array('id'=>'start_date','class'=>'form-control')) }}
							</div>
							<div class="col-lg-6 col-sm-12 mb-3">
								{{ Form::label('start_time','Start  Time',array('id'=>'','class'=>'')) }}
								{{ Form::text('start_time', '16:00',array('id'=>'start_time','class'=>'form-control')) }}
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 col-sm-12 mb-3">
								{{ Form::label('end_date','End Date',array('id'=>'','class'=>'')) }}
								{{ Form::text('end_date', '',array('id'=>'end_date','class'=>'form-control')) }}
							</div>
							<div class="col-lg-6 col-sm-12 mb-3">
								{{ Form::label('end_time','End Time',array('id'=>'','class'=>'')) }}
								{{ Form::text('end_time', '18:00',array('id'=>'end_time','class'=>'form-control')) }}
							</div>
						</div>
						<div class="mb-3">
							{{ Form::label('desc_short','Short Description',array('id'=>'','class'=>'')) }}
							{{ Form::textarea('desc_short', '',array('id'=>'desc_short','class'=>'form-control wysiwyg-editor-small', 'rows'=>'2')) }}
						</div>
						<div class="mb-3">
							{{ Form::label('desc_long','Long Description',array('id'=>'','class'=>'')) }}
							{{ Form::textarea('desc_long', '',array('id'=>'desc_long','class'=>'form-control wysiwyg-editor-small', 'rows'=>'2')) }}
						</div>
						<div class="mb-3">
							{{ Form::label('essential_info','Essential Info',array('id'=>'','class'=>'')) }}
							{{ Form::textarea('essential_info', '',array('id'=>'essential_info','class'=>'form-control wysiwyg-editor-small', 'rows'=>'2')) }}
						</div>
						<div class="mb-3">
							{{ Form::label('event_live_info','Event Live Info',array('id'=>'','class'=>'')) }}
							{{ Form::textarea('event_live_info', '',array('id'=>'event_live_info','class'=>'form-control wysiwyg-editor-small', 'rows'=>'2')) }}
						</div>
						<div class="row">
							<div class="col-lg-6 col-sm-12 mb-3">
								{{ Form::label('capacity','Capacity',array('id'=>'','class'=>'')) }}
								{{ Form::text('capacity', '',array('id'=>'capacity','class'=>'form-control')) }}
							</div>
							<div class="col-lg-6 col-sm-12 mb-3">
								{{ Form::label('venue','Venue',array('id'=>'','class'=>'')) }}
								{{ Form::select('venue', Helpers::getVenues(), null, array('id'=>'venue','class'=>'form-control')) }}
							</div>
						</div>
						<div class="form-check">
							<label class="form-check-label">
								{{ Form::checkbox('allow_spec','Y',true, array('id'=>'allow_spec')) }} Allow Spectators
							</label>
						</div>
						<div class="form-check">
								<label class="form-check-label">
									{{ Form::checkbox('online_event', null, false, array('id'=>'online_event')) }} Online Event (allow tournament registration and home redirection without being signed in to the event)
								</label>
						</div>						
						<div class="form-check">
								<label class="form-check-label">
									{{ Form::checkbox('private_participants', null, false, array('id'=>'private_participants')) }} Private participants (show participants only to participants)
								</label>
						</div>
						<div class="mb-3">
							<div class="form-check">
									<label class="form-check-label">
										{{ Form::checkbox('matchmaking_enabled', null, false, array('id'=>'matchmaking_enabled')) }} Show Matchmaking (on the redirected home of the event)
									</label>
							</div>
						</div>
						<div class="form-check">
								<label class="form-check-label">
									{{ Form::checkbox('tournaments_staff', null, false, array('id'=>'tournaments_staff')) }} Allow Tournaments for Staff (Allow Staff to sign into Tournaments)
								</label>
						</div>
						<div class="form-check">
								<label class="form-check-label">
									{{ Form::checkbox('tournaments_freebie', null, false, array('id'=>'tournaments_freebies')) }} Allow Tournaments for Freebies (Allow Freebies to sign into Tournaments)
								</label>
						</div>
						@if ($eventTags)
							<div class="mb-3">
								<label>Eventula Tags</label>
								<p><small>These are used and edited on the Eventula Hub site</small></p>
								<div class="row">
								 	@foreach ($eventTags as $eventTag)
								 		<div class="col-12 col-sm-6">
			                                <div class="form-check form-check-inline">
			                                    <input class="form-check-input" type="checkbox" value="{{ $eventTag->id }}" name="event_tags[{{ $eventTag->id }}]" id="event_tags[{{ $eventTag->id }}]">
			                                    <label class="form-check-label" for="event_tags[{{ $eventTag->id }}]">
			                                        {{ $eventTag->name }}
			                                    </label>
			                                </div>
			                            </div>
		                            @endforeach
		                        </div>
							</div>
						@endif
						<button type="submit" class="btn btn-success btn-block">Submit</button>
					{{ Form::close() }}
				</div>
			</div>
		</div>

	</div>
</div>

<!-- JavaScript-->
<script type="text/javascript">
	jQuery( function() {
		jQuery( "#start_date" ).datepicker();
		jQuery( "#end_date" ).datepicker();
	});
</script>

@endsection
