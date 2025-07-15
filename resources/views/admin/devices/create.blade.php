@extends('layouts.app')

@section('content')
	
	<div class="container">

		@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
	<div class="col-sm-12">		
		<div class="card bg-secondary rounded p-4 mt-4">
			<div class="card-header ">
			<h6 class="mb-4">Adding device</h6>
			</div>
			
			<div class="card-body bg-secondary rounded p-4">
				
			<hr>
			<div class="row mb-3 mt-3">
				  <div class="col-md-3">	
				  	<label for="formFile" class="form-label">Serial number :</label>
				  </div>
				  <div class="col-md-4">
				  	<input class="form-control @error('serial') is-invalid @enderror" type="text" id="serial" >
				  </div>
				  
			</div>
			<div class="row mb-3 mt-3">
				  <div class="col-md-3">	
				  	<label for="formFile" class="form-label">Name :</label>
				  </div>
				  <div class="col-md-4">
				  	<input class="form-control @error('name') is-invalid @enderror" type="text" id="name" >
				  </div>
				  
			</div>	
			
			<hr>
			
			
		</div>
	</div>

	</div>

@endsection