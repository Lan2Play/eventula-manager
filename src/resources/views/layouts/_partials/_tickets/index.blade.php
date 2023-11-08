<div class="card mb-3">
	<div class="card-header  bg-success-light text-success">
		@if ($participant->ticket)
		<strong>{{ $participant->ticket->name }} 
			@if ($participant->ticket && $participant->ticket->seatable) - @lang('events.seat'): 
				@if ($participant->seat) {{ $participant->seat->getName() }} 
					<small>in {{$participant->seat->seatingPlan->name}}</small> 
				@else 
					@lang('events.notseated') 
				@endif 
			@endif
		</strong>
		@else
			@if ($participant->staff)
				<strong>
					@lang('tickets.staff_ticket') - @lang('events.seat'):
					@if ($participant->seat) 
						{{ $participant->seat->getName() }}
						<small>in {{$participant->seat->seatingPlan->name}}</small>
					@else
						@lang('events.notseated')
					@endif
				</strong>
			@else
				<strong>
					@lang('tickets.free_ticket') - @lang('events.seat'):
					@if ($participant->seat)  
						{{ $participant->seat->getName() }} 
						<small>in {{$participant->seat->seatingPlan->name}}</small>
					@else
						@lang('events.notseated')
					@endif					
				</strong>
			@endif
		@endif
		@if ($participant->gift == 1 && $participant->gift_accepted != 1)
			<span class="badge text-bg-info float-end" style="margin-left: 3px; margin-top:2px;">@lang('tickets.has_been_gifted')</span>
		@endif
		@if ($participant->ticket && !$participant->ticket->seatable)
			<span class="badge text-bg-info float-end" style="margin-top:2px;">@lang('tickets.not_eligable_for_seat')</span>
		@endif
	</div>
	<div class="card-body">
		<div class="row" style="display: flex; align-items: center;">
			<div class="col-md-8 col-sm-8 col-12">

				<!-- @if ($participant->gift != 1 && $participant->gift_accepted != 1 && !$participant->event->online_event)
					<button class="btn btn-md btn-success btn-block" onclick="giftTicket('{{ $participant->id }}')" data-bs-toggle="modal" data-bs-target="#giftTicketModal">
						@lang('tickets.gift_ticket')
					</button>
				@endif -->
				@if ($participant->gift == 1 && $participant->gift_accepted != 1)
				<label>@lang('tickets.gift_url')</label>
				<p>
					<strong>
						{{ config('app.url') }}/gift/accept/?url={{ $participant->gift_accepted_url }}
					</strong>
				</p>
				{{ Form::open(array('url'=>'/gift/' . $participant->id . '/revoke', 'id'=>'revokeGiftTicketForm')) }}
				<button type="submit" class="btn btn-primary btn-md btn-block">@lang('tickets.revoke_gift')</button>
				{{ Form::close() }}
				@endif
				@if ($participant->seat)
					<hr>
						{{ Form::open(array('url'=>'/events/' . $participant->event->slug . '/seating/' . $participant->seat->seatingPlan->slug)) }}
							{{ Form::hidden('_method', 'DELETE') }}
							{{ Form::hidden('user_id', $user->id, array('id'=>'user_id','class'=>'form-control')) }}
							{{ Form::hidden('participant_id', $participant->id, array('id'=>'participant_id','class'=>'form-control')) }}
							{{ Form::hidden('seat_column_delete', $participant->seat->column, array('id'=>'seat_column_delete','class'=>'form-control')) }}
							{{ Form::hidden('seat_row_delete', $participant->seat->row, array('id'=>'seat_row_delete','class'=>'form-control')) }}
						<h5>
						<button class="btn btn-danger btn-block">
							@lang('events.remove_seating')
						</button>
						</h5>
						{{ Form::close() }}
				@endif
			</div>
			<div class="offset-md-2 col-md-2 offset-sm-2 col-sm-4 col-12">
				<img class="img img-fluid" src="/{{ $participant->qrcode }}" />
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="btn-group">
			<a href="/account/ticket/pdf/{{ $participant->id }}" class="btn btn-primary">PDF</a>
		</div>
	</div>
</div>

@include ('layouts._partials._gifts.modal')