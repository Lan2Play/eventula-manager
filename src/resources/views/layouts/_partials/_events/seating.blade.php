<!-- SEATING -->
@if (!$event->online_event &&
!$event->seatingPlans->isEmpty() &&
(
in_array('PUBLISHED', $event->seatingPlans->pluck('status')->toArray()) ||
in_array('PREVIEW', $event->seatingPlans->pluck('status')->toArray())
)
)
<div class="pb-2 mt-4 mb-4 border-bottom">
    <a name="seating"></a>
    <h3><i class="fas fa-chair me-3"></i>@lang('events.seatingplans') <small>- {{ $event->getSeatingCapacity() - $event->getSeatedCount() }} / {{ $event->getSeatingCapacity() }} @lang('events.seatsremaining')</small></h3>
</div>
<div class="card-group" id="accordion" role="tablist" aria-multiselectable="true">
    @foreach ($event->seatingPlans as $seatingPlan)
    @if ($seatingPlan->status != 'DRAFT')
    <div class="card mb-3">
        <a class="collapsed" role="button" data-bs-toggle="collapse" data-parent="#accordion" href="#collapse_{{ $seatingPlan->slug }}" aria-expanded="true" aria-controls="collapse_{{ $seatingPlan->slug }}">
            <div class="card-header  bg-success-light" role="tab" id="headingOne">
                <h4 class="card-title m-0">
                    {{ $seatingPlan->name }} <small>- {{ $seatingPlan->getSeatingCapacity() - $seatingPlan->getSeatedCount() }} / {{ $seatingPlan->getSeatingCapacity() }} @lang('events.available')</small>
                    @if ($seatingPlan->status != 'PUBLISHED')
                    <small> - {{ $seatingPlan->status }}</small>
                    @endif
                </h4>
            </div>
        </a>
        <div id="collapse_{{ $seatingPlan->slug }}" class="collapse @if ($loop->first) in @endif" role="tabpanel" aria-labelledby="collaspe_{{ $seatingPlan->slug }}">
            <div class="card-body">
                <div class="table-responsive text-center">
                    <table class="table">

                        <tbody>
                            @for ($row = 1; $row <= $seatingPlan->rows; $row++)
                                <tr>
                                    <td>
                                        <h4><strong>{{ Helpers::getLatinAlphabetUpperLetterByIndex($row) }}</strong></h4>
                                    </td>
                                @for ($column = 1; $column <= $seatingPlan->columns; $column++)

                                    <td style="padding-top:14px;">
                                        @php
                                        $seat = $event->getSeat($seatingPlan->id, $column, $row);
                                        $seatLabel = Helpers::getLatinAlphabetUpperLetterByIndex($row) . $column;
                                        $ticket = $event->getTicket();
                                        $canPickSeat = Auth::user()
                                        && $ticket
                                        && ($ticket->staff || $ticket->free || ($ticket->ticketType &&
                                        $ticket->ticketType->seatable));
                                        @endphp

                                        @if ($seat && $seat->status == 'ACTIVE')
                                        <button class="btn btn-success btn-sm" disabled>
                                            {{ $seatLabel }} - {{ $seat->eventTicket->user->username }}
                                        </button>
                                        @else
                                        @if ($seatingPlan->locked || !$canPickSeat)
                                        <button class="btn btn-primary btn-sm" disabled>
                                            {{ $seatLabel }} - @lang('events.empty')
                                        </button>
                                        @else
                                        <button class="btn btn-primary btn-sm" onclick="pickSeat(
                '{{ $seatingPlan->slug }}',
                '{{ $column }}',
                '{{ $row }}',
                '{{ $seatLabel }}'
            )" data-bs-toggle="modal" data-bs-target="#pickSeatModal">
                                            {{ $seatLabel }} - @lang('events.empty')
                                        </button>
                                        @endif
                                        @endif
                                    </td>
                                        @endfor
                                </tr>
                                @endfor
                        </tbody>
                    </table>
                    @if ($seatingPlan->locked)
                    <p class="text-center"><strong> @lang('events.seatingplanlocked')</strong></p>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <div class="row" style="display: flex; align-items: center;">
                    <div class="col-6 col-md-2">
                        <img class="img-fluid" alt="{{ $seatingPlan->name }}" src="{{$seatingPlan->image_path}}" />
                    </div>
                    <div class="col-6 col-md-6">
                        @if ($user && !$user->getAllTickets($event->id)->isEmpty() && $user->hasManagedTickets($event->id))
                        <h5>@lang('events.yourmanagedseats')</h5>
                        @foreach ($user->getAllTickets($event->id, false, true) as $managedTicket)
                        @if ($managedTicket->seat && $managedTicket->seat->event_seating_plan_id == $seatingPlan->id)
                        {{ Form::open(array('url'=>'/events/' . $event->slug . '/seating/' . $seatingPlan->slug)) }}
                        {{ Form::hidden('_method', 'DELETE') }}
                        {{ Form::hidden('user_id', $user->id, array('id'=>'user_id','class'=>'form-control')) }}
                        {{ Form::hidden('ticket_id', $managedTicket->id, array('id'=>'ticket_id','class'=>'form-control')) }}
                        {{ Form::hidden('seat_column_delete', $managedTicket->seat->column, array('id'=>'seat_column_delete','class'=>'form-control')) }}
                        {{ Form::hidden('seat_row_delete', $managedTicket->seat->row, array('id'=>'seat_row_delete','class'=>'form-control')) }}
                        <h5>
                            <button class="btn btn-danger btn-block">
                                @lang('events.remove') - {{ $managedTicket->seat->getName() }}
                            </button>
                        </h5>
                        {{ Form::close() }}
                        @endif
                        @endforeach
                        @elseif($user && !$user->hasManagedTickets($event->id))
                        <div class="alert alert-info">
                            <h5>@lang('events.noseatableticket')</h5>
                        </div>
                        @elseif(Auth::user())
                        <div class="alert alert-info">
                            <h5>@lang('events.plspurchaseticket')</h5>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <h5>@lang('events.plslogintopurchaseticket')</h5>
                        </div>
                        @endif
                    </div>
                    <div class="col-6 col-md-2">
                        @if ($user && !$user->getAllTickets($event->id)->isEmpty() && $user->hasSeatableTicket($event->id))
                        <h5>@lang('events.yourseats')</h5>
                        @foreach ($user->getAllTickets($event->id, false, false, true) as $ticket)
                        @if ($ticket->seat && $ticket->seat->event_seating_plan_id == $seatingPlan->id)
                        {{ Form::open(array('url'=>'/events/' . $event->slug . '/seating/' . $seatingPlan->slug)) }}
                        {{ Form::hidden('_method', 'DELETE') }}
                        {{ Form::hidden('user_id', $user->id, array('id'=>'user_id','class'=>'form-control')) }}
                        {{ Form::hidden('ticket_id', $ticket->id, array('id'=>'ticket_id','class'=>'form-control')) }}
                        {{ Form::hidden('seat_column_delete', $ticket->seat->column, array('id'=>'seat_column_delete','class'=>'form-control')) }}
                        {{ Form::hidden('seat_row_delete', $ticket->seat->row, array('id'=>'seat_row_delete','class'=>'form-control')) }}
                        <h5>
                            <button class="btn btn-danger btn-block">
                                @lang('events.remove') - {{ $ticket->seat->getName() }}
                            </button>
                        </h5>
                        {{ Form::close() }}
                        @endif
                        @endforeach
                        @elseif($user && !$user->hasSeatableTicket($event->id))
                        <div class="alert alert-info">
                            <h5>@lang('events.noseatableticket')</h5>
                        </div>
                        @elseif(Auth::user())
                        <div class="alert alert-info">
                            <h5>@lang('events.plspurchaseticket')</h5>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <h5>@lang('events.plslogintopurchaseticket')</h5>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>
