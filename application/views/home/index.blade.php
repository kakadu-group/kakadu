
{{-- Scripts --}}
@section('scripts')
{{Asset::scripts()}}
{{ HTML::script('js/favorites.js')}}
<script>
	$(document).ready(function() {	
		$("#home").addClass('active');
		initialiseFavorites("{{URL::base()}}", "{{__('favorites.learn')}}");
		$('.carousel').carousel({
			  interval: 5000
		})
	});
</script>

@endsection

@section('content')


<html>
<div class="row-fluid">
	<div class="offset1 span11">
		<div id="this-carousel-id" class="carousel slide">
			<!-- class of slide for animation -->
			<div class="carousel-inner">
				<div class="item active">
					<!-- class of active since it's the first item -->
					{{HTML::image('img/kakadu.png')}}
					<div class="carousel-caption">
						<p>{{__('descriptions.kakadu')}} {{ HTML::link_to_route('auth/register', __('authentification.register_link')) }}</p>
					</div>
				</div>
				<div class="item">
					{{HTML::image('img/group.png')}}
					<div class="carousel-caption">
						<p>{{__('descriptions.groups')}}</p>
					</div>
				</div>
				<div class="item">
					{{HTML::image('img/algorythm.png')}}
					<div class="carousel-caption">
						<p>{{__('descriptions.algorythm')}}</p>
					</div>
				</div>
			</div>
			<!--  Next and Previous controls below href values must reference the id for this carousel -->
			<a class="carousel-control left" href="#this-carousel-id" data-slide="prev">&lsaquo;</a> 
			<a class="carousel-control right" href="#this-carousel-id" data-slide="next">&rsaquo;</a>
		</div>
	</div>
	<div class="offset1">
		@if($roleSystem == Const_Role::GUEST)
			<div class="span4">
				<i class="offset2 icon-folder-open-alt icon-4x"></i>
				<h3>{{__('descriptions.course')}}</h3>
				<p>{{__('descriptions.course_description')}}</p>
			</div>
			<div class="span4">
				<i class="offset2 icon-group icon-4x"></i>
				<h3>{{__('descriptions.groups2')}}</h3>
				<p>{{__('descriptions.group_description')}}</p>
			</div>
			<div class="span4">
				<i class="offset3 icon-lightbulb icon-4x"></i>
				<h3>{{__('descriptions.algorythm2')}}</h3>
				<p>{{__('descriptions.algorythm_description')}}</p>
			</div>
		@endif
		@if($roleSystem !== Const_Role::GUEST)
			<div class="span4">	
				<h3>{{__('favorites.learngroups')}}</h3>
				<table class="table table-hover table-condensed">
					<thead>
						<tr>
							<th>{{__('favorites.name')}}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($learngroups as $g)
						<tr>
							<td>{{HTML::link_to_route('group', $g['name'], array($g['id']))}}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="span4">	
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
			<div class="span4">
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
									<button class="btn btn-small" onclick="parent.location='{{URL::to_route('catalog/learning', array($c['id']))}}'">{{__('favorites.learn')}}</button>
									<button class="btn btn-small btn-danger" onclick="removeFavoriteCatalog({{$c['id']}})" title="{{__('favorites.remove')}}"><i class="icon-remove icon-mini"></i></button>
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</div>
</div>


</html>

@endsection
