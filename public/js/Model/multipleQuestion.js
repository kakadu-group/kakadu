/**
 * Model for a multiple choice question
 */
multipleQuestion = Backbone.Model.extend({
	
	 initialize: function(data) {
		 this.id = data.id;
		 this.type = data.type;
		 this.question = data.question;
		 this.choices = data.choices;
		 this.rightAnswers = data.answer;
		 this.mixEntries();
		 //this.printData();
	 },
	 
	 printData: function(){
		 console.log("ID:" + this.id);
		 console.log("Type:" + this.type);
		 console.log("Question:" + this.question);
		 console.log("Choices:" + this.choices);
		 console.log("Right Answers:" + this.rightAnswers);
	 },
	 
	 getRightAnswers: function(){
		 return this.rightAnswers;		 
	 },
	 
	 mixEntries: function(){
		 $answers = new Array();
		 console.log("Richtig:" + this.rightAnswers);
		 for(var i = 0; i < this.rightAnswers.length; i++){
			 $answers.push(this.choices[this.rightAnswers[i]]);
		 }
		 this.choices = this.choices.sort(function() { return 0.5 - Math.random();});
		 var rightNew = new Array();
		 console.log("Answers: dsfa" + $answers);
		 console.log("Choices:" + this.choices);
		 for(var i = 0; i < $answers.length; i++){
			 var index = $.inArray($answers[i], this.choices);
			 if(index !== -1){
				 rightNew.push(index);
			 }			 
		 }
		 this.rightAnswers = rightNew;
	 }
});