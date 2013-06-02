{{-- Sidebar --}}
@section('sidebar')
	@if($roleLearngroup == Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)
		<legend><font size="3"><font style='color: #ADA5B5'>{{__('general.group')}}</font> {{ $group['name'] }}</font></legend>	
		<li id="edit"> <i class="icon-pencil"></i> <a href='#' onclick=edit()>{{__('group.edit')}}</a></li>
		<li><i class="icon-trash"></i> <a href='#' onclick=deletegroup()>{{__('group.delete')}}</a></li>
		<br>
	@endif
@endsection


{{-- Scripts --}}
@section('scripts')


@endsection

{{-- Content --}}
@section('content')

	{{ HTML::script('js/addUser.js')}}
	
	<script>

	//Counter for members in group
	$count = 0;

	$(document).ready(function(){
		
		//Initialise the JavaScript File with the base url and the groupId.
		//Function can be found in add_user.js
		initialiseAddUser("{{URL::base()}}", "{{ $group['id'] }}", "{{__('group.drag')}}");
		
		$('#number').text($count++);
		$('#notfound').hide();
		$('#users').hide();
		$('#alreadyInGroup').hide();
		$('#userDeleted').hide();
		$('#user_added').hide();
		$('#groupInfo').hide();
		$('#notDeleted').hide();
		$('#dragGoal').hide();
		$('#dragGoalAdmin').hide();
		$('#admin_added').hide();
		$('#admin_not_added').hide();
		$('#admin_not_deleted').hide();

		//fade the info field in and out
		$("#showInfo").click(function(){	
			if($("#groupInfo").is(":visible")){
				$("#groupInfo").slideUp();
				$("#showInfo").html("{{__('group.show')}} <i class='icon-chevron-down icon-white'></i>");
			}else{
				$("#groupInfo").slideDown();
				$("#showInfo").html("{{__('group.hide')}} <i class='icon-chevron-up icon-white'></i>");
			}
		});

		//Hides the alerts when clicking close
		$('.alert .close').live('click',function(){
			$(this).parent().hide();
			return false;
		});

	});

	
	//Hides the updated label if the catalog was never updated
	function hide(){
		$(document).ready(function(){
			$('#updated').hide();
		}); 	
	}

	//Displays the number of members
	function displayMembers(){
		$("#membersCount").text("{{__('group.member')}} " + $count);
	}

	//function which is called on delete
	function deletegroup(){
		bootbox.dialog("{{__('group.check')}}", [{

			"label" : "{{__('general.no')}}",
			"class" : "btn-danger",
			"callback": function() {
				console.log("No delete");
			}

			}, {
			"label" : "{{__('general.yes')}}",
			"class" : "btn-success",
			"callback": function() {
				var urldelete = "{{ URL::to_route('group/delete', array($group['id'])) }}";
				window.location=urldelete;
			}

			}]);
	}

</script>

<!-- Check if the group was ever updated -->
@if($group['created_at'] == $group['updated_at'])
	<script>hide();</script>
@endif

