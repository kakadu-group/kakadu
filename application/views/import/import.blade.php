{{-- Sidebar --}}
@section('sidebar')

@endsection

{{-- Scripts --}}
@section('scripts')

@endsection


@section('content')

	<div class="row-fluid">
		<div class="offset1">
			<div class="span12">			    
				    <legend>{{__('import.check_import')}}</legend>
				    <p>{{__('import.check_import2')}}</p>
				    <table class="table table-hover">
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Parent</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								foreach($import['catalogs'] as $catalog){
									echo "<tr>
											<td>".$catalog['id']."</td>
											<td>".$catalog['name']."</td>
											<td>".$catalog['parent']."</td>
										 </tr>";
								}
							
							?>
						</tbody>
					</table>
					
					<table class="table table-hover">
						<thead>
							<tr>
								<th>{{__('import.catalog')}}</th>
								<th>{{__('import.typ')}}</th>
								<th>{{__('import.question')}}</th>
								<th>{{__('import.answer')}}</th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php 
								foreach($import['questions'] as $question){
									echo "<tr>";
									echo "<td>";
									foreach($question['catalogs'] as $catalog){
										echo $catalog.", ";
									}
									echo "</td>";
									echo "<td>".$question['type']."</td>";
									echo "<td>".$question['data']['question']."</td>";
									if($question['type'] === "simple"){
										echo "<td>".$question['data']['answer']."</td>";
									}else{
										echo "<td>";
										foreach($question['data']['answer'] as $answer){
											echo $answer.", ";
										}
										echo "</td>";
										foreach($question['data']['choices'] as $choice){
											echo "<td>".$choice."</td>";
										}

									}
									echo "</tr>";
									
								}
							
							?>
						</tbody>
					</table>
					
					{{ Form::open('import/save', 'POST') }}

					    {{ Form::hidden('answer', 'true') }}
					
					    {{ Form::submit(__('import.confirm'), array('class' => 'btn btn-success')) }}
					    <a href="{{ URL::to_route('course/import', array($course['id'])) }}" class="btn btn-danger">{{__('import.abort')}}</a>
					
					    {{ Form::token() }}
				    {{ Form::close() }}
			</div>
		</div>
	</div>

@endsection
