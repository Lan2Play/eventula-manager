@extends ('layouts.admin-default')

@section ('page_title', $event->display_name)

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">{{ $event->display_name }}</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/events/">Events</a>
			</li>
			<li class="breadcrumb-item active">
				{{ $event->display_name }}
			</li>
		</ol>
	</div>
</div>

@include ('layouts._partials._admin._event.dashMini')

<div class="row">
	<div class="col-lg-8">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Settings
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug, 'files' => 'true')) }}
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="mb-3">
								{{ Form::label('event_name','Name',array('id'=>'','class'=>'')) }}
								{{ Form::text('event_name',$event->display_name,array('id'=>'event_name','class'=>'form-control')) }}
							</div>
							<div class="row">
								<div class="col-md-6 col-12">
									<div class="row">
										<div class="col-md-6 col-12">
											<div class="mb-3">
												{{ Form::label('capacity','Capacity',array('id'=>'','class'=>'')) }}
												{{ Form::text('capacity',$event->capacity,array('id'=>'capacity','class'=>'form-control')) }}
											</div>
										</div>
										<div class="col-md-6 col-12">
											<div class="mb-3">
												{{ Form::label('seating_cap','Seating',array('id'=>'','class'=>'')) }}
												{{ Form::text('seating_cap',$event->getSeatingCapacity(),array('id'=>'seating_cap','class'=>'form-control', 'disabled'=>'true')) }}
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6 col-12">
									<div class="mb-3">
										{{ Form::label('status','Status',array('id'=>'','class'=>'')) }}
										{{
											Form::select(
												'status',
												array(
													'draft'=>'Draft',
													'preview'=>'Preview',
													'published'=>'Published',
													'private' => 'Private',
													'registeredonly' => 'Registered only',
												),
												strtolower($event->status),
												array(
													'id'=>'status',
													'class'=>'form-control'
												)
											)
										}}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<div class="mb-3">
										{{ Form::label('no_tickets_per_user','No. tickets per user',array('id'=>'','class'=>'')) }}
										{{ Form::number(
												'no_tickets_per_user',
												$event->no_tickets_per_user,
												array(
													'id'=>'no_tickets_per_user',
													'class'=>'form-control',
													'min' => 0
												)
										) }}
										<div class="form-text">Limit total amount of tickets a user can buy for the event. 0 or leave empty for
											unlimited.
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-12">
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<div class="mb-3">
										{{ Form::label('start_date','Start Date',array('id'=>'','class'=>'')) }}
										{{ Form::text('start_date', date('m/d/Y', strtotime($event->start)),array('id'=>'start_date','class'=>'form-control')) }}
									</div>
									<div class="mb-3">
										{{ Form::label('end_date','End Date',array('id'=>'','class'=>'')) }}
										{{ Form::text('end_date', date('m/d/Y', strtotime($event->end)),array('id'=>'end_date','class'=>'form-control')) }}
									</div>
								</div>
								<div class="col-md-6 col-sm-12">
									<div class="mb-3">
										{{ Form::label('start_time','Start Time',array('id'=>'','class'=>'')) }}
										{{ Form::text('start_time', date('H:i', strtotime($event->start)),array('id'=>'start_time','class'=>'form-control')) }}
									</div>
									<div class="mb-3">
										{{ Form::label('end_time','End Time',array('id'=>'','class'=>'')) }}
										{{ Form::text('end_time', date('H:i', strtotime($event->end)),array('id'=>'end_time','class'=>'form-control')) }}
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-8 col-sm-12">
							<strong>Venue</strong>
							<address>
								@if ($event->venue->display_name) {{ $event->venue->display_name }}<br> @endif
								@if ($event->venue->address_1) {{ $event->venue->address_1 }}<br> @endif
								@if ($event->venue->address_2) {{ $event->venue->address_2 }}<br> @endif
								@if ($event->venue->address_street) {{ $event->venue->address_street }}<br> @endif
								@if ($event->venue->address_city) {{ $event->venue->address_city }}<br> @endif
								@if ($event->venue->address_postcode) {{ $event->venue->address_postcode }}<br> @endif
								@if ($event->venue->address_country) {{ $event->venue->address_country }}<br> @endif
							</address>
						</div>
						<div class="col-md-4 col-sm-12">

						</div>
					</div>
					<div class="mb-3">
						<div class="form-check">
								<label class="form-check-label">
									{{ Form::checkbox('online_event', null, $event->online_event, array('id'=>'online_event')) }} Online Event (allow tournament registration and home redirection without being signed in to the event)
								</label>
						</div>
					</div>
					<div class="mb-3">
						<div class="form-check">
								<label class="form-check-label">
									{{ Form::checkbox('private_participants', null, $event->private_participants, array('id'=>'private_participants')) }} Private participants (show participants only to participants)
								</label>
						</div>
					</div>
					<div class="mb-3">
						<div class="form-check">
								<label class="form-check-label">
									{{ Form::checkbox('matchmaking_enabled', null, $event->matchmaking_enabled, array('id'=>'matchmaking_enabled')) }} Show Matchmaking (on the redirected home of the event)
								</label>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<div class="mb-3">
								{{ Form::label('desc_short','Short Description',array('id'=>'','class'=>'')) }}
								{{ Form::textarea('desc_short', $event->desc_short,array('id'=>'desc_short','class'=>'form-control wysiwyg-editor')) }}
							</div>
							<div class="mb-3">
								{{ Form::label('desc_long','Long Description',array('id'=>'','class'=>'')) }}
								<label>Long Description <span class="text-muted"><small>This will show on the events own page</small></span></label>
								{{ Form::textarea('desc_long',$event->desc_long,array('id'=>'desc_long','class'=>'form-control wysiwyg-editor', 'rows' => '4')) }}
							</div>
							<div class="mb-3">
								<label>Essential Info <span class="text-muted"><small>This will show on the all events and the index page when the Event is not live / the participant is not signed in to the event</small></span></label>
								{{ Form::textarea('essential_info',$event->essential_info,array('id'=>'essential_info','class'=>'form-control wysiwyg-editor')) }}
							</div>
							<div class="mb-3">
								<label>Event Live Info <span class="text-muted"><small>This will show on the home page if the event is Live and the Participant is signed in to the event</small></span></label>
								{{ Form::textarea('event_live_info',$event->event_live_info,array('id'=>'event_live_info','class'=>'form-control wysiwyg-editor')) }}
							</div>


						</div>
					</div>
					<button type="submit" class="btn btn-success btn-block">Submit</button>
				{{ Form::close() }}
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-info fa-fw"></i> Event Information
				<a href="#" class="btn btn-info btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addEventInformationModal">Add Event Information</a>
			</div>
			<div class="card-body">
				@if ($event->information->count() != 0)
					@foreach ($event->information as $section)
						<div class="card mb-3">
							<div class="card-header">
								<h4 class="card-title">
									<a data-bs-toggle="collapse" href="#collaspe-{{ $section->id }}" aria-expanded="false" class="collapsed" aria-controls="collaspe-{{ $section->id }}">{{ $section->title }}</a>
									<div class="float-end">
										{{ Form::open(array('url' => '/admin/information/' . $section->id)) }}
											{{ Form::hidden('_method', 'DELETE') }}
											{{ Form::submit('Delete', array('class' => 'btn btn-danger btn-xs float-right', 'style' => 'margin-top:-2px;')) }}
										{{ Form::close() }}
									</div>
								</h4>
							</div>
							<div id="collaspe-{{ $section->id }}" class="collapse">
								<div class="card-body">
									{{ Form::open(array('url'=>'/admin/information/' . $section->id, 'files' => 'true')) }}
										<div class="row">
											<div class="col-md-6 col-sm-12">
												<div class="mb-3">
													{{ Form::label('title','Title',array('id'=>'','class'=>'')) }}
													{{ Form::text('title', $section->title,array('id'=>'title','class'=>'form-control')) }}
												</div>
											</div>
											<div class="col-md-6 col-sm-12">
												<div class="mb-3">
													{{ Form::label('image','Image',array('id'=>'','class'=>'')) }}
													{{ Form::file('image',array('id'=>'image','class'=>'form-control')) }}
												</div>
											</div>
											<div class="mb-3 col-lg-6 col-sm-12">
												{{ Form::label('order','Order',array('id'=>'')) }}
												{{ Form::number('order', $section->order, array('id'=>'order', 'name' => 'order', 'class'=>'form-control')) }}
											</div>
										</div>
										<div class="mb-3">
											{{ Form::label('text','Text',array('id'=>'','class'=>'')) }}
											{{ Form::textarea('text', $section->text,array('id'=>'text','class'=>'form-control wysiwyg-editor', 'rows' => '4')) }}
										</div>
										@if(isset($section->image_path))
											<div class="row">
												<div class="mb-3 col-lg-6">
													{{ Form::label('preview','Image Preview',array('id'=>'','class'=>'')) }}
													<center>
														<img class="img-fluid img-thumbnail" src="{{ $section->image_path }}" />
													</center>
												</div>
											</div>
										@endif
										<button type="submit" class="btn btn-success btn-block">Update</button>
									{{ Form::close() }}
								</div>
							</div>
						</div>
					@endforeach
				@else
					<h4>None</h4>
				@endif
			</div>
		</div>

	</div>
	<div class="col-lg-4">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-bullhorn fa-fw"></i> Announcements
				<a href="#" class="btn btn-info btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">Add Announcement</a>
			</div>
			<div class="card-body">
				@if ($announcements->count() != 0)
					<div class="list-group">
						@foreach ($announcements as $announcement)
							<a href="#" class="list-group-item-action" data-bs-toggle="modal" onclick="editAnnouncement('{{$announcement->id}}', '{{$announcement->message}}')" data-bs-target="#editAnnouncementModal">
								<i class="fa fa-comment fa-fw"></i> {{ $announcement->message }}
							</a>
						@endforeach
						{{ $announcements->links() }}
					</div>
				@else
					<h4>None</h4>
				@endif
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-ticket fa-fw"></i> Tickets
				<a href="/admin/events/{{ $event->slug }}/tickets" style="margin-left:3px;" class="btn btn-info btn-sm float-right">All Tickets</a>
				<a href="#" class="btn btn-info btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addTicketModal">Add Ticket</a>
			</div>
			<div class="card-body">
				@if ($event->tickets->count() != 0)
					<div class="list-group">
						@foreach ($event->tickets as $ticket)
							<a href="/admin/events/{{ $event->slug }}/tickets/{{ $ticket->id }}" class="list-group-item-action">
								<i class="fa fa-pencil fa-fw"></i> {{ $ticket->name }} - {{ Settings::getCurrencySymbol() }}{{ $ticket->price }}
								<span class="float-end text-muted small">
									@if($ticket->quantity != 0)
										<em>{{ $ticket->participants()->count() }} / {{ $ticket->quantity }}</em>
									@else
										<em>Unlimited</em>
									@endif
								</span>
							</a>
						@endforeach
					</div>
				@else
					<h4>None</h4>
				@endif
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-list fa-fw"></i> Polls
			</div>
			<div class="card-body">
				@if (!$event->polls->isEmpty())
					<div class="list-group">
						@foreach ($event->polls as $poll)
							<a href="/admin/polls/{{ $poll->slug }}" class="list-group-item-action">
								<i class="fa fa-pencil fa-fw"></i> {{ $poll->name }}
							</a>
						@endforeach
					</div>
				@else
					<h4>None</h4>
				@endif
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-user fa-fw"></i> Attendees
				<a href="/admin/events/{{ $event->slug }}/participants" style="margin-left:3px;" class="btn btn-info btn-sm float-right">All Attendees</a>
				<a href="/admin/events/{{ $event->slug }}/tickets#freebies" class="btn btn-info btn-sm float-right">Freebies</a>
			</div>
			<div class="card-body">
				@if (!$event->eventParticipants->isEmpty())
					<div class="list-group">
						@foreach ($participants as $participant)
							<a href="/admin/events/{{ $event->slug }}/participants/{{ $participant->id }}" class="list-group-item-action">
								<i class="fa fa-comment fa-fw"></i> {{ $participant->user->username }}
								@if ($participant->user->steamid)
									- <span class="text-muted"><small>Steam: {{ $participant->user->steamname }}</small></span>
								@endif
								<span class="float-end text-muted small">
									<em>{{ date('d-m-y H:i', strtotime($participant->created_at)) }}</em>
								</span>
							</a>
						@endforeach
						{{ $participants->links() }}
					</div>
				@else
					<h4>None</h4>
				@endif
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-suitcase fa-fw"></i> Sponsors
				<a href="#" class="btn btn-info btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addSponsorModal">Add Sponsor</a>
			</div>
			<div class="card-body">
				@if (!$event->sponsors->isEmpty())
					<div class="list-group">
						@foreach ($event->sponsors as $sponsor)
							<a href="#" class="list-group-item-action" data-bs-toggle="modal" onclick="editSponsor('{{$sponsor->id}}', '{{$sponsor->name}}', '{{$sponsor->website}}', '{{$sponsor->image_path}}')" data-bs-target="#editSponsorModal">
								<i class="fa fa-pencil fa-fw"></i> {{ $sponsor->name }} - {{ ucwords($sponsor->website) }}
								<img class="img-fluid img-thumbnail" src="{{ $sponsor->image_path }}" />
							</a>
						@endforeach
					</div>
				@else
					<h4>None</h4>
				@endif
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Danger Zone
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug, 'onsubmit' => 'return ConfirmDelete()')) }}
					{{ Form::hidden('_method', 'DELETE') }}
					<button type="submit" class="btn btn-danger btn-block">Delete</button>
				{{ Form::close() }}
			</div>
		</div>

	</div>
