var url;
var learn;


/**
 * Initialises the base url and the word learn
 * @param urlBase: the base url
 * @param textLearn: the word learn in the rigth language
 */
function initialiseFavorites(urlBase, textLearn){
	this.url = urlBase;
	this.learn = textLearn;
}

/**
 * Adds a course to the favorites of the user
 * 
 * @param idCourse: The id of the course
 */
function addFavoriteCourse(idCourse){
	$.post(url+"/favorites/add", {csrf_token: $('input[name="csrf_token"]').val(), id: idCourse, type: "course"}
	, function(data) {
		console.log(data);
		if(data.status === 'Ok'){
			$('#added').show();
			$('#added').delay(2000).fadeOut("slow", function () { $(this).hide(); });
			$('#favorite'+idCourse).attr("onclick", "linktest("+idCourse+")");
			$('#favorite'+idCourse).text(learn);
		}
	});	
}

/**
 * Remove a course from the favorites of the user
 * 
 * @param idCourse: The id of the course
 */
function removeFavoriteCourse(idCourse){
	$.post(url+"/favorites/remove", {csrf_token: $('input[name="csrf_token"]').val(), id: idCourse, type: "course"}
	, function(data) {
		console.log(data);
		if(data.status === "Ok"){
			$("#course"+idCourse).remove();
		}
	});	
}

/**
 * Adds acatalog to the favorites of a user
 * @param idCatalog: The id of the catalog
 */
function addFavoriteCatalog(idCatalog){
	$.post(url+"/favorites/add", {csrf_token: $('input[name="csrf_token"]').val(), id: idCatalog, type: "catalog"}
	, function(data) {
		console.log(data);
		if(data.status === 'Ok'){
			$('#added').show();
			$('#added').delay(2000).fadeOut("slow", function () { $(this).hide(); });
			$('#favorite'+idCatalog).attr("onclick", "linkTestCatalog("+idCatalog+")");
			$('#favorite'+idCatalog).text(learn);
		}
	});	
}

/**
 * Removes a catalog from the favorites of a user
 * @param idCatalog: The id of the catalog
 */
function removeFavoriteCatalog(idCatalog){
	$.post(url+"/favorites/remove", {csrf_token: $('input[name="csrf_token"]').val(), id: idCatalog, type: "catalog"}
	, function(data) {
		console.log(data);
		if(data.status === "Ok"){
			$("#catalog"+idCatalog).remove();
		}
	});	
}



/**
 * Links to the Quiz of the course
 * 
 * @param id: id of the course
 */
function linktest(id){
	window.location.href=url+"/course/"+id+"/learning";	
}

/**
 * Links to the Quiz of the catalog
 * @param id: id of the catalog
 */
function linkTestCatalog(id){
	window.location.href=url+"/catalog/"+id+"/learning";	
}