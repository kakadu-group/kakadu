describe("Learning test", function() {
	
	beforeEach(function(){
		jasmine.getFixtures().fixturesPath = '/kakadu/kakadu/application/tests/Jasmine/src';
        jasmine.getFixtures().load('learning.html');
        
        var question = {
        		"coures":"1",
        		"catalog":"1",
        		"id":"1",
        		"type":"simple",
				"question":"Question1",
				"answer":"Answer1",				
		};
		
		this.view = new QuizView(1, "baseUrl", "Favorites", question, 1);
		
	});
	
	it("should render the view", function() {
		this.view.render();
		expect($('#showAnswer').is(':visible')).toEqual(true); 
		expect($('#correct').is(':visible')).toEqual(false); 
		expect($('#finish').is(':visible')).toEqual(false);
		
	});
	
	it("should show the answer", function() {
		this.view.showAnswer();
		expect($('#showAnswer').is(':visible')).toEqual(false); 
		expect($('#correct').is(':visible')).toEqual(true);
		expect($('#answerSimple').text()).toEqual("Answer1");	
		
	});
	
	it("should count as correct answer", function() {
		this.view.correct();
		expect($('#correct').is(':visible')).toEqual(false);
		expect($('.percent').text()).toEqual("1/1 - 100%1/1 - 100%");
		expect(this.view.percent).toEqual(100);
		expect(this.view.right).toEqual(1);
		expect(this.view.number).toEqual(1);
		expect(this.view.answerBoolean).toEqual(true); 
		
	});
	
	it("should count as incorrect answer", function() {
		this.view.notCorrect();
		expect($('#correct').is(':visible')).toEqual(false);
		expect($('.percent').text()).toEqual("0/1 - 0%0/1 - 0%");
		expect(this.view.percent).toEqual(0);
		expect(this.view.right).toEqual(0);
		expect(this.view.number).toEqual(1);
		expect(this.view.answerBoolean).toEqual(false); 
		
	});
	
	it("should load the next question", function() {
		this.view.nextQuestion();
		expect($('#correct').is(':visible')).toEqual(false);
		expect($('#answer').text()).toEqual("");
		expect($('#showAnswer').is(':visible')).toEqual(true);
	});
	
	it("should set the data recieved from the post-method", function() {
		var question = {
        		"coures":"1",
        		"catalog":"1",
        		"id":"2",
        		"type":"simple",
				"question":"Question2",
				"answer":"Answer2",				
		};
		this.view.setData(question);
		expect(this.view.answer).toEqual("Answer2");
		expect(this.view.questionId).toEqual("2");
		expect($('#questionSimple').text()).toEqual("Question2");
		expect($('#answerSimple').text()).toEqual("");
		
	});
	
	it("should set the data recieved from the post-method - multiple choice", function() {
		var question2 = {
        		"coures":"1",
        		"catalog":"1",
        		"id":"3",
        		"type":"multiple",
				"question":"Question3",
				"answer":"1",		
				"choices":[
				           "Answer1",
				           "Answer2",
				           "Answer3",
				           
				           ]
		};
		this.view.setData(question2);
		expect(this.view.question).toEqual("Question3");
		expect(this.view.rightAnswer).toEqual("1");
		expect(this.view.questionId).toEqual("3");
		expect(this.view.choices).toEqual([ 'Answer1', 'Answer2', 'Answer3' ]);
		expect($('#questionMultiple').text()).toEqual("Question3");
		expect($('#choicesRight').text()).toEqual("Answer2");
		expect($('#choicesLeft').text()).toEqual("Answer1Answer3");
	});
	
	
	
	
	
});