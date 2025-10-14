{{-- ATTENDEES PARTIAL --}}
@if (!$event->private_participants || ($user && isset($userTickets) && !$userTickets->isEmpty()))
    @php
    // Pre-calculate which users have seatable tickets to avoid nested loops
    $usersWithSeatableTickets = [];
    if (isset($event->tickets)) {
        foreach ($event->tickets as $ticket) {
            if (!isset($usersWithSeatableTickets[$ticket->user_id])) {
                $usersWithSeatableTickets[$ticket->user_id] = false;
            }
            // Check if this ticket is seatable
            if ($ticket->staff || $ticket->free || ($ticket->ticketType && $ticket->ticketType->seatable)) {
                $usersWithSeatableTickets[$ticket->user_id] = true;
            }
        }
    }
    @endphp
    <!-- ATTENDEES -->
    <div class="pb-2 mt-4 mb-4 border-bottom">
        <a name="attendees"></a>
        <h3><i class="fas fa-users me-3"></i>@lang('events.attendees')</h3>
    </div>
    <table class="table table-striped">
        <thead>
        <th width="15%">
        </th>
        <th>
            @lang('events.user')
        </th>
        <th>
            @lang('events.name')
        </th>
        <th>
            @lang('events.seat')
        </th>
        </thead>
        <tbody>
        @foreach ($event->tickets->unique('user.id') as $ticket)
        <tr>
                <td>
                    <img class="img-fluid rounded img-small" style="max-width: 30%;"
                         alt="{{ $ticket->user->username}}'s Avatar"
                         src="{{ $ticket->user->avatar }}">
                </td>
                <td style="vertical-align: middle;">
                    {{ $ticket->user->username }}
                    @if ($ticket->user->steamid)
                        -
                        <span class="text-muted"><small>Steam: {{ $ticket->user->steamname }}</small></span>
                    @endif
                </td>
                <td style="vertical-align: middle;">
                    {{$ticket->user->firstname}}
                </td>
                <td style="vertical-align: middle;">
                    @if ($ticket->seat)
                        @if ($ticket->seat->seatingPlan)
                            {{ $ticket->seat->seatingPlan->getShortName() }}
                            | {{ $ticket->seat->getName() }}
                        @else
                            @lang('events.seatingplannotaccessable')
                        @endif
                    @else
                        {{-- Use pre-calculated seatable ticket information --}}
                        @if (isset($usersWithSeatableTickets[$ticket->user_id]) && $usersWithSeatableTickets[$ticket->user_id])
                            @lang('events.notseated')
                        @else
                            @lang('events.noseatableticketlist')
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
