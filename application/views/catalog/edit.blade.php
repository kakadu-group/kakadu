{{--Content--}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<legend>{{__('catalog.edit')}}</legend>
			{{ Form::open('catalog/edit', 'POST') }}
	
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('id', $catalog['id']) }}
			
			{{ Form::label('name', __('catalog.name')) }}
			@if(Input::old('name') != '')
				{{ Form::text('name', Input::old('name'), array('class' => 'row-fluid', 'rows' => '1')) }}
			@else
				{{ Form::text('name', $catalog['name'], array('class' => 'row-fluid', 'rows' => '1')) }}
			@endif
			
			
			{{ Form::label('number', __('catalog.number')) }}
			@if(Input::old('name') != '')
				{{ Form::number('number', Input::old('number'), array('class' => 'row-fluid', 'rows' => '1')) }}
			@else
				{{ Form::number('number', $catalog['number'], array('class' => 'row-fluid', 'rows' => '1')) }}
			@endif
			
			
			{{ Form::label('parent', __('catalog.parent')) }}
			@if(Input::old('parent') != '')
				{{ Form::select('parent', $catalogs, Input::old('parent'), array('class' => 'row-fluid', 'rows' => '1')) }}
			@else
				{{ Form::select('parent', $catalogs, $catalog['parent'], array('class' => 'row-fluid', 'rows' => '1')) }}
			@endif
			
			
			{{ Form::token() }}
			<button class="btn btn-small btn-primary" type="submit" name="change_course" id="change_course" onclick="$(catalog/edit).submit()">{{__('course.save')}}</button>
			
			{{ Form::close() }}
		</div>
	</div>
</div>

@endsection