@extends('layouts.admin')
@section('content')
<div class="container">
    <h2>
        <a href="{{route('admin.accountmanagement')}}">🔙</a>
        Editing {{ $waiter->name}}</h2>  
    <section>
        <form action="{{ route('waiters.update', $waiter->id) }}" method="post">
            @csrf
            @method('put')
            <div class="form-group">
                <label for="name">Waiter Name</label>
                <input value="{{$waiter->name}}" name="name" type="text" class="form-control" placeholder="Enter Waiter Name" required>
                <p style="color:red">{{ $errors->first('name') }}</p>
            </div>
            <div class="form-group">
                <label for="name">Username</label>
                <input value="{{$waiter->username}}" name="username" type="text" class="form-control" placeholder="Enter Username" required>
                <p style="color:red">{{ $errors->first('username') }}</p>
            </div>
            <div class="form-group">
                <label for="name">Password</label>
                <input value="" name="password" type="password" class="form-control" placeholder="" required>
                <p style="color:red">{{ $errors->first('password') }}</p>
            </div>
            
            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-info" href="{{route('waiters.index')}}">Back</a>
        </form>
        <hr>
        <button 
            onclick="document.querySelector('#delete-form').submit();" 
            class="btn btn-danger">
                Delete	
            </button>
            {{-- hidden delete form --}}
            <form id="delete-form" class="hidden" action="{{ route('waiters.destroy', $waiter->id) }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="id" value="{{ $waiter->id }}">
            </form>
    </section>
    
</div>
@endsection