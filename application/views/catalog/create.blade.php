{{--Content--}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">	
		<legend>{{__('catalog.create')}}</legend>	
		{{ Form::open('catalog/create', 'POST') }}
		
			{{ Form::hidden('course', $course['id']) }}
			
			{{ Form::label('name', __('catalog.name')) }}
			{{ Form::text('name', Input::old('name'), array('class' => 'row-fluid')) }}
			
			{{ Form::label('number', __('catalog.number_create')) }}
			{{ Form::number('number', Input::old('number'), array('class' => 'row-fluid')) }}
			
			{{ Form::label('parent', __('catalog.parent')) }}
			{{ Form::select('parent', $catalogs, Input::old('parent'), array('class' => 'row-fluid')) }}
			
			{{ Form::token() }}<br>
			{{ Form::submit(__('catalog.create'), array('class' => 'btn btn-primary')) }}
		
		{{ Form::close() }}
		</div>
	</div>
</div>

@endsection