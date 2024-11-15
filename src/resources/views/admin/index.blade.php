@extends ('layouts.admin-default')

@section('page_title', 'Admin')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <h3 class="pb-2 mt-4 mb-4 border-bottom">Dashboard</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">
                    <i class="fa fa-dashboard"></i> Dashboard
                </li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card mb-3">
                <div class="card-header text-bg-info">
                    <div class="row">
                        <div class="col-3">
                            <i class="fas fa-comments fa-5x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <div class="huge">{{ $comments->count() }}</div>
                            <div>New Comments!</div>
                        </div>
                    </div>
                </div>
				<a href="/admin/news">
                <div class="card-footer">

                        <span class="float-start">View Details</span>
                        <span class="float-end"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                </div>
			</a>

            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card mb-3">
                <div class="card-header text-bg-success">
                    <div class="row">
                        <div class="col-3">
                            <i class="fa fa-tasks fa-5x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <div class="huge">{{ $votes->count() }}</div>
                            <div>New Votes!</div>
                        </div>
                    </div>
                </div>
                <a href="/admin/polls">
                    <div class="card-footer">
                        <span class="float-start">View Details</span>
                        <span class="float-end"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card mb-3">
                <div class="card-header text-bg-warning">
                    <div class="row">
                        <div class="col-3">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <div class="huge">{{ $orders->count() }}</div>
                            <div>New Orders!</div>
                        </div>
                    </div>
                </div>
                <a href="/admin/purchases">
                    <div class="card-footer">
                        <span class="float-start">View Details</span>
                        <span class="float-end"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card mb-3">
                <div class="card-header text-bg-danger">
                    <div class="row">
                        <div class="col-3">
                            <i class="fa fa-support fa-5x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <div class="huge">{{ $participants->count() }}</div>
                            <div>New Participants!</div>
                        </div>
                    </div>
                </div>
                <a href="/admin/events">
                    <div class="card-footer">
                        <span class="float-start">View Details</span>
                        <span class="float-end"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-12 d-none">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-ticket fa-fw"></i> Ticket Sales Per Month
                </div>
                <div class="card-body">
                    <div id="ticket-breakdown"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-12 d-none mb-3">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-credit-card fa-fw"></i> Orders Per Month
                </div>
                <div class="card-body">
                    <div id="orders-breakdown"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-user fa-fw"></i> Users
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @if ($userLastLoggedIn)
                            <li class="list-group-item list-group-item-info"><strong>Last Logged In: <span
                                        class="float-end">{{ $userLastLoggedIn->username }} on
                                        {{ $userLastLoggedIn->last_login }}</span></strong></li>
                        @endif
                        <li class="list-group-item list-group-item-info"><strong>No. of Users: <span
                                    class="float-end">{{ $userCount }}</span></strong></li>
                        <li class="list-group-item list-group-item-info"><strong>No. of Active Login Methods: <span
                                    class="float-end">{{ count($activeLoginMethods) }}</span></strong></li>
                        @foreach ($supportedLoginMethods as $method)
                            <li
                                class="list-group-item @if (in_array($method, $activeLoginMethods)) list-group-item-success @else list-group-item-danger @endif">
                                <strong>No. of {{ ucwords(str_replace('-', ' ', str_replace('_', ' ', $method))) }}
                                    Accounts: <span class="float-end">{{ $userLoginMethodCount[$method] }}</span></strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-book fa-fw"></i> Events
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-info"><strong>Next Event: <span
                                    class="float-end">{{ $nextEvent }}</span></strong></li>
                        <li class="list-group-item list-group-item-info"><strong>No of Events: <span
                                    class="float-end">{{ $events->count() }}</span></strong></li>
                        <li class="list-group-item list-group-item-info"><strong>No of Attendees: <span
                                    class="float-end">{{ $participantCount }}</span></strong></li>
                        <li class="list-group-item list-group-item-info"><strong>No of Tournaments: <span
                                    class="float-end">{{ $tournamentCount }}</span></strong></li>
                        <li class="list-group-item list-group-item-info"><strong>No of Tournament Participants: <span
                                    class="float-end">{{ $tournamentParticipantCount }}</span></strong></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-list fa-fw"></i> Active Polls
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @if ($activePolls->count() > 0)
                            @foreach ($activePolls as $poll)
                                <li class="list-group-item list-group-item-info"><strong>{{ $poll->name }}: <span
                                            class="float-end">{{ $poll->getTotalVotes() }}</span></strong></li>
                            @endforeach
                        @else
                            <li class="list-group-item list-group-item-info"><strong>Nothing to see here...</strong></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-wrench fa-fw"></i> Features
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li
                            class="list-group-item @if ($shopEnabled) list-group-item-success @else list-group-item-danger @endif">
                            <strong>Shop: <span class="float-end">
                                    @if ($shopEnabled)
                                        Enabled
                                    @else
                                        Disabled
                                    @endif
                                </span></strong></li>
                        <li
                            class="list-group-item @if ($creditEnabled) list-group-item-success @else list-group-item-danger @endif">
                            <strong>Credit: <span class="float-end">
                                    @if ($creditEnabled)
                                        Enabled
                                    @else
                                        Disabled
                                    @endif
                                </span></strong></li>
                        <li
                            class="list-group-item @if ($helpEnabled) list-group-item-success @else list-group-item-danger @endif">
                            <strong>Help: <span class="float-end">
                                    @if ($helpEnabled)
                                        Enabled
                                    @else
                                        Disabled
                                    @endif
                                </span></strong></li>
                        <li
                            class="list-group-item @if ($galleryEnabled) list-group-item-success @else list-group-item-danger @endif">
                            <strong>Gallery: <span class="float-end">
                                    @if ($galleryEnabled)
                                        Enabled
                                    @else
                                        Disabled
                                    @endif
                                </span></strong></li>
                        @foreach ($supportedLoginMethods as $method)
                            <li
                                class="list-group-item @if (in_array($method, $activeLoginMethods)) list-group-item-success @else list-group-item-danger @endif">
                                <strong>{{ ucwords(str_replace('-', ' ', str_replace('_', ' ', $method))) }} Login:
                                    <span class="float-end">
                                        @if (in_array($method, $activeLoginMethods))
                                            Enabled
                                        @else
                                            Disabled
                                        @endif
                                    </span></strong></li>
                        @endforeach

                        @foreach ($supportedPaymentGateways as $gateway)
                            <li
                                class="list-group-item @if (in_array($gateway, $activePaymentGateways)) list-group-item-success @else list-group-item-danger @endif">
                                <strong>{{ ucwords(str_replace('-', ' ', str_replace('_', ' ', $gateway))) }} Payment
                                    Gateway: <span class="float-end">
                                        @if (in_array($gateway, $activePaymentGateways))
                                            Enabled
                                        @else
                                            Disabled
                                        @endif
                                    </span></strong></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-info fa-fw"></i> Version
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-info"><strong>BUILDNUMBER: <span
                                    class="float-end">{{ env('BUILDNUMBER', 'dev') }}</span></strong></li>
                        <li class="list-group-item list-group-item-info"><strong>BUILDID: <span
                                    class="float-end">{{ env('BUILDID', 'dev') }}</span></strong></li>
                        <li class="list-group-item list-group-item-info"><strong>SOURCE_COMMIT: <span
                                    class="float-end">{{ env('SOURCE_COMMIT', 'dev') }}</span></strong></li>
                        <li class="list-group-item list-group-item-info"><strong>SOURCE_REF: <span
                                    class="float-end">{{ env('SOURCE_REF', 'dev') }}</span></strong></li>
                        <li class="list-group-item list-group-item-info"><strong>BUILDNODE: <span
                                    class="float-end">{{ env('BUILDNODE', 'dev') }}</span></strong></li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    {{-- TODO: Replace Morris --}}
    {{-- <script>
        Morris.Bar({
            element: 'ticket-breakdown',
            data: [
                @foreach ($ticketBreakdown as $key => $month)
                    {
                        month: '{{ $key }}',
                        value: {{ count($month) }}
                    },
                @endforeach
            ],
            // The name of the data record attribute that contains x-values.
            xkey: 'month',
            // A list of names of data record attributes that contain y-values.
            ykeys: ['value'],
            // Labels for the ykeys -- will be displayed when you hover over the
            // chart.
            labels: ['Number of Tickets']
        });
        Morris.Bar({
            element: 'orders-breakdown',
            data: [
                @foreach ($orderBreakdown as $key => $month)
                    {
                        month: '{{ $key }}',
                        value: {{ count($month) }}
                    },
                @endforeach
            ],
            // The name of the data record attribute that contains x-values.
            xkey: 'month',
            // A list of names of data record attributes that contain y-values.
            ykeys: ['value'],
            // Labels for the ykeys -- will be displayed when you hover over the
            // chart.
            labels: ['Number of Orders']
        });
    </script> --}}

@endsection
