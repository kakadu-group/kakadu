
var url;
var groupId;
var text;
var _csrf = $('input[name="csrf_token"]').val();

/**
 * Sets the url, the group id and the text for the drag and drop
 * 
 * @param url2: base url
 * @param id: id of the group
 * @param text2: the text in the right language
 */
function initialise(url2, id, text2){	
	this.url = url2;
	this.groupId = id;
	this.text = text2;
}

/**
 * Sends on every keypress in the search form an ajax request to the server
 * with the string typed in the search form.
 */
$(document).ready(function(){
	$("#search").keyup(function() {
		$('#alreadyInGroup').hide();
		$('#user_added').hide();
		var search_query = $('#search').val();
		$.post(url+"/users/search", {csrf_token: _csrf, search: search_query}
		, function(data) {
			displaydata(data);
		});
	
	});
});

/**
 *  Displays all users in a table below the search form
 *  
 * @param data: the data from the ajax request (all users who match with the given search query)
 */
function displaydata(data){
	$('#user_search').empty();
	if(data.status === "Ok"){
		if(data.users.length != 0){
			$('#notfound').hide();
			$('#table').show();
			var table = "";
			for(var i = 0; i < data.users.length; i++){
				var help = data.users[i].email;
				var help2 = "'"+data.users[i].email+"'";
				var help3 = "'"+data.users[i].displayname+"'";
				table = '<tr draggable="true" ondragstart="drag(event)" id="'+help+'" title="Drag to add">'+
						'<td>'+data.users[i].displayname+'</td>'+
						'<td>'+help+'</td>'+
						'<td><button id='+i+' title="Add" onclick="addUser('+help2+','+i+');return false;" class="btn-primary"><i class="icon-plus icon-white"></i></button></td>'+
						'<td><button id="addAdmin'+i+'" title="Add as admin" onclick="addAdmin('+help2+','+help3+','+i+');return false;" class="btn-primary"><i class="icon-user-md icon-white"></i></button></td>'+
						'<td><button title="Invite" onclick="inviteUser('+help2+');return false;" class="btn-primary"><i class="icon-envelope icon-white"></i></button></td>'+
						'</tr>';
				$('#user_search').append(table);
			}
		}else{
			$('#notfound').show();
			$('#table').hide();
		}
	}else{
		$('#notfound').hide();
		$('#table').hide();
	}
}

/**
 * Add the given user to the actual group
 * 
 * @param userEmail: the email address of the user that is added to the group.
 * @param id: the id of the button that was pressed
 */
function addUser(userEmail, id){
	$('#alreadyInGroup').hide();
	$('#user_added').hide();
	
	$("#"+id).html('<i class="icon-spinner icon-spin icon-white"></i>');
	$('#dragGoal').html('<td></td><td><i class="icon-spinner icon-spin icon-4x icon-white"></i></td><td></td>');
	$('#dragGoal').show();
	
	$.post(url+"/group/user/add", {csrf_token: _csrf, id: groupId, user: userEmail}
	, function(data) {
		if(data.status === "Error" || data.status == "Info"){
			$('#alreadyInGroup').show();
		}
		if(data.status === "Ok"){
			//$('#user_added').show();
			
			$.get(url+"/group/"+groupId, function(data){
				$("#members").html(data);
				$('#dragGoal').hide();
			});
			
			
		}
		
		$("#"+id).html('<i class="icon-plus icon-white"></i>');
		$('#dragGoal').html('<td></td><td><h3>'+text+'</h3></td><td></td>');
		$('#dragGoal').hide();
		
	});
	
	
	
}

/**
 * Delets a given user from the group.
 * 
 * @param userEmail: the email address of the user that is deleted from the group.
 * @param id: the id of the button which was pressed.
 */
