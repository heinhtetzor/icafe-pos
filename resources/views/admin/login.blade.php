@extends('layouts.admin')
@section('style')
<style>
    
</style>
@endsection
@section('content')
    <div class="container">
        <div class="card col-md-4">
            <form action="{{route('admin.login')}}" method="POST">
                @csrf
                <div class="card-header">
                    <h2 class="card-title">Please Login</h2>
                </div>
                <div class="card-body">
                    @if(session('error'))
                    <div class="alert alert-danger">
                        <span>{{session('error')}}</span>
                    </div>
                    @enderror
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input name="username" type="text" required class="form-control" placeholder="" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input name="password" type="password" required class="form-control" placeholder="">
                    </div>                    
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Login</button>
                    <a style="float:right;" class="btn btn-secondary" href="/">HOME</a>
                </div>
            </form>
        </div>
    </div>
@endsection