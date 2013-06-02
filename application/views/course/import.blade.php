{{-- Sidebar --}}
@section('sidebar')

@endsection

{{-- Scripts --}}
@section('scripts')
	{{HTML::style('css/bootstrap-fileupload.css')}} 
	{{HTML::script('js/bootstrap-fileupload.js')}}

@endsection


@section('content')

	<div class="row-fluid">
		<div class="offset1">
			<div class="span12">
				<legend>{{__('import.import_questions')}}</legend>
				{{ Form::open_for_files('import/check', 'POST') }}
				
					{{ Form::hidden('id', $course['id']) }}
					
					<label>{{__('import.select')}}</label>    
					<div class="fileupload fileupload-new" data-provides="fileupload">
						<div class="input-append">
							<div class="uneditable-input span3">
								<i class="icon-file fileupload-exists"></i> 
								<span class="fileupload-preview"></span>
							</div>
							<span class="btn btn-file">
								<span class="fileupload-new">Select file</span>
								<span class="fileupload-exists">Change</span>
								<input name="file" type="file" />
							</span>
							<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
						</div>
					</div>
					
				    {{ Form::submit('Import', array('class' => 'btn btn-primary')) }}
					
				    {{ Form::token() }}
				{{ Form::close() }}		
				
				<br>
				<br>
				<h4>{{__('import.format')}}</h4>
				<div>{{__('import.format_description')}}</div>		
				<h5>{{__('import.catalog')}}</h5>
				<div>
					<ul>
						<li>{{__('import.courses_description')}}</li>
						<li>{{__('import.courses_description2')}}</li>
						<li>{{__('import.courses_description3')}}</li>
					</ul>
				</div>
				<h6>{{__('import.example')}}</h6>
				<div class="span6">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Parent</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>1</td>
								<td>{{__('import.course')}}</td>
								<td></td>
							</tr>
							<tr>
								<td>2</td>
								<td>{{__('import.chapter1')}}</td>
								<td>1</td>
							</tr>
							<tr>
								<td>3</td>
								<td>{{__('import.chapter2')}}</td>
								<td>1</td>
							</tr>
							<tr>
								<td>4</td>
								<td>{{__('import.chapter1_1')}}</td>
								<td>2</td>
							</tr>
							<tr>
								<td>5</td>
								<td>{{__('import.chapter2_1')}}</td>
								<td>3</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="row-fluid"></div>
				<h5>{{__('import.questions')}}</h5>
				<div>
					<ul>
						<li>{{__('import.questions_description')}}</li>
						<li>{{__('import.questions_description2')}}</li>
						<li>{{__('import.questions_description3')}}</li>
					</ul>
				</div>
				<h6>{{__('import.example')}}</h6>
				<div class="span6">
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
							<tr>
								<td>1</td>
								<td>simple</td>
								<td>{{__('import.question1')}}</td>
								<td>{{__('import.answer1')}}</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>2</td>
								<td>simple</td>
								<td>{{__('import.question2')}}</td>
								<td>{{__('import.answer2')}}</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>3,4</td>
								<td>multiple</td>
								<td>{{__('import.question3')}}</td>
								<td>2</td>
								<td>{{__('import.choice1')}}</td>
								<td>{{__('import.choice2')}}</td>
								<td>{{__('import.choice3')}}</td>
							</tr>
							<tr>
								<td>4</td>
								<td>multiple</td>
								<td>{{__('import.question4')}}</td>
								<td>0,1</td>
								<td>{{__('import.choice1')}}</td>
								<td>{{__('import.choice2')}}</td>
								<td>{{__('import.choice3')}}</td>
							</tr>
						</tbody>
					</table>
				</div>     
    		</div>
    	</div>
    </div>
@endsection
