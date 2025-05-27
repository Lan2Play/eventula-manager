@extends ('layouts.default')

@section ('page_title', Settings::getOrgName() . ' - ' . __('reset_password'))

@section ('content')

    <div class="container pt-1">

        <div class="pb-2 mt-4 mb-4 border-bottom">
            <h1>
                @lang('auth.reset_password')
            </h1>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        {!! Html::form('POST', route('password.update')) !!}

                        {!! Html::hidden('token', $token) !!}

                            <div class="mb-3 row">
								{!! Html::label(__('auth.email'), 'email')->class('col-md-4 col-form-label text-md-end') !!}
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
								{!! Html::label(__('auth.password'), 'password')->class('col-md-4 col-form-label text-md-end') !!}

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
								{!! Html::label(__('auth.confirm_password'), 'password-confirm') !!}

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="mb-3 row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        @lang('auth.reset_password')
                                    </button>
                                </div>
                            </div>
                        {!! Html::form()->close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
