{{-- Scripts --}}
@section('scripts')


{{ HTML::script('js/Model/simpleQuestion.js')}}
{{ HTML::script('js/Model/multipleQuestion.js')}}
{{ HTML::script('js/View/learningView.js')}}

<script>

$(document).ready(function(){
	
	//Create View
	var question = <?php echo json_encode($question); ?>;
	view = new QuizView('{{$course["id"]}}', '{{URL::base()}}', '{{$section}}', question, '{{$catalog["id"]}}');

	//Hides the alerts when clicking close
	$('.alert .close').live('click',function(){
		$(this).parent().hide();
		return false;
	});
	
});
	

</script>


@endsection


{{-- Content --}}
@section('content')

    
<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			
			<legend><h1>Quiz</h1></legend>
			<h3><span id="name"></span></h3>
			
			<!-- Include of the different question types. Depending on the question type only one type is visible.  -->
			
			<!-- Simple Question type -->
			@include('learning.types.simple')
			
			<!-- Multiple choice question type -->
			@include('learning.types.multiple')
			
			<p>{{__('general.back_course')}}{{Html::link_to_route('course', $course['name'], array($course['id']))}}</p>
			<p>{{__('general.back_catalog')}}{{Html::link_to_route('catalog', $catalog['name'], array($catalog['id']))}}</p>
		</div>
	</div>
</div>

			

@endsection