@endif

<!-- Seat Modal -->
<div class="modal fade" id="pickSeatModal" tabindex="-1" role="dialog" aria-labelledby="editSeatingModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="pickSeatModalLabel"></h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			@if (Auth::user())
			{{ Form::open(array('url'=>'/events/' . $event->slug . '/seating/', 'id'=>'pickSeatFormModal')) }}
			<div class="modal-body">
				<div class="mb-3">
					<h4>@lang('events.wichtickettoseat')</h4>
                    {{-- Debug: Show raw ticket data --}}
                    @php
                    $ticketOptions = [];
                    foreach($user->getAllTickets($event->id) as $ticket) {
                    $ticketName = '';
                    if ($ticket->ticketType) {
                    $ticketName = $ticket->ticketType->name;
                    } elseif ($ticket->staff) {
                    $ticketName = 'Staff Ticket';
                    } elseif ($ticket->free) {
                    $ticketName = 'Free Ticket';
                    } else {
                    $ticketName = 'Unknown Ticket';
                    }
                    $ticketOptions[$ticket->id] = $ticketName . ' (ID: ' . $ticket->id . ')';
                    }
                    @endphp

                    {{
					        			Form::select(
									'ticket_id',
                    $ticketOptions,

                    null,
									array(
										'id'    => 'format',
										'class' => 'form-control'
									)
								)
							}}
					<p class="pt-2">@lang('events.wantthisseat')</p>
					<p>@lang('events.removeitanytime')</p>
				</div>
			</div>
			{{ Form::hidden('user_id', $user->id, array('id'=>'user_id','class'=>'form-control')) }}
			{{ Form::hidden('seat_column', null, array('id'=>'seat_column','class'=>'form-control')) }}
			{{ Form::hidden('seat_row', null, array('id'=>'seat_row','class'=>'form-control')) }}
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">@lang('events.yes')</button>
				<button type="button" class="btn btn-danger" data-bs-dismiss="modal">@lang('events.no')</button>
			</div>
			{{ Form::close() }}
			@endif
		</div>
	</div>
