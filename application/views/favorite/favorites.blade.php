{{-- Scripts --}}
@section('scripts')
	
	{{ HTML::script('js/favorites.js')}}
	<script>
	$(document).ready(function() {	
		$("#favorites").addClass('active');
		initialiseFavorites("{{URL::base()}}", "{{__('favorites.learn')}}");
	});
	</script>
	

@endsection



{{--Content--}}
@section('content')

	<div class="row-fluid">
		<div class="offset1">
		    <div class="span5">	
				<h3>{{__('favorites.courses')}}</h3>
				<table class="table table-hover table-condensed">
					<thead>
						<tr>
							<th>{{__('favorites.name')}}</th>
							<th>{{__('favorites.learn_remove')}}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($courses as $c)
						<tr id="course{{$c['id']}}">
							<td>{{HTML::link_to_route('course', $c['name'], array($c['id']))}}</td>
							<td>
								<div class="btn-group">
									<button onclick="parent.location='{{URL::to_route('course/learning', array($c['id']))}}'" class="btn btn-small">{{__('favorites.learn')}}</button>
									<button class="btn btn-small btn-danger" onclick="removeFavoriteCourse({{$c['id']}})" title="{{__('favorites.remove')}}"><i class="icon-remove icon-mini"></i></button>
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="span5">
				<h3>{{__('favorites.catalogs')}}</h3>
				<table class="table table-hover table-condensed">
					<thead>
						<tr>
							<th>{{__('favorites.name')}}</th>
							<th>{{__('favorites.learn_remove')}}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($catalogs as $c)
						<tr id="catalog{{$c['id']}}">
							<td>{{HTML::link_to_route('catalog', $c['name'], array($c['id']))}}</td>
							<td>
								<div class="btn-group">
									<button onclick="parent.location='{{URL::to_route('catalog/learning', array($c['id']))}}'" class="btn btn-small">{{__('favorites.learn')}}</button>
									<button class="btn btn-small btn-danger" onclick="removeFavoriteCatalog({{$c['id']}})" title="{{__('favorites.remove')}}"><i class="icon-remove icon-mini"></i></button>
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>    
    </div>
    <div class="row-fluid">
		<div class="offset1">		
    		<div class="span5">
    			<h3>{{__('favorites.learn_title')}}</h3>
				{{HTML::link_to_route('favorites/learning', __('favorites.learn_all'))}}
			</div>
		</div>
	</div>

@endsection
