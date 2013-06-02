describe("List sorting test", function() {
	
	beforeEach(function(){
        jasmine.getFixtures().fixturesPath = '/kakadu/kakadu/application/tests/Jasmine/src';
        jasmine.getFixtures().load('courses.html');
    });
	
    it("should go to  page 3", function() {
    	var url = "http://localhost/kakadu/kakadu/public/courses";
        change_page(3, url);
        expect(current_page).toEqual(3);        
    });
    
    it("should go to the previous page", function() {
    	var url = "http://localhost/kakadu/kakadu/public/courses";
    	change_page(3, url);
        change_page(0, url);
        expect(current_page).toEqual(2);        
    });
    
    it("should go to the next page", function() {
    	var url = "http://localhost/kakadu/kakadu/public/courses";
    	change_page(3, url);
        change_page(-1, url);
        expect(current_page).toEqual(4);
        
    });
    
    it("should not go to the next page, cause we are on the last page", function() {
    	var url = "http://localhost/kakadu/kakadu/public/courses";
    	change_page(4, url);
        change_page(-1, url);
        expect(current_page).toEqual(4);
        
    });
    
    it("should not go to the previous page, cause we are on the first page", function() {
    	var url = "http://localhost/kakadu/kakadu/public/courses";
    	change_page(1, url);
        change_page(0, url);
        expect(current_page).toEqual(1);
        
    });
    
    it("should sort by name, id and create date", function() {
    	var url = "http://localhost/kakadu/kakadu/public/courses";
    	expect(sort).toEqual("name");
    	sorting("sort", "id",  url);
    	expect(sort).toEqual("id");
    	sorting("sort", "created_at",  url);
    	expect(sort).toEqual("created_at");
        
    });
    
    it("should show 10, 20, 30, 40 and 50 items per page", function() {
    	var url = "http://localhost/kakadu/kakadu/public/courses";
    	expect(number).toEqual('20');
    	sorting("number", 10,  url);
    	expect(number).toEqual(10);
    	sorting("number", 50,  url);
    	expect(number).toEqual(50);
    	sorting("number", 30,  url);
    	expect(number).toEqual(30);
    	sorting("number", 40,  url);
    	expect(number).toEqual(40);
        
    });
    
    it("should show the list ascending and descending", function() {
    	var url = "http://localhost/kakadu/kakadu/public/courses";
    	expect(sort_dir).toEqual("asc");
    	sorting("order", "desc",  url);
    	expect(sort_dir).toEqual("desc");
        
    });
});