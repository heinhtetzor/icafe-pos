@extends('layouts.client')
@section('style')
<style>
    .card {
        margin-top: 2rem;
    }
</style>
@endsection
@section('content')
<div class="container mt-5">
    <div class="card bg-primary text-whit col-md-4">
        <form action="{{route('kitchen.login')}}" method="POST">
            @csrf 
            <div class="card-header">
                <h3>Login</h3>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    <span>{{session('error')}}</span>
                </div>
                @endif
                <div class="form-group">
                    <input name="username" autofocus type="text" class="form-control" placeholder="Username">
                    <br>
                    <input name="password" type="password" class="form-control" placeholder="Password">
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-warning">Login</button>
                <a style="float:right;" class="btn btn-secondary" href="/">HOME</a>
            </div>
        </form>
    </div>
</div>
@endsection