/**
 * Backbone View of the test
 */
QuizView = Backbone.View.extend({


	/**
	 * Initializes the view with all data.
	 * 
	 * @param: course: the it of the course which the user is learning
	 * @param: url: the base url
	 * @param: section: course|catalog|favorite
	 * @param: question: the first question
	 * @param: catalog: the id of the catalog which the user is learning
	 */
	initialize: function(course, url, section, question, catalog) {
		this.section = section;
		this.courseId = course;
		this.baseUrl = url;
		this._csrf = $('input[name="csrf_token"]').val();
		this.right = 0;
		this.catalogId = catalog;
		this.answerBoolean = true;
		this.type = question.type;
		this.multipleAnswers = new Array();

		//writing the question and reading the answer
		if(this.type === "simple"){
			this.simple = new simpleQuestion(question);
			$("#multiple").hide();
			$("#questionSimple").html(this.simple.get("question"));
			$("#answerSimple").html("");
		}else{
			this.multiple = new multipleQuestion(question);
			$("#nextQuestion").hide();
			$("#simple").hide();
			$("#questionMultiple").html(this.multiple.get("question"));
			this.setAnswers(this.multiple.get("choices"));
		}

		this.percent = 0;
		this.number = 0;
		_.bindAll(this);
		$(document).bind('keyup', this.logKey);
		this.state = "question";
		this.render();
	},

	el: 'body',
	render: function() {
		$(document).ready(function() {
			$("#correct").hide();
			return this;
		});
	},

	/**
	 * Assigns which function is called on which button press
	 */
	events: {
		"click button[id=showAnswer]": "showAnswer",
		"click button[id=yes]": "correct",
		"click button[id=no]": "notCorrect",
		"click button[id=checkAnswer]": "checkAnswer",
		"click button[id=nextQuestion]": "nextMultipleQuestion",
		"click button[class='btn row-fluid answer']": "checkClick",
		

	},

	/**
	 * Method which handles the keyboard control of the quiz
	 */
	logKey: function(e) {
		if(this.type === "simple"){
			if(this.state === "question"){
				if(e.keyCode == 13){
					this.showAnswer();
				}
			}else{
				if(e.keyCode == 49){
					this.notCorrect();
				}
				if(e.keyCode == 50){
					this.correct();
				}
			}
		}
		if(this.type === "multiple"){
			if(this.state === "question"){
				if(e.keyCode === 49){
					var pressed = this.multiple.get("choices")[0];
					pressed = escape(pressed);
					this.markAnswer(pressed);
				}
				if(e.keyCode === 50){
					var pressed = this.multiple.get("choices")[1];
					pressed = escape(pressed);
					this.markAnswer(pressed);
				}
				if(e.keyCode === 51){
					var choices = this.multiple.get("choices");
					if(choices.length >= 3){
						var pressed = this.multiple.get("choices")[2];
						pressed = escape(pressed);
						this.markAnswer(pressed);
					}					
				}
				if(e.keyCode === 52){
					var choices = this.multiple.get("choices");
					if(choices.length >= 4){
						var pressed = this.multiple.get("choices")[3];
						pressed = escape(pressed);
						this.markAnswer(pressed);
					}
				}
				if(e.keyCode == 16){
					this.checkAnswer();
				}
			}
			if(this.state === "answer"){
				if(e.keyCode == 13){
					this.nextMultipleQuestion();
				}
				
			}
		}
	},

	/**
	 * Loads the next question from the server
	 */
	nextQuestion: function(){
		
		//store question ID
		if(this.type === "simple"){
			this.questionId = this.simple.get("id");
		}else{
			this.questionId = this.multiple.get("id");
		}
		
		$this = this;
		$.post(this.baseUrl+"/learning/next", {csrf_token: this._csrf, question: this.questionId, catalog: this.catalogId, course: this.courseId, answer: this.answerBoolean, section: this.section}
		, function(data) {
			if(data.status==="Ok"){
				$this.setData(data);
			}
		}); 
		

	},

	/**
	 * Sets all relevant data of the new question. Is called on a successfull post-method call.
	 * 
	 * @param data: all data recieved from the post method
	 */
	setData: function(data){
		this.courseId = data.course;
		this.catalogId = data.catalog;
		this.type = data.type;
		this.state = "question";
		this.questionId = data.id;
		if(this.type !== "multiple"){
			this.simple = new simpleQuestion(data);
			$("#multiple").hide();
			$("#simple").show();
			$("#correct").hide();
			$("#questionSimple").html(this.simple.get("question"));
			$("#answerSimple").html("");
			$("#showAnswer").show();
		}else{
			this.multiple = new multipleQuestion(data);
			$("#simple").hide();
			$("#nextQuestion").hide();
			$("#multiple").show();
			$("#questionMultiple").html(this.multiple.get("question"))
			this.setAnswers(this.multiple.get("choices"));
		}
	},

	/**
	 * Shows the answer and the fields if the users knows the answer or not.
	 * Only nedded for simple question type
	 */
	showAnswer: function(){
		$("#answerSimple").html(this.simple.get("answer"));
		$("#showAnswer").hide();
		$("#correct").show();
		this.state = "answer";
	},

	/**
	 * Is called when the user knows the answer. 
	 * Calculates the percent of right questions and calls the nextQuestion function 
	 */
	correct: function(){		
		if(this.state !== "working"){
			this.state = "working";		
			if(this.type !== "simple"){
				$("#correct").hide();
			}	
			this.right++;
			this.number++;
			this.answerBoolean = true;
			this.percent = Math.round((100/this.number)*this.right);
			$(".percent").text(this.right + "/" + this.number + " - " + this.percent + "%");
			this.nextQuestion();
		}
		
	},

	/**
	 * Is called when the user doesn't know the answer. 
	 * Calculates the percent of right questions and calls the nextQuestion function 
	 */
	notCorrect: function(){
		if(this.state !== "working"){		
			this.state = "working";
			if(this.type !== "simple"){
				$("#correct").hide();
			}		
			this.number++;
			this.answerBoolean = false;
			this.percent = Math.round((100/this.number)*this.right);
			$(".percent").text(this.right + "/" + this.number + " - " + this.percent + "%");
			this.nextQuestion();
		}
	},
	
	/**
	 * Set all answers for a multiple choice question.
	 * 
	 * @param choices: all choices
	 */
	setAnswers: function(choices){
		this.state = "question";
		$("#checkAnswer").show();
		$("#choicesLeft").empty();
		$("#choicesRight").empty();
		this.multipleAnswers = new Array();
		for(var i = 0; i < choices.length; i++){
			var answer = choices[i];
			var button = '<br><button class="btn row-fluid answer" style="border-style:solid; border-width:thick;" value="'+answer+'"><p><br>'+answer+'<br></p></button><br>';
			if(i%2 === 0){
				$("#choicesLeft").append(button);
			}else{
				$("#choicesRight").append(button);
			}
		}
		this.render();
	},
	
	/**
	 * Check if the answer of the multiple choice question
	 * was right.
	 * 
	 * @param the event which was released. It contains the button which was pressed.
	 */
	checkAnswer: function(){
		$("#checkAnswer").hide();
		$("#nextQuestion").show();
		
		$rightAnswers = this.multiple.getRightAnswers();
		$choices = this.multiple.get("choices");
		
		//save all right ansers in $answers
		$answers = new Array();
		for(var i = 0; i < $rightAnswers.length; i++){
			$answers.push(this.multiple.get("choices")[$rightAnswers[i]]);
		}
		if(this.state !== "working"){
			
			//mark right and false answers			
			for(var i = 0; i < $choices.length; i++){
				//all not selected questions which area wrong
				if($.inArray($choices[i], $answers) === -1 && $.inArray($choices[i], this.multipleAnswers) === -1){
					$("button[value='"+$choices[i]+"']").attr("disabled", "disabled");
				}
				//all not selected questions which are true
				if($.inArray($choices[i], $answers) !== -1 && $.inArray($choices[i], this.multipleAnswers) === -1){
					$("button[value='"+$choices[i]+"']").attr("style", "border-style:solid; border-width:thick; border-color:green;");
					$("button[value='"+$choices[i]+"']").attr("disabled", "disabled");
				}
			}
			for(var i = 0; i < this.multipleAnswers.length; i++){
				//all selected questions which area wrong
				if($.inArray(this.multipleAnswers[i], $answers) !== -1){;
					$("button[value='"+this.multipleAnswers[i]+"']").attr("class", "btn btn-success row-fluid answer");
				}else{
					$("button[value='"+this.multipleAnswers[i]+"']").attr("class", "btn btn-danger row-fluid answer");
				}
			}
			
			//check if all answers are correct or not
			if($(this.multipleAnswers).not($answers).length == 0 && $($answers).not(this.multipleAnswers).length == 0){
				this.answerBoolean = true;
			}else{
				this.answerBoolean = false;
			}
			this.state = "answer";
		}
		
		
	},
	
	checkClick: function(event){
		var pressed = $(event.currentTarget).val();
		this.markAnswer(pressed);
	},
	
	/**
	 * Adds/removes the pressed button with a orange border and writes/removes it in/from an array.
	 * 
	 * @param pressed: the value of the button which was pressed
	 */
	markAnswer: function(pressed){
		console.log(pressed);
		if($.inArray(pressed, this.multipleAnswers) !== -1){
			this.multipleAnswers.splice($.inArray(pressed, this.multipleAnswers), 1);
			$("button[value='"+pressed+"']").attr("style", "border-style:solid; border-width:thick;");
		}else{
			$("button[value='"+pressed+"']").attr("style", "border-style:solid; border-width:thick; border-color:orange;");
			this.multipleAnswers.push(unescape(pressed));
		}
	},
	
	/**
	 * Loads the next multiple choice Question
	 */
	nextMultipleQuestion: function(){
		$("#nextQuestion").hide();
		if(this.answerBoolean){
			this.correct();
		}else{
			this.notCorrect();
		}
		
	}
	
	


});