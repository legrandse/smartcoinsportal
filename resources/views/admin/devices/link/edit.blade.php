@extends('layouts.app')

@section('content')
	
	<div class="container">

		@livewire('control-panel', ['device' => request('linked_device')])

	</div>

@endsection



