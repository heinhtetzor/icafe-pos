@extends('layouts.admin')
@section('content')
    <div class="container">
        <h2>
        <a href="{{route('admin.accountmanagement')}}">🔙</a>
        Waiters</h2>
        <h4>Create New Waiter</h4>
        @if  (session('msg'))
        <p class="alert alert-success">
            {{ session('msg') }}
        </p>
        @endif
        <section>
            <form action="{{ route('waiters.store') }}" method="post">
                @csrf
                <input name="store_id" type="hidden" value="{{Auth::guard('admin_account')->user()->store_id}}"/>
                <div class="form-group">
                    <label for="name">Waiter Name</label>
                    <input autofocus name="name" type="text" class="form-control" placeholder="Enter Waiter Name" required>
                    <p style="color:red">{{ $errors->first('name') }}</p>
                </div>
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input autofocus name="username" type="text" class="form-control" placeholder="Enter Username" required>
                    <p style="color:red">{{ $errors->first('username') }}</p>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input autofocus name="password" type="password" class="form-control" placeholder="Enter Password" required>
                    <p style="color:red">{{ $errors->first('password') }}</p>
                </div>
                <button class="btn btn-primary">Submit</button>
            </form>
        </section>

        <section>
            <h2>All Waiters</h2>
             <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username **</th>
                        <th>Password **</th>
                        <th>Status</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($waiters as $waiter)
                    <tr>
                        <td>{{$waiter->name}}</td>
                        <td>{{$waiter->username}}</td>
                        <td>
                            <i class="bi bi-lock"></i>
                            <i class="bi bi-lock"></i>
                            <i class="bi bi-lock"></i>
                        </td>
                        <td>{{$waiter->status}}</td>
                        <td>
                            <a href="{{ route('waiters.edit', $waiter->id) }}">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>
@endsection