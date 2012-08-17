<h1>Sign In</h1>
{{ Form::open('sentrysocial/auth/login', 'POST') }}
	{{ Session::get('login_error') }}
	<div>
		<label for="email">Email:</label>
		<input type="text" id="email" name="email" value="{{ Input::old('email') }}" />
	</div>
	<div>
		<label for="password">Password:</label>
		<input type="password" id="password" name="password" value="" />
	</div>
	<input type="submit" value="Sign In">
{{ Form::close() }}

<h1>Register</h1>
{{ Form::open('sentrysocial/auth/register', 'POST') }}
	<div>
		<label for="email">Email:</label>
		<input type="text" id="email" name="email" value="{{ Input::old('email') }}" />
		{{ $errors->first('email') }}
	</div>
	<div>
		<label for="email_confirm">Email Confirm:</label>
		<input type="text" id="email_confirm" name="email_confirm" value="{{ Input::old('email_confirm') }}" />
		{{ $errors->first('email_confirm') }}
	</div>
	<input type="submit" value="Register">
{{ Form::close() }}