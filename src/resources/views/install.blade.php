@extends ('layouts.default')

@section ('content')

<div class="container pt-1">
	<div class="row">
		<div class="col-12 col-sm-12 col-md-12">
			<div class="pb-2 mt-4 mb-4 border-bottom">
				<h1>Hello & Welcome to your new Event Management Platform!</h1>
			</div>
			<p>Before you can start planning and adding events we need to do a little setup...</p>
			<p>Please Fill out the form below. Once this is done you will be redirected to the Admin Panel.</p>
			{{ Form::open(array('url'=>'/install' )) }}
				<h2>Step 1: Create Admin User</h2>
				<hr>
                <div class="row">
                	<div class="col-sm-12 col-md-6">
		                <div class="row">
		                    <div class="col-12 col-md-6">
		                        <div class="mb-3 @error('firstname') is-invalid @enderror">
		                            {{ Form::label('firstname','First Name',array('id'=>'','class'=>'')) }}
		                            <input id="firstname" type="firstname" class="form-control" name="firstname" value="{{ old('firstname') }}" required autocomplete="firstname">
		                        </div>
		                    </div>
		                    <div class="col-12 col-md-6">
		                        <div class="mb-3  @error('surname') is-invalid @enderror">
		                            {{ Form::label('surname','Last Name',array('id'=>'','class'=>'')) }}
		                            <input id="surname" type="surname" class="form-control" name="surname" value="{{ old('surname') }}" required autocomplete="surname">
		                        </div>
		                    </div>
		                    <div class="col-12">
				                <div class="mb-3 @error('username') is-invalid @enderror">
				                    {{ Form::label('username','Username',array('id'=>'','class'=>'')) }}
				                    <input id="username" type="username" class="form-control" name="username" value="{{ old('username') }}" required autocomplete="username">
				                </div>
			                    <div class="mb-3 @error('email') is-invalid @enderror">
			                        {{ Form::label('email','E-Mail',array('id'=>'','class'=>'')) }}
			                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email">
			                    </div>
			                    <div class="mb-3 @error('password1') is-invalid @enderror">
			                        {{ Form::label('password1','Password',array('id'=>'','class'=>'')) }}
			                         <input id="password1" type="password" class="form-control" name="password1" required autocomplete="new-password">
			                    </div>
			                    <div class="mb-3 @error('password2') is-invalid @enderror">
			                        {{ Form::label('password2','Confirm Password',array('id'=>'','class'=>'')) }}
			                        <input id="password2" type="password" class="form-control" name="password2" required autocomplete="new-password">
			                    </div>
			                    <input id="url" type="hidden" class="form-control" name="url">
			                </div>
			            </div>
			        </div>
                	<div class="col-sm-12 col-md-6">
						<p>Once Registered you can link your account to any other login method.</p>
						<p>Once Installed you can add more admins via the admin panel</p>
                	</div>
	            </div>
				<h2>Step 2: Confirm Organization Details</h2>
				<hr>
				<div class="row">
					<div class="col-12 col-md-6">
						<div class="mb-3">
							{{ Form::label('org_name','Name',array('id'=>'','class'=>'')) }}
							{{ Form::text('org_name', Settings::getOrgName() ,array('id'=>'','class'=>'form-control')) }}
						</div>
						<div class="mb-3">
							{{ Form::label('org_tagline','Tagline/Title',array('id'=>'','class'=>'')) }}
							{{ Form::text('org_tagline', Settings::getOrgTagline() ,array('id'=>'','class'=>'form-control')) }}
						</div>
					</div>
					<div class="col-12 col-md-6">
						<p>These Can be changed at any time. It's just best to get it right first time.</p>
					</div>
				</div>
			    <h2>Step 3: Select Default Payment Gateway</h2>
			    <hr>
				<div class="row">
					<div class="col-12 col-md-6">
				        <h4>Paypal</h4>
			        	<div class="mb-3">
							{{ Form::label('paypal_username','Username',array('id'=>'','class'=>'')) }}
							{{ Form::text('paypal_username', '',array('id'=>'','class'=>'form-control')) }}
						</div>
						<div class="mb-3">
							{{ Form::label('paypal_password','Password',array('id'=>'','class'=>'')) }}
							 <input id="paypal_password" type="password" class="form-control" name="paypal_password">
						</div>
						<div class="mb-3">
							{{ Form::label('paypal_signature','Signature',array('id'=>'','class'=>'')) }}
							{{ Form::text('paypal_signature', '',array('id'=>'','class'=>'form-control')) }}
						</div>
				        <h4>Stripe</h4>
				        <div class="mb-3">
							{{ Form::label('stripe_public','Public Key',array('id'=>'','class'=>'')) }}
							{{ Form::text('stripe_public', '',array('id'=>'','class'=>'form-control')) }}
						</div>
						<div class="mb-3">
							{{ Form::label('stripe_secret','Secret Key',array('id'=>'','class'=>'')) }}
							{{ Form::text('stripe_secret', '',array('id'=>'','class'=>'form-control')) }}
						</div>
					</div>
					<div class="col-12 col-md-6">
				        <p>You Must supply at least one Payment Gateway API.</p>
				        <p>
				        	To find out more visit the @if (config('app.env') == 'staging') <a href="https://staging.eventula.com/faq/manager">Eventula FAQ</a> @else <a href="https://staging.eventula.com/faq/manager">Eventula FAQ</a> @endif
				        </p>
				    </div>
				</div>
		        <h2>Step 4: Confirm settings in the Admin Panel</h2>
		        <hr>
				<div class="row">
					<div class="col-12 col-md-6">
						<button type="submit" class="btn btn-lg btn-block btn-success">Confirm</button>
					</div>
					<div class="col-12 col-md-6">
				        <p>Once Submitted you will be redirected to the Admin Panel where you can changed more settings.</p>
				    </div>
				</div>
	       {{ Form::close() }}
		</div>
	</div>
</div>

@endsection