</div>

<!-- Modals -->

<div class="modal fade" id="addTicketModal" tabindex="-1" role="dialog" aria-labelledby="addTicketModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="addTicketModalLabel">Add Ticket</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			<div class="modal-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/tickets')) }}
					@include('layouts._partials._admin._event._tickets.form', ['empty' => true])
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="addSponsorModal" tabindex="-1" role="dialog" aria-labelledby="addSponsorModalModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="addSponsorModalLabel">Add Sponsor</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/sponsors', 'files' => 'true')) }}
				<div class="modal-body">
					<div class="mb-3">
						{{ Form::label('sponsor_name','Sponsor Name',array('id'=>'','class'=>'')) }}
						{{ Form::text('sponsor_name',NULL,array('id'=>'sponsor_name','class'=>'form-control')) }}
					</div>
					<div class="mb-3">
						{{ Form::label('sponsor_website','Sponsor Website',array('id'=>'','class'=>'')) }} <small>should start with http(s)://</small>
						{{ Form::text('sponsor_website',NULL,array('id'=>'sponsor_website','class'=>'form-control')) }}
					</div>
					<div class="mb-3">
						{{ Form::label('sponsor_image','Sponsor Image',array('id'=>'','class'=>'')) }}
						{{ Form::file('sponsor_image',array('id'=>'sponsor_image','class'=>'form-control')) }}
					</div>
					<button type="submit" name="action" value="add_sponsor" class="btn btn-secondary">Submit</button>
				</div>
			{{ Form::close() }}
		</div>
	</div>
