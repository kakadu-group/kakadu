@section('content')

<div class="row-fluid">
	<div class="offset1">
		<legend><h1>Reset Password</h1></legend>
		<form class="form-horizontal" method="POST" id="auth/forgotpassword">
			<fieldset>
				<ol>
					{{ Form::label('email', 'Email') }}
					 {{ Form::email('email', Input::old('email')) }} {{ Form::token() }}<br>
					<br><button class="btn btn-primary" type="submit" name="register" id="register" onclick="$(auth/forgotpassword).submit()">Reset password</button>
				</ol>
			</fieldset>
	</form>	
	</div>
</div>

@endsection