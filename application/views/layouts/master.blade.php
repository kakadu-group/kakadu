<!DOCTYPE html>
<html lang='de'>
<head>
		
	<meta charset="UTF-8" />        
	<title>Kakadu</title>

    {{Asset::scripts()}}
    {{HTML::style('css/bootstrap.css')}} 
    {{HTML::style('css/font-awesome.css')}} 
    {{HTML::style('css/footer.css')}} 
    
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
		
	    //Hides/show the inline edit fields
	    function edit(){
	    	$('#view').toggle();
	    	$('#edit').toggle();
	    }
		
	    $(document).ready(function() {
	        $('.dropdown-toggle').dropdown();
	
	        //Hides all inline edit-fields
	    	$('#edit').hide();
		
	        //Additional Label for IE -> cause placeholders are not working in IE
	    	if (navigator.appName == "Netscape"){
	    		$("#emailLabel").hide();
				$("#passwordLabel").hide();
	    	}
	    });
		    
	</script>
		
	@yield('scripts')


</head>

<body>
 <div id="wrap">
 	<div class="row-fluid">
	    <div class="navbar navbar-fixed-top">
	        <div class="navbar-inner">
	        	<a href="{{ URL::to_route('home')}}" class="brand">Kakadu</a>
	            <ul class="nav">            
	            	<li id="courses">{{ HTML::link_to_route('courses', __('home.courses_link'))}}</li>
	                <li id="groups">{{ HTML::link_to_route('groups', __('home.groups_link'))}}</li>
	                @if($roleSystem != Const_Role::GUEST)
	                  	<li id="favorites">{{ HTML::link_to_route('favorites', __('home.favorites'))}}</li>
	                @endif
	                <li class="divider-vertical"></li>
	                    
	                {{ Form::open('courses/search', 'GET', array('class' => 'navbar-search')) }}
	                	<div class="input-append">
	                    	{{Form::search('search', Input::get('search'))}}
	                        {{Form::token()}}
							<button class="btn" type="submit">{{__('home.search_placeholder')}}</button>
						</div>
					{{ Form::close() }}                    
	            </ul>                
	               	
	            <ul class="nav pull-right" id="dropdown">
	              	<li id="help">{{ HTML::link_to_route('help', __('home.help_link'), array('class'=>'pull-right'))}}</li>
	               	@if($roleSystem != Const_Role::GUEST)
		        	    <li class="dropdown" id="accountmenu">  
		            	    <a class="dropdown-toggle" data-toggle="dropdown" href="#" ><u id="userinfo">{{ $user['displayname'] }}</u> <i class="icon-cog"></i><b class="caret"></b></a>
		                    <ul class="dropdown-menu">  
		                	    <li>{{ HTML::link_to_route('profile/edit', __('profile.profile_edit_link')) }}</li>  
		                        <li class="divider"></li>  
		                        <li>{{ HTML::link_to_route('auth/logout', __('profile.logout_link')) }}</li>  
		                    </ul>  
		                </li> 
		             @endif
	           </ul> 
	        </div>
	    </div>
    </div>
    <div class="row-fluid">
	    <div id="content" class="span9">@yield('content')</div>
        <div class="span3">
        	<div id="sidebar" class="well sidebar-nav-fixed">
            	<ul class="nav nav-list">
                	<div id= "hide-sidebar" class="span1" style="position: relative;">
                    	<a style="cursor: pointer;"><i cursor: pointer style="position: absolute; bottom: 2em; right: 2em;" class="icon-chevron-right"></i>
                        <i style="position: absolute; bottom: 2em; right: 3em;" class="icon-chevron-right"></i>
                        <i style="position: absolute; bottom: 2em; right: 1em;" class="icon-chevron-right"></i></a>
                    </div>

                    @if($roleSystem == Const_Role::GUEST)
                   		<div id="guest">
                            <div class="span11">                    
                                {{ Form::open('auth/login', 'POST') }}
                                    <h3 class="form-signin-heading">{{__('authentification.sign_in_label')}}</h3>                                   
                                    {{ Form::label('email', 'Email', array('id'=>'emailLabel')) }}
                                    {{ Form::email('email', Input::old('email'), array('placeholder'=>'Email')) }}
                                    {{ Form::label('password', __('authentification.password_label'), array('id'=>'passwordLabel')) }}
                                    {{ Form::password('password', array('placeholder'=>__('authentification.password_label'))) }}                                    
                                    <label class="checkbox">
										<input id="remember" checked type="checkbox">{{__('authentification.remember_label')}}
									</label>                           
                                    {{ Form::token() }}
                                    <button class="btn btn-primary" type="submit" name="login" id="login" onclick="$('auth/login').submit()">Login</button>
                                {{Form::close()}}
                                <li>{{ HTML::link_to_route('auth/register', __('authentification.register_link')) }}<li>
                                <li>{{ HTML::link_to_route('auth/forgotpassword', __('authentification.forgot_password_link')) }}</li>
                            </div>      
                        </div>
                   	@else
                        <div id="user">
                            <div class="span11">
                            @yield('sidebar')
                            
                            <legend><font size="3">{{__('general.main_menu')}}</font></legend>
                            <li class="nav-header">{{__('group.sidebar_title')}}</li>
							<li id="show"><i class="icon-info-sign"></i> {{HTML::link_to_route('groups', __('general.show_groups'))}}</li>
							<li id="create"><i class="icon-plus"></i> {{HTML::link_to_route('group/create', __('profile.create_group'))}}</li>
						
							<li class="nav-header">{{__('course.sidebar_title')}}</li>
							<li id="show"><i class="icon-info-sign"></i> {{HTML::link_to_route('courses', __('general.show_courses'))}}</li>
							<li id="create"><i class="icon-plus"></i> {{HTML::link_to_route('course/create', __('profile.create_course'))}}</li>

                            <li class="nav-header">{{__('profile.profile_header')}}</li>
                            <li><i class="icon-user"></i>{{ HTML::link_to_route('profile/edit', __('profile.profile_edit_link')) }}</li>
                            <li><i class="icon-signout"></i>{{ HTML::link_to_route('auth/logout', __('profile.logout_link')) }}</li>
                            </div>      
                        </div> 

                  	@endif                     
            	</ul>
         	</div>
    	</div>
        <div id="show-sidebar" class="span1">
     		<a style="cursor: pointer;"><i style="position: absolute; bottom: 58%; right: 1%;" class="icon-chevron-left"></i>
            <i style="position: absolute; bottom: 56%; right: 1%;" class="icon-chevron-left"></i>
            <i style="position: absolute; bottom: 54%; right: 1%;" class="icon-chevron-left"></i>
            <i style="position: absolute; bottom: 52%; right: 1%;" class="icon-chevron-left"></i></a>
        </div>
    </div>
    <div id="push"></div>
