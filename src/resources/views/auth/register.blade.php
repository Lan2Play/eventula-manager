@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' - ' . __('auth.register'))

@section ('content')

<div class="container pt-1">
    <div class="pb-2 mt-4 mb-4 border-bottom">
        <h1>@lang('auth.register_details')</h1>
    </div>
    <div class="row">
        {!! Html::form('POST', '/register/' . $loginMethod) !!}
        {!! Html::hidden('method', $loginMethod)->id('method')->class('form-control') !!}
        @if ($loginMethod == "steam")
        {!! Html::hidden('avatar', $steam_avatar)->id('avatar')->class('form-control') !!}
        {!! Html::hidden('steamid', $steamid)->id('steamid')->class('form-control') !!}
        {!! Html::hidden('steamname', $steamname)->id('steamname')->class('form-control') !!}
        @endif
        <div class="col-12 col-md-6">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="mb-3 @error('firstname') is-invalid @enderror">
                        {!! Html::label(__('auth.firstname'), 'firstname') !!}
                        {!! Html::text('firstname', old('firstname'))->id('firstname')->class('form-control')->required()->attribute('autocomplete', 'firstname') !!}
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="mb-3  @error('surname') is-invalid @enderror">
                        {!! Html::label(__('auth.surname'), 'surname') !!}
                        {!! Html::text('surname', old('surname'))->id('surname')->class('form-control')->required()->attribute('autocomplete', 'surname') !!}
                    </div>
                </div>
            </div>
            <div class="mb-3 @error('username') is-invalid @enderror">
                {!! Html::label(__('auth.username'), 'username') !!}
                {!! Html::text('username', old('username'))->id('username')->class('form-control')->required()->attribute('autocomplete', 'username') !!}
            </div>


            @if($loginMethod == "standard" || ($loginMethod == "steam" && Settings::isAuthSteamRequireEmailEnabled()))

            <div class="mb-3 @error('email') is-invalid @enderror">
                {!! Html::label(__('auth.email'), 'email') !!}
                {!! Html::email('email', old('email'))->id('email')->class('form-control')->required()->attribute('autocomplete', 'email') !!}
            </div>

            @endif

            @if(Settings::isAuthRequirePhonenumberEnabled())

            <div class="mb-3 @error('phonenumber') is-invalid @enderror">
                {!! Html::label(__('auth.phonenumber'), 'phonenumber') !!}
                {!! Html::text('phonenumber', old('phonenumber'))->id('phonenumber')->class('form-control')->required()->attribute('autocomplete', 'phonenumber') !!}
            </div>

            @endif

            @if ($loginMethod == "standard")

            <div class="mb-3 @error('password1') is-invalid @enderror">
                {!! Html::label(__('auth.password'), 'password1') !!}
                {!! Html::password('password1')->id('password1')->class('form-control')->required()->attribute('autocomplete', 'new-password') !!}
            </div>
            <div class="mb-3 @error('password2') is-invalid @enderror">
                {!! Html::label(__('auth.confirm_password'), 'password2') !!}
                {!! Html::password('password2')->id('password2')->class('form-control')->required()->attribute('autocomplete', 'new-password') !!}
            </div>
            {!! Html::hidden('url')->id('url')->class('form-control') !!}

            @endif
            @if ($loginMethod == "steam")
            <div class="mb-3">
                {!! Html::label(__('auth.steamname'), 'steamname') !!}
                {!! Html::text('steamname', $steamname)->id('steamname')->class('form-control')->disabled() !!}
            </div>
            @endif
        </div>
        <div class="col-12 col-md-6">
            {!! Settings::getRegistrationTermsAndConditions() !!}
            <h5>@lang('auth.register_confirmtext') {!! Settings::getOrgName() !!}</h5>
            <button type="submit" class="btn btn-block btn-primary">@lang('auth.register')</button>
        </div>
        {!! Html::form()->close() !!}
    </div>
</div>

@endsection