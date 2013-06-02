{{-- Scripts --}}
@section('scripts')

	{{ HTML::script('js/addGroup.js')}}	
	 
	<script>
	$(document).ready(function() {
		//initialises the js-file addGroup (can be found in public/js/addGroup).
		initialiseAddGroup("{{URL::base()}}");	
	});
	</script>

@endsection


@section('content')


<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<legend>{{__('profile.create_course')}}</legend>
			{{ Form::open('course/create', 'POST', array('id'=>'courseCreate')) }}

			{{ Form::label('name', __('course.name')) }}
			{{ Form::text('name', Input::old('name'), array('class' => 'row-fluid', 'rows' => '1')) }}
			
			{{ Form::label('description', __('course.description')) }}
			{{ Form::textarea('description', Input::old('description'), array('class' => 'row-fluid', 'rows' => '6'))}}
			
			{{Form::label('group', __('course.group_search'))}}			
			{{Form::search('group', '', array('id' => 'searchGroup', 'placeholder' => __('course.search_group')))}}
			
			<div class="row-fluid" id="showGroups"></div>
			<div id="nothingFound" class="alert alert-block alert-error fade in">
				<a class="close" href="#">&times;</a>
				<h5>{{__('course.not_found')}}</h5>
			</div>
			<div id="inList" class="alert alert-block alert-error fade in">
				<a class="close" href="#">&times;</a>
				<h5>{{__('course.in_list')}}</h5>
			</div>
			
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="offset1">
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
			
			<button class="btn btn-small btn-primary" type="submit" name="create_course" id="create_course" onclick="$(course/create).submit()">{{__('profile.create_course')}}</button>
		</div>
		<div class="span4">
			<table id="tabelReferences" class="table table-hover table-condensed">
				<thead>
					<th>{{__('course.group_referenced')}}</th>
					<th>{{__('course.remove_reference')}}</th>
				</thead>
				<tbody id="referencedCourses"></tbody>
			</table>
		</div>
		{{ Form::token() }}
		{{ Form::close() }}
	</div>
</div>

			
			



@endsection