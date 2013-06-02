{{-- Sidebar --}}
@section('sidebar')

	@if($roleLearngroup == Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)
		<legend><font size="3">{{ __('question.question')}}</font></legend>

		<li id="editQuestion"><i class='icon-pencil'></i> <a href="#" onclick="edit()">{{__('question.edit')}}</a></li>
		<li> <i class='icon-trash'></i> <a href='#' onclick=deletequestion()>{{__('question.delete')}}</a></li>
	@endif
@endsection

{{-- Scripts --}}
@section('scripts')

{{ HTML::script('js/questionType.js')}}
<script>

$(document).ready(function(){
	initialiseQuestionType("{{$course['id']}}", "{{URL::base()}}");
});

//function which is called on delete
function deletequestion(){
	bootbox.dialog("{{__('question.check')}}", [{

		"label" : "{{__('general.no')}}",
		"class" : "btn-danger",
		"callback": function() {
			console.log("No delete");
		}

		}, {
		"label" : "{{__('general.yes')}}",
		"class" : "btn-success",
		"callback": function() {
			var urldelete = "{{ URL::to_route('question/delete', array($question['id'])) }}";
			window.location=urldelete;
		}

		}]);
}
</script>

@endsection

@section('content')


<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			@if(!is_null($navCatalog))
				<font size="2">{{ HTML::link_to_route('course', $course['name'], array($course['id'])) }} > {{ HTML::link_to_route('catalog', $navCatalog['name'], array($navCatalog['id'])) }} > {{__('question.question')}}</font>
			@else
				<font size="2">{{ HTML::link_to_route('course', $course['name'], array($course['id'])) }} > {{__('question.question')}}</font>
			@endif
			<div id="view">
				<legend>
					{{__('question.question')}}
					@if($roleLearngroup == Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)
						<a style="cursor: pointer;" onclick=edit(); class="pull-right" title="{{__('course.edit')}}"><i class="icon-edit"></i></a>
					@endif
				</legend>

				<div class="row-fluid">
					<div class="span12">
							<h5>Info</h5>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<label>{{__('question.course')}} {{ HTML::link_to_route('course', $course['name'], array($course['id'])) }}</label>
					</div>
					
					<div class="span6">
						<div class="btn-group">
							<button class="btn  btn-small dropdown-toggle" data-toggle="dropdown">{{__('question.catalogs')}} <span class="caret"></span></button>
							<ul class="dropdown-menu">
								@foreach($catalogs as $catalog)
									<li>{{ HTML::link_to_route('catalog', $catalog['name'], array($catalog['id'])) }}</li>
								@endforeach		
							</ul>
						</div>
					</div>
				</div>
				@if($question['type'] == 'simple' || $question['type'] === 'UndefType')	
					<h5>{{__('question.question')}}:</h5>	
					<p>{{$question['question']}}</p>
					<h5>{{__('question.answer')}}:</h5>
					<p>{{ $question['answer'] }}</p>
				@else
					<h5>{{__('question.question')}}:</h5>	
					<p>{{$question['question']}}</p>
					<h5>{{__('question.correct_answer')}}:</h5>
					@foreach($question['answer'] as $answer)	
						<p>{{$question['choices'][$answer]}}</p>
					@endforeach
					<h5>{{__('question.choices')}}</h5>	
					@foreach($question['choices'] as $choice)
						<p>{{ $choice}}</p>
					@endforeach
				@endif
			</div>			

			@if($roleLearngroup == Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)
				<div id="edit">		
					{{ Form::open('question/edit', 'POST', array('id' => 'formMultiple')) }}					
					<legend>
						{{__('question.question')}}
						<div class="btn-group pull-right">
							<button class="btn" type="submit" name="change_question" onclick="$(question/edit).submit()">{{__('course.save')}}</button>
							<button class="btn" onclick="edit();return false;">{{__('general.abort')}}</button>
						</div>
					</legend>
					<div class="row-fluid">
						<div class="span12">
							<h5>Info</h5>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<label>{{__('question.course')}} {{ HTML::link_to_route('course', $course['name'], array($course['id'])) }}</label>
						</div>
						<div class="span6">
							<div class="btn-group">
								<button class="btn  btn-small dropdown-toggle" data-toggle="dropdown">{{__('question.catalogs')}} <span class="caret"></span></button>									<ul class="dropdown-menu">
									@foreach($catalogs as $catalog)
										<li>{{ HTML::link_to_route('catalog', $catalog['name'], array($catalog['id'])) }}</li>
									@endforeach		
								</ul>
							</div>
						</div>
					</div>
					<div id="editQuestion{{$question['id']}}">	
						@if($question['type'] == 'simple' || $question['type'] === 'UndefType')
			
							{{ Form::hidden('course', $course['id']) }}
							{{ Form::hidden('type', 'simple') }}
							{{ Form::hidden('id', $question['id']) }}
											
							<!-- The simple question type -->
							@include('question.types.simple')
							<label>{{__('catalog.sidebar_title')}}</label>	
							{{ Form::select('catalogs[]', $allCatalogs, $question['catalogs'], array('multiple' => 'multiple', 'class' => 'row-fluid' , 'id'=>'selectCatalogs')) }}
						@else		
						
							{{ Form::hidden('course', $course['id']) }}
							{{ Form::hidden('type', 'multiple') }}
							{{ Form::hidden('id', $question['id']) }}
												
							<!-- The multiplechoice question type -->
							@include('question.types.multiple')
							<label>{{__('catalog.sidebar_title')}}</label>						
							{{ Form::select('catalogs[]', $allCatalogs, $question['catalogs'], array('multiple' => 'multiple', 'class' => 'row-fluid' , 'id'=>'selectCatalogs')) }}
						@endif
					</div>
					{{ Form::token() }}
					{{ Form::close() }}	
				</div>
			@endif			
		</div>
	</div>
</div>

@endsection