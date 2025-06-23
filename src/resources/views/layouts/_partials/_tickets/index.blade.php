<div class="card mb-3">
	<div @class([
			"card-header",
			"bg-success-light" => !$ticket->revoked,
			"text-success" => !$ticket->revoked,
			"bg-danger-light" => $ticket->revoked,
			"text-danger" => $ticket->revoked
        ])>
		<strong>
		{{ $ticket->event->display_name }}
		</strong>
		@if ($ticket->tickettype)
		<strong>{{ $ticket->tickettype->name }} 
			@if ($ticket->tickettype && $ticket->tickettype->seatable) - @lang('events.seat'): 
				@if ($ticket->seat) {{ $ticket->seat->getName() }} 
					<small>in {{$ticket->seat->seatingPlan->name}}</small> 
				@else 
					@lang('events.notseated') 
				@endif 
			@endif
		</strong>
		@else
			@if ($ticket->staff)
				<strong>
					@lang('tickets.staff_ticket') - @lang('events.seat'):
					@if ($ticket->seat) 
						{{ $ticket->seat->getName() }}
						<small>in {{$ticket->seat->seatingPlan->name}}</small>
					@else
						@lang('events.notseated')
					@endif
				</strong>
			@else
				<strong>
					@lang('tickets.free_ticket') - @lang('events.seat'):
					@if ($ticket->seat)
						{{ $ticket->seat->getName() }} 
						<small>in {{$ticket->seat->seatingPlan->name}}</small>
					@else
						@lang('events.notseated')
					@endif					
				</strong>
			@endif
		@endif
		@if ($ticket->gift == 1 && $ticket->gift_accepted != 1)
			<span class="badge text-bg-info float-end" style="margin-left: 3px; margin-top:2px;">@lang('tickets.has_been_gifted')</span>
		@endif
		@if ($ticket->tickettype && !$ticket->tickettype->seatable)
			<span class="badge text-bg-info float-end" style="margin-top:2px;">@lang('tickets.not_eligable_for_seat')</span>
		@endif
		@if ($ticket->revoked)
			<span class="badge text-bg-danger float-end" style="margin-top: 2px;">@lang('tickets.has_been_revoked')</span>
		@endif
	</div>
	<div class="card-body">
		<div class="row" style="display: flex; align-items: center;">
			<div class="col-md-8 col-sm-8 col-12">

				<!-- @if ($ticket->gift != 1 && $ticket->gift_accepted != 1 && !$ticket->event->online_event)
					<button class="btn btn-md btn-success btn-block" onclick="giftTicket('{{ $ticket->id }}')" data-bs-toggle="modal" data-bs-target="#giftTicketModal">
						@lang('tickets.gift_ticket')
					</button>
				@endif -->
				@if ($ticket->gift == 1 && $ticket->gift_accepted != 1)
				<label>@lang('tickets.gift_url')</label>
				<p>
					<strong>
						{{ URL::to('/') }}/gift/accept/?url={{ $ticket->gift_accepted_url }}
					</strong>
				</p>
				{{ Form::open(array('url'=>'/gift/' . $ticket->id . '/revoke', 'id'=>'revokeGiftTicketForm')) }}
				<button type="submit" class="btn btn-primary btn-md btn-block">@lang('tickets.revoke_gift')</button>
				{{ Form::close() }}
				@endif
				@if ($ticket->seat)
					<hr>
						{{ Form::open(array('url'=>'/events/' . $ticket->event->slug . '/seating/' . $ticket->seat->seatingPlan->slug)) }}
							{{ Form::hidden('_method', 'DELETE') }}
							{{ Form::hidden('user_id', $user->id, array('id'=>'user_id','class'=>'form-control')) }}
							{{ Form::hidden('participant_id', $ticket->id, array('id'=>'participant_id','class'=>'form-control')) }}
							{{ Form::hidden('seat_column_delete', $ticket->seat->column, array('id'=>'seat_column_delete','class'=>'form-control')) }}
							{{ Form::hidden('seat_row_delete', $ticket->seat->row, array('id'=>'seat_row_delete','class'=>'form-control')) }}
						<h5>
						<button class="btn btn-danger btn-block">
							@lang('events.remove_seating')
						</button>
						</h5>
						{{ Form::close() }}
				@endif
			</div>
			<div class="offset-md-2 col-md-2 offset-sm-2 col-sm-4 col-12">
				<img class="img img-fluid" src="/{{ $ticket->qrcode }}" />
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="btn-group">
			<a href="/events/participants/{{ $ticket->id }}/pdf" class="btn btn-primary">@lang('tickets.download_pdf')</a>
		</div>
	</div>
</div>

@include ('layouts._partials._gifts.modal')