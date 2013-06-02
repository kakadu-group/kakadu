
var url;
var groupId;
var text;
var _csrf;

/**
 * Sets the url, the group id and the text for the drag and drop
 * 
 * @param url2: base url
 * @param id: id of the group
 * @param text2: the text in the right language
 */
function initialiseAddUser(url2, id, text2){	
	this.url = url2;
	this.groupId = id;
	this.text = text2;
	this._csrf = $('input[name="csrf_token"]').val();
}

/**
 * Sends on every keypress in the search form an ajax request to the server
 * with the string typed in the search form.
 */
$(document).ready(function(){
	$("#searchUser").keyup(function() {
		$('#alreadyInGroup').hide();
		$('#user_added').hide();
		var search_query = $('#searchUser').val();
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
			$('#users').show();
			var table = "";
			for(var i = 0; i < data.users.length; i++){
				var help = data.users[i].email;
				var help2 = "'"+data.users[i].email+"'";
				var help3 = "'"+data.users[i].displayname+"'";
				table = '<tr draggable="true" ondragstart="drag(event)" id="'+help+','+help3+'" title="Drag to add">'+
						'<td>'+data.users[i].displayname+'</td>'+
						'<td>'+help+'</td>'+
						'<td><button id='+i+' title="Add" onclick="addUser('+help2+','+i+');return false;" class="btn-primary"><i class="icon-plus icon-white"></i></button></td>'+
						'<td><button id="addAdmin'+i+'" title="Add as admin" onclick="addAdmin('+help2+','+help3+','+i+');return false;" class="btn-primary"><i class="icon-user-md icon-white"></i></button></td>'+
						'</tr>';
				$('#user_search').append(table);
			}
		}else{
			$('#notfound').show();
			$('#notfound').delay(2000).fadeOut("slow", function () { $(this).hide(); });
			$('#users').hide();
		}
	}else{
		$('#notfound').hide();
		$('#users').hide();
	}
}

/**
 * Add the given user to the actual group
 * 
 * @param userEmail: the email address of the user that is added to the group.
 * @param id: the id of the button that was pressed
 */
function addUser(userEmail, id){
	$('#dragGoalAdmin').hide();
	$('#alreadyInGroup').hide();
	$('#user_added').hide();
	
	$("#"+id).html('<i class="icon-spinner icon-spin icon-white"></i>');
	$('#dragGoal').html('<td></td><td><i class="icon-spinner icon-spin icon-4x icon-white"></i></td><td></td>');
	$('#dragGoal').show();
	
	$.post(url+"/group/user/add", {csrf_token: _csrf, id: groupId, user: userEmail}
	, function(data) {
		if(data.status === "Error" || data.status == "Info"){
			$('#alreadyInGroup').show();
			$('#alreadyInGroup').delay(2000).fadeOut("slow", function () { $(this).hide(); });
		}
		if(data.status === "Ok"){
			//$('#user_added').show();
			
			$.get(url+"/group/"+groupId, function(data){
				$("#members").html(data);
				$('#dragGoal').hide();
			});
			
			
		}
		
		$("#"+id).html('<i class="icon-plus icon-white"></i>');
		$('#dragGoal').html('<td></td><td><h4>'+text+'</h4></td><td></td>');
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
			$('#userDeleted').delay(2000).fadeOut("slow", function () { $(this).hide(); });
			$("."+id).wrapInner("<del></del>");
			$("."+id).find("#button").html('<i class="icon-undo icon-white"></i>');
			$("."+id).find("#button").attr("title", "Undo");
			$("."+id).find("#button").attr("onclick", "addUser('"+userEmail+"');return false;");
			$("."+id).find("#button").attr("class", "btn-success");
			updateAdmins(userEmail);
		}
		if(data.status === "Error" || data.status === "Info"){
			$('#notDeleted').show();
			$('#notDeleted').delay(2000).fadeOut("slow", function () { $(this).hide(); });
			$("."+id).find("#button").html('<i class="icon-remove icon-white"></i>');
		}
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
	$('#dragGoalAdmin').show();
	var values = ev.target.id.split(',');
	var email = values[0];
	var name = values[1].replace("'", "");
	name = name.replace("'", "");
	console.log(email);
	console.log(name);
	ev.dataTransfer.setData("Email",email);
	ev.dataTransfer.setData("Name",name);
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
 * Calls the addAdmin method, when the user is dropped on the
 * rigth place
 * 
 * @param ev: the event with the user email address
 */
function dropAdmin(ev){
	//$('#dragGoal').html('<i class="icon-spinner icon-spin icon-4x icon-white"></i>');
	var email = ev.dataTransfer.getData("Email");
	var name = ev.dataTransfer.getData("Name");
	addAdmin(email, name);
}

/**
 * Adds a user as admin of the group
 * 
 * @param userEmail: the email address of the user who is added as admin
 * @param userName: the displayname of the user who is added as admin
 * @param id: the id of the button that was pressed
 */
function addAdmin(userEmail, userName,  id){
	$('#dragGoal').hide();
	$("#addAdmin"+id).html('<i class="icon-spinner icon-spin icon-white"></i>');
	$('#dragGoalAdmin').html('<td></td><td><i class="icon-spinner icon-spin icon-4x icon-white"></i></td><td></td>');
	
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
			$("#admin_added").delay(2000).fadeOut("slow", function () { $(this).hide(); });
			$("#addAdmin"+id).html('<i class="icon-user-md icon-white"></i>');
			$('#dragGoalAdmin').html('<td></td><td><h4>'+text+'</h4></td><td></td>');
			$('#dragGoalAdmin').hide();
			reloadMembers();
		}else{
			$('#admin_not_added').show();
			$('#admin_not_added').delay(2000).fadeOut("slow", function () { $(this).hide(); });
			$("#addAdmin"+id).html('<i class="icon-user-md icon-white"></i>');
			$('#dragGoalAdmin').html('<td></td><td><h4>'+text+'</h4></td><td></td>');
			$('#dragGoalAdmin').hide();
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
			$('#admin_not_deleted').delay(2000).fadeOut("slow", function () { $(this).hide(); });
			$("#admin"+id).html('<i class="icon-remove icon-white"></i>');	
		}
		
	});
	
}


function reloadMembers(){
	$.get(url+"/group/"+groupId, function(data){
		$("#members").html(data);
		$("#dragGoal").hide();
	});
}

function updateAdmins(userEmail){
	console.log("dsfaads");
	var search = userEmail;
    $("#adminTable tr td").filter(function() {
        return $(this).text() == search;
    }).parent('tr').remove();
}