</div>

<script>
    function pickSeat(seating_plan_slug, seatColumn, seatRow, seatDisplay) {
        jQuery("#seat_column").val(seatColumn);
        jQuery("#seat_row").val(seatRow);
        jQuery("#seat_column_delete").val(seatColumn);
        jQuery("#seat_row_delete").val(seatRow);
        jQuery("#seat_number_modal").val(seatDisplay);
        jQuery("#pickSeatModalLabel").html('Do you what to choose seat ' + seatDisplay); // TODO add Language
        jQuery("#pickSeatFormModal").prop('action', '/events/{{ $event->slug }}/seating/' + seating_plan_slug);
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prüfen ob URL-Parameter expand_seating gesetzt ist
        const urlParams = new URLSearchParams(window.location.search);
        const expandSeating = urlParams.get('expand_seating');
        
        if (expandSeating === 'true') {
            // Alle Seating-Karten aufklappen
            const collapseElements = document.querySelectorAll('[id^="collapse_"]');
            collapseElements.forEach(function(element) {
                element.classList.add('show');
            });
            
            // Collapsed-Klasse von Links entfernen
            const toggleLinks = document.querySelectorAll('a[data-bs-toggle="collapse"]');
            toggleLinks.forEach(function(link) {
                link.classList.remove('collapsed');
                link.setAttribute('aria-expanded', 'true');
            });
        }
        
        // Oder bei Hash #seating automatisch aufklappen
        if (window.location.hash === '#seating') {
            setTimeout(function() {
                const collapseElements = document.querySelectorAll('[id^="collapse_"]');
                collapseElements.forEach(function(element) {
                    element.classList.add('show');
                });
                
                const toggleLinks = document.querySelectorAll('a[data-bs-toggle="collapse"]');
                toggleLinks.forEach(function(link) {
                    link.classList.remove('collapsed');
                    link.setAttribute('aria-expanded', 'true');
                });
            }, 100);
        }
    });
</script>