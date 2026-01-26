@extends ('layouts.admin-default')

@section ('page_title', 'Purchases')

@section ('content')
<div class="row">
	<div class="col-lg-12">
		<h3 class="pb-2 mt-4 mb-4 border-bottom">Purchases</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item active">
				Purchases
			</li>
		</ol>
	</div>
</div>

<div class="row">
	<div class="col-12 col-sm-12">
		<div class="card mb-3">
			<div class="card-header">
				<i class="fa fa-credit-card fa-fw"></i> Purchases
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-12 col-md-3">
						<div class="card border-secondary">
							<a href="/admin/purchases">
								<div class="card-footer">
									<span class="float-start">All Purchases</span>
									<span class="float-end"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-12 col-md-3">
						<div class="card border-secondary">
							<a href="/admin/purchases/shop">
								<div class="card-footer">
									<span class="float-start">Shop Purchases</span>
									<span class="float-end"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-12 col-md-3">
						<div class="card border-secondary">
							<a href="/admin/purchases/event">
								<div class="card-footer">
									<span class="float-start">Event Purchases</span>
									<span class="float-end"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-12 col-md-3">
						<div class="card border-secondary">
							<a href="/admin/purchases/revoked">
								<div class="card-footer">
									<span class="float-start">Purchases with revoked Participants</span>
									<span class="float-end"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
				</div>
				<table width="100%" class="table table-striped table-hover">
					<thead>
						<tr>
							<th>ID</th>
							<th>User</th>
							<th>Name</th>
							<th>Status</th>
							<th>Type</th>
							<th>Paypal Email</th>
							<th>Transaction ID</th>
							<th>Created At</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($purchases as $purchase)
							@php
								$statusColor = 'warning';
								if ($purchase->status == \App\Purchase::STATUS_SUCCESS) {
									$statusColor = 'success';
								}elseif($purchase->status == \App\Purchase::STATUS_DANGER) {
									$statusColor = 'danger';
								}
                                $participantRevoked = false;
                                if (!empty($purchase->participants)) {
                                    foreach ($purchase->participants as $participant) {
                                        if ($participant->revoked) {
                                            $participantRevoked = true;
                                            break;
                                        }
                                    }
                                }
							@endphp
							<tr class="table-{{ $statusColor }} text-{{ $statusColor }}">
								<td>{{ $purchase->id }}</td>
								<td>
									@if(isset($purchase->user)) 
										{{ $purchase->user->username }}
										@if ($purchase->user->steamid)
											- <span class="text-muted"><small>Steam: {{ $purchase->user->steamname }}</small></span>
										@endif
									@else
										User deleted
									@endif
								</td>
								<td>@if(isset($purchase->user)) {{ $purchase->user->firstname }} {{ $purchase->user->surname }} @else User deleted @endif</td>
								<td>
									{{ $purchase->status }}
									@if ($participantRevoked)
										<span class="badge text-bg-warning">Participant(s) revoked</span>
									@endif
								</td>
								<td>{{ $purchase->type }}</td>
								<td>{{ $purchase->paypal_email }}</td>
								<td>{{ $purchase->transaction_id }}</td>
								<td>{{ $purchase->created_at }}</td>
								<td>
									<a href="/admin/purchases/{{ $purchase->id }}">
										<button class="btn btn-block btn-success btn-sm">
											View
										</button>
									</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				{{ $purchases->links() }}
			</div>
		</div>
	</div>
</div>

@endsection