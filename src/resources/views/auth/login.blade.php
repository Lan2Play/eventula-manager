@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' - ' . __('auth.login_to_continue'))

@section ('content')

    <div class="container pt-1">
        <div class="pb-2 mt-4 mb-4 border-bottom">
            <h1>@lang('auth.please_login')</h1>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <p>@lang('auth.login_methods')
                @if (in_array('standard', $activeLoginMethods))
                @lang('auth.or') <a href="/register/standard">@lang('auth.register_now')</a>
                @endif
                </p>
                @if (in_array('steam', $activeLoginMethods))
                    <a href="/login/steam">
                        <img class="img img-fluid" src="https://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_01.png">
                    </a>
                @endif
            </div>
            <div class="col-sm-12 col-md-6">
                @if (in_array('standard', $activeLoginMethods))
                    {!! Html::form('POST', route('login.standard')) !!}

                        <div class="mb-3 row">
                            {!! Html::label(__('auth.email_short'), 'email')->class('col-sm-2 col-form-label') !!}
                            <div class="col-sm-10 @error('email') is-invalid @enderror">
                                <input type="email" id="email" name="email" class="form-control" placeholder="@lang('auth.email')" required autofocus>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            {!! Html::label(__('auth.password'), 'password')->class('col-sm-2 col-form-label') !!}
                            <div class="col-sm-10 @error('password') is-invalid @enderror">
                                <input type="password" id="password" name="password" class="form-control" placeholder="@lang('auth.password')" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="offset-sm-2 col-sm-10">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" value="remember-me"> @lang('auth.remember')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="offset-sm-2 col-sm-10">
                                <button class="btn btn-lg btn-primary btn-block" type="submit">@lang('auth.signin')</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="coffset-sm-2 col-sm-10">
                                <a class="btn btn-link" href="/login/forgot">
                                @lang('auth.forgot_password')
                                </a>
                            </div>
                        </div>
					{!! Html::form()->close() !!}
                @endif
            </div>
        </div>
    </div>

@endsection