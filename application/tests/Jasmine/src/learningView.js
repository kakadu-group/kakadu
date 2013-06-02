/**
 * Backbone View of the test
 */
QuizView = Backbone.View.extend({


	/**
	 * Initializes zhe view with all data.
	 * 
	 * @param: course: the it of the course which the user is learning
	 * @param: url: the base url
	 * @param: section: course|catalog|favorite
	 * @param: question: the first question
	 * @param: catalog: the id of the catalog which the user is learning
	 */
	initialize: function(course, url, section, question, catalog) {
		this.section = section;
		console.log(this.section);
		this.courseId = course;
		this.baseUrl = url;
		this._csrf = $('input[name="csrf_token"]').val();
		this.right = 0;
		this.questionId = question.id;
		this.catalogId = catalog;
		this.answerBoolean = true;
		this.type = question.type;
		this.question = question.question;

		//writing the question and reading the answer
		if(this.type === "simple"){
			$("#multiple").hide();
			$("#questionSimple").text(this.question);
			$("#answerSimple").text("");
			this.answer = question.answer;
		}else{
			$("#simple").hide();
			$("#questionMultiple").text(this.question);
			console.log(question.choices);
			this.choices = question.choices;
			this.rightAnswer = question.answer;
			console.log(this.rightAnswer);
			this.setAnswers(this.choices);
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
		"click button[class='btn row-fluid answer']": "checkAnswer",

	},

	logKey: function(e) {
		if(this.state === "question" && this.type !== "multiple"){
			if(e.keyCode == 13){
				this.showAnswer();
			}
		}else{
			if(e.keyCode == 39){
				this.notCorrect();
			}
			if(e.keyCode == 37){
				this.correct();
			}
		}
		console.log(e.keyCode);
	},

	/**
	 * Loads the next question from the server
	 */
	nextQuestion: function(){	  

		$("#correct").hide();
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
	 * @param answer: the answer of the actual question
	 * @param id: the id of the question
	 */
	setData: function(data){
		console.log(data);
		this.courseId = data.course;
		this.catalogId = data.catalog;
		this.type = data.type;
		this.state = "question";
		this.questionId = data.id;
		this.question = data.question;
		if(this.type !== "multiple"){
			this.answer = data.answer;
			$("#multiple").hide();
			$("#simple").show();
			$("#correct").hide();
			$("#questionSimple").text(this.question);
			$("#answerSimple").text("");
			$("#showAnswer").show();
		}else{
			this.choices = data.choices;
			this.rightAnswer = data.answer;
			console.log(this.rightAnswer);
			$("#simple").hide();
			$("#multiple").show();
			$("#questionMultiple").text(this.question)
			this.setAnswers(this.choices);
		}
	},

	/**
	 * Shows the answer and the fields if the users knows the answer or not
	 */
	showAnswer: function(){
		$("#answerSimple").text(this.answer);
		$("#showAnswer").hide();
		$("#correct").show();
		this.state = "answer";
	},

	/**
	 * Is called when the user knows the answer. 
	 * Calculates the percent of right questions and calls the nextQuestion function 
	 */
	correct: function(){
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
	},

	/**
	 * Is called when the user doesn't know the answer. 
	 * Calculates the percent of right questions and calls the nextQuestion function 
	 */
	notCorrect: function(){
		this.state = "working";
		if(this.type !== "simple"){
			$("#correct").hide();
		}		
		this.number++;
		this.answerBoolean = false;
		this.percent = Math.round((100/this.number)*this.right);
		$(".percent").text(this.right + "/" + this.number + " - " + this.percent + "%");
		this.nextQuestion();
	},
	
	/**
	 * Set all answers for a multiple choice question.
	 * 
	 * @param choices: all choices
	 */
	setAnswers: function(choices){
		$("#choicesLeft").empty();
		$("#choicesRight").empty();
		for(var i = 0; i < choices.length; i++){
			var answer = choices[i];
			var button = '<br><button class="btn row-fluid answer" value="'+answer+'"><p><br>'+answer+'<br></p></button><br>';
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
	checkAnswer: function(event){
		console.log("checkAnswer");
		var pressed = $(event.currentTarget).val();
		console.log(pressed);
		console.log(this.rightAnswer);
		console.log(this.choices);
		if(this.state !== "working"){
			if(this.choices[this.rightAnswer] == pressed){
				console.log("Richtige Antwort");
				$(event.currentTarget).attr("class", "btn-success row-fluid");				
				this.correct();	
			}else{
				$(event.currentTarget).attr("class", "btn-danger row-fluid");
				this.notCorrect();	
			}
		}
		
	}
	
	


});