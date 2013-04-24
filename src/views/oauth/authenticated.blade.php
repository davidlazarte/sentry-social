@extends('cartalyst/sentry-social::template')

@section('content')
<div class="container">
	<h3>Congratulations! <small>You're authenticated</small></h3>
	{{ var_dump($user) }}
</div>
@endsection
