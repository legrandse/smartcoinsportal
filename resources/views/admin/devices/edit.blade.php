@extends('layouts.app')

@section('content')
	
	<div class="container">

		@livewire('control-panel', ['device' => request('device')])

	</div>

@endsection