<div class="accordion" id="accordion1">
	<!-- For loop over all groups  -->
	@foreach ($groups as $group)
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#{{$group['id']}}">
				{{$group['name'] }} <i class="icon-chevron-down"></i>
				<div class="btn-group pull-right">
					<button href="#" class="btn btn-small" onclick=link({{$group['id']}})>{{__('course.show')}}</button>
				</div> 
			</a>
		</div>
		<div id="{{$group['id']}}" class="accordion-body collapse" style="height: 0px;">
			<div class="accordion-inner">{{ $group['description'] }}</div>
		</div>
	</div>
	@endforeach
	
	{{ $links }}
</div>