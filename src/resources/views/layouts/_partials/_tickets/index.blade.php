<div class="card mb-3">
	<div @class([
			"card-header",
			"bg-success-light" => !$ticket->revoked,
			"text-success" => !$ticket->revoked,
			"bg-danger-light" => $ticket->revoked,
			"text-danger" => $ticket->revoked
        ])>
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<i class="fa fa-ticket-alt fa-fw me-1"></i>
				<strong>{{ $ticket->event->display_name }}</strong>
				@if ($ticket->ticketType)
					- <strong>{{ $ticket->ticketType->name }}</strong>
					@if ($ticket->ticketType && $ticket->ticketType->seatable)
						<span class="ms-1">
							<i class="fa fa-chair fa-fw"></i>
							@if ($ticket->seat)
								{{ $ticket->seat->getName() }}
								<small class="text-muted">in {{$ticket->seat->seatingPlan->name}}</small>
							@else
								<span class="text-muted">@lang('events.notseated')</span>
							@endif
						</span>
					@endif
				@else
					@if ($ticket->staff)
						- <strong>@lang('tickets.staff_ticket')</strong>
						<span class="ms-1">
							<i class="fa fa-chair fa-fw"></i>
							@if ($ticket->seat)
								{{ $ticket->seat->getName() }}
								<small class="text-muted">in {{$ticket->seat->seatingPlan->name}}</small>
							@else
								<span class="text-muted">@lang('events.notseated')</span>
							@endif
						</span>
					@else
						- <strong>@lang('tickets.free_ticket')</strong>
						<span class="ms-1">
							<i class="fa fa-chair fa-fw"></i>
							@if ($ticket->seat)
								{{ $ticket->seat->getName() }}
								<small class="text-muted">in {{$ticket->seat->seatingPlan->name}}</small>
							@else
								<span class="text-muted">@lang('events.notseated')</span>
							@endif
						</span>
					@endif
				@endif
                @if ($ticket->signed_in)
                    <span class="ms-1">
                        <i class="fa fa-check-circle fa-fw"></i>
                        @lang('tickets.signed_in')
                    </span>
                @endif
			</div>
			<div>
				@if ($ticket->gift == 1 && $ticket->gift_accepted != 1)
					<span class="badge text-bg-info ms-1">@lang('tickets.has_been_gifted')</span>
				@endif
				@if ($ticket->ticketType && !$ticket->ticketType->seatable)
					<span class="badge text-bg-info ms-1">@lang('tickets.not_eligable_for_seat')</span>
				@endif
				@if ($ticket->revoked)
					<span class="badge text-bg-danger ms-1">@lang('tickets.has_been_revoked')</span>
				@endif
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="row d-flex align-items-center">
			<div class="col-lg-6 col-md-12 mb-3">
				<!-- Gift and Seating Management Section -->
				@if ($ticket->gift == 1 && $ticket->gift_accepted != 1)
					<div class="mb-3">
						<label class="form-label">@lang('tickets.gift_url')</label>
						<div class="input-group mb-2">
							<input type="text" class="form-control" value="{{ URL::to('/') }}/gift/accept/?url={{ $ticket->gift_accepted_url }}" readonly>
							<button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('{{ URL::to('/') }}/gift/accept/?url={{ $ticket->gift_accepted_url }}')">
								<i class="fas fa-copy"></i>
							</button>
						</div>
						{{ Form::open(array('url'=>'/gift/' . $ticket->id . '/revoke', 'id'=>'revokeGiftTicketForm')) }}
							<button type="submit" class="btn btn-primary">@lang('tickets.revoke_gift')</button>
						{{ Form::close() }}
					</div>
				@endif

                @if ($ticket->seat)
                <div class="mt-3">
                    {{ Form::open(array('url'=>'/events/' . $ticket->event->slug . '/seating/' .
                    $ticket->seat->seatingPlan->slug)) }}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::hidden('_token', csrf_token()) }}
                    {{ Form::hidden('user_id', $user->id, array('id'=>'user_id','class'=>'form-control')) }}
                    {{ Form::hidden('ticket_id', $ticket->id, array('id'=>'ticket_id','class'=>'form-control')) }}
                    {{ Form::hidden('seat_column_delete', $ticket->seat->column,
                    array('id'=>'seat_column_delete','class'=>'form-control')) }}
                    {{ Form::hidden('seat_row_delete', $ticket->seat->row,
                    array('id'=>'seat_row_delete','class'=>'form-control')) }}
                    {{-- TODO Change this to disabled directive for later --}}
                    <button class="btn btn-danger" {{ (auth()->id() != $ticket->user_id && auth()->id() !=
                        $ticket->manager_id && !auth()->user()->getAdmin()) ? 'disabled' : '' }}>
                        <i class="fas fa-chair me-1"></i> @lang('events.remove_seating')
                    </button>
                    @if(auth()->id() != $ticket->user_id && auth()->id() != $ticket->manager_id &&
                    !auth()->user()->getAdmin())
                    <div class="alert alert-warning mt-2">
                        <small><i class="fas fa-exclamation-triangle me-1"></i> @lang('tickets.change_seat_tooltip')</small>
                    </div>
                    @endif
                    {{ Form::close() }}
                </div>
                @elseif(($ticket->ticketType && $ticket->ticketType->seatable) || ($ticket->free || $ticket->staff))
                <div class="mt-3">
                    @if(auth()->id() != $ticket->user_id && auth()->id() != $ticket->manager_id &&
                    !auth()->user()->getAdmin())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>@lang('tickets.change_seat_tooltip')
                    </div>
                    @elseif(auth()->id() == $ticket->user_id || auth()->id() == $ticket->manager_id)
                    <a href="/events/{{$ticket->event->slug}}?expand_seating=true#seating" class="btn btn-primary">
                        <i class="fas fa-chair me-1"></i> @lang('tickets.select_seat')
                    </a>
                    @endif
                </div>
                @endif
			</div>

			<div class="col-lg-4 col-md-12 mb-3 text-center">
				<img class="img-fluid rounded border" src="/{{ $ticket->qrcode }}" alt="Ticket QR Code" style="max-width: 200px;" />
			</div>

			<div class="col-12">
				<hr>
				<h5 class="mb-3"><i class="fas fa-users me-2"></i>@lang('tickets.ticket_roles')</h5>
			</div>

			<div class="col-md-4 col-sm-12 mb-3">
                <div class="card role-card">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-crown me-2 text-warning"></i>
                            <strong>Owner</strong>
                            <a href="#" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('tickets.tooltip_ticket_role_owner')">
                                <i class="fas fa-question-circle"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="flex-grow-1">
                            @if ($ticket->owner_id)
                                <p class="mb-2"><strong>{{ $ticket->owner->username }}</strong></p>
                            @else
                                <p class="text-muted mb-2">@lang('tickets.no_owner')</p>
                            @endif
                        </div>
                        <div class="role-description">
                            <small class="text-muted">@lang('tickets.owner_cant_be_changed')</small>
                        </div>
                    </div>
                </div>
            </div>

			<div class="col-md-4 col-sm-12 mb-3">
                <div class="card role-card">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-cog me-2 text-primary"></i>
                            <strong>@lang('tickets.manager')</strong>
                            <a href="#" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('tickets.tooltip_ticket_role_manager')">
                                <i class="fas fa-question-circle"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="flex-grow-1">
                            @if ($ticket->manager_id)
                            <p class="mb-2"><strong>{{ $ticket->manager->username }}</strong></p>
                            @else
                            <p class="text-muted mb-2">@lang('tickets.no_manager')</p>
                            @endif
                        </div>
                        <div class="mt-auto d-flex">
                            <button
                                    class="btn btn-primary btn-sm w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#changeManagerModal{{ $ticket->id }}"
                                    {{ auth()->id() != $ticket->owner_id && !auth()->user()->getAdmin() ? 'disabled' : '' }}>
                                <i class="fas fa-edit me-1"></i> @lang('tickets.change_manager')
                            </button>
                            @if(auth()->id() == $ticket->owner_id && $ticket->manager_id != auth()->id())
                            {{ Form::open(array('url'=>'/events/' . $ticket->event->slug . '/participants/' . $ticket->id . '/resetManager', 'method'=>'POST')) }}
                            {{ Form::hidden('_token', csrf_token()) }}
                            <button
                                    class="btn btn-secondary btn-sm ms-2"
                                    id="resetManagerBtn{{ $ticket->id }}"
                                    data-ticket-id="{{ $ticket->id }}"
                                    type="submit"
                                    {{ auth()->id() != $ticket->owner_id && !auth()->user()->getAdmin() ? 'disabled' : '' }}>

                                <i class="fas fa-undo me-1"></i>
                            </button>
                            @endif
                        </div>
                        @if(auth()->id() != $ticket->owner_id && !auth()->user()->getAdmin())
                        <small class="d-block text-muted mt-1">@lang('tickets.only_owner_can_change_manager')</small>
                        @endif
                    </div>

                </div>
            </div>

            <div class="col-md-4 col-sm-12 mb-3">
                <div class="card role-card">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user me-2 text-success"></i>
                            <strong>@lang('tickets.user')</strong>
                            <a href="#" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('tickets.tooltip_ticket_role_user')">
                                <i class="fas fa-question-circle"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="flex-grow-1">
                            @if ($ticket->user_id)
                                <p class="mb-2"><strong>{{ $ticket->user->username }}</strong></p>
                            @else
                                <p class="text-muted mb-2">@lang('tickets.no_user')</p>
                            @endif
                        </div>
                        <div class="mt-auto d-flex">
                            <button class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#changeUserModal{{ $ticket->id }}" {{ (auth()->id() != $ticket->owner_id && auth()->id() != $ticket->manager_id && !auth()->user()->getAdmin()) ? 'disabled' : '' }}>
                                <i class="fas fa-edit me-1"></i> @lang('tickets.change_user')
                            </button>
                            @if(auth()->id() != $ticket->owner_id && auth()->id() != $ticket->manager_id && !auth()->user()->getAdmin())
                                <small class="d-block text-muted mt-1">@lang('tickets.only_owner_or_manager_can_change_user')</small>
                            @endif
                            @if(auth()->id() == $ticket->owner_id && $ticket->user_id != auth()->id())
                            {{ Form::open(array('url'=>'/events/' . $ticket->event->slug . '/participants/' . $ticket->id . '/resetUser', 'method'=>'POST')) }}
                            {{ Form::hidden('_token', csrf_token()) }}
                            <button
                                    class="btn btn-secondary btn-sm ms-2"
                                    id="resetUserBtn{{ $ticket->id }}"
                                    data-ticket-id="{{ $ticket->id }}"
                                    type="submit"
                                    {{ auth()->id() != $ticket->owner_id && !auth()->user()->getAdmin() ? 'disabled' : '' }}>
                                <i class="fas fa-undo me-1"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <!-- Change Manager Modal -->
            <div class="modal fade" id="changeManagerModal{{ $ticket->id }}" tabindex="-1" aria-labelledby="changeManagerModalLabel{{ $ticket->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changeManagerModalLabel{{ $ticket->id }}">@lang('tickets.change_manager')</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        {{ Form::open(array('url'=>'/events/' . $ticket->event->slug . '/participants/' . $ticket->id, 'method'=>'POST')) }}
                        {{ Form::hidden('_token', csrf_token()) }}
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="manager_search">@lang('tickets.modal_change_manager_search_manager_label')</label>
                                <input type="text" id="manager_search{{ $ticket->id }}" class="form-control manager-search" placeholder="Type to search users or leave empty for none..." autocomplete="off">
                                <input type="hidden" name="manager_id" id="manager_id{{ $ticket->id }}" value="{{ $ticket->manager_id }}">
                                <div id="manager_search_results{{ $ticket->id }}" class="search-results"></div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var managerSearch = document.getElementById('manager_search{{ $ticket->id }}');
                                    var managerIdInput = document.getElementById('manager_id{{ $ticket->id }}');
                                    var managerSearchResults = document.getElementById('manager_search_results{{ $ticket->id }}');

                                    @if($ticket->manager_id and $ticket->manager)
                                        managerSearch.value = "{{ $ticket->manager->username }}";
                                    @endif

                                    managerSearch.addEventListener('input', function() {
                                        var query = this.value;
                                        if (query.length < 2) {
                                            managerSearchResults.innerHTML = '';
                                            if (query.length === 0) {
                                                managerIdInput.value = '';
                                            }
                                            return;
                                        }

                                        fetch('/search/users/autocomplete?query=' + encodeURIComponent(query))
                                            .then(function(response) { return response.json(); })
                                            .then(function(data) {
                                                managerSearchResults.innerHTML = '';
                                                if (data.length > 0) {
                                                    var ul = document.createElement('ul');
                                                    ul.className = 'list-group';

                                                    for (var i = 0; i < data.length; i++) {
                                                        var user = data[i];
                                                        var li = document.createElement('li');
                                                        li.className = 'list-group-item user-result';
                                                        li.textContent = user.username;

                                                        (function(user) {
                                                            li.addEventListener('click', function() {
                                                                managerSearch.value = user.username;
                                                                managerIdInput.value = user.id;
                                                                managerSearchResults.innerHTML = '';
                                                            });
                                                        })(user);

                                                        ul.appendChild(li);
                                                    }

                                                    managerSearchResults.appendChild(ul);
                                                }
                                            });
                                    });
                                });
                            </script>
                            <div class="alert alert-info mt-3">
                                <strong>@lang('tickets.modal_change_manager_headline')</strong>
                                <p>@lang('tickets.modal_change_manager_text')</p>
                                <p><small>@lang('tickets.modal_change_manager_example')</small></p>
                            </div>
                            {{ Form::hidden('action', 'change_manager') }}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('tickets.buttons_close')</button>
                            <button type="submit" class="btn btn-primary">@lang('tickets.buttons_save')</button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>

            <!-- Change User Modal -->
            <div class="modal fade" id="changeUserModal{{ $ticket->id }}" tabindex="-1" aria-labelledby="changeUserModalLabel{{ $ticket->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changeUserModalLabel{{ $ticket->id }}">@lang('tickets.change_user')</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        {{ Form::open(array('url'=>'/events/' . $ticket->event->slug . '/participants/' . $ticket->id, 'method'=>'POST')) }}
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="username_search">@lang('tickets.modal_change_user_search_user_label')</label>
                                <input type="text" id="username_search{{ $ticket->id }}" class="form-control username-search" placeholder="Type to search users..." autocomplete="off">
                                <input type="hidden" name="user_id" id="user_id{{ $ticket->id }}" value="{{ $ticket->user_id }}">
                                <div id="search_results{{ $ticket->id }}" class="search-results"></div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var usernameSearch = document.getElementById('username_search{{ $ticket->id }}');
                                    var userIdInput = document.getElementById('user_id{{ $ticket->id }}');
                                    var searchResults = document.getElementById('search_results{{ $ticket->id }}');

                                    @if($ticket->user_id and $ticket->user)
                                        usernameSearch.value = "{{ $ticket->user->username }}";
                                    @endif

                                    usernameSearch.addEventListener('input', function() {
                                        var query = this.value;
                                        if (query.length < 2) {
                                            searchResults.innerHTML = '';
                                            return;
                                        }

                                        fetch('/search/users/autocomplete?query=' + encodeURIComponent(query))
                                            .then(function(response) { return response.json(); })
                                            .then(function(data) {
                                                searchResults.innerHTML = '';
                                                if (data.length > 0) {
                                                    var ul = document.createElement('ul');
                                                    ul.className = 'list-group';

                                                    for (var i = 0; i < data.length; i++) {
                                                        var user = data[i];
                                                        var li = document.createElement('li');
                                                        li.className = 'list-group-item user-result';
                                                        li.textContent = user.username;

                                                        (function(user) {
                                                            li.addEventListener('click', function() {
                                                                usernameSearch.value = user.username;
                                                                userIdInput.value = user.id;
                                                                searchResults.innerHTML = '';
                                                            });
                                                        })(user);

                                                        ul.appendChild(li);
                                                    }

                                                    searchResults.appendChild(ul);
                                                }
                                            });
                                    });
                                });
                            </script>
                            <div class="alert alert-info mt-3">
                                <strong>@lang('tickets.modal_change_user_headline')</strong>
                                <p>@lang('tickets.modal_change_user_text')</p>
                                <p><small>@lang('tickets.modal_change_user_example')</small></p>
                            </div>
                            {{ Form::hidden('action', 'change_user') }}
                            {{ Form::hidden('_token', csrf_token()) }}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('tickets.buttons_close')</button>
                            <button type="submit" class="btn btn-primary">@lang('tickets.buttons_save')</button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
		</div>
	</div>
	<div class="card-footer d-flex justify-content-between align-items-center">
		<div>
			<a href="/events/participants/{{ $ticket->id }}/pdf" class="btn btn-primary">
				<i class="fas fa-file-pdf me-1"></i> @lang('tickets.download_pdf')
			</a>
            <a href="/audits/ticket/{{ $ticket->id }}" class="btn btn-outline-secondary">
                <i class="fas fa-history me-1"></i>@lang('tickets.audit_log')
            </a>
		</div>
		<div class="text-muted small">
			<i class="fas fa-info-circle me-1"></i> @lang('tickets.ticket_id'): {{ $ticket->id }}
		</div>
	</div>
</div>

@include ('layouts._partials._gifts.modal')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>