</div>


<div class="modal fade" id="editSponsorModal" tabindex="-1" role="dialog" aria-labelledby="editSponsorModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="editSponsorModalLabel">Edit Sponsor</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			<div class="modal-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/sponsors', 'files' => 'true', 'id' => 'editSponsorForm')) }}
					<div class="mb-3">
						{{ Form::label('sponsor_name','Sponsor Name',array('id'=>'','class'=>'')) }}
						{{ Form::text('sponsor_name',NULL,array('id'=>'sponsor_name_id','class'=>'form-control')) }}
					</div>
					<div class="mb-3">
						{{ Form::label('sponsor_website','Sponsor Website',array('id'=>'','class'=>'')) }} <small>should start with http(s)://</small>
						{{ Form::text('sponsor_website',NULL,array('id'=>'sponsor_website_id','class'=>'form-control')) }}
					</div>
					<div class="mb-3">
						{{ Form::label('sponsor_image_id','Current Sponsor Image',array('id'=>'','class'=>'')) }}
						<img class="img-fluid img-thumbnail" id='sponsor_image_preview' src=""/>
					</div>
					<div class="mb-3">
						{{ Form::label('sponsor_image','New Sponsor Image',array('id'=>'','class'=>'')) }}
						{{ Form::file('sponsor_image',array('id'=>'sponsor_image_id','class'=>'form-control')) }}
					</div>
					<button type="submit" name="action" value="add_sponsor" class="btn btn-secondary">Submit</button>

				{{ Form::close() }}
				{{ Form::open(array('method'  => 'delete', 'url'=>'/admin/events/' . $event->slug . '/sponsors', 'id'=>'deleteSponsorForm', 'files' => 'true' , 'onsubmit' => 'return ConfirmDelete()')) }}
					<button type="submit" class="btn btn-danger btn-block">Delete</button>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