</div>
</div>
    <div id="footer">
    	<div class="container">
			@if($roleSystem == Const_Role::GUEST)
				<div class="container">
					<div class="row">
						<div class="span5 offset5">
							{{ Form::open('language/edit', 'POST', array('class' => 'navbar-search')) }}
					        	{{ Form::hidden('language', 'en') }}
					            {{ Form::token() }}
					            {{ Form::submit(__('home.english'), array('id' => 'language', 'class' => 'btn btn-link')) }}
					       	{{ Form::close() }}
					       	{{ Form::open('language/edit', 'POST', array('class' => 'navbar-search')) }}
					        	{{ Form::hidden('language', 'de') }}
					            {{ Form::token() }}
					            {{ Form::submit(__('home.german'), array('id' => 'language', 'class' => 'btn btn-link')) }}
					       	{{ Form::close() }}
					    </div>
				    </div>
			    </div>
			@endif
			<div class="row-fluid span12">
			<p class="text-center">
				@if($roleSystem !== Const_Role::GUEST)
					<br>
				@endif
				Version 0.1 of Kakadu 
				<a href="http://uibk.ac.at/">Universit&auml;t Innsbruck</a> - <a href="http://informatik.uibk.ac.at/">Institut f&uuml;r Informatik</a> - <a href="http://dbis-informatik.uibk.ac.at/">Databases and Information Systems</a>       		
		    </p>
		    </div>
	    </div>
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
