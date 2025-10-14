<h5>@lang('tickets.current_ticket_details')</h5>
<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item"><strong>ID:</strong> {{ $ticket->id }}</li>
    <li class="list-group-item"><strong>@lang('events.event'):</strong> {{ $ticket->event->slug }}</li>
    <li class="list-group-item">
        <strong>@lang('tickets.ticket_type'):</strong>
        {{ optional($ticket->ticketType)->name ?? __('tickets.either_staff_or_free') }}
    </li>
    <li class="list-group-item"><strong>@lang('tickets.free_ticket'):</strong> {{ $ticket->free ? __('yes') : __('no')
        }}
    </li>
    <li class="list-group-item"><strong>@lang('tickets.staff_ticket'):</strong> {{ $ticket->staff ? __('yes') : __('no')
        }}
    </li>
    <li class="list-group-item"><strong>@lang('general.created_at'):</strong> {{ $ticket->created_at->format('Y-m-d
        H:i') }}
    </li>
    <li class="list-group-item"><strong>@lang('general.updated_at'):</strong> {{ $ticket->updated_at->format('Y-m-d
        H:i') }}
    </li>
    <li class="list-group-item"><strong>@lang('tickets.owner'):</strong> {{ $ticket->owner->username }}</li>
    <li class="list-group-item"><strong>@lang('tickets.manager'):</strong> {{ $ticket->manager->username }}</li>
    <li class="list-group-item"><strong>@lang('tickets.user'):</strong> {{ $ticket->user->username }}</li>
</ul>

@if($audits->isEmpty())
<p>@lang('audit.no_logs_available')</p>
@else
<h5>@lang('audits.audit_log')</h5>
<div  class="table-responsive">
<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th>@lang('audits.timestamp')</th>
        <th>@lang('audits.user')</th>
        <th>@lang('audits.action')</th>
        <th>@lang('audits.changes')</th>
    </tr>
    </thead>
    <tbody>
    @foreach($audits as $index => $audit)
    <tr>
        <td>{{ $index + 1 }}</td>

        @if(! empty($audit->redacted))
        <td colspan="4" class="text-center text-muted">
            @lang('audits.redacted_for_privacy')
        </td>
        @else
        <td>{{ $audit->created_at->format('Y-m-d H:i:s') }}</td>
        <td>
            @if($audit->user_type === \App\User::class)
            {{ $audit->user_id }}
            @else
            {{ __('audit.system') }}
            @endif
        </td>
        <td>{{ $audit->event }}</td>
        <td>
            @foreach($audit->new_values as $field => $new)
            <div>
                <strong>{{ $field }}</strong>:
                <span class="text-muted">{{ $audit->old_values[$field] ?? '-' }}</span>
                &rarr; {{ $new }}
            </div>
            @endforeach
        </td>
        @endif
    </tr>
    @endforeach
    </tbody>
</table>
</div>
@endif