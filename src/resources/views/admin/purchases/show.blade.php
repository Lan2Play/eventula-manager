@extends ('layouts.admin-default')

@section('page_title', 'Purchase - ' . $purchase->id)

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="pb-2 mt-4 mb-4 border-bottom">Purchases</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admin/purchases/">Purchases</a>
                </li>
                <li class="breadcrumb-item active">
                    {{ $purchase->id }}
                </li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-8">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-credit-card fa-fw"></i> Items
                </div>
                <div class="card-body">
                    <table width="100%" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($purchase->getPurchaseContentType() == 'shopOrder')
                                @foreach ($purchase->order->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->item->name }}
                                        </td>
                                        <td>
                                            {{ $item->quantity }}
                                        </td>
                                        <td>
                                            @if ($item->price != null)
                                                {{ Settings::getCurrencySymbol() }}{{ $item->price }}
                                                @if ($item->price_credit != null && Settings::isCreditEnabled())
                                                    /
                                                @endif
                                            @endif
                                            @if ($item->price_credit != null && Settings::isCreditEnabled())
                                                {{ $item->price_credit }} Credits
                                            @endif
                                            Each |
                                            @if ($item->price != null)
                                                {{ Settings::getCurrencySymbol() }}{{ $item->price * $item->quantity }}
                                                @if ($item->price_credit != null && Settings::isCreditEnabled())
                                                    /
                                                @endif
                                            @endif
                                            @if ($item->price_credit != null && Settings::isCreditEnabled())
                                                {{ $item->price_credit * $item->quantity }} Credits
                                            @endif
                                            Total
                                        </td>
                                    </tr>
                                @endforeach
                            @elseif ($purchase->getPurchaseContentType() == 'eventTickets')
                                @foreach ($purchase->participants as $participant)
                                    <tr @class(['table-warning' => $participant->revoked])>
                                        <td>
                                            @if ($participant->ticket)
                                                {{ $participant->ticket->name }} for
                                                {{ $participant->event->display_name }}
                                                @if ($participant->revoked)
                                                    <span class="badge text-bg-warning">Participant revoked</span>
                                                @endif
                                            @endif
                                            @php
                                                $labels = [];
                                                if ($participant->free == 1) {
                                                    $labels[] = 'Freebie Ticket';
                                                }
                                                if ($participant->staff == 1) {
                                                    $labels[] = 'Staff Ticket';
                                                }
                                            @endphp
                                            @if (!empty($labels))
                                                {{ implode(', ', $labels) }} for
                                                {{ $participant->event->display_name }}
                                            @endif
                                        </td>

                                        <td>
                                            1
                                        </td>
                                        <td>
                                            @if ($participant->ticket)
                                                @if ($participant->ticket->price != null)
                                                    {{ Settings::getCurrencySymbol() }}{{ $participant->ticket->price }}
                                                    @if ($participant->ticket->price_credit != null && Settings::isCreditEnabled())
                                                        /
                                                    @endif
                                                @endif
                                                @if ($participant->ticket->price_credit != null && Settings::isCreditEnabled())
                                                    {{ $participant->ticket->price_credit }} Credits
                                                @endif
                                            @else
                                                0,- {{ Settings::getCurrencySymbol() }}
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <div class="text-end">
                        <strong style="font-weight: bold;">Total:</strong>
                        @if ($purchase->getPurchaseContentType() == 'shopOrder')
                            @if ($purchase->order->getTotalPrice() != 0)
                                {{ Settings::getCurrencySymbol() }}{{ number_format($purchase->order->getTotalPrice(), 2) }}
                                @if ($purchase->order->getTotalCreditPrice() != 0 && Settings::isCreditEnabled())
                                    /
                                @endif
                            @endif
                            @if ($purchase->order->getTotalCreditPrice() != 0 && Settings::isCreditEnabled())
                                {{ number_format($purchase->order->getTotalCreditPrice(), 2) }}
                                Credits
                            @endif
                        @endif

                        @if ($purchase->getPurchaseContentType() == 'eventTickets')
                            @if ($purchase->getTotalTicketPrice() != 0)
                                {{ Settings::getCurrencySymbol() }}{{ number_format($purchase->getTotalTicketPrice(), 2) }}
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-credit-card fa-fw"></i> Details
                </div>
                <div class="card-body">
                    <ul class="list-group">



                        <li class="list-group-item list-group-item-info"><strong>Purchase ID: <span
                                    class="float-end">{{ $purchase->id }}</span></strong></li>
                        <li
                            class="list-group-item @if (isset($purchase->user)) list-group-item-info @else list-group-item-danger @endif">
                            <strong>
                                @if (isset($purchase->user))
                                    User:
                                    <span class="float-end">
                                        {{ $purchase->user->username }}
                                        @if ($purchase->user->steamid)
                                            - <span class="text-muted"><small>Steam:
                                                    {{ $purchase->user->steamname }}</small></span>
                                        @endif
                                    </span>
                                @else
                                    User deleted
                                @endif
                            </strong>
                        </li>
                        <li
                            class="list-group-item @if (isset($purchase->user)) list-group-item-info @else list-group-item-danger @endif">
                            <strong>Name: <span class="float-end">
                                    @if (isset($purchase->user))
                                        {{ $purchase->user->firstname }} {{ $purchase->user->surname }}
                                    @else
                                        User deleted
                                    @endif
                                </span></strong></li>
                        <li
                            class="list-group-item @if (strtolower($purchase->status) != 'success' && strtolower($purchase->status) != 'pending') list-group-item-danger @endif @if (strtolower($purchase->status) == 'success') list-group-item-success @endif @if (strtolower($purchase->status) == 'pending') list-group-item-warning @endif">
                            <strong>Status: <span class="float-end">{{ $purchase->status }}</span></strong></li>
                        <li class="list-group-item list-group-item-info"><strong>Type: <span
                                    class="float-end">{{ $purchase->type }}</span></strong></li>
                        @if ($purchase->paypal_email != null)
                            <li class="list-group-item list-group-item-info">
                                <strong>Paypal Email: <span class="float-end">{{ $purchase->paypal_email }}</span></strong>
                            </li>
                        @endif
                        <li class="list-group-item list-group-item-info"><strong>Transaction ID: <span
                                    class="float-end">{{ $purchase->transaction_id }}</span></strong></li>
                        <li class="list-group-item list-group-item-info"><strong>Timestamp: <span
                                    class="float-end">{{ $purchase->created_at }}</span></strong></li>
                    </ul>
                    <div class="mb-3">
                    </div>
                    @if (strtolower($purchase->status) == 'pending')
                        <div class="mb-3">

                            {{ Form::open(['url' => '/admin/purchases/' . $purchase->id . '/setSuccess', 'onsubmit' => 'return ConfirmSubmit()']) }}
                            {{ Form::hidden('_method', 'GET') }}

                            <button type="submit"
                                class="btn btn-block @if (isset($purchase->user)) btn-success	@else btn-warning @endif">Mark
                                as payed</button>
                            @if (!isset($purchase->user))
                                <small>caution, the user is already deleted!</small>
                            @endif
                            {{ Form::close() }}
                        </div>
                    @endif
                    @if ($purchase->order)
                        <div class="mb-3">
                            <a href="/admin/orders/{{ $purchase->order->id }}"><button
                                    class="btn btn-block btn-success">View Order</button></a>
                        </div>
                    @endif

                    @if (count($purchase->participants) > 0)
                        <div class="mb-3">
                            @foreach ($purchase->participants as $participant)
                                <a
                                    href="/admin/events/{{ $participant->event->slug }}/participants/{{ $participant->id }}">
                                    <button @class([
                                        'btn',
                                        'btn-block',
                                        'btn-warning' => $participant->revoked,
                                        'btn-success' => !$participant->revoked,
                                    ])>
                                        View Participant - {{ $participant->user->username }}
                                        @if ($participant->revoked)
                                            (revoked)
                                        @endif
                                    </button></a>
                            @endforeach
                        </div>
                    @endif


                </div>
            </div>

            @if (config('admin.super_danger_zone'))
                <div class="card mb-3">
                    <div class="card-header">Super Danger Zone</div>
                    <div class="card-body">
                        {{ Form::open([
                            'url' => '/admin/purchases/' . $purchase->id,
                            'onSubmit' => 'return ConfirmDelete()',
                        ]) }}
                        {{ Form::hidden('_method', 'DELETE') }}
                        <div class="mb-3">
                            <div class="alert alert-danger">Deleting a purchase will also remove related data, like event
                                participants. This has the potential to break stuff!<br />Having a backup is highly
                                recommended!</div>
                            <button type="submit" class="btn btn-danger btn-block">Delete purchase</button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
