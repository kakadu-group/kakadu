
//Variables for list-sorting
var number = "20";
var sort_dir = "asc";
var sort = "name";
var current_page = "1";


//Initialises the pagination links, that the pages are loaded dynamically
//It prevents the links from linking to th ehref argument and adds them a onclick event with the page number
function initialise_links(url){
	
	$(".pagination").addClass('pagination-centered');

	//Find all links and prevent them from redirecting
	$(".pagination").find("a").click(function(){return false;});
	
	var length = $(".pagination").find("a").size();

	//Set the onclick event for the previous page link
	$(".pagination").find("a").eq(0).attr("onclick", "change_page("+0+",'"+ url+"')");

	//Set the onclick event for the next page link
	$(".pagination").find("a").eq(length-1).attr("onclick", "change_page("+-1+",'" + url+"')");

	//Set the onclick event for the other links
	for(var i = 1; i < length-1; i++){
		$(".pagination").find("a").eq(i).attr("onclick", "change_page("+i+",'" + url+"')");
	}
}

//Changes the page in the list
function change_page(page, url){
	
	//If a page links was pressed
	if(page != -1 && page != 0){
		current_page = page;
	}
	
	//if the previous link is clicked
	if(page == 0 &&  current_page > 1){
		current_page--;
	}
	
	//if the next link is pressed
	var length = $(".pagination").find("a").size()-2;
	console.log("Length: " + length);
	if(page == -1 && current_page < length){
		console.log("get");
		current_page++;
	}
	
	console.log("Page: " + current_page);

	var url2 = url+"?page="+current_page+"&per_page="+number+"&sort="+sort+"&sort_dir="+sort_dir;

	$.get(url2, function(data) {
		 $('#list').html(data);
		 initialise_links(url);
	});
	
	
}

//Function that sorts the list by the arguments selected by the user
function sorting(method, value, url){

	//Set the selected Value by the user
	if(method == "number"){
		number = value;
		current_page = 1;
	}
	if(method == "sort"){
		sort = value;
		current_page = 1;
	}
	if(method == "order"){
		sort_dir = value;
		current_page = 1;
	}

	var url2 = url+"?page="+current_page+"&per_page="+number+"&sort="+sort+"&sort_dir="+sort_dir;

	$.get(url2, function(data) {
		 $('#list').html(data);
		 initialise_links(url);
	});
	
}