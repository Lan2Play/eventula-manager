@extends ('layouts.admin-default')

@section ('page_title', 'Ticket group - ' . $ticketGroup->name)

@section ('content')

    <div class="row">
        <div class="col-lg-12">
            <h3 class="pb-2 mt-4 mb-4 border-bottom">Ticket group - {{ $ticketGroup->name }}</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admin/events/">Events</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/admin/events/{{ $event->slug }}">{{ $event->display_name }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/admin/events/{{ $event->slug }}/tickets">Tickets</a>
                </li>
                <li class="breadcrumb-item active">
                    {{ $ticketGroup->name }}
                </li>
            </ol>
        </div>
    </div>

    @include ('layouts._partials._admin._event.dashMini')

    <div class="row">
        <div class="col-md-12">

            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-pencil fa-fw"></i> Edit
                </div>
                <div class="card-body">
                    {{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/ticketgroups/' . $ticketGroup->id)) }}
                    @include ('layouts._partials._admin._event._tickets.ticket-group-form')
                    {{ Form::close() }}
                </div>
            </div>

        </div>
    </div>

@endsection