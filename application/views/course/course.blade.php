{{-- Sidebar --}}
@section('sidebar')	
	
	@if($roleLearngroup == Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)
		<legend><font size="3"><font style='color: #ADA5B5'>{{__('general.course')}}</font> {{ $course['name'] }}</font></legend>		
		
		<li id="editCourse" class="admin"><i class="icon-pencil"></i> <a href="#" onclick=edit()>{{__('course.edit_course')}}</a></li>
		<li class="admin"><i class="icon-trash"></i> <a href="#" onclick=deletecourse()>{{__('course.delete')}}</a></li>		
		<li id="createQuestion"><i class="icon-plus"></i> {{HTML::link_to_route('question/create', __('question.create'), array($course['id']))}}</li>		
		<li id="createCatalog"><i class="icon-plus"></i> {{HTML::link_to_route('catalog/create', __('catalog.create'), array($course['id']))}}</li>
		<li id="importQuestions"><i class="icon-upload"></i> {{HTML::link_to_route('course/import', __('import.import_questions'), array($course['id']))}}</li>
		<br>
	@endif
@endsection

{{-- Scripts --}}
@section('scripts')

	{{ HTML::script('js/addGroup.js')}}
	{{ HTML::script('js/favorites.js')}}
	{{ HTML::script('js/iterator.js')}}
	
	<!-- Check if courses are referenced -->
	@if(count($groups) > 0)
		<script>var set = true;</script>
	@else
		<script>var set = false;</script>
	@endif
	<script>

		$(document).ready(function() {	
	
			//initialises the js-file addGroup (can be found in public/js/addGroup).
			initialiseAddGroup("{{URL::base()}}", set);
			//initialises the js-file addGroup (can be found in public/js/favorites).
			initialiseFavorites("{{URL::base()}}", "{{__('favorites.learn')}}")
			
			$("#added").hide();
			$(".pagination").addClass('pagination-centered');
		});
	
		//function which is called on delete
		function deletecourse(){
			bootbox.dialog("{{__('course.check')}}", [{
		
				"label" : "{{__('general.no')}}",
				"class" : "btn-danger",
				"callback": function() {
					console.log("No delete");
				}
		
				}, {
				"label" : "{{__('general.yes')}}",
				"class" : "btn-success",
				"callback": function() {
					var urldelete = "{{ URL::to_route('course/delete', array($course['id'])) }}";
					window.location=urldelete;
				}
		
				}]);
		}
		
		//Hides the updated label if the course was never updated
		function hide(){
			$(document).ready(function(){
				$('#updated').hide();
			});
			
		}
	</script>


@endsection

{{--Content--}}
@section('content')

<!-- Check if the Course was ever updated -->
@if($course['created_at'] == $course['updated_at'])
	<script>hide();</script>
@endif

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<div id="view">
				<div id="added" class="alert alert-block alert-success fade in">
					<a class="close" href="#">&times;</a>
					<h5>{{__('course.added_to_favorites')}}</h5>
				</div>
				<legend>
					<font style='color: #ADA5B5'>{{__('general.course')}}</font> {{ $course['name'] }}
					@if($roleLearngroup == Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)
						<a style="cursor: pointer;" onclick=edit(); class="pull-right" title="{{__('course.edit')}}"><i class="icon-edit"></i></a>
					@endif
					@if($roleSystem !== Const_Role::GUEST)
							@if($course['favorite'] != 1)
								<button id="favorite{{$course['id']}}" onclick="addFavoriteCourse({{$course['id']}})" class="btn btn-small" title="{{__('favorites.favorite')}}"><i class="icon-star"></i></button>
							@else
								<button onclick="linktest({{$course['id']}})" class="btn btn-small">{{__('favorites.learn')}}</button>
							@endif
								
					@endif
				</legend>
				<p>{{ $course['description'] }}</p>
				@if(count($groups) > 0)
					<p><strong>{{__('course.reference')}}</strong></p>
					
					<p>
					@foreach($groups as $group)
						{{HTML::link_to_route('group', $group['name'], array($group['id']))}}<br />
					@endforeach
					</p>
				@endif
			</div>
			@if($roleLearngroup == Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)
				<div id="edit">
					{{ Form::open('course/edit', 'POST', array('id'=>'courseCreate')) }}

					{{ Form::hidden('id', $course['id']) }}
					
					<legend>
						{{ Form::text('name', $course['name']) }}
						<div class="btn-group pull-right">
							<button class="btn" type="submit" name="change_course" onclick="$(course/edit).submit()">{{__('course.save')}}</button>
							<button class="btn" onclick="edit();return false;">{{__('general.abort')}}</button>
						</div>
					</legend>
					
					{{ Form::label('description', __('course.description')) }}
					{{ Form::textarea('description', $course['description'], array('class' => 'row-fluid', 'rows' => '6'))}}
					
					{{Form::label('group', __('course.group_search'))}}			
					{{Form::search('group', '', array('id' => 'searchGroup', 'placeholder' => __('course.search_group')))}}
					
					@foreach($groups as $group)
						<input type="hidden" name="groups[]" value="{{$group['id']}}" />
					@endforeach
					{{ Form::token() }}
					{{ Form::close() }}	
					
					<div class="row-fluid" id="showGroups"></div>
					<div id="nothingFound" class="alert alert-block alert-error fade in">
						<a class="close" href="#">&times;</a>
						<h5>{{__('course.not_found')}}</h5>
					</div>
					<div id="inList" class="alert alert-block alert-error fade in">
						<a class="close" href="#">&times;</a>
						<h5>{{__('course.in_list')}}</h5>
					</div>
					<div class="row-fluid">
						<div class="span8">			
							<table id="groupsTable" class="table table-hover table-condensed">
								<thead>
									<tr>
										<th>{{__('group.name')}}</th>
										<th>{{__('group.description')}}</th>
										<th>{{__('course.add')}}</th>
									</tr>
								</thead>
								<tbody id="groups_search">
									
								</tbody>
							</table>		
						</div>
						<div class="span4">
							<table id="tabelReferences" class="table table-hover table-condensed">
								<thead>
									<th>{{__('course.group_referenced')}}</th>
									<th>{{__('course.remove_reference')}}</th>
								</thead>
								<tbody id="referencedCourses">
									@foreach($groups as $group)
										<tr id="reference{{$group['id']}}">
											<td>{{Html::link_to_route('group', $group['name'], array($group['id']))}}</td>
											<td><button onclick=removeReference({{$group['id']}}) class='btn-danger btn-mini'><i class='icon-remove'></i></button></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>			
				</div>
			@endif
			<label>{{__('course.created')}} {{ $course['created_at'] }}<label>
			<label id="updated">{{__('course.updated')}} {{ $course['updated_at'] }}</label><br>
              
           <h3>{{__('catalog.subcatalogs')}}</h3>
           
           <!-- Printing all catalogs of the course in tree structure -->
           @include('course.iterator')
           <table>
           	<tbody>
           		<?php iterate($catalogs, 0, Url::base(), $course['id'], __('question.create'))?>
           	</tbody>
           </table>
		</div>
	</div>
</div>



@endsection