function deleteUser(userEmail, id){
	
	$("."+id).find("#button").html('<i class="icon-spinner icon-spin icon-white"></i>');
	
	$.post(url+"/group/user/remove", {csrf_token: _csrf, id: groupId, user: userEmail}
	, function(data) {
		if(data.status === "Ok"){
			$('#userDeleted').show();
			$("."+id).wrapInner("<del></del>");
			$("."+id).find("#button").html('<i class="icon-undo icon-white"></i>');
			$("."+id).find("#button").attr("title", "Undo");
			$("."+id).find("#button").attr("onclick", "addUser('"+userEmail+"');return false;");
			$("."+id).find("#button").attr("class", "btn-success");
		}
		if(data.status === "Error" || data.status === "Info"){
			$('#notDeleted').show();
			$("."+id).find("#button").html('<i class="icon-remove icon-white"></i>');
		}
	});
	
}

/**
 * Sends a adding-request to the server
 * 
 * @param group: the id of the group
 */
function requestMember(groupId){
	
	$.post(url+"/group/request", {csrf_token: _csrf, group: groupId}
	, function(data) {
		console.log(data);
	});
}


/**
 * Invites a user to come to join the group.
 * 
 * @param userEmail: Email address of the user who is invited
 */
function inviteUser(userEmail){
	
	console.log(userEmail);
	
	$.post(url+"/group/invite", {csrf_token: _csrf, group: groupId, user:userEmail}
	, function(data) {
		console.log(data);
	});
}

/**
 * Prevents the default event
 * 
 * @param ev: the event
 */
function allowDrop(ev){
	ev.preventDefault();
}

/**
 * Dispays all informations when a user is dragged and stores the users
 * email address in the event.
 * 
 * @param ev: the event
 */
function drag(ev){
	$('#dragGoal').show();
	ev.dataTransfer.setData("Email",ev.target.id);
}

/**
 * Calls the addUser method, when the user is dropped on the
 * rigth place
 * 
 * @param ev: the event with the user email address
 */
function drop(ev){
	//$('#dragGoal').html('<i class="icon-spinner icon-spin icon-4x icon-white"></i>');
	var data=ev.dataTransfer.getData("Email");
	addUser(data);
}

/**
 * Adds a user as admin of the group
 * 
 * @param userEmail: the email address of the user who is added as admin
 * @param userName: the displayname of the user who is added as admin
 * @param id: the id of the button that was pressed
 */
function addAdmin(userEmail, userName,  id){
	$("#addAdmin"+id).html('<i class="icon-spinner icon-spin icon-white"></i>');
	
	$.post(url+"/group/admin/add", {csrf_token: _csrf, id: groupId, user: userEmail}
	, function(data){
		if(data.status === "Ok"){
			help = "'"+userEmail+"'";
			help2 = "'"+userName+"'";
			$("#adminTable").append('<tr class="admin'+userName+'">'+
									'<td>'+userName+'</td>' +
									'<td>'+userEmail+'</td>' +
									'<td><button id="admin'+userName+'" onclick="removeAdmin('+help+', '+help2+');return false" class="btn-danger btn-mini" ><i class="icon-remove icon-white"></i></button></td>'+
									'</tr>');
			$("#admin_added").show();
			$("#addAdmin"+id).html('<i class="icon-user-md icon-white"></i>');
		}else{
			$('#admin_not_added').show();
			$("#addAdmin"+id).html('<i class="icon-user-md icon-white"></i>');
		}
		
		
		
	});
}

/**
 * Removes a admin from the group
 * 
 * @param userEmail: the email address of the user who is removed
 * @param id: the id of the button which was pressed
 */
function removeAdmin(userEmail, id){
	
	$("#admin"+id).html('<i class="icon-spinner icon-spin icon-white"></i>');	
	
	$.post(url+"/group/admin/remove", {csrf_token: _csrf, id: groupId, user: userEmail}
	, function(data){
		if(data.status === "Ok"){
			$(".admin"+id).remove();
		}else{
			$('#admin_not_deleted').show();
			$("#admin"+id).html('<i class="icon-remove icon-white"></i>');	
		}
		
	});
	
}

