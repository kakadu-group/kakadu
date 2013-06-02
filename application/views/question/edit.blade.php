
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
		<legend><h3>{{__('question.edit')}}</h3></legend>
		{{ Form::open('question/edit', 'POST') }}
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('id', $question['id']) }}
			
			@if(Input::old('type') != '')
				{{ Form::hidden('type', Input::old('type'), array('class' => 'row-fluid')) }}
			@else
				{{ Form::hidden('type', $question['type'], array('class' => 'row-fluid')) }}
			@endif
			
			{{ Form::label('question', __('question.question')) }}
			@if(Input::old('question') != '')
				{{ Form::textarea('question', Input::old('question'), array('class' => 'row-fluid', 'rows' => '4', 'style' => 'resize:none')) }}
			@else
				{{ Form::textarea('question', $question['question'], array('class' => 'row-fluid', 'rows' => '4', 'style' => 'resize:none')) }}
			@endif
			
			{{ Form::label('answer', __('question.answer')) }}
			@if(Input::old('answer') != '')
				{{ Form::textarea('answer', Input::old('answer'), array('class' => 'row-fluid', 'rows' => '4', 'style' => 'resize:none')) }}
			@else
				{{ Form::textarea('answer', $question['answer'], array('class' => 'row-fluid', 'rows' => '4', 'style' => 'resize:none')) }}
			@endif
			
			{{ Form::label('catalogs[]', __('question.catalogs')) }}
			@if(Input::old('catalogs') != '')
				{{ Form::select('catalogs[]', $catalogs, Input::old('catalogs'), array('multiple' => 'multiple', 'class' => 'row-fluid')) }}
			@else
				{{ Form::select('catalogs[]', $catalogs, $question['catalogs'], array('multiple' => 'multiple', 'class' => 'row-fluid')) }}
			@endif
			
			{{ Form::token() }}
			{{ Form::submit(__('question.save_changes'), array('class' => 'btn btn-primary')) }}
			
		{{ Form::close() }}
		</div>
	</div>
</div>

@endsection