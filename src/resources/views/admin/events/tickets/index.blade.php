@extends ('layouts.admin-default')

@section ('page_title', 'Tickets - ' . $event->display_name)

@section ('content')

	<div class="row">
		<div class="col-lg-12">
			<h3 class="pb-2 mt-4 mb-4 border-bottom">Tickets</h3>
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<a href="/admin/events/">Events</a>
				</li>
				<li class="breadcrumb-item">
					<a href="/admin/events/{{ $event->slug }}">{{ $event->display_name }}</a>
				</li>
				<li class="breadcrumb-item active">
					Tickets
				</li>
			</ol>
		</div>
	</div>


    @include ('layouts._partials._admin._event.dashMini')

	<div class="row">

		<div class="col-md-8 col-lg-8">
            @php
            $charts = [
            [
            'title' => 'Purchase Breakdown',
            'canvasId' => 'purchaseBreakdownChart',
            'containerClasses' => 'h-75 w-75'
            ],
            [
            'title' => 'Income Breakdown',
            'canvasId' => 'incomeBreakdownChart',
            'containerClasses' => 'w-75'
            ]
            ];
            @endphp

            <div class="d-lg-block collapse d-md-none d-sm-none" id="ticketBreakdown">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-bar-chart-o fa-fw"></i> Ticket Breakdown
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($charts as $chart)
                        <div class="col-sm-6 col-12">
                            <h4>{{ $chart['title'] }}</h4>
                            <div class="{{ $chart['containerClasses'] }}">
                                <canvas id="{{ $chart['canvasId'] }}"></canvas>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            </div>


			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-ticket fa-fw"></i> Tickets
				</div>
				<div class="card-body table-responsive">
					<table width="100%" class="table table-striped table-hover" id="dataTables-example">
						<thead>
						<tr>
							<th>Name</th>
							<th>Type</th>
							<th>Ticket group</th>
							<th>Price</th>
							<th>Quantity</th>
							<th>Purchased</th>
							<th>Purchase Period</th>
                            <th>Hide policy?</th>
							<th>Seatable</th>
							<th></th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach ($event->ticketTypes as $ticketType)
							<tr class="table-row odd gradeX"
								data-href="/admin/events/{{ $event->slug }}/tickets/{{ $ticketType->id }}">
								<td>
									{{ $ticketType->name }}
								</td>
								<td>
									{{ $ticketType->type }}
								</td>
								<td>
									@if ($ticketType->ticketGroup)
										{{ $ticketType->ticketGroup->name }}
									@else
										<i>ungrouped</i>
									@endif
								</td>
								<td>
									{{ $ticketType->price }}
								</td>
								<td>
									@if ($ticketType->quantity == 0)
										N/A
									@else
										{{ $ticketType->quantity }}
									@endif
								</td>
								<td>
									{{ $ticketType->tickets()->count() }}
								</td>
								<td>

									@if ($ticketType->sale_start)
                                    Start:
										{{ date('H:i d-m-Y', strtotime($ticketType->sale_start)) }}
									@else
										Immediate
									@endif
									-
									@if ($ticketType->sale_end)
                                        End:
										{{ date('H:i d-m-Y', strtotime($ticketType->sale_end)) }}
									@else
										Never
									@endif
								</td>
                                <td>
                                    @if ($ticketType->tickettype_hide_policy >= 0)
                                        Custom
                                    @else
                                        Inherit
                                    @endif
                                </td>
								<td>
									@if ($ticketType->seatable)
										Yes
									@else
										No
									@endif
								</td>
								<td width="15%">
									<a href="/admin/events/{{ $event->slug }}/tickets/{{ $ticketType->id }}">
										<button type="button" class="btn btn-primary btn-sm btn-block">Edit</button>
									</a>
								</td>
								<td width="15%">
									{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/tickets/' . $ticketType->id, 'onsubmit' => 'return ConfirmDelete()')) }}
									{{ Form::hidden('_method', 'DELETE') }}
									<button type="submit" class="btn btn-danger btn-sm btn-block"
											data-confirm="Are you sure to delete this Ticket?">Delete
									</button>
									{{ Form::close() }}
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-gift fa-fw"></i> Freebies
					<a name="freebies"></a>
				</div>
				<div class="card-body">
					<div class="row mb-4">
						<div class="col-lg-12">
							<h5>Add Tickets to User</h5>
							<div class="input-group mb-3">
								<input type="text" id="user-search" class="form-control" placeholder="Search for user...">
								<div class="input-group-append">
									<button class="btn btn-outline-secondary" type="button" id="search-button">Search</button>
								</div>
							</div>
							<div id="search-results" class="mb-3" style="display: none;">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title" id="selected-user-name"></h5>
										<input type="hidden" id="selected-user-id">
										<div class="row">
											<div class="col-md-6">
												{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/freebies/gift', 'id'=>'free-ticket-form')) }}
												<input type="hidden" name="user_id" id="free-ticket-user-id" value=""/>
												<button type="submit" name="action" class="btn btn-success btn-block">Add Free Ticket</button>
												{{ Form::close() }}
											</div>
											<div class="col-md-6">
												{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/freebies/staff', 'id'=>'staff-ticket-form')) }}
												<input type="hidden" name="user_id" id="staff-ticket-user-id" value=""/>
												<button type="submit" name="action" class="btn btn-success btn-block">Add Staff Ticket</button>
												{{ Form::close() }}
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<table width="100%" class="table table-striped table-hover" id="dataTables-example">
						<thead>
						<tr>
							<th>Name</th>
							<th>Free Tickets (total: {{ $totalFreeTickets }})</th>
							<th>Staff Tickets (total: {{ $totalStaffTickets }})</th>
							<th></th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach ( $users as $user )
							<tr class="table-row odd gradeX">
								<td>
									{{ $user->username }}
									@if ($user->steamid)
										<br><span class="text-muted"><small>Steam: {{ $user->steamname }}</small></span>
									@endif
								</td>
								<td>
									{{ $user->getFreeTickets($event->id)->count() }}
								</td>
								<td>
									{{ $user->getStaffTickets($event->id)->count() }}
								</td>
								<td width="15%">
									{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/freebies/gift')) }}
									<input type="hidden" name="user_id" value="{{ $user->id }}"/>
									<button type="submit" name="action" class="btn btn-success btn-sm btn-block">Free
										Ticket
									</button>
									{{ Form::close() }}
								</td>
								<td width="15%">
									{{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/freebies/staff')) }}
									<input type="hidden" name="user_id" value="{{ $user->id }}"/>
									<button type="submit" name="action" class="btn btn-success btn-sm btn-block">Staff
										Ticket
									</button>
									{{ Form::close() }}
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>
        <div class="col-md-4 col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> Ticket Groups
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Tickets in group</th>
                            <th>No. tickets per user</th>
                            <th colspan="2">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><i>ungrouped</i></td>
                            <td>{{ $event->getUngroupedTickets()->count() }}</td>
                            <td>N/A</td>
                            <td colspan="2"></td>
                        </tr>
                        @foreach($event->ticketGroups as $group)
                        <tr>
                            <td>{{ $group->name }}</td>
                            <td>{{ $group->tickets->count() }}</td>
                            <td>{{ $group->tickets_per_user }}</td>
                            <td>
                                <a class="btn btn-primary btn-sm"
                                   href="/admin/events/{{ $event->slug }}/ticketgroups/{{ $group->id }}">Edit</a>
                            </td>
                            <td>
                                {{ Form::open([
                                'url' => "/admin/events/{$event->slug}/ticketgroups/{$group->id}",
                                'onsubmit' => 'return ConfirmSubmit()'
                                ]) }}
                                {{ Form::hidden('_method', 'DELETE') }}
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                {{ Form::close() }}
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-plus fa-fw"></i> Add Ticket Group
                </div>
                <div class="card-body">
                    {{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/ticketgroups')) }}
                    @include('layouts._partials._admin._event._tickets.ticket-group-form')
                    {{ Form::close() }}
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-plus fa-fw"></i> Add Tickets
                </div>
                <div class="card-body">
                    {{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/tickets')) }}
                    @include('layouts._partials._admin._event._tickets.form', ['empty' => true])
                    {{ Form::close() }}
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-eye-slash fa-fw"></i> Ticket Visibility Options
                </div>
                <div class="card-body">
                    <p>Control what ticket should be hidden from the users.</p>
                    <p>The Global Setting for this is set to: {{$global_ticket_hide_policy}}</p>
                    <p>The current value is: {{$event->tickettype_hide_policy}}</p>
                    {{ Form::open(['url' => '/admin/events/' . $event->slug . '/updateTicketHidePolicy', 'onsubmit' => 'return Confirm()']) }}
                    <div class="form-group">
                        <label for="ticket_hide_policy">New Hide Policy Value</label>
                        <small class="text-muted"><i>To use global setting enter -1</i></small>
                        {{ Form::number('ticket_hide_policy', $event->tickettype_hide_policy, [
    'class' => 'form-control mb-2',
    'id' => 'ticket_hide_policy',
    'min' => -1,
    'max' => 15]) }}
                        <button type="submit" class="btn btn-success">Save Policy</button>
                    </div>
                    {{ Form::close() }}
                    <h4>Current Settings:</h4>
                    <p>Current hide policy value: {{ $event->tickettype_hide_policy }}</p>
                    <div class="dataTable_wrapper table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Filter Value</th>
                                <th>Explanation</th>
                                <th>Global</th>
                                <th>Y/N</th>
                            </tr>
                            </thead>
                            <tbody>
                            @for ($bit = 0; $bit <= 3; $bit++)
                                @php
                                    $isGloballyEnabled = ($global_ticket_hide_policy & (1 << $bit)) !== 0;
                                    $isEnabled = ($event->tickettype_hide_policy & (1 << $bit)) !== 0;
                                    $isOverridden = $event->tickettype_hide_policy >= 0;
                                @endphp
                                <tr>
                                    <td>{{ pow(2, $bit) }}</td>
                                    <td>
                                        @if ($bit === 0)
                                            Hide upcoming tickets
                                        @elseif ($bit === 1)
                                            Hide expired tickets
                                        @elseif ($bit === 2)
                                            Hide sold out tickets
                                        @elseif ($bit === 3)
                                            Hide timeless tickets
                                        @endif
                                    </td>
                                    <td>
                                        @if ($isGloballyEnabled)
                                            @if (!$isOverridden)
                                                <i class="fa fa-check-circle-o fa-1x" style="color:green"></i>
                                            @else
                                                <i class="fa fa-check-circle-o fa-1x" style="color:grey"></i>
                                            @endif
                                        @else
                                            @if (!$isOverridden)
                                                <i class="fa fa-times-circle-o fa-1x" style="color:red"></i>
                                            @else
                                                <i class="fa fa-times-circle-o fa-1x" style="color:grey"></i>
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
                    <p><i class="bg-warning">Note:</i> these settings can be overridden by each TicketTypes</p>
                </div>
            </div>
        </div>
            <script>

                const purchaseBreakdownChartCanvas = document.getElementById('purchaseBreakdownChart');
                const incomeBreakdownChartCanvas = document.getElementById('incomeBreakdownChart');

                let purchaseBreakdownData = @json($purchaseBreakdownData);
                let incomeBreakdownData = @json($incomeBreakdownData);

                // Purchase Breakdown

                new Chart(purchaseBreakdownChartCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: purchaseBreakdownData.map(item => item.name),
                        datasets: [{
                            label: 'Purchase Breakdown',
                            data: purchaseBreakdownData.map(item => item.count),
                        }]
                    }
                });

                // Income Breakdown

                new Chart(incomeBreakdownChartCanvas, {
                    type: 'bar',
                    data: {
                        labels: incomeBreakdownData.map(item => item.name),
                        datasets: [{
                            label: 'Income Breakdown',
                            data: incomeBreakdownData.map(item => item.income),
                        }]
                    }
                });
            </script>
        </div>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const userSearch = document.getElementById('user-search');
			const searchButton = document.getElementById('search-button');
			const searchResults = document.getElementById('search-results');
			const selectedUserName = document.getElementById('selected-user-name');
			const selectedUserId = document.getElementById('selected-user-id');
			const freeTicketUserId = document.getElementById('free-ticket-user-id');
			const staffTicketUserId = document.getElementById('staff-ticket-user-id');

			// Function to search for users
			function searchUsers() {
				const query = userSearch.value.trim();
				if (query.length < 2) {
					alert('Please enter at least 2 characters to search');
					return;
				}

				fetch(`/search/users/autocomplete?query=${encodeURIComponent(query)}`)
					.then(response => response.json())
					.then(data => {
						if (data && data.length > 0) {
							// Display the first user
							const user = data[0];
							selectedUserName.textContent = user.username;
							selectedUserId.value = user.id;
							freeTicketUserId.value = user.id;
							staffTicketUserId.value = user.id;
							searchResults.style.display = 'block';
						} else {
							alert('No users found with that username');
							searchResults.style.display = 'none';
						}
					})
					.catch(error => {
						console.error('Error searching for users:', error);
						alert('Error searching for users');
					});
			}

			// Add event listeners
			searchButton.addEventListener('click', searchUsers);
			userSearch.addEventListener('keypress', function(e) {
				if (e.key === 'Enter') {
					e.preventDefault();
					searchUsers();
				}
			});
		});
	</script>

@endsection