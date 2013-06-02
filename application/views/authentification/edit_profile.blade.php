{{--Content--}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<legend><h3>{{__('profile.profile_edit_link')}}</h3></legend>
		<div class="span12">
			<div class="accordion" id="accordion1">
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">{{__('profile.change_info')}} <i class="icon-chevron-down"></i></a>
					</div>
					<div id="collapseOne" class="accordion-body collapse">
						<div class="accordion-inner">
							{{ Form::open('profile/edit', 'POST') }}
								<fieldset>
									<ol>
										{{ Form::label('displayname', 'Displayname') }}
										@if(Input::old('displayname') != '')
											{{ Form::text('displayname', Input::old('displayname')) }}
										@else
											{{ Form::text('displayname', $user['displayname']) }}
										@endif
					
										{{ Form::label('email', 'Email') }}
										@if(Input::old('email') != '')
											{{ Form::email('email', Input::old('email')) }}
										@else
											{{ Form::email('email', $user['email']) }}
										@endif
										{{ Form::label('language', __('profile.language')) }}

										{{ Form::select('language', $languages, $language) }}
										
										{{ Form::token() }}
										<br>
										<button class="btn btn-small btn-primary" type="submit" name="change_email" id="change_email" onclick="$(profile/edit).submit()">{{__('profile.change_info')}}</button>
									</ol>
								</fieldset>
							{{ Form::close() }}
						</div>
					</div>
				</div>
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">{{__('profile.change_password')}} <i class="icon-chevron-down"></i></a>
					</div>
					<div id="collapseTwo" class="accordion-body collapse">
						<div class="accordion-inner">
							{{ Form::open('profile/changepassword', 'POST') }}
								<fieldset>
									<ol>
										{{ Form::label('password_old', __('profile.old_password')) }}
										{{ Form::password('password_old') }}
					
										{{ Form::label('password', __('profile.new_password')) }}
										{{ Form::password('password') }}
					
										{{ Form::label('password_confirmation', __('profile.confirm_password')) }}
										{{ Form::password('password_confirmation') }}
					
										{{ Form::token() }}
										<br>
										<button class="btn btn-small btn-primary" type="submit" name="change_pw" id="change_pw" onclick="$(profile/changepassword).submit()">{{__('profile.change_password')}}</button>
									</ol>
								</fieldset>
							{{ Form::close() }}
						</div>
					</div>
				</div>
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">{{__('profile.delete_profile')}} <i class="icon-chevron-down"></i></a>
					</div>
					<div id="collapseThree" class="accordion-body collapse">
						<div class="accordion-inner">
							{{ HTML::link_to_route('profile/delete', __('profile.delete_profile_link')) }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
