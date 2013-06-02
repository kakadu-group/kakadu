<!-- View for create and edit a simple question -->
<div>
	<label>{{__('question.question')}}</label>
	<textarea name="question" class="row-fluid" rows="4" style="resize:none">{{Input::old('question')}}@if(isset($question)){{$question['question']}}@endif</textarea>
				
	<label>{{__('question.answer')}}</label>
	<textarea name="answer" class="row-fluid" rows="4" style="resize:none"><?php if(Input::old('type') === 'simple'){echo Input::old('answer');}?>@if(isset($question)){{$question['answer']}}@endif</textarea>
</div>