<!DOCTYPE html>
<html lang='de'>
<head>
		
	<meta charset="UTF-8" />        
	<title>Kakadu</title>

    {{HTML::style('css/bootstrap.css')}}  
    {{HTML::style('css/footer.css')}}
    {{HTML::script('js/jquery-1.8.2.js')}}
    {{HTML::script('js/bootstrap.js')}}
    {{HTML::script('js/bootbox.js')}}
    
    
    <style>
    
    	/* A fixed navbar needs a padding-top of at least 40px (see: http://twitter.github.io/bootstrap/components.html#navbar)
    	*  The padding is added to the div where the content section is printed
    	*/
    	#content{
    		padding-top: 60px;
    	}
    
    	/* This css-code is needed for the fixed right sidebar	*/
    	.sidebar-nav-fixed {
		     position:fixed;
		     top:60px;
		     width:21.97%;
		 }
    
    </style>

	<script>
		
	   //Shows the error messages
	    function error(message){                
	        bootbox.alert(message);
	     }
		    
	</script>

</head>

<body>
 	<div class="row-fluid">
	    <div class="navbar navbar-fixed-top">
	        <div class="navbar-inner">
	     		<a href="{{ URL::to_route('install')}}" class="brand">Kakadu</a>
	        </div>
	   </div>
	</div>
	<div class="row-fluid">
	    <div id="content" class="span9">@yield('content')</div>
	</div>
	
	<!-- Error handling fo all forms -->
@if(isset($errors))
	<?php
		$message = "";
		foreach ($errors as $error){
			if(!is_array($error) && ($error != ":message")){
				$message .= $error;
			}
		}
		if($message !== ""){
			$test = "error('".$message."')";
			echo "<script>".$test."</script>";
		}
	?>

@endif 
</body>
</html>