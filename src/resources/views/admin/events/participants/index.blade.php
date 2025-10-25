@extends ('layouts.admin-default')

@section ('page_title', 'Participants - ' . $event->display_name . ' | ' . Settings::getOrgName() . ' Admin')

@section ('content')

<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Participants</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="/admin/events/">Events</a>
			</li>
			<li class="breadcrumb-item">
				<a href="/admin/events/{{ $event->slug }}">{{ $event->display_name }}</a>
			</li>
			<li class="breadcrumb-item active">
				Participants
			</li>
		</ol>
	</div>
</div>

    @include ('layouts._partials._admin._event.dashMini')

<div class="row">
	<div class="col-lg-12">

		<div class="card mb-3">
            <div class="card-header">
                <div class="d-flex flex-column flex-xl-row align-items-md-center gap-3">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-users fa-fw me-2"></i>
                        <span>All Participants (Tickets)</span>
                    </div>

                    {{ Form::open(['url'=>'/admin/events/' . $event->slug . '/participants', 'method'=>'GET',
                    'class'=>'d-inline-block me-md-2']) }}
                    <div class="input-group input-group-sm gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Search by username..."
                               value="{{ request('search') }}">
                        @if(request()->has('payment'))
                        <input type="hidden" name="payment" value="{{ request('payment') }}">
                        @endif
                        @if(request()->has('signed_in'))
                        <input type="hidden" name="signed_in" value="{{ request('signed_in') }}">
                        @endif
                        @if(request()->has('page'))
                        <input type="hidden" name="page" value="{{ request('page') }}">
                        @endif
                        <button type="submit" class="btn btn-secondary btn-sm">Search</button>
                    </div>
                    {{ Form::close() }}
                    <div class="ms-md-auto gap-2 d-flex flex-column flex-xl-row">
                        {{ Form::open(['url'=>'/admin/events/' . $event->slug . '/participants', 'method'=>'GET',
                        'class'=>'d-inline-block me-md-2']) }}
                        <div class="d-flex gap-2 align-items-center">
                            <label for="signed_in_filter" class="me-2">Sign-In Status:</label>
                            <select id="signed_in_filter" name="signed_in" class="form-select form-select-sm w-auto">
                                <option value="any" {{ request(
                                'signed_in', 'any') == 'All' || request('signed_in') === null ? 'selected' : ''
                                }}>Any</option>
                                <option value="yes" {{ request(
                                'signed_in') == 'yes' ? 'selected' : '' }}>Signed In</option>
                                <option value="no" {{ request(
                                'signed_in') == 'no' ? 'selected' : '' }}>Not Signed In</option>
                            </select>
                            @if(request()->has('payment'))
                            <input type="hidden" name="payment" value="{{ request('payment') }}">
                            @endif
                            @if(request()->has('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if(request()->has('page'))
                            <input type="hidden" name="page" value="{{ request('page') }}">
                            @endif
                            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                        </div>
                        {{ Form::close() }}

                        {{ Form::open(['url'=>'/admin/events/' . $event->slug . '/participants', 'method'=>'GET',
                        'class'=>'d-inline-block me-md-2']) }}
                        <div class="d-flex gap-2">
                            <label for="payment_filter" class="me-2">Payment Status:</label>
                            <select id="payment_filter" name="payment" class="form-select form-select-sm w-auto">
                                <option value="">-</option>
                                <option value="success" {{ request(
                                'payment') == 'success' ? 'selected' : '' }}>Paid</option>
                                <option value="free" {{ request(
                                'payment') == 'free' ? 'selected' : '' }}>Free/Staff/Gift</option>
                                <option value="unpaid" {{ request(
                                'payment') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            </select>
                            @if(request()->has('signed_in'))
                            <input type="hidden" name="signed_in" value="{{ request('signed_in') }}">
                            @endif
                            @if(request()->has('page'))
                            <input type="hidden" name="page" value="{{ request('page') }}">
                            @endif
                            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                        </div>
                        {{ Form::close() }}

                        {{ Form::open(['url'=>'/admin/events/' . $event->slug . '/participants/signoutall', 'onsubmit'
                        => 'return ConfirmSignOutAll()', 'class'=>'d-inline-block me-md-2']) }}
                        {{ Form::hidden('_method', 'GET') }}
                        <button type="submit" class="btn btn-danger btn-sm">Sign Out all Participants</button>
                        {{ Form::close() }}

                        <a href="/admin/events/{{ $event->slug }}/tickets#freebies"
                           class="btn btn-info btn-sm">Freebies</a>
                    </div>
                </div>
            </div>
			<div class="card-body">
				<div class="dataTable_wrapper table-responsive">
					<table class="table table-striped table-hover participants-table" id="seating_table">
						<thead>
							<tr>
								<th>User</th>
								<th>Name</th>
                                <th class="d-none d-md-table-cell">Contact</th>
                                <th class="d-none d-md-table-cell">Seat</th>
								<th class="d-none d-md-table-cell">Ticket Type</th>
								<th class="d-none d-md-table-cell">Paypal Email</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($participants as $participant)
                            <tr @class([
                            "odd", "gradeX", "table-danger revoked" => $participant->revoked])>
                            <td>
                                <a href="/admin/events/{{ $event->slug }}/participants/{{ $participant->id }}"
                                   class="d-md-none d-block text-decoration-none text-dark">
									{{ $participant->user->username }}
									@if ($participant->user->steamid)
									<br><span class="text-muted"><small>Steam: {{ $participant->user->steamname }}</small></span>
									@endif
								</a>
								<span class="d-none d-md-inline">
									{{ $participant->user->username }}
									@if ($participant->user->steamid)
									<br><span class="text-muted"><small>Steam: {{ $participant->user->steamname }}</small></span>
									@endif
								</span>
                            </td>
								<td>{{ $participant->user->firstname }} {{ $participant->user->surname }}</td>
								<td class="d-none d-md-table-cell">{{ $participant->user->email }}
                                    @if (isset($participant->user->phonenumber) && !empty($participant->user->phonenumber))
                                    <br /><a href="tel:{{ $participant->user->phonenumber }}">{{ $participant->user->phonenumber }}</a>
                                    @endif
                                </td>
								<td class="d-none d-md-table-cell">
									@if (isset($participant->seat)) {{ $participant->seat->getName() }} @endif
								</td>
								<td class="d-none d-md-table-cell">
									@if ($participant->free) Free @endif
									@if ($participant->staff) Staff @endif
									@if ($participant->ticketType) {{ $participant->ticketType->name }} @endif
								</td>
								<td class="d-none d-md-table-cell">
									@if ($participant->purchase) {{ $participant->purchase->paypal_email }} @endif
								</td>
								<td >
									@if ($participant->free)
									<strong>Free</strong>
									<small>assigned by: {{ $participant->getAssignedByUser()->username }}</small>
									@elseif ($participant->staff)
									<strong>Staff</strong>
									<small>assigned by: {{ $participant->getAssignedByUser()->username }}</small>
									@elseif ($participant->gift)
									<strong>Gift</strong>
									<small>assigned by: {{ $participant->getGiftedByUser()->username }}</small>
									@elseif ($participant->purchase()->exists())
                                    @if ($participant->purchase->status == \App\Purchase::STATUS_SUCCESS)
									<strong>Paid</strong>
                                    @else
                                    <strong>Not Paid</strong>
                                    @endif
                                    @if($participant->purchase->user_id != $participant->user_id)
                                    <small>by: {{ $participant->owner->username }}</small>
                                    @endif
									@else
									<strong>No</strong>
                                    @endif
								</td>
                                <td>
                                    <a href="/admin/events/{{ $event->slug }}/participants/{{ $participant->id }}">
                                        <button type="button" class="btn btn-primary btn-sm btn-block mb-2">View
                                        </button>
                                    </a>
                                    @if (!$participant->revoked)
                                    @if(!$participant->signed_in)
                                    {{ Form::open(array('url'=>'/admin/events/' . $event->slug . '/participants/' .
                                    $participant->id . '/signin')) }}
                                    <a href="/admin/events/{{ $event->slug }}/participants/{{ $participant->id}}/signin">
                                        <button type="submit"
                                                class="btn btn-success btn-sm float-right mr-3 ml-3 btn-block mb-2">Sign-In
                                        </button>
                                    </a>
                                    {{ Form::close() }}
                                    @else
                                    <a href="/admin/events/{{ $event->slug }}/participants/{{ $participant->id}}/signout/">
                                        <button type="submit"
                                                class="btn btn-danger btn-sm float-right mr-3 ml-3 btn-block mb-2">Sign-Out
                                        </button>
                                    </a>
                                    @endif
                                    @endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
                    {{ $participants->appends(['payment' => request('payment'), 'search' => request('search'), 'signed_in' => request('signed_in', 'any')])->links() }}
                </div>
			</div>
		</div>

	</div>
</div>

<script>
	function ConfirmSignOutAll() {
		var x = confirm("do you really want to sign out all participants? This cannot be undone!");
		return x;
	}
</script>

@endsection