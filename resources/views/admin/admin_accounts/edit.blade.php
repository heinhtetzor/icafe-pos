@extends('layouts.admin')
@section('content')
    <h2>Editing {{ $admin_account->username}}</h2>  
    <section>
        <form action="{{ route('admin_accounts.update', $admin_account->id) }}" method="post">
            @csrf
            @method('put')
            <div class="form-group">
                <label for="name">Username</label>
                <input value="{{$admin_account->username}}" name="username" type="text" class="form-control" placeholder="Enter Username" required>
                <p style="color:red">{{ $errors->first('username') }}</p>
            </div>
            <div class="form-group">
                <label for="name">Password</label>
                <input value="" name="password" type="password" class="form-control" placeholder="Enter Password" required>
                <p style="color:red">{{ $errors->first('password') }}</p>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-info" href="{{route('admin_accounts.index')}}">Back</a>
        </form>
        <hr>
        <button 
            onclick="document.querySelector('#delete-form').submit();" 
            class="btn btn-danger">
                Delete	
            </button>
            {{-- hidden delete form --}}
            <form id="delete-form" class="hidden" action="{{ route('admin_accounts.destroy', $admin_account->id) }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="id" value="{{ $admin_account->id }}">
            </form>
    </section>
@endsection