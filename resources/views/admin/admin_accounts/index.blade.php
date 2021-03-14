@extends('layouts.admin')
@section('content')
    <div class="container">
        <h3>
        <a href="{{route('admin.accountmanagement')}}">🔙</a>
        Admin Account</h3>
        <h4>Create new Admin Account</h4>
        @if  (session('msg'))
        <p class="alert alert-success">
            {{ session('msg') }}
        </p>
        @endif
        <section>
            <form action="{{ route('admin_accounts.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="name">Username</label>
                    <input autofocus name="username" type="text" class="form-control" placeholder="Enter  username" required>
                    <p style="color:red">{{ $errors->first('username') }}</p>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input autofocus name="password" type="password" class="form-control" placeholder="Enter  password" required>
                    <p style="color:red">{{ $errors->first('password') }}</p>
                </div>
                <button class="btn btn-primary">Submit</button>
            </form>
        </section>

        <section>
            <h2>All Admin Accounts</h2>
             <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admin_accounts as $admin_account)
                    <tr>
                        <td>{{$admin_account->username}}</td>
                        <td>
                            <i class="bi bi-lock"></i>
                            <i class="bi bi-lock"></i>
                            <i class="bi bi-lock"></i>

                        </td>
                        <td>
                            <a href="{{ route('admin_accounts.edit', $admin_account->id) }}">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>
@endsection