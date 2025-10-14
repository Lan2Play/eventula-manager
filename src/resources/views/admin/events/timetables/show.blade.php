@extends ('layouts.admin-default')

@section ('page_title', 'Timetables - ' . $event->display_name)

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Timetables - {{ $timetable->name }}</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/events/">Events</a>
			</li>
			<li class="breadcrumb-item">
				<a href="/admin/events/{{ $event->slug }}">{{ $event->display_name }}</a>
			</li>
			<li class="breadcrumb-item">
				<a href="/admin/events/{{ $event->slug }}/timetables">Timetables</a>
			</li>
			<li class="breadcrumb-item active">
				{{ $timetable->name }}
			</li>
		</ol>
	</div>
</div>

<div class="d-lg-block collapse d-md-none d-sm-none" id="dashMini">
    @include ('layouts._partials._admin._event.dashMini')
</div>

<div class="row">
	<div class="col-lg-8">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Timeslots
			</div>
			<div class="card-body table-responsive dataTables_wrapper">
				<table width="100%" class="table table-striped table-hover" id="timetable_table">
					<thead>
						<tr>
							<th>Time</th>
							<th>Name</th>
							<th>Desc</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($timetable->data as $slot)
							<tr class="odd gradeX">
								<td>{{ date("D", strtotime($slot->start_time)) }} - {{ date("H:i", strtotime($slot->start_time)) }}</td>
								@if($slot->name == NULL && $slot->desc == NULL)
									<td>EMPTY</td>
									<td>EMPTY</td>
								@else
									<td>{{ $slot->name }}</td>
									<td>{{ $slot->desc }}</td>
								@endif
								<td width="10%">
									<button type="button" class="btn btn-primary btn-sm btn-block" data-bs-toggle="modal" onclick="editTimeSlot('{{ $slot->id }}', '{{ $slot->start_time }}', '{{ $slot->name }}', '{{ $slot->desc }}')" data-bs-target="#editTimeSlotModal">Edit</button>
								</td>
								<td>
								{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/timetables/' . $timetable->slug . '/data/' . $slot->id, 'onsubmit' => 'return ConfirmDelete()')) }}
								{{ Form::hidden('_method', 'DELETE') }}
								<button type="submit" class="btn btn-danger btn-sm btn-block">Delete</button>
								{{ Form::close() }}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>


	</div>
	<div class="col-lg-4">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-plus fa-fw"></i> Add New Slot
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/timetables/' . $timetable->slug . '/data')) }}
					<div class="row">
						<div class="mb-3 col-lg-6">
							{{ Form::label('name','Name',array('id'=>'','class'=>'')) }}
							{{ Form::text('name', NULL ,array('id'=>'name','class'=>'form-control')) }}
						</div>
						 <div class="mb-3 col-lg-6">
							{{ Form::label('start_time','Start',array('id'=>'','class'=>'')) }}
							{{ Form::select('start_time', $timetable->getAvailableTimes(), null, array('id'=>'start_time','class'=>'form-control')) }}
						</div>
						<div class="mb-3 col-lg-12">
							{{ Form::label('desc','Description',array('id'=>'','class'=>'')) }}
							{{ Form::text('desc', NULL ,array('id'=>'desc','class'=>'form-control')) }}
						</div>
					</div>
					<button type="submit" class="btn btn-success btn-block">Submit</button>
				{{ Form::close() }}
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-wrench fa-fw"></i> Settings
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/timetables/' . $timetable->slug)) }}
					<div class="row">
						<div class="mb-3 col-lg-6">
							{{ Form::label('name','Name',array('id'=>'','class'=>'')) }}
							{{ Form::text('name', $timetable->name ,array('id'=>'name','class'=>'form-control')) }}
						</div>
						<div class="mb-3 col-lg-6">
							{{ Form::label('status','Status',array('id'=>'','class'=>'')) }}
							{{
								Form::select(
									'status',
									array(
										'PUBLISHED'=>'Published',
										'DRAFT'=>'Draft'
									),
									strtoupper($timetable->status),
									array(
										'id'=>'status',
										'class'=>'form-control'
									)
								)
							}}
						</div>
					</div>
					<div class="mb-3">
						<div class="form-check">
							<label class="form-check-label">
								@if ($timetable->primary)
									{{ Form::checkbox('primary', 1, true, array('id'=>'primary'))}} Primary Timetable
								@else
									{{ Form::checkbox('primary', 1, false, array('id'=>'primary'))}} Primary Timetable
								@endif
							</label>
						</div>
					</div>
					<button type="submit" class="btn btn-success btn-block">Submit</button>
				{{ Form::close() }}
				<hr>
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/timetables/' . $timetable->slug, 'onsubmit' => 'return ConfirmDelete()')) }}
					{{ Form::hidden('_method', 'DELETE') }}
					<button type="submit" class="btn btn-danger btn-block">Delete</button>
				{{ Form::close() }}
			</div>
		</div>

	</div>
</div>

<!-- Modals -->

<div class="modal fade" id="editTimeSlotModal" tabindex="-1" role="dialog" aria-labelledby="editTimeSlotModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="editTimeSlotModal">Edit Time Slot</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			<div class="modal-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/timetables/' . $timetable->slug . '/data', 'id'=>'editTimeSlotForm')) }}
					<div class="row">
						<div class="mb-3 col-lg-6">
							{{ Form::label('name','Name',array('id'=>'','class'=>'')) }}
							{{ Form::text('name', NULL ,array('id'=>'editTimetableGame','class'=>'form-control')) }}
						</div>
						 <div class="mb-3 col-lg-6">
							{{ Form::label('start_time','Start',array('id'=>'','class'=>'')) }}
							{{ Form::select('start_time', $timetable->getAvailableTimes(), null, array('id'=>'editTimetableStart','class'=>'form-control')) }}
						</div>
						<div class="mb-3 col-lg-12">
							{{ Form::label('desc','Description',array('id'=>'','class'=>'')) }}
							{{ Form::text('desc', NULL ,array('id'=>'editTimetableDesc','class'=>'form-control')) }}
						</div>
					</div>
					<button type="submit" class="btn btn-secondary">Submit</button>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
<script>
	function editTimeSlot(id, timestamp, name, desc)
	{
		jQuery("#editTimeSlotForm").prop('action', '/admin/events/{{ $event->slug }}/timetables/{{ $timetable->slug }}/data/' + id);
		jQuery('#editTimetableStart').val(timestamp);
		jQuery('#editTimetableGame').val(name);
		jQuery('#editTimetableDesc').val(desc);
	}
</script>

@endsection
