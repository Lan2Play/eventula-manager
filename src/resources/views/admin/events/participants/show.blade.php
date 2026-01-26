@extends ('layouts.admin-default')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Events - {{ $event->display_name }} - Participant - {{ $participant->user->username }}</h3>
		<ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/admin/events/">Events</a>
            </li>
            <li class="breadcrumb-item">
                <a href="/admin/events/{{ $event->slug }}">{{ $event->display_name }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="/admin/events/{{ $event->slug }}/participants">Participants</a>
            </li>
			<li class="breadcrumb-item active">
				{{ $participant->user->username }}
			</li>
		</ol>
	</div>
</div>

<div class="d-lg-block collapse d-md-none d-sm-none" id="dashMini">
@include ('layouts._partials._admin._event.dashMini')
</div>

@if ($participant->revoked)
	<div class="alert alert-danger">This participant has been revoked!</div>
@endif

<div class="row">
	<div class="col-lg-8">

		<div class="card mb-3">
			<div class="card-header">
				Edit Participant
			</div>
			<div class="card-body">
				@if (!empty($_POST))
					Successfully Posted
				@endif
				<div class="dataTable_wrapper table-responsive">
					<table width="100%" class="table table-striped table-hover" id="seating_table">
						<thead>
							<tr>
								<th>User</th>
								<th>Name</th>
								<th>Seat</th>
								<th>Ticket</th>
								<th>Paypal Email</th>
								<th>Gift</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									{{ $participant->user->username }}
									@if ($participant->user->steamid)
										<br><span class="text-muted"><small>Steam: {{ $participant->user->steamname }}</small></span>
									@endif
								</td>
								<td>{{ $participant->user->firstname }} {{ $participant->user->surname }}</td>
								<td>@if($participant->seat) {{ $participant->seat->seat }} @endif</td>
								<td>
									@if ($participant->ticketType)
										{{ $participant->ticketType->name }}
									@else
										No Ticket Bought / Free
									@endif
								</td>
								<td>@if ($participant->purchase) {{ $participant->purchase->paypal_email }} @endif</td>
								<td>
									@if ($participant->gift)
										<strong>Yes</strong>
										<small>Assigned by: {{ $participant->getGiftedByUser()->username }}</small>
									@else
										No
									@endif
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
        <div class="card mb-3">
            <div class="card-header">
                Audit Log
            </div>
            <div class="card-body">
                @include('layouts._partials._audit.ticket', [
    'ticket' => $participant,
    'audits' => $participant->audits ?? collect()
])
            </div>
        </div>
	</div>
	<div class="col-lg-4">

		<div class="card mb-3">
			<div class="card-header">
				More Editing
			</div>
			<div class="card-body">
				@if ($participant->signed_in)
					<h4>User is signed in at present at the event</h4>
				@endif
				{{ Form::label('','Ticket',array('id'=>'','class'=>'')) }}
				@if ($participant->ticketType)
					<p>{{ $participant->ticketType->name }}</p>
				@else
					<p>No Ticket Bought / Free</p>
				@endif
				@if ($participant->purchase)
					{{ Form::label('','Purchase Info',array('id'=>'','class'=>'')) }}
					<p><a href="/admin/purchases/{{ $participant->purchase->id }}">{{ $participant->purchase->type }}</a></p>
					@if ($participant->purchase->paypal_email)
						<p>{{ $participant->purchase->paypal_email }}</p>
					@endif
				@endif
				@if (
        				(!$participant->revoked) &&
        				(!$participant->signed_in) &&
        				((!$participant->ticketType) || ($participant->purchase->status == "Success"))
					)
					{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/participants/' . $participant->id . '/transfer')) }}
						<div class="mb-3">
							{{ Form::label('event_id','Transfer to event',array('id'=>'','class'=>'')) }}
							{{
								Form::select(
									'event_id',
									Helpers::getEventNames('DESC', 0, true),
									'',
									array(
										'id'=>'event_id',
										'class'=>'form-control'
									)
								)
							}}
						</div>
						<div class="mb-3">
							<button type="submit" class="btn btn-success btn-block">Transfer</button>
						</div>
					{{ Form::close() }}
					<hr>
					{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/participants/' . $participant->id . '/signin')) }}
						<div class="mb-3">
							<button type="submit" class="btn btn-success btn-block">Sign in</button>
					{{ Form::close() }}
					<hr>
					<div class="mb-3">
						<button type="submit" class="btn btn-danger btn-block" disabled>Refund - <small>Coming soon</small></button>
					</div>
				@else
				<hr>
				@if (!$participant->revoked)
				<div class="form-group">
					<a href="/admin/events/{{ $event->slug }}/participants/{{ $participant->id}}/signout/">
						<button type="submit" class="btn btn-danger btn-block">Sign Out </button>
					</a>	
				</div>
				@endif
						</hr>
				@endif
				
				@if ((!$participant->signed_in) && ($participant->ticketType) && ($participant->purchase->status != "Success"))
				<div class="mb-3">
					complete payment to transfer or sign in the user
				</div>
				@endif
			</div>
		</div>
            @if (!$participant->revoked)
            <div class="card mb-3">
                <div class="card-header">Danger Zone</div>
                <div class="card-body">
                    {{ Form::open([
                    'url' => '/admin/events/' . $event->slug . '/participants/' . $participant->id . '/revoke',
                    'onSubmit' => 'return ConfirmRevoke()'
                    ]) }}
                    <div class="mb-3">
                        <button type="submit" class="btn btn-danger btn-block">Revoke</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
            @endif

		@if (config('admin.super_danger_zone'))
		<div class="card mb-3">
			<div class="card-header">Super Danger Zone</div>
			<div class="card-body">
			{{ Form::open([
    				'url' => '/admin/events/' . $event->slug . '/participants/' . $participant->id,
    				'onSubmit' => 'return ConfirmDelete()'
				]) }}
				{{ Form::hidden('_method', 'DELETE') }}
				<div class="mb-3">
					<div class="alert alert-danger">
						This will remove the participant and related data, including clearing their seat. The underlying Purchase will not be removed.<br/>
						This may have unintended side effects. Having a backup is highly recommended!
					</div>
					<button type="submit" class="btn btn-danger btn-block">Delete participant</button>
				</div>
			{{ Form::close() }}
			</div>
		</div>
		@endif

	</div>

</div>

<script type="text/javascript">
	function ConfirmRevoke() {
		return confirm('Are you sure you want to revoke this participant? This does not trigger any refund and this action can not be reverted!')
	}
</script>

@endsection