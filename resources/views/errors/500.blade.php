@extends('layouts.app')

@section('content')
    


            <!-- 500 Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row vh-100 bg-secondary rounded align-items-center justify-content-center mx-0">
                    <div class="col-md-6 text-center p-4">
                        <i class="bi bi-exclamation-triangle display-1 text-primary"></i>
                        <h1 class="display-1 fw-bold">500</h1>
                        <h1 class="mb-4">Internal Server Error</h1>
                        <p class="mb-4">Oooops... Something went wrong.</p>
                        <p class="mb-4">Sorry for inconvenience, we are working on it</p>
                        <a class="btn btn-primary rounded-pill py-3 px-5" href="{{url('/')}}">Go Back To Home</a>
                    </div>
                </div>
            </div>
            <!-- 500 End -->


           
   
@endsection