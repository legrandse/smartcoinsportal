@extends('layouts.app')

@section('content')
	
<div class="container">

	@if(session('success'))
	    <div class="alert alert-success">
	        {{ session('success') }}
	    </div>
	@endif

	@if($errors->any())
	    <div class="alert alert-danger">
	        <ul class="mb-0">
	            @foreach($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif

	<div class="col-sm-12">		
		<div class="card bg-secondary rounded p-4 mt-4">
			<div class="card-header">
				<h6 class="mb-4">Devices list</h6>
			</div>
			
			<div class="card-body bg-secondary rounded p-4">
				<table class="table table-striped">
				<thead>
					<tr>
						
						<th scope="col">Name</th>	
						
						<th scope="col">Settings</th>	
					</tr>
				</thead>
				<tbody>
				@foreach($devices as $device)
					<tr>
						<td>{{$device->ref}}</td>
						
						<td><a href="{{route('devices.edit',['device'=> $device->id])}}"  ><i class="fas fa-cog"></i></a></td>
					</tr>
				@endforeach
				</tbody>
				</table>

			</div>
		</div>
	</div>
</div>

@endsection
