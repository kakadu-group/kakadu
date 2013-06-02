{{-- Sidebar --}}
@section('sidebar')
    <li class="nav-header">{{__('group.sidebar_title')}}</li>
    <li id="create">{{HTML::link_to_route('group/create', __('profile.create_group'))}}</li>

    <li class="nav-header">{{__('course.sidebar_title')}}</li>
    <li id="create">{{HTML::link_to_route('course/create', __('profile.create_course'))}}</li>
@endsection

{{-- Content --}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
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
	</div>
</div>

@endsection
