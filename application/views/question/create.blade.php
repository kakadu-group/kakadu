{{-- Scripts --}}
@section('scripts')
	{{ HTML::script('js/questionType.js')}}
	
	<script>
		$(document).ready(function(){
			initialiseQuestionType("{{$course['id']}}", "{{URL::base()}}", "{{Input::old('type')}}");
		});
	</script>
@endsection

@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<legend><h3>{{__('question.create')}}</h3></legend>
			<label>{{__('question.choose_type')}}</label>
			<div class="btn-group">
				<button class="btn  btn-small dropdown-toggle" data-toggle="dropdown">{{__('question.type')}} <span class="caret"></span></button>
				<ul class="dropdown-menu">
					<li><a onclick="changeType('simple')" style="cursor: pointer;">{{__('question.simple')}}</a></li>
					<li><a onclick="changeType('multiple')" style="cursor: pointer;">{{__('question.multiple')}}</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- Simple question -->
<div class="row-fluid" id="simple">
	<div class="offset1">
		<br>
		{{ Form::open('question/create', 'POST', array('id' => 'formSimple')) }}
			
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('type', 'simple') }}
		
			<!-- The simple question type -->
			@include('question.types.simple')
	
			@include('question.catalogs')
			
			{{ Form::token() }}
			{{ Form::submit(__('question.create'), array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}	
	</div>
</div>
<!-- Multiple choice question -->
<div class="row-fluid" id="multiple">
	<div class="offset1">
		<br>
		{{ Form::open('question/create', 'POST', array('id' => 'formMultiple')) }}
		
			{{ Form::hidden('course', $course['id']) }}
			{{ Form::hidden('type', 'multiple') }}
			
			<!-- The multiplechoice question type -->
			@include('question.types.multiple')
					
			<br>
			@include('question.catalogs')
			
			{{ Form::token() }}
			{{ Form::submit(__('question.create'), array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}	
	</div>
</div>

@endsection