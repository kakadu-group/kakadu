
var url;
var _csrf;
var groups;
var groupsSet

/**
 * Sets the url, and if groups are referenced to the course.
 * 
 * @param url2: base url
 * @param set: true if groups are referenced
 */
function initialiseAddGroup(url2, set){	
	this.url = url2;
	this.groupsSet = set;
	this._csrf =  $('input[name="csrf_token"]').val();
	this.groups = [];
	if(this.groupsSet){
		$("#tabelReferences").show();
		var allReferences = $(":input[name='groups[]']");
		for(var i = 0; i < allReferences.length; i++){
			var test = allReferences[i];
			console.log(test.value);
			this.groups.push(parseInt(test.value));
		}
	}
	console.log(this.groups);
}


/**
 * Sends on every keypress in the search form an ajax request to the server
 * with the string typed in the search form.
 */
$(document).ready(function(){
	$("#nothingFound").hide();
	$("#groupsTable").hide();
	$("#inList").hide();
	$("#tabelReferences").hide();
	
	$("#searchGroup").keyup(function() {
		var search_query = $('#searchGroup').val();
		
		if(search_query.length >= 1){
			$.post(url+"/groups/search", {csrf_token: _csrf, search: search_query}
			, function(data) {
				displaydata(data);
			});
		}else{
			$("#groupsTable").hide();
		}
		
	
	});
	
	//Hides the alerts when clicking close
	$('.alert .close').live('click',function(){
		$(this).parent().hide();
	});
});

/**
 * Shows all groups which match with the text typed in the search from
 * 
 * @param data: the data given from the server(all groups which match with the given string)
 */
function displaydata(data){
	$('#groups_search').empty();
	if(data.status === "Ok" && data.groups.length >= 1){
		$("#nothingFound").hide();
		$("#groupsTable").show();
		for(var i = 0; i < data.groups.length; i++){
			help = data.groups[i].id;
			table = '<tr>'+
				'<td id='+help+'>'+data.groups[i].name+'</td>'+
				'<td>'+data.groups[i].description+'</td>'+
				'<td><button title="Add" onclick="addGroup('+help+');return false;" class="btn-primary"><i class="icon-plus icon-white"></i></button></td>'+
				'</tr>';
			$('#groups_search').append(table);
		}
	}else{
		$("#nothingFound").show();
		$('#nothingFound').delay(2000).fadeOut("slow", function () { $(this).hide(); });
		$("#groupsTable").hide();
	}	
}

/**
 * Writes the group id in the hidden group field of the "create Course" form.
 * 
 * @param id: the id of the group
 */
function addGroup(id){
	console.log(id);
	if(jQuery.inArray(id, this.groups) === -1){
		this.groups.push(id);		
		var field = '<input type="hidden" name="groups[]" value='+id+' />';
		$("#courseCreate").append(field);
		var link = url+"/group/"+id
		var entry = "<tr id=reference"+id+">" +
					"<td><a href='"+link+"'>"+$("#"+id).text()+"</a></td> " +
					"<td><button onclick='removeReference("+id+")' class='btn-danger btn-mini'><i class='icon-remove'></i></button></td>" +
					"</tr>";
		$("#referencedCourses").append(entry);
		$("#tabelReferences").show();
		console.log(this.groups);
	}else{
		$("#inList").show();
		$('#inList').delay(2000).fadeOut("slow", function () { $(this).hide(); });
	}
}

/**
 * Removes the referenced group from the array
 * 
 * @param id: the group id
 */
function removeReference(id){
	console.log(id);
	if(jQuery.inArray(id, this.groups) !== -1){
		this.groups.splice( $.inArray(id,this.groups) ,1 );
		$("input[value="+id+"]").remove();
		$("#reference"+id).remove();
		if(this.groups.length === 0){
			$("#labelReferences").hide();
		}
	}
}

function test(){
	$.post(url+"/course/create", {csrf_token: _csrf, name: "dsfadsafdsa", description: "dsfadsafdsadsfadsafdsadsfadsafdsadsfadsafdsadsfadsafdsadsfadsafdsa", groups: this.groups}
	, function(data) {
		$("body").html(data);
	});
}
