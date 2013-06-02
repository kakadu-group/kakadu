<!-- View for create and edit a multiplechoice question -->
	<label>{{__('question.question')}}</label>

	<textarea id="question" name="question" class="row-fluid" rows="4" style="resize:none">{{Input::old('question')}}@if(isset($question)){{$question['question']}}@endif</textarea>
	
	<div class="row-fluid">
		<div class="span10">
			<label>{{__('question.answer')}}
				<p class="pull-right">{{__('question.choose_answer')}}</p>
			</label>
		</div>
	</div>
	
	<!-- If we edit a question the question field is set -->
	@if(isset($question))
		@foreach($question['answer'] as $answer)	
			{{ Form::hidden('answer[]', $answer) }}
		@endforeach
		<?php $count = 0?>
		<div id="choices">
			@foreach($question['choices'] as $choice)
				<div id=<?php echo $count?>>
					<textarea name="choices[]" class="span8 choices" rows="1" style="resize:none">{{$choice}}</textarea>
					<?php $right = false?>
					@foreach($question['answer'] as $answer)
						@if($choice === $question['choices'][$answer])
							<?php $right = true?>
							<input id="check<?php echo $count?>" name="right" class="offset1" checked="checked" type="checkbox" value="<?php echo $count?>" name="checkbox">
						@endif
					@endforeach					
					@if(!$right)
						<input id="check<?php echo $count?>" name="right" class="offset1" type="checkbox" value="<?php echo $count?>" name="checkbox">
					@endif
					@if($count >= 2)
						<button class="btn-danger offset1" onclick="removeChoice(<?php echo $count?>);return false;"><i class="icon-remove"></i></button>
					@endif
				</div>
				<?php $count++?>
			@endforeach
		</div>
	<!-- For creating a question -->			
	@else
		<div id="choices">
			<textarea name="choices[]" class="span8 choices" rows="1" style="resize:none">{{Input::old('choices.0')}}</textarea> 
			<input id="check0" name="right" class="offset1" type="checkbox" value="0" name="checkbox">
					
			<textarea name="choices[]" class="span8 choices" rows="1" style="resize:none">{{Input::old('choices.1')}}</textarea>
			<input id="check1" name="right" class="offset1" type="checkbox" value="1" name="checkbox">
			
		</div>	
	@endif
	<br>
	<button class="btn-small btn-primary" onclick="addChoice();return false;">{{__('question.add_choice')}}</button><br>
