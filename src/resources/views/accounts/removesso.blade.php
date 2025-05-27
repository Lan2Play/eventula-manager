@extends ('layouts.default')

@section('page_title', __('accounts.accounts_title'))

@section('content')

    <div class="container pt-1">

        <div class="row">

            <div class="col-12  col-lg-12 mt-3 mb-3">
                <!-- Email -->
                <div class="card mb-3">
                    <div class="card-header ">
                        <h3 class="card-title">@lang('accounts.removesso'): {{ $method }}</h3>
                    </div>
                    <div class="card-body">
                        {{ Html::form('POST', "/account/sso/remove/$method/") }}
                        <div class="row" style="align-items: center;">
                            <div class="col ms-4 alert alert-warning">
                                @lang('accounts.removessowarning')
                            </div>
                        </div>
                        <div class="row mb-4" style="align-items: center;">
                            <div class="col">
                                <table class="table">
                                    <tr>
                                        <td>@lang('accounts.email')</td>
                                        @if ($user->email && $user->email != '')
                                            <td class="text-success">@lang('accounts.setted')</td>
                                        @else
                                            <td class="text-danger">@lang('accounts.notsetted')</td>
                                        @endif
                                    </tr>

                                    <tr>
                                        <td>@lang('accounts.password')</td>
                                        @if ($user->password && $user->password != '')
                                            <td class="text-success">@lang('accounts.setted')</td>
                                        @else
                                            <td class="text-danger">@lang('accounts.notsetted')</td>
                                        @endif
                                </table>
                            </div>
                        </div>
                        <div class="row" style="display: flex; align-items: center;">
                            <div class="col-md-2 col-sm-12">
                                @if ($user->email && $user->email != '')
                                    @if (Settings::isAuthAllowEmailChangeEnabled())
                                        <p class="text-success">@lang('accounts.ssochangeemailmessage')</p>
                                    @else
                                        <p class="text-success">@lang('accounts.ssodontemailmessage')</p>
                                    @endif
                                @else
                                    <p class="text-danger">@lang('accounts.ssoemailmessage')</p>
                                @endif

                            </div>
                            <div class="col-md-10 col-sm-12">

                                @if ($user->email)
                                    <div class="mb-3">
                                        {!! Html::label(__('accounts.email'), 'email') !!}
                                        @if (Settings::isAuthAllowEmailChangeEnabled())
                                            <input type="email" class="form-control" name="email"
                                                id="email @error('email') is-invalid @enderror" aria-describedby="email"
                                                value="{{ $user->email }}" placeholder="@lang('accounts.email')">
                                        @else
                                            <input type="email" class="form-control" name="emails"
                                                id="emails @error('email') is-invalid @enderror" aria-describedby="email"
                                                value="{{ $user->email }}" disabled placeholder="@lang('accounts.email')">
                                            <input type="hidden" class="form-control" name="email"
                                                id="email @error('email') is-invalid @enderror" aria-describedby="email"
                                                value="{{ $user->email }}">
                                        @endif

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                @else
                                    <div class="mb-3">
                                        {!! Html::label(__('accounts.email'), 'email') !!}
                                        <input type="email" class="form-control" name="email"
                                            id="email @error('email') is-invalid @enderror" aria-describedby="email"
                                            value="{{ $user->email }}" placeholder="@lang('accounts.email')">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                @endif
                            </div>

                            <div class="col-md-2 col-sm-12">
                                @if ($user->password && $user->password != '')
                                    <p class="text-success">@lang('accounts.ssochangpasswordmessage')</p>
                                @else
                                    <p class="text-danger">@lang('accounts.ssopasswordmessage')</p>
                                @endif

                            </div>
                            <div class="col-md-10 col-sm-12 mb-4">
                                <div class="mb-3">

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
                                </div>
                            </div>





                            <button type="submit" class="btn btn-primary btn-block">@lang('accounts.submit')</button>

                        </div>
                        {!! Html::form()->close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
