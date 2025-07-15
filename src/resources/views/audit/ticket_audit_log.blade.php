@extends ('layouts.default')

@section('page_title', __('audits.ticket_audit_log'))

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h2>@lang('audits.audit_log_for_ticket'): {{ $ticket->id }}</h2>
    </div>
    <div class="card-body">
        <h5>@lang('tickets.current_ticket_details')</h5>
        <ul class="list-group list-group-flush mb-3">
            <li class="list-group-item"><strong>ID:</strong> {{ $ticket->id }}</li>
            <li class="list-group-item"><strong>@lang('events.event'):</strong> {{ $ticket->event->slug }}</li>
            <li class="list-group-item">
                <strong>@lang('tickets.ticket_type'):</strong>
                {{ optional($ticket->ticketType)->name ?? __('tickets.either_staff_or_free') }}
            </li>
            <li class="list-group-item"><strong>@lang('tickets.free_ticket'):</strong> {{ $ticket->free }}</li>
            <li class="list-group-item"><strong>@lang('tickets.staff_ticket'):</strong> {{ $ticket->staff }}</li>
            <li class="list-group-item"><strong>@lang('general.created_at'):</strong> {{ $ticket->created_at->format('Y-m-d H:i') }}</li>
            <li class="list-group-item"><strong>@lang('general.updated_at'):</strong> {{ $ticket->updated_at->format('Y-m-d H:i') }}</li>
            <li class="list-group-item"><strong>@lang('tickets.owner'):</strong> {{ $ticket->owner->username }}</li>
            <li class="list-group-item"><strong>@lang('tickets.manager'):</strong> {{ $ticket->manager->username }}</li>
            <li class="list-group-item"><strong>@lang('tickets.user'):</strong> {{ $ticket->user->username }}</li>
        </ul>

        @if($audits->isEmpty())
        <p>@lang('audit.no_logs_available')</p>
        @else
        <h5>@lang('audits.audit_log')</h5>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>@lang('audit.timestamp')</th>
                <th>@lang('audit.user')</th>
                <th>@lang('audit.event')</th>
                <th>@lang('audit.changes')</th>
            </tr>
            </thead>
            <tbody>
            @foreach($audits as $index => $audit)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $audit->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ optional($audit->user)->name ?? __('audit.system') }}</td>
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
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
