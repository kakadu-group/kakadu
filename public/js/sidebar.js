
$(document).ready(function() {
	
	$("#show-sidebar").hide();
	$("#hide-sidebar").fadeTo(1,0);
	
	//checks if there exist a cookie for the sidebar
	if(document.cookie){
		a = document.cookie;
		cookiewert = a.substr(a.search('=')+1,5);
		if(cookiewert == "false"){
			$("#sidebar").hide();
			$("#content").removeClass("span9").addClass("span11");
			$("#show-sidebar").show();
			console.log("Get");
		}
		
	}
	
	//Displays the hide symbol only on hover over the sidebar
	$("#sidebar").hover(function (){
    	$("#hide-sidebar").fadeTo(1,1);
    },function(){
    	$("#hide-sidebar").fadeTo(1,0);
    });

	//hides the sidebar and sets the cookie
    $("#hide-sidebar").click(function (){
    	document.cookie = 'sidebar=false; path=/';	
		$("#sidebar").hide('slide',{direction:'right'},500);

		setTimeout(function(){
			$("#content").removeClass("span9").addClass("span11");
			$("#show-sidebar").show();
		}, 500);
		
    });

    //shows the sidebar and sets the cookie
    $("#show-sidebar").click(function (){
    	document.cookie = 'sidebar=true; path=/';
    	
    	$("#content").removeClass("span11").addClass("span9");
    	$("#show-sidebar").hide();
	    
    	$("#sidebar").show('slide',{direction:'right'},500);

    });
	
});