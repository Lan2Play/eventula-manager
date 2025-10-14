{{-- ATTENDEES PARTIAL --}}
@if (!$event->private_participants || ($user && isset($userTickets) && !$userTickets->isEmpty()))
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
        @foreach ($event->tickets as $ticket)
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
                        {{-- Check if user has a seatable ticket --}}
                        @php
                        $hasSeatable = false;
                        if (isset($userTickets)) {
                            foreach ($userTickets as $ut) {
                                if ($ut->user_id == $ticket->user_id && ($ut->staff || $ut->free || ($ut->ticketType && $ut->ticketType->seatable))) {
                                    $hasSeatable = true;
                                    break;
                                }
                            }
                        }
                        @endphp
                        @if ($hasSeatable)
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
