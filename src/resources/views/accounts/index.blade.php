@extends ('layouts.default')

@section('page_title', __('accounts.accounts_title'))

@section('content')

    <div class="container pt-1">

        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
        <div class="pb-2 mt-4 mb-4 border-bottom">
            <h1>
                @lang('accounts.my_account')
            </h1>
        </div>
        @if (session('newtoken'))
            <div class="alert alert-success">
                @lang('accounts.new_token')
                <br>
                <br>
                {{ session('newtoken') }}
            </div>
        @endif
        <div class="row">
            <!-- ACCOUNT DETAILS -->
            <div class="col-12  col-lg-12 mt-3 mb-3">
                <div class="card mb-3">
                    <div class="card-header ">
                        <h3 class="card-title">@lang('accounts.account_details')</h3>
                    </div>
                    <div class="card-body">
                        {{ Form::open(['url' => '/account/']) }}
                        <div class="row" style="display: flex; align-items: center;">
                            <div class="col-md-7 col-sm-12">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="mb-3 @error('firstname') is-invalid @enderror">
                                            {{ Form::label('firstname', __('accounts.firstname'), ['id' => '', 'class' => '']) }}
                                            <input id="firstname" type="firstname" class="form-control" name="firstname"
                                                value="{{ $user->firstname }}" required autocomplete="firstname">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="mb-3  @error('surname') is-invalid @enderror">
                                            {{ Form::label('surname', __('accounts.surname'), ['id' => '', 'class' => '']) }}
                                            <input id="surname" type="surname" class="form-control" name="surname"
                                                value="{{ $user->surname }}" required autocomplete="surname">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    {{ Form::label('Username', __('accounts.username'), ['id' => '', 'class' => '']) }}
                                    {{ Form::text('name', $user->username, ['id' => 'name', 'class' => 'form-control', 'disabled' => 'disabled']) }}
                                </div>
                                @if ($user->steamid && $user->steamname)
                                    <div class="mb-3">
                                        {{ Form::label('steamname', __('accounts.steamname'), ['id' => '', 'class' => '']) }}
                                        {{ Form::text('steamname', $user->steamname, ['id' => 'steamname', 'class' => 'form-control', 'disabled' => 'true']) }}
                                    </div>
                                @endif
                                @if ($user->password)
                                    <div class="mb-3">
                                        <label for="password1">@lang('accounts.change_password')</label>
                                        <input type="password" name="password1"
                                            class="form-control @error('password1') is-invalid @enderror" id="password1"
                                            placeholder="@lang('accounts.password')">
                                        @error('password1')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="password2">@lang('accounts.confirm_password')</label>
                                        <input type="password" name="password2"
                                            class="form-control @error('password2') is-invalid @enderror" id="password2"
                                            placeholder="@lang('accounts.password')">
                                        @error('password2')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    @if (Settings::isUserLocaleEnabled())
                                        <div class="col-12 col-md-6">
                                            <div class="mb-3 @error('locale') is-invalid @enderror">
                                                {{ Form::label('locale', __('accounts.locale'), ['class' => '']) }}
                                                <select name="locale" id="locale"
                                                    class="form-control @error('locale') is-invalid @enderror" required
                                                    autocomplete="locale">
                                                    @foreach (Helpers::getSupportedLocales() as $locale => $language)
                                                        <option value="{{ $language }}"
                                                            {{ $user->locale == $language ? 'selected' : '' }}>
                                                            {{ $language }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @error('locale')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif


                                @endif
                                <button type="submit" class="btn btn-primary btn-block">@lang('accounts.submit')</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <!-- Email -->
                <div class="card mb-3">
                    <div class="card-header ">
                        <h3 class="card-title">@lang('accounts.contactdetails')</h3>
                    </div>
                    <div class="card-body">
                        {{ Form::open(['url' => '/account/email']) }}
                        <div class="row" style="display: flex; align-items: center;">
                            <div class="col-md-2 col-sm-12">

                            </div>
                            <div class="col-md-10 col-sm-12">
                                <div class="mb-3">
                                    {{ Form::label('email', __('accounts.email'), ['id' => '', 'class' => '']) }}
                                    <input type="email" class="form-control" name="email"
                                        id="email @error('email') is-invalid @enderror" aria-describedby="email"
                                        value="{{ $user->email }}" @if (!Settings::isAuthAllowEmailChangeEnabled()) readonly @endif
                                        placeholder="@lang('accounts.email')">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                @if (Settings::isAuthRequirePhonenumberEnabled())
                                    <div class="mb-3">
                                        {{ Form::label('phonenumber', __('accounts.phonenumber'), ['id' => '', 'class' => '']) }}
                                        <input type="phonenumber" class="form-control" name="phonenumber"
                                            id="phonenumber @error('phonenumber') is-invalid @enderror"
                                            aria-describedby="phonenumber" value="{{ $user->phonenumber }}"
                                            placeholder="@lang('accounts.phonenumber')">
                                        @error('phonenumber')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                @endif

                                @if (!$user->email || Settings::isAuthAllowEmailChangeEnabled() || Settings::isAuthRequirePhonenumberEnabled())
                                    <button type="submit" class="btn btn-primary btn-block">@lang('accounts.submit')</button>
                                @endif
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>

                <!-- Avatar Settings -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">@lang('accounts.avatarsettings')</h3>
                    </div>
                    <div class="card-body">
                        <p>@lang('accounts.avatardescription')</p>
                        <div class="btn-group mb-3" role="group" aria-label="avatar selector">
                            {{ Form::open(['url' => '/account/avatar/selected']) }}
                            @if ($user->steamid && $user->steamname)
                                <input type="radio" class="btn-check" name="selected_avatar" id="selected_avatar1"
                                    value="steam" autocomplete="off" onclick="this.form.submit()">
                                <label class="btn btn-outline-primary" for="selected_avatar1">@lang('accounts.avatarsteam')</label>
                            @endif
                            <input type="radio" class="btn-check" name="selected_avatar" id="selected_avatar2"
                                value="local" autocomplete="off" onclick="this.form.submit()">
                            <label class="btn btn-outline-primary" for="selected_avatar2">@lang('accounts.avatarlocal')</label>
                            {{ Form::close() }}
                        </div>
                        <div class="row" style="display: flex; align-items: center;">
                            <div class="col-md-4 col-sm-12">
                                <!-- Avatar display area -->
                                <img src="{{ $user->avatar }}" alt="{{ $user->username }}'s Avatar"
                                    class="img-fluid img-thumbnail mb-3" />
                                {{ Form::open(['url' => '/account/avatar', 'files' => 'true']) }}

                                <!-- File upload for Custom Avatar -->
                                <div class="mb-3 custom-avatar-upload">
                                    {{ Form::label('custom_image', 'Select Images', ['id' => '', 'class' => '']) }}
                                    {{ Form::file('avatar', ['id' => 'avatar', 'class' => 'form-control']) }}
                                </div>

                                <button type="submit" class="btn btn-primary btn-block">@lang('accounts.submit')</button>

                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>

                <!-- creditlogs -->
                @if ($creditLogs)
                    <div class="card mb-3">
                        <div class="card-header ">
                            <h3 class="card-title">@lang('accounts.credit') - {{ $user->credit_total }}</h3>
                        </div>
                        <div class="card-body">
                            <table width="100%" class="table table-striped table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>@lang('accounts.credit_action')</th>
                                        <th>@lang('accounts.credit_amount')</th>
                                        <th>@lang('accounts.credit_item')</th>
                                        <th>@lang('accounts.credit_reason')</th>
                                        <th>@lang('accounts.credit_timestamp')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($creditLogs->reverse() as $creditLog)
                                        <tr class="table-row" class="odd gradeX">
                                            <td>{{ $creditLog->action }}</td>
                                            <td>{{ $creditLog->amount }}</td>
                                            <td>
                                                @if (strtolower($creditLog->action) == 'buy')
                                                    @if (!$creditLog->purchase->participants->isEmpty())
                                                        @foreach ($creditLog->purchase->participants as $participant)
                                                            {{ $participant->event->display_name }} -
                                                            {{ $participant->ticket->name }}
                                                            @if (!$loop->last)
                                                                <hr>
                                                            @endif
                                                        @endforeach
                                                    @elseif ($creditLog->purchase->order != null)
                                                        @foreach ($creditLog->purchase->order->items as $item)
                                                            @if ($item->item)
                                                                {{ $item->item->name }}
                                                            @endif
                                                            - x {{ $item->quantity }}
                                                            <br>
                                                            @if ($item->price != null)
                                                                {{ Settings::getCurrencySymbol() }}{{ $item->price * $item->quantity }}
                                                                @if ($item->price_credit != null && Settings::isCreditEnabled())
                                                                    /
                                                                @endif
                                                            @endif
                                                            @if ($item->price_credit != null && Settings::isCreditEnabled())
                                                                {{ $item->price_credit * $item->quantity }} Credits
                                                            @endif
                                                            @if (!$loop->last)
                                                                <hr>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $creditLog->reason }}</td>
                                            <td>
                                                {{ $creditLog->updated_at }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $creditLogs->links() }}
                        </div>
                    </div>
                @endif
            </div>


            <!-- TICKETS -->
            <div class="col-sm-12 col-12 col-md-6 col-lg-7 mt-3 mb-3">
                <div class="card mb-3">
                    <div class="card-header ">
                        <h3 class="card-title">@lang('accounts.tickets')</h3>
                    </div>
                    <div class="card-body">
                        @if (count($eventParticipants))
                            @foreach ($eventParticipants as $participant)
                                @include ('layouts._partials._tickets.index')
                            @endforeach
                            {{ $eventParticipants->links() }}
                        @else
                            @lang('accounts.no_tickets')
                        @endif
                    </div>
                </div>
            </div>

            <!-- PURCHASES -->
            <div class="col-sm-12 col-12 col-md-6 col-lg-5 mt-3 mb-3">
                <div class="card mb-3">
                    <div class="card-header ">
                        <h3 class="card-title">@lang('accounts.purchases')</h3>
                    </div>
                    <div class="card-body">
                        @if (count($user->purchases))
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            @lang('accounts.purchases_id')
                                        </th>
                                        <th>
                                            @lang('accounts.purchases_method')
                                        </th>
                                        <th>
                                            @lang('accounts.purchases_time')
                                        </th>
                                        <th>
                                            @lang('accounts.purchases_basket')
                                        </th>
                                        <th>
                                            @lang('accounts.purchases_total')
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchases as $purchase)
                                        <tr>
                                            <td>
                                                {{ $purchase->id }}
                                            </td>
                                            <td>
                                                {{ $purchase->getPurchaseType() }}
                                            </td>
                                            <td>
                                                {{ date('d-m-y H:i', strtotime($purchase->created_at)) }}
                                            </td>
                                            <td>
                                                @if (!$purchase->participants->isEmpty())
                                                    @foreach ($purchase->participants as $participant)
                                                        @if ($participant->free)
                                                            {{ $participant->event->display_name }} - Freebie
                                                        @elseif($participant->staff)
                                                            {{ $participant->event->display_name }} - Staff
                                                        @else
                                                            {{ $participant->event->display_name }} -
                                                            {{ $participant->ticket->name }}
                                                        @endif
                                                        @if (!$loop->last)
                                                            <hr>
                                                        @endif
                                                    @endforeach
                                                @elseif ($purchase->order != null)
                                                    @foreach ($purchase->order->items as $item)
                                                        @if ($item->item)
                                                            {{ $item->item->name }}
                                                        @endif
                                                        - x {{ $item->quantity }}
                                                        <br>
                                                        @if ($item->price != null)
                                                            {{ Settings::getCurrencySymbol() }}{{ $item->price * $item->quantity }}
                                                            @if ($item->price_credit != null && Settings::isCreditEnabled())
                                                                /
                                                            @endif
                                                        @endif
                                                        @if ($item->price_credit != null && Settings::isCreditEnabled())
                                                            {{ $item->price_credit * $item->quantity }} Credits
                                                        @endif
                                                        @if (!$loop->last)
                                                            <hr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if ($purchase->order != null)
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

                                                @if (!$purchase->participants->isEmpty())
                                                    @if ($purchase->getTotalTicketPrice() != 0)
                                                        {{ Settings::getCurrencySymbol() }}{{ number_format($purchase->getTotalTicketPrice(), 2) }}
                                                    @endif
                                                @endif



                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $purchases->links() }}
                        @else
                            @lang('accounts.no_purchases')
                        @endif
                        @if (Settings::isShopEnabled())
                            <a href="/shop/orders">
                                <button class="btn btn-success">@lang('accounts.show_shop_orders')</button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tokens -->
            <div class="col-12  col-lg-12">
                <div class="card mb-3">
                    <div class="card-header ">
                        <h3 class="card-title">@lang('accounts.tokens')</h3>
                    </div>
                    <div class="card-body">
                        @if (count($user->tokens))


                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            @lang('accounts.token_id')
                                        </th>
                                        <th>
                                            @lang('accounts.token_name')
                                        </th>
                                        <th>
                                            @lang('accounts.token_lastuse_date')
                                        </th>
                                        <th>
                                            @lang('accounts.token_creation_date')
                                        </th>
                                        <th>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user->tokens as $token)
                                        <tr>
                                            <td>
                                                {{ $token->id }}
                                            </td>
                                            <td>
                                                {{ $token->name }}
                                            </td>
                                            <td>
                                                @if ($token->last_used_at)
                                                    {{ date('d-m-y H:i', strtotime($token->last_used_at)) }}
                                                @else
                                                    @lang('accounts.token_never_used')
                                                @endif
                                            </td>
                                            <td>
                                                {{ date('d-m-y H:i', strtotime($token->created_at)) }}
                                            </td>
                                            <td>
                                                {{ Form::open(['url' => '/account/tokens/remove/' . $token->id, 'onsubmit' => 'return ConfirmDeleteToken()']) }}
                                                {{ Form::hidden('_method', 'DELETE') }}
                                                <button type="submit"
                                                    class="btn btn-danger btn-sm btn-block">@lang('accounts.token_remove_button')</button>
                                                {{ Form::close() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            @lang('accounts.no_tokens')
                        @endif

                        {{-- {{ Form::open(array('url'=>'/account/tokens/add' )) }}
					<div class="row mt-3" style="display: flex; align-items: center;">
						<div class="col-md-2 col-sm-12">

						</div>
						<div class="col-md-10 col-sm-12">
							<div class="mb-3">
								{{ Form::label('token_name',__('accounts.token_name'),array('id'=>'','class'=>'')) }}
								{{ Form::text('token_name', null ,array('id'=>'token_name','class'=>'form-control')) }}
							</div>



							<button type="submit" class="btn btn-primary btn-block">@lang('accounts.add_token')</button>
						</div>
					</div>
					{{ Form::close() }} --}}

                    </div>
                </div>
            </div>

            <!-- Single Sign-on -->
            <div class="col-sm-12 col-12 col-md-6 col-lg-7 mt-3 mb-3">
                <div class="card mb-3">
                    <div class="card-header ">
                        <h3 class="card-title">@lang('accounts.single_sign_on')</h3>
                    </div>
                    <div class="card-body">

                        @if (in_array('steam', Settings::getLoginMethods()))

                            @if (!$user->steamid && !$user->steamname)
                                <a href="/account/sso/add/steam" type="button" name="" value=""
                                    class="btn btn-success">@lang('accounts.add_steam_account')</a>
                            @else
                                <a href="/account/sso/remove/steam" type="button" name="" value=""
                                    class="btn btn-danger">@lang('accounts.remove_steam_account')</a>
                            @endif

                        @endif

                        <button type="button" name="" value=""
                            class="btn btn-danger d-none">@lang('accounts.add_second_steam_account')</button>
                        <button type="button" name="" value=""
                            class="btn btn-danger d-none">@lang('accounts.add_twitch_account')</button>
                        <button type="button" name="" value=""
                            class="btn btn-danger d-none">@lang('accounts.remove_twitch_account')</button>
                    </div>
                </div>
            </div>

            <!-- DANGER ZONE -->
            <div class="col-sm-12 col-12 col-md-6 col-lg-5 mt-3 mb-3">
                <div class="card mb-3">
                    <div class="card-header ">
                        <h3 class="card-title">@lang('accounts.danger_zone')</h3>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal">@lang('accounts.remove_account')</button>
                    </div>
                </div>
            </div>
        </div>
        @include ('layouts._partials._gifts.modal')
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="confirmDeleteModalLabel">@lang('accounts.confirm_remove_account')</h4>
                    <button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal"
                        aria-hidden="true"></button>
                </div>
                {{ Form::open(['url' => '/account/delete/', 'id' => 'confirmDeleteFormModal']) }}
                <div class="modal-body">
                    <div class="mb-3">
                        <p>@lang('accounts.remove_account_line1')</p>
                        <p><strong>@lang('accounts.remove_account_all')</strong> @lang('accounts.remove_account_line2')</p>
                        <p><strong>@lang('accounts.remove_account_all')</strong> @lang('accounts.remove_account_line3')</p>
                        <p><strong>@lang('accounts.remove_account_all')</strong> @lang('accounts.remove_account_line4')</p>
                        <p>@lang('accounts.remove_account_line5_1') <strong>@lang('accounts.remove_account_not')</strong> @lang('accounts.remove_account_line5_1')</p>
                        <p>@lang('accounts.remove_account_line6_1') <strong>@lang('accounts.remove_account_not')</strong> @lang('accounts.remove_account_line6_2')</p>
                        <p><strong>@lang('accounts.remove_account_line7')</strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">@lang('accounts.remove_account_accept')Accept</button>
                    <button type="button" class="btn btn-danger"
                        data-bs-dismiss="modal">@lang('accounts.remove_account_cancel')Cancel</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <script>
        function ConfirmDeleteToken() {
            var x = confirm("@lang('accounts.token_remove_confirmation')");
            if (x)
                return true;
            else
                return false;
        }
    </script>


@endsection
