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
			<legend>{{__('course.edit_course')}}</legend>
			{{ Form::open('course/edit', 'POST') }}
	
			{{ Form::hidden('id', $course['id']) }}
			
			{{ Form::label('name', __('course.name')) }}
			@if(Input::old('name') != '')
				{{ Form::text('name', Input::old('name'), array('class' => 'row-fluid', 'rows' => '1')) }}
			@else
				{{ Form::text('name', $course['name'], array('class' => 'row-fluid', 'rows' => '1')) }}
			@endif
			
			
			{{ Form::label('description', __('course.description')) }}
			@if(Input::old('description') != '')
				{{ Form::textarea('description', Input::old('description'), array('class' => 'row-fluid', 'rows' => '6')) }}
			@else
				{{ Form::textarea('description', $course['description'], array('class' => 'row-fluid', 'rows' => '6')) }}
			@endif
			
			{{Form::label('group', __('course.group'))}}

			@if(!isset($group))
				<div class="input-append">
				  {{Form::search('group', '', array('id' => 'searchGroup', 'placeholder' => __('course.search_group')))}}
				  <button class="btn" type="button" onclick="removeReference({{__('course.search_group')}});"><i class="icon-remove"></i></button>
				</div>
			@else
				<div class="input-append">
				  {{Form::search('group', $group['name'], array('id' => 'searchGroup', 'placeholder' => __('course.search_group')))}}
				  <button class="btn" type="button" onclick="removeReference('{{__('course.search_group')}}');"><i class="icon-remove"></i></button>
				</div>
			@endif
			
			<div class="row-fluid" id="showGroups"></div>
			<div id="nothingFound" class="alert alert-block alert-error fade in">
				<a class="close" href="#">&times;</a>
				<h5>{{__('course.not_found')}}</h5>
			</div>
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
				
			@if(!isset($group))
				{{ Form::hidden('group', null, array('id' => 'groupId')) }}
			@else
				{{ Form::hidden('group', $group['id'], array('id' => 'groupId')) }}
			@endif
			
			
			{{ Form::token() }}
			<button class="btn btn-small btn-primary" type="submit" name="change_course" id="change_course" onclick="$(course/edit).submit()">{{__('course.save')}}</button>
			
			{{ Form::close() }}
		</div>
	</div>
</div>





@endsection