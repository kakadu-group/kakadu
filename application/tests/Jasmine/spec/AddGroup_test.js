describe("Adding course to group test", function() {
	
	beforeEach(function(){
        jasmine.getFixtures().fixturesPath = '/kakadu/kakadu/application/tests/Jasmine/src';
        jasmine.getFixtures().load('course_create.html');
        
    });
	
	/**
	 * Tests if the values are initialized correctly
	 */
	it("should initialise the value", function() {
		var url = "testurl";
		initialiseAddGroup(url, false);
        expect(url).toEqual("testurl");  
    });
	
	/**
	 * Tests if the given Entries are displayed correctly
	 */
	it("should display the given entries", function() {
		var data = {
				"status":"Ok",
				"groups":[
				             {"id":"1", "name":"Group1", "description":"Description1"},
				             {"id":"2", "name":"Group2", "description":"Description2"},
				             ]
		};
		displaydata(data);
        expect($('#groupsTable').is(':visible')).toEqual(true); 
        expect($('#groups_search').text()).toEqual("Group1Description1Group2Description2");   
    });
	
	/**
	 * Tests if it is displayed that no user was found
	 */
	it("should not display any entries", function() {
		var data = {
				"status":"Ok",
				"groups":[]
		};
		displaydata(data);
        expect($('#table').is(':visible')).toEqual(false); 
        expect($('#nothingFound').is(':visible')).toEqual(true); 
        expect($('#groups_search').text()).toEqual("");   
    });
	
	it("should remove the group reference", function(){
		text = "Search learngroup";
		removeReference(text);
		expect($("#groupId").val()).toEqual('');
		expect($("#searchGroup").val()).toEqual('');
		expect($("#searchGroup")).toHaveAttr('placeholder', text);
		
	});

});