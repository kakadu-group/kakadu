{{-- Sidebar --}}
@section('sidebar')
	<li class="nav-header">{{__('group.sidebar_title')}}</li>
	<li id="create">{{HTML::link_to_route('group/create', __('profile.create_group'))}}</li>

	<li class="nav-header">{{__('course.sidebar_title')}}</li>
	<li id="create">{{HTML::link_to_route('course/create', __('profile.create_course'))}}</li>
@endsection

{{--Content--}}
@section('content')


<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<legend>{{__('group.edit')}}</legend>
			{{ Form::open('group/edit', 'POST') }}

			{{ Form::hidden('id', $group['id']) }}

			{{ Form::label('name', __('group.name')) }}
			@if(Input::old('name') != '')
				{{ Form::text('name', Input::old('name'), array('class' => 'row-fluid', 'rows' => '1')) }}
			@else
				{{ Form::text('name', $group['name'], array('class' => 'row-fluid', 'rows' => '1')) }}
			@endif
			
			
			{{ Form::label('description', __('group.description')) }}
			@if(Input::old('description') != '')
				{{ Form::textarea('description', Input::old('description'), array('class' => 'row-fluid', 'rows' => '6')) }}
			@else
				{{ Form::textarea('description', $group['description'], array('class' => 'row-fluid', 'rows' => '6')) }}
			@endif
			
			{{ Form::token() }}
			<br>
			<button class="btn btn-small btn-primary" type="submit" name="edit_group" id="edit_group" onclick="$(group/edit).submit()">{{__('course.save')}}</button>
			
			{{ Form::close() }}
		</div>
	</div>
</div>



@endsection
