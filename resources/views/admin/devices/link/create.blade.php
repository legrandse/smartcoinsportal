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
				<h6 class="mb-4">Adding device</h6>
			</div>
			
			<div class="card-body bg-secondary rounded p-4">

				{{-- 🔽 Début du formulaire --}}
				<form method="POST" action="{{ route('linked-devices.store') }}">
					@csrf

					<div class="row mb-3 mt-3">
						<div class="col-md-3">	
							<label for="serial" class="form-label">Serial number :</label>
						</div>
						<div class="col-md-4">
							<input class="form-control @error('serial') is-invalid @enderror" 
							       type="text" 
							       name="serial" 
							       id="serial" 
							       value="{{ old('serial') }}">
							<input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
							@error('serial')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row mb-3 mt-3">
						<div class="col-md-3">	
							<label for="ref" class="form-label">Reference :</label>
						</div>
						<div class="col-md-4">
							<input class="form-control @error('ref') is-invalid @enderror" 
							       type="text" 
							       name="ref" 
							       id="ref" 
							       value="{{ old('ref') }}">
							@error('ref')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>	

					<hr>

					<div class="row">
						<div class="col-md-3"></div>
						<div class="col-md-4">
							<button type="submit" class="btn btn-primary">Link Device</button>
						</div>
					</div>
				</form>
				{{-- 🔼 Fin du formulaire --}}

			</div>
		</div>
	</div>
</div>

@endsection
