@extends('layouts.app')

@section('content')
	
	

		@livewire('control-panel', ['device' => request('linked_device')])

	

@endsection



