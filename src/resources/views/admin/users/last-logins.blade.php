@extends ('layouts.admin-default')

@section ('page_title', 'Last Logins')

@section ('content')

<div class="row">
    <div class="col-lg-12">
        <h3 class="pb-2 mt-4 mb-4 border-bottom">Last Logins</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/admin">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="/admin/users">Users</a>
            </li>
            <li class="breadcrumb-item active">
                Last Logins
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">

        <div class="card mb-3">
            <div class="card-header">
                <i class="fa fa-sign-in fa-fw"></i> Last Logins

                <div class="row mt-2">
                    <div class="col-sm-4 ms-auto">
                        {{ Form::open(['url' => '/admin/users/last-logins', 'method' => 'GET']) }}
                        {{ Form::hidden('sort_by', $sortBy) }}
                        {{ Form::hidden('sort_dir', $sortDir) }}
                        <div class="input-group">
                            {{ Form::text('search', $search, ['id' => 'search', 'class' => 'form-control', 'placeholder' => 'Search by username...']) }}
                            {{ Form::submit('Search', ['class' => 'btn btn-primary btn-sm']) }}
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover table-responsive">
                    <thead>
                        <tr>
                            <th>
                                @php
                                    $usernameSortDir = ($sortBy === 'username' && $sortDir === 'asc') ? 'desc' : 'asc';
                                @endphp
                                <a href="/admin/users/last-logins?sort_by=username&sort_dir={{ $usernameSortDir }}&search={{ urlencode($search) }}">
                                    Username
                                    @if ($sortBy === 'username')
                                        <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'asc' : 'desc' }}"></i>
                                    @else
                                        <i class="fa fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                @php
                                    $loginSortDir = ($sortBy === 'last_login' && $sortDir === 'asc') ? 'desc' : 'asc';
                                @endphp
                                <a href="/admin/users/last-logins?sort_by=last_login&sort_dir={{ $loginSortDir }}&search={{ urlencode($search) }}">
                                    Last Login
                                    @if ($sortBy === 'last_login')
                                        <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'asc' : 'desc' }}"></i>
                                    @else
                                        <i class="fa fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>
                                <a href="/admin/users/{{ $user->id }}">{{ $user->username }}</a>
                            </td>
                            <td>{{ $user->last_login }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $users->links() }}
            </div>
        </div>

    </div>
</div>

@endsection
