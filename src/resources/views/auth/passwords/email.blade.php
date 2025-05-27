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
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        {!! Html::form('POST', route('password.email')) !!}
                        

                            <div class="mb-3 row">
								{!! Html::label(__('auth.email'), 'email')->class('col-md-4 col-form-label text-md-end') !!}

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        @lang('auth.send_password_link')
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
