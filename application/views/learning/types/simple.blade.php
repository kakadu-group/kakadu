<div id="simple">
	<div id="questionsAnswers">
		<div style="height: 12ex;">
			<h4>
				{{__('test.question_label')}}
				<div class="percent pull-right" class="pull-right"></div>
			</h4>
			<p id="questionSimple"></p>
		</div>
		<br>
		<div style="height: 14ex;" id="answerLabel">
			<h4>{{__('test.answer_label')}}</h4>
			<p id="answerSimple"></p>
		</div>
		<button class="btn btn-primary" id="showAnswer">{{__('test.show_answer_button')}}</button>
		<div id="correct">
			<button class="btn btn-success" id="yes">
				<i class="icon-ok"></i> {{__('test.yes')}}
			</button>
			<button class="btn btn-danger" id="no">
				<i class="icon-remove"></i> {{__('test.no')}}
			</button>
		</div>
	</div>
	<br>
	<div id="keyboard control" class="alert alert-block fade in">
		<a class="close" href="#">&times;</a>
		<h5>{{__('descriptions.keys')}}</h5>
		<p>{{__('descriptions.key_show')}}</p>
		<p>{{__('descriptions.use')}} 1 {{__('descriptions.key_correct')}}</p>
		<p>{{__('descriptions.use')}} 2 {{__('descriptions.key_incorrect')}}</p>
	</div>
</div>
