@extends ('layouts.admin-default')

@section ('page_title', 'Tickets - ' . $event->display_name)

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Ticket Type - {{ $ticketType->name }}</h3>
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
				{{ $ticketType->name }}
			</li>
		</ol>
	</div>
</div>


<div class="d-lg-block collapse d-md-none d-sm-none" id="dashMini">
    @include ('layouts._partials._admin._event.dashMini')
</div>
<div class="row">
	<div class="col-lg-6">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-pencil fa-fw"></i> Edit
			</div>
			<div class="card-body">
				{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/tickets/' . $ticketType->id)) }}
					@if (isset($ticketType) && !$ticketType->tickets->isEmpty()) @php $priceLock = true; @endphp @endif

					@include ('layouts._partials._admin._event._tickets.form')
				{{ Form::close() }}
			</div>
		</div>

	</div>
	<div class="col-lg-6">

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-bar-chart-o fa-fw"></i> Statistics
			</div>
			<div class="card-body">
				<div class="list-group">
					Chart me
					<h4>Money Made</h4>
					<p>{{ Settings::getCurrencySymbol() }}{{ $ticketType->tickets()->count() * $ticketType->price }}</p>
					<h4>Purchases</h4>
					<p>{{ $ticketType->tickets()->count() }}</p>
					@if ($ticketType->quantity > 0)
						<h4>Quantity</h4>
						<p>{{ $ticketType->quantity }}</p>
					@endif
				</div>
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-user fa-fw"></i> Purchases
			</div>
			<div class="card-body">
				<div class="list-group">
					@foreach ($event->tickets as $ticket)
						@if ($ticket->ticket_type_id == $ticketType->id)
							<a href="/admin/events/{{ $event->slug }}/participants/{{ $ticket->id }}" class="list-group-item">
								<i class="fa fa-comment fa-fw"></i> {{ $ticket->user->username }}
								@if ($ticket->user->steamid)
									- <span class="text-muted"><small>Steam: {{ $ticket->user->steamname }}</small></span>
								@endif
								<span class="float-end text-muted small">
									<em>
										{{ date('d-m-Y H:i', strtotime($ticket->created_at)) }}
									</em>
								</span>
							</a>
						@endif
					@endforeach
				</div>
			</div>
		</div>

        <div class="card mb-3">
            <div class="card-header">
                <i class="fa fa-eye-slash fa-fw"></i> Ticket Visibility Options
            </div>
            <div class="card-body">
                <p>Control what ticket should be hidden from the users.</p>
                <p>The global value is set to: {{$global_tickettype_hide_policy}}</p>
                <p>The event wide Value is set to: {{$event_tickettype_hide_policy}}</p>
                <p>The current value is: {{$ticketType->tickettype_hide_policy}}</p>
                {{ Form::open(['url' => '/admin/events/' . $event->slug . '/tickets/' . $ticketType->id, 'onsubmit' => 'return Confirm()']) }}
                <div class="form-group">
                    <label for="tickettype_hide_policy">New Hide Policy Value</label>
                    <small class="text-muted"><i>To use global setting enter -1</i></small>
                    {{ Form::number('tickettype_hide_policy', $ticketType->tickettype_hide_policy, [
    'class' => 'form-control mb-2',
    'id' => 'tickettype_hide_policy',
    'min' => -1,
    'max' => 15]) }}
                    <button type="submit" class="btn btn-success">Save Policy</button>
                </div>
                {{ Form::close() }}
                <h4>Current Settings:</h4>
                <p>Current hide policy value: {{ $ticketType->tickettype_hide_policy }}</p>
                <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Filter Value</th>
                        <th>Explanation</th>
                        <th>Global</th>
                        <th>Event</th>
                        <th>Y/N</th>
                    </tr>
                    </thead>
                    <tbody>
                    @for ($bit = 0; $bit <= 3; $bit++)
                        @php
                            $isGloballyEnabled = ($global_tickettype_hide_policy & (1 << $bit)) !== 0;
                            $isEventWideEnabled = ($event_tickettype_hide_policy & (1 << $bit)) !== 0;
                            $isEnabled = ($ticketType->tickettype_hide_policy & (1 << $bit)) !== 0;
                            $isOverriddenEvent = $event->tickettype_hide_policy >= 0;
                            $isOverridden = $ticketType->tickettype_hide_policy >= 0;
                        @endphp
                        <tr>
                            <td>{{ pow(2, $bit) }}</td>
                            <td>
                                @if ($bit === 0)
                                    Hide when upcoming
                                @elseif ($bit === 1)
                                    Hide when expired
                                @elseif ($bit === 2)
                                    Hide when solt out
                                @elseif ($bit === 3)
                                    Hide always
                                @endif
                            </td>
                            <td>
                                @if ($isGloballyEnabled)
                                    @if ($isOverriddenEvent || $isOverridden)
                                        <i class="fa fa-check-circle-o fa-1x" style="color:grey"></i>
                                    @else
                                        <i class="fa fa-check-circle-o fa-1x" style="color:green"></i>
                                    @endif
                                @else
                                    @if ($isOverriddenEvent || $isOverridden)
                                        <i class="fa fa-times-circle-o fa-1x" style="color:grey"></i>
                                    @else
                                        <i class="fa fa-times-circle-o fa-1x" style="color:red"></i>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if ($isOverridden || !$isOverriddenEvent)
                                    @if ($isEventWideEnabled && $isOverriddenEvent)
                                        <i class="fa fa-check-circle-o fa-1x" style="color:grey"></i>
                                    @else
                                        <i class="fa fa-times-circle-o fa-1x" style="color:grey"></i>
                                    @endif
                                @else
                                    @if ($isEventWideEnabled)
                                        <i class="fa fa-check-circle-o fa-1x" style="color:green"></i>
                                    @else
                                        <i class="fa fa-times-circle-o fa-1x" style="color:red"></i>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if ($isOverridden && $isEnabled)
                                    <i class="fa fa-check-circle-o fa-1x" style="color:green"></i>
                                @elseif($isOverridden && !$isEnabled)
                                    <i class="fa fa-times-circle-o fa-1x" style="color:red"></i>
                                @else
                                    <i class="fa fa-times-circle-o fa-1x" style="color:grey"></i>
                                @endif
                            </td>
                        </tr>
                    @endfor
                    </tbody>
                </table>
                </div>
            </div>
        </div>

	</div>
</div>

@endsection