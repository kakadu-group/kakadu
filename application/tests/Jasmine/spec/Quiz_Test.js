describe("Client Quiz Test", function() {
	
	beforeEach(function() {
		
		//creates the TestView
		this.view = new TestView();
		
		//Data to test
		var data = {
				"Name":"Course1",
				"Questions":[
				             {"Question":"Question1", "Answer":"Answer1"},
				             {"Question":"Question2", "Answer":"Answer2"},
				             {"Question":"Question3", "Answer":"Answer3"},
				             ]
		};
		
		//Sets the data recieved from the server (is tested below)
		this.view.setData(data);
		
	});
	
	/**
	 * Tests if the data is set correctly on the local variables
	 */
	it("should set the data recieved from the server", function(){
		expect(this.view.name).toEqual("Course1");
		expect(this.view.questions.Questions[0].Question).toEqual("Question1");
		expect(this.view.questions.Questions[0].Answer).toEqual("Answer1");
		expect(this.view.questions.Questions[1].Question).toEqual("Question2");
		expect(this.view.questions.Questions[1].Answer).toEqual("Answer2");
		expect(this.view.questions.Questions[2].Question).toEqual("Question3");
		expect(this.view.questions.Questions[2].Answer).toEqual("Answer3");
		expect(this.view.number_questions).toEqual(3);
	});
  
	/**
	 * Tests if the increment counter is incremented correctly
	 */
	it("should increment the right counter", function() {
		this.view.correct();
		expect(this.view.right).toEqual(1);
	});
	
	/**
	 * Tests if the position counter is incremented
	 */
	it("should increment the position counter", function() {
		this.view.nextQuestion();
		expect(this.view.pos).toEqual(1);
	});
	
	/**
	 * Test the output at finish of the Quiz
	 */
	it("should display the result when the quiz is finished", function() {
		this.view.correct();
		this.view.correct();
		this.view.correct();
		expect(this.view.right).toEqual(3);
		expect(this.view.percent).toEqual(100);
	});
	
	/**
	 * Test the output at finish of the Quiz (part2)
	 */
	it("should display the result when the quiz is finished 2", function() {
		this.view.correct();
		this.view.correct();
		this.view.nextQuestion();
		expect(this.view.right).toEqual(2);
		expect(this.view.percent).toEqual(66.6666666666666667);
	});
  
});