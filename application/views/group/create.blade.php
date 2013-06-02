
{{--Content--}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<legend>{{__('group.create')}}</legend>
			{{ Form::open('group/create', 'POST') }}

			{{ Form::label('name', __('group.name')) }}
			{{ Form::text('name', Input::old('name'), array('class' => 'row-fluid', 'rows' => '1')) }}
			
			{{ Form::label('description', __('group.description')) }}
			{{ Form::textarea('description', Input::old('description'), array('class' => 'row-fluid', 'rows' => '6'))}}
			
			{{ Form::token() }}
			<br>
			<button class="btn btn-small btn-primary" type="submit" name="create_group" id="create_group" onclick="$(group/create).submit()">{{__('group.create')}}</button>
			
			{{ Form::close() }}
		</div>
	</div>
</div>

@endsection
