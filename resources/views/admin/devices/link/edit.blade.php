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
						
					</tr>
				</thead>
				<tbody>
				
				</tbody>
				</table>

			</div>
		</div>
	</div>
</div>

@endsection
