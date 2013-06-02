<!-- The select field with all catalogs -->

<div>
	<label>{{__('question.catalogs')}}</label>
	@if(Input::has('catalog'))
		{{ Form::select('catalogs[]', $catalogs, Input::get('catalog'), array('multiple' => 'multiple', 'class' => 'row-fluid')) }}
	@elseif(isset($question['catalogs'])) 
		{{ Form::select('catalogs[]', $catalogs, $question['catalogs'], array('multiple' => 'multiple', 'class' => 'row-fluid')) }}
	@else
		{{ Form::select('catalogs[]', $catalogs, Input::old('catalogs'), array('multiple' => 'multiple', 'class' => 'row-fluid', 'id'=>'selectCatalogs')) }}
	@endif
</div>
