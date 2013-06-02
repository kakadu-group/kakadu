@section('content')

<div class="row-fluid">
	<div class="offset1">
		<legend><h1>{{__('authentification.registration_label')}}</h1></legend>
		{{ Form::open('auth/register', 'POST') }}
					{{Form::label('displayname', __('authentification.displayname_label')) }}
					{{Form::text('displayname', Input::old('displayname')) }}
					{{Form::label('email', 'Email') }}
					{{Form::email('email', Input::old('email')) }}
					{{Form::label('password', __('authentification.password_label')) }}
					{{Form::password('password') }}
				 	{{Form::label('password_confirmation',__('authentification.confirm_password_label')) }} 
				 	{{Form::password('password_confirmation') }}
					{{Form::token() }}<br>
					<button class="btn btn-primary" type="submit" name="register" id="register" onclick="$(auth/register).submit()">{{__('authentification.register_button')}}</button> 
		{{Form::close()}}
	</div>
</div>


@endsection
