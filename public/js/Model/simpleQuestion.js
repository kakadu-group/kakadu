/**
 * Model for a simple question
 */
simpleQuestion = Backbone.Model.extend({
	
	 initialize: function(data) {
		 this.id = data.id;
		 this.type = data.type;
		 this.question = data.question;
		 this.answer = data.answer;
		 
	 },

	printData: function(){
		 console.log("ID:" + this.id);
		 console.log("Type:" + this.type);
		 console.log("Question:" + this.question);
		 console.log("Right Answer:" + this.answer);
	}
	 
	
});