<script>
	function editSponsor(sponsor_id, sponsor_name, sponsor_website, sponsor_image)
	{
		jQuery('#sponsor_image_preview').hide();
		jQuery("#editSponsorForm").prop('action', '/admin/events/{{ $event->slug }}/sponsors/' + sponsor_id);
		jQuery("#deleteSponsorForm").prop('action', '/admin/events/{{ $event->slug }}/sponsors/' + sponsor_id);
		jQuery('#sponsor_name_id').val(sponsor_name);
		jQuery('#sponsor_website_id').val(sponsor_website);
		if (sponsor_image !== "") {
			jQuery('#sponsor_image_preview').attr("src", sponsor_image);;
			jQuery('#sponsor_image_preview').show();
		}
	}
</script>


<div class="modal fade" id="addAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="announcementModalLabel">Add Announcement</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			<div class="modal-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/announcements', 'files' => 'true')) }}
					<div class="mb-3">
						{{ Form::label('message','Message',array('id'=>'','class'=>'')) }}
						{{ Form::textarea('message', NULL,array('id'=>'message','class'=>'form-control', 'rows' => '2')) }}
					</div>
					<button type="submit" class="btn btn-secondary btn-block">Submit</button>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="editAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="editAnnouncementModalLabel">Edit Announcement</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			<div class="modal-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/announcements', 'files' => 'true', 'id' => 'editAnnouncementForm')) }}
					<div class="mb-3">
						{{ Form::label('message','Message',array('id'=>'','class'=>'')) }}
						{{ Form::textarea('message', NULL,array('id'=>'edit_announcement','class'=>'form-control', 'rows' => '2')) }}
					</div>
					<div class="mb-3">
						<button type="submit" class="btn btn-secondary btn-block">Submit</button>
					</div>
				{{ Form::close() }}
				{{ Form::open(array('method'  => 'delete', 'url'=>'/admin/events/' . $event->slug . '/announcements', 'id'=>'deleteAnnouncementForm', 'files' => 'true')) }}
					<button type="submit" class="btn btn-danger btn-block">Delete</button>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