<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<div id="view">
				<legend>
					<font style='color: #ADA5B5'>{{__('general.group')}}</font> {{ $group['name'] }}
					@if($roleLearngroup == Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)
						<a style="cursor: pointer;" onclick=edit(); class="pull-right" title="{{__('course.edit')}}"><i class="icon-edit"></i></a>
					@endif
				</legend>
				<p>{{ $group['description'] }}</p>
			</div>
			@if($roleLearngroup == Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)	
				<div id="edit">
					{{ Form::open('group/edit', 'POST') }}	
						{{ Form::hidden('id', $group['id']) }}		
						<legend>
							{{ Form::text('name', $group['name']) }}
							<div class="btn-group pull-right">
								<button class="btn" type="submit" name="change_group" onclick="$(group/edit).submit()">{{__('course.save')}}</button>
								<button class="btn" onclick="edit();return false;">{{__('general.abort')}}</button>
							</div>
						</legend>					
						{{ Form::textarea('description', $group['description'], array('class' => 'row-fluid', 'rows' => '6')) }}					
						{{ Form::token() }}				
					{{ Form::close() }}
				</div>
			@endif
			<div id="groupInfo">
				<div class="row-fluid">
						<div class="span12">
							<legend><h5>Info</h5></legend>
						</div>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<label><font size="2">{{__('group.created')}} {{ $group['created_at'] }}</font></label>
						<label id="updated"><font size="2">{{__('group.updated')}} {{ $group['updated_at'] }}</font></label>
						<label id="updated"><font id="membersCount" size="2"></font></label>	
					</div>
					<div class="span6">
						<label>{{__('general.reference_course')}}</label>
						@foreach($courses as $course)
							<li>{{Html::link_to_route('course', $course['name'], array($course['id']))}}</li>
						@endforeach
					</div>
				</div>

				

			</div>
			<button class="btn btn-primary btn-mini pull-right" id="showInfo">{{__('group.show')}} <i class="icon-chevron-down icon-white"></i></button>	
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
		@if($roleLearngroup == Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)
			<div class="span12">
				<label><strong>{{__('group.add')}}</strong></label>
				<input id="searchUser" type="text" placeholder="{{__('home.search_placeholder')}}">
				<div id="notfound" class="alert alert-block alert-error fade in">
					<a class="close" href="#">&times;</a>
					<h5>{{__('group.not_found')}}</h5>
				</div>
				<div id="alreadyInGroup" class="alert alert-block alert-error fade in">
					<a class="close" href="#">&times;</a>
					<h5>{{__('group.already_in_group')}}</h5>
				</div>
				<div id="user_added" class="alert alert-block alert-success fade in">
					<a class="close" href="#">&times;</a>
					<h5>{{__('group.user_added')}}</h5>
				</div>
				<div id="admin_added" class="alert alert-block alert-success fade in">
					<a class="close" href="#">&times;</a>
					<h5>{{__('group.admin_added')}}</h5>
				</div>
				<div id="admin_not_added" class="alert alert-block alert-error fade in">
					<a class="close" href="#">&times;</a>
					<h5>{{__('group.admin_not_added')}}</h5>
				</div>
				<table id="users" class="table table-hover table-condensed">
					<thead>
						<th>{{__('group.username')}}</th>
						<th>{{__('group.email')}}</th>
						<th>{{__('group.add')}}</th>
						<th>{{__('group.add_admin')}}</th>
					</thead>
					<tbody id="user_search">
					
					</tbody>
				</table>
				
			</div>
		@endif
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="offset1">
		<div class="span12">
			<div id="membersList" class="span6">
				<legend>{{__('group.users')}}</legend>
				<div id="userDeleted" class="alert alert-block alert-error fade in">
					<a class="close"  href="#">&times;</a>
					<h5>{{__('group.user_deleted')}}</h5>
				</div>
				<div id="notDeleted" class="alert alert-block alert-error fade in">
					<a class="close" href="#">&times;</a>
					<h5>{{__('group.not_deleted')}}</h5>
				</div>
				<div id="members">					
					@include('group.members')
				</div>
			</div>
			<div class="span6">
				<legend>Admins</legend>
				<div id="admin_not_deleted"
					class="alert alert-block alert-error fade in">
					<a class="close" href="#">&times;</a>
					<h5>{{__('group.admin_not_deleted')}}</h5>
				</div>
				<table class="table table-hover table-condensed">
					<thead>
						<th>{{__('group.username')}}</th>
						<th>{{__('group.email')}}</th> 
						@if($roleLearngroup ==	Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)
						<th>{{__('group.remove_admin')}}</th> 
						@endif
					</thead>
					<tbody id="adminTable">
						<tr id="dragGoalAdmin" ondragover="allowDrop(event)" ondrop="dropAdmin(event)">
							<td></td>
							<td><h4>{{__('group.drag')}}</h4></td>
							<td></td>
						</tr>
						<?php $count = 0?>
						@foreach($admins as $admin)
						<tr class="admin<?php echo $count?>">
							<td>{{ $admin['displayname'] }}</td>
							<td>{{ $admin['email'] }}</td> 
							@if($roleLearngroup ==Const_Role::ADMIN || $roleLearngroup == Const_Role::GROUPADMIN)
							<td>
								<button id="admin<?php echo $count?>"
									onclick="removeAdmin('{{$admin['email'] }}',<?php echo $count?>);return false;"
									class="btn-danger btn-mini"
									title="{{__('group.remove_admin')}}">
									<i class="icon-remove icon-white"></i>
								</button>
							</td> 
							@endif
						</tr>
						<?php $count++?>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>	
</div>

@endsection
