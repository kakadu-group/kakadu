describe("User adding test", function() {
	
	beforeEach(function(){
        jasmine.getFixtures().fixturesPath = '/kakadu/kakadu/application/tests/Jasmine/src';
        jasmine.getFixtures().load('group.html');
        
    });
	
	/**
	 * Tests if the values are initialized correctly
	 */
	it("should initialise the values", function() {
		var url = "testurl";
		var id = 1;
		initialise(url, id);
        expect(url).toEqual("testurl"); 
        expect(id).toEqual(1);   
    });
	
	/**
	 * Tests if the given Entries are displayed correctly
	 */
	it("should display the given entries", function() {
		var data = {
				"status":"Ok",
				"users":[
				             {"id":"1", "displayname":"Admin", "email":"admin@example.com"},
				             {"id":"2", "displayname":"User", "email":"user@example.com"},
				             ]
		};
		displaydata(data);
        expect($('#users').is(':visible')).toEqual(true); 
        expect($('#user_search').text()).toEqual("Adminadmin@example.comUseruser@example.com");   
    });
	
	
	/**
	 * Test if it is displayed that no user was found
	 */
	it("should not display any entries", function() {
		var data = {
				"status":"Ok",
				"users":[]
		};
		displaydata(data);
        expect($('#table').is(':visible')).toEqual(false); 
        expect($('#notfound').is(':visible')).toEqual(true); 
        expect($('#user_search').text()).toEqual("");   
    });
	
	/**
	 * Test if the table is hidden when we get an error from the server
	 */
	it("should not display any entries (2)", function() {
		var data = {
				"status":"Error",
		};
		displaydata(data);
        expect($('#table').is(':visible')).toEqual(false); 
        expect($('#notfound').is(':visible')).toEqual(false); 
        expect($('#user_search').text()).toEqual("");   
    });
	
	
});