@section('content')

	@if ($errors->has())
	<ul class="error">
		<script>error("{{ $errors->first('email') }}  {{ $errors->first('password') }} {{ $errors->first('sentry') }} {{ $errors->first('other') }}")</script>
	</ul>
	@endif
	
	@if (Session::has('activation_info'))
		<script>error(<span class="info">{{ Session::get('activation_info') }}</span>)</script>
	@endif

@endsection