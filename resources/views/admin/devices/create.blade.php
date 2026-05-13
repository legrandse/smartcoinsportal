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
            <div class="card-header">
                <h6 class="mb-4">Adding device</h6>
            </div>
            
            <div class="card-body bg-secondary rounded p-4">
                
                <form action="{{ route('devices.store') }}" method="POST">
                    @csrf 

                    <hr>
                    
                    {{-- Champ Serial Number --}}
                    <div class="row mb-3 mt-3">
                        <div class="col-md-3">    
                            <label for="serial" class="form-label text-white">Serial number :</label>
                        </div>
                        <div class="col-md-4">
                            {{-- 3. Ajout de l'attribut 'name' et de 'value' pour garder la saisie en cas d'erreur --}}
                            <input class="form-control @error('serial') is-invalid @enderror" 
                                   type="text" 
                                   
                                   name="serial" 
                                   value="{{ old('serial') }}">
                            @error('serial')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Champ Name --}}
                    <div class="row mb-3 mt-3">
                        <div class="col-md-3">    
                            <label for="model" class="form-label text-white">Model :</label>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control @error('model') is-invalid @enderror" 
                                   type="text" 
                                   
                                   name="model" 
                                   value="{{ old('model') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>    
                    
                    <hr>

                    {{-- 4. Bouton de soumission --}}
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <a href="{{ route('devices.index') }}" class="btn btn-outline-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Device</button>
                        </div>
                    </div>

                </form> {{-- Fin du formulaire --}}
            </div>
        </div>
    </div>
</div>
@endsection