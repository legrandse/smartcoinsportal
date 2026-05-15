@extends('layouts.app')

@section('content')
<div class="container">
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
                <h6 class="mb-0">Edit User: {{ $user->firstname }} {{ $user->name }}</h6>
            </div>
            
            <div class="card-body bg-secondary rounded p-4">
                <form method="POST" action="{{ route('users.update', $user->id) }}">
                    @csrf
                    @method('PATCH')

                    {{-- Ligne Nom / Prénom --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Firstname</label>
                            <input type="text" name="firstname" class="form-control" value="{{ old('firstname', $user->firstname) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <input type="text" name="company" class="form-control" value="{{ old('company', $user->company) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">VAT Number</label>
                            <input type="text" name="VAT" class="form-control" value="{{ old('VAT', $user->VAT) }}">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                        </div>
                        
                    </div>

                    {{-- Adresse --}}
                    <div class="mb-3">
                        <label class="form-label">Street</label>
                        <input type="text" name="street" class="form-control" value="{{ old('street', $user->street) }}">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Postcode</label>
                            <input type="text" name="postcode" class="form-control" value="{{ old('postcode', $user->postcode) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $user->city) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country', $user->country) }}">
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    {{-- Password (optionnel) --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection