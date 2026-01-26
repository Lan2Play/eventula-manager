@php
    // Determine event status color/icon used in links (also duplicated in the cards partial)
    if ($event->status == 'PUBLISHED') {
        $eventStatusColor = 'success';
        $eventStatusIcon = 'fa-check-circle-o';
    } elseif ($event->status == 'REGISTEREDONLY') {
        $eventStatusColor = 'info';
        $eventStatusIcon = 'fa-check-circle-o';
    } elseif ($event->status == 'DRAFT') {
        $eventStatusColor = 'danger';
        $eventStatusIcon = 'fa-times-circle-o';
    } elseif ($event->status == 'PREVIEW' || $event->status == 'PRIVATE') {
        $eventStatusColor = 'warning';
        $eventStatusIcon = 'fa-ban';
    } else {
        $eventStatusColor = 'success';
        $eventStatusIcon = 'fa-question-circle ';
    }
@endphp

<!-- Small screens: compact menu with collapse to reveal full dashboard -->
<div class="d-md-none">
    <div class="row g-2 mb-3">
        <div class="col-6">
            <a href="/admin/events/{{ $event->slug }}" class="list-group-item list-group-item-action">
                <i class="fa {{ $eventStatusIcon }} me-2"></i> Event Page
            </a>
        </div>
        <div class="col-6">
            <a href="/admin/events/{{ $event->slug }}/seating" class="list-group-item list-group-item-action">
                <i class="fa fa-wheelchair me-2"></i> Seating Plans
            </a>
        </div>
        <div class="col-6">
            <a href="/admin/events/{{ $event->slug }}/tournaments" class="list-group-item list-group-item-action">
                <i class="fa fa-list-ol me-2"></i> Tournaments
            </a>
        </div>
        <div class="col-6">
            <a href="/admin/events/{{ $event->slug }}/participants" class="list-group-item list-group-item-action">
                <i class="fa fa-user me-2"></i> Attendees
            </a>
        </div>
        <div class="col-6">
            <a href="/admin/events/{{ $event->slug }}/tickets" class="list-group-item list-group-item-action">
                <i class="fa fa-ticket me-2"></i> Tickets
            </a>
        </div>
        <div class="col-6">
            <a href="/admin/events/{{ $event->slug }}/timetables" class="list-group-item list-group-item-action">
                <i class="fa fa-calendar me-2"></i> Timetables
            </a>
        </div>
    </div>
    <button class="btn w-100 mb-3" type="button" data-bs-toggle="collapse"
            data-bs-target="#miniDashCards" aria-expanded="false" aria-controls="miniDashCards">
        <span class="collapse show" data-bs-target="#miniDashCards">Show dashboard</span>
        <span class="collapse" data-bs-target="#miniDashCards">Hide dashboard</span>
    </button>

    <div id="miniDashCards" class="collapse">
        @include('layouts._partials._admin._event._dashMiniCards')
    </div>
</div>

<!-- Medium and larger screens: always show full dashboard -->
<div class="d-none d-md-block">
    @include('layouts._partials._admin._event._dashMiniCards')
</div>