@section('content')

<div class="row-fluid">
	<div class="offset1 span11">
		<h5>{{__('question.deleted')}}</h5>
		<h5>{{__('question.link')}} {{ HTML::link_to_route('courses', __('home.courses_link')) }}</h5>
	</div>
</div>

@endsection