<script>
	function editAnnouncement(announcement_id, message)
	{
		jQuery("#editAnnouncementForm").prop('action', '/admin/events/{{ $event->slug }}/announcements/' + announcement_id);
		jQuery("#deleteAnnouncementForm").prop('action', '/admin/events/{{ $event->slug }}/announcements/' + announcement_id);
		jQuery('#edit_announcement').val(message);
	}
</script>

<div class="modal fade" id="addEventInformationModal" tabindex="-1" role="dialog" aria-labelledby="addEventInformationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="addEventInformationModal">Add Event Information</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			<div class="modal-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/information', 'files' => 'true')) }}
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="mb-3">
								{{ Form::label('title','Title',array('id'=>'','class'=>'')) }}
								{{ Form::text('title',NULL,array('id'=>'title','class'=>'form-control')) }}
							</div>
						</div>
						<div class="col-md-6 col-sm-12">
							<div class="mb-3">
								{{ Form::label('image','Image',array('id'=>'','class'=>'')) }}
								{{ Form::file('image',array('id'=>'image','class'=>'form-control')) }}
							</div>
						</div>
					</div>
					<div class="mb-3">
						{{ Form::label('text','Text',array('id'=>'','class'=>'')) }}
						{{ Form::textarea('text', NULL,array('id'=>'text','class'=>'form-control', 'rows' => '4')) }}
					</div>
					<button type="submit" class="btn btn-secondary">Submit</button>
				{{ Form::close() }}
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
