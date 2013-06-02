{{--Content--}}
@section('content')

<div class="row-fluid">
	<div class="offset1">
		    <div class="alert alert-block alert-error fade in">
    			<strong>{{__('profile.warning')}}</strong>
    			<p>{{__('profile.delete_warning')}}<p>
    			
    			{{ Form::open('profile/delete', 'DELETE') }}
				<br><button class="btn btn-success" onclick="$('profile/delete').submit()">{{__('general.yes')}}</button>
				<button class="btn btn-danger" onclick="parent.location='{{URL::to_route('profile/edit')}}';return false;">{{__('general.no')}}</button>
				{{ Form::token() }}
				{{ Form::close() }}
				
    		</div>
	</div>
</div>

@endsection
          