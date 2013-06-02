/**
 * Backbone View of the test
 */
var TestView = Backbone.View.extend({

  initialize: function() {
	 this.right = 0;
	 this.questions = "";
	 this.number_questions = "";
	 this.name = "";
	 this.pos = 0;
	 this.percent = 0;
	 this.render();
  },

  el: 'body',
  render: function() {
	  $(document).ready(function() {
		  $("#correct").hide();
		  $("#finish").hide();
		  $("#showAnswer").hide();
		});
  },
  
  /**
   * Sets all relevant Quiz information from the Server to local variables and displays them
   * 
   * @param option contains the Answers, Questions, name and number of Questions for this Quiz
   */
  setData: function(option){
	  this.questions = option;
	  this.name = option.Name;
	  this.number_questions = option.Questions.length;
	  $("#name").text(this.name);
	  this.setText();
  },
  
  /**
   * Sets Question and Answer in the textareas
   */
  setText: function(){
	 $("#question").text(this.questions.Questions[this.pos].Question);
	 $("#showAnswer").show();
  },
  
  /**
   * Displays the next Question or the result if the quiz is over
   */
  nextQuestion: function(){
	  this.pos++;
	  if(this.number_questions != this.pos){
		  $("#question").text(this.questions.Questions[this.pos].Question);
		  $("#answer").text("");
		  $("#showAnswer").show();
		  $("#correct").hide();
	  }else{
		  $("#correct").hide();
		  $("#questionsAnswers").hide();
		  $("#right").text(this.right);
		  this.percent = (100/(this.number_questions))*this.right;
		  $("#result").text(this.percent + "%");
		  $("#finish").show();
	  }
	 
  },
  
  /**
   * Assigns which function is called on which button press
   */
  events: {
	  "click button[id=showAnswer]": "showAnswer",
	  "click button[id=yes]": "correct",
	  "click button[id=no]": "nextQuestion",
  },
  
  /**
   * Shows next Answer
   */
  showAnswer: function(){
	  $("#showAnswer").hide();
	  $("#correct").show();
	  $("#answer").text(this.questions.Questions[this.pos].Answer);
  },
  
  /**
   * Is called when the user knows the answer and increments the right counter
   */
  correct: function(){
	  this.right++;
	  this.nextQuestion();
  },
  
});