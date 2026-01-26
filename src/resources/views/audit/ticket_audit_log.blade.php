@extends ('layouts.default')

@section('page_title', __('audits.ticket_audit_log'))

@section('content')
<div class="container pt-1">

<h2>@lang('audits.audit_log_for_ticket'): {{ $ticket->id }}</h2>
    @include('layouts._partials._audit.ticket')
</div>
@endsection
