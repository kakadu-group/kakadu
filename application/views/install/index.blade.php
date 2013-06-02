@section('content')
<div class="row-fluid">
	<div class="offset1">
		{{ Form::open('install', 'POST') }}
		<div class="row-fluid">
			<legend>{{__('install.installation')}}</legend>
		</div>
		<div class="row-fluid">
			<div class="span6">
				{{Form::label('user_displayname', __('install.display')) }}
			    {{Form::text('user_displayname', Input::old('user_displayname')) }}
			
			    {{Form::label('user_email', 'Email') }}
			    {{Form::email('user_email', Input::old('user_email')) }}
			
			    {{Form::label('user_password', __('install.password')) }}
			    {{Form::password('user_password') }}
			
			    {{Form::label('user_password_confirmation', __('install.password_confirm')) }} 
	    		{{Form::password('user_password_confirmation') }}		
	    		
			</div>	
			<div class="span6">				
				{{Form::label('db_host', 'Host') }}
			    {{Form::text('db_host', Input::old('db_host')) }}
			    	
			    {{Form::label('db_database', __('install.database')) }}
			    {{Form::text('db_database', Input::old('db_database')) }}
			
			    {{Form::label('db_username', __('install.username')) }}
			    {{Form::text('db_username', Input::old('db_username')) }}
			
			    {{Form::label('db_password', __('install.password')) }}
			    {{Form::password('db_password') }}
			
			    {{Form::label('db_password_confirmation', __('install.password_confirm')) }} 
			    {{Form::password('db_password_confirmation') }}
			</div>
		</div>
		<div class="row-fluid">
			{{ Form::submit(__('install.install'), array('class' => 'btn btn-primary')) }}
	
			{{ Form::token() }}
			{{ Form::close() }}
		</div>
	</div>
</div>
@endsection