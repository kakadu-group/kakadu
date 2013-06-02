{{-- Scripts --}}
@section('scripts')
	{{ HTML::script('js/sorting_list.js')}}
	{{ HTML::script('js/favorites.js')}}

	<script>
	$(document).ready(function() {	
		$("#courses").addClass('active');
		initialise_links("{{URL::to_route('courses')}}");
		initialiseFavorites("{{URL::base()}}", "{{__('favorites.learn')}}");
	});


	function linkcourse(id){
		var url = "{{URL::to_route('course')}}";
		setTimeout(function(){
			window.location.href=url+"/"+id;	
		}, 300);
	    
					
	}

	function linktest(id){
		var url = "{{URL::to_route('course')}}";
		setTimeout(function(){
			window.location.href=url+"/"+id+"/learning";	
		}, 300);
	}
	</script>
@endsection


{{-- Content --}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<form class="form-inline">
				<ul class="nav nav-pills">
		            <li class="dropdown">
		            	<a class="dropdown-toggle" id="drop5" role="button" data-toggle="dropdown" href="#">{{__('pagination.sort')}}<b class="caret"></b></a>
		                <ul id="menu2" class="dropdown-menu" role="menu" aria-labelledby="drop5">
		                	<li><a href="#" onclick="sorting('sort', 'name', '{{URL::to_route('courses')}}');">Name</a></li>
							<li><a href="#" onclick="sorting('sort', 'id', '{{URL::to_route('courses')}}');">Id</a></li>
							<li><a href="#" onclick="sorting('sort', 'created_at', '{{URL::to_route('courses')}}');">{{__('pagination.create_date')}}</a></li>
		                </ul>
					</li>
					<li class="dropdown">
		            	<a class="dropdown-toggle" id="drop4" role="button" data-toggle="dropdown" href="#">{{__('pagination.number')}}<b class="caret"></b></a>
		                <ul id="menu1" class="dropdown-menu" role="menu" aria-labelledby="drop4">
							<li><a href="#" onclick="sorting('number', '10', '{{URL::to_route('courses')}}');">10</a></li>
							<li><a href="#" onclick="sorting('number', '20', '{{URL::to_route('courses')}}');">20</a></li>
							<li><a href="#" onclick="sorting('number', '30', '{{URL::to_route('courses')}}');">30</a></li>
							<li><a href="#" onclick="sorting('number', '40', '{{URL::to_route('courses')}}');">40</a></li>
							<li><a href="#" onclick="sorting('number', '50', '{{URL::to_route('courses')}}');">50</a></li>
		                </ul>
					</li>
					<li class="dropdown">
		            	<a class="dropdown-toggle" id="drop5" role="button" data-toggle="dropdown" href="#">{{__('pagination.order')}}<b class="caret"></b></a>
		                <ul id="menu2" class="dropdown-menu" role="menu" aria-labelledby="drop5">
		                	<li><a href="#" onclick="sorting('order', 'asc', '{{URL::to_route('courses')}}');">{{__('pagination.up')}}</a></li>
							<li><a href="#" onclick="sorting('order', 'desc', '{{URL::to_route('courses')}}');">{{__('pagination.down')}}</a></li>
		                </ul>
					</li>
				</ul>
			</form>
			<div id="list">
				@include('course.list')
			</div>
		</div>
	</div>
</div>

@endsection
