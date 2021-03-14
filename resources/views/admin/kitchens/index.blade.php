@extends('layouts.admin')
@section('content')
<div class="container">
    
    <header class="header">
        <h3>
            <a href="{{route('admin.accountmanagement')}}">🔙</a>
            Kitchens
        </h3>
        <h4>Create New Kitchen</h4>
        @if  (session('msg'))
        <p class="alert alert-success">
            {{ session('msg') }}
        </p>
        @endif
        <section>
            <form action="{{ route('kitchens.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="name">Kitchen Name</label>
                    <input autofocus name="name" type="text" class="form-control" placeholder="Enter Kitchen Name" required>
                    <p style="color:red">{{ $errors->first('name') }}</p>
                </div>
                <div class="form-group">
                    <label for="name">Color</label>
                    <input name="color" type="color" class="form-control" required>
                    <p style="color:red">{{ $errors->first('color') }}</p>
                </div>
                <div class="form-group">
                   <label for="menu_groups">Assign Menu Groups</label>
                   <select multiple name="menu_groups[]" class="form-control" required>
                       <option>----</option>
                       @foreach($menu_groups as $menu_group)
                       <option value="{{$menu_group->id}}">{{$menu_group->name}}</option>
                       @endforeach
                       <option></option>
                   </select>
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
            <h2>All Kitchens</h2>
             <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Color **</th>
                        <th>Username **</th>
                        <th>Password **</th>
                        <th>Status</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kitchens as $kitchen)
                    <tr>
                        <td>{{$kitchen->name}}</td>
                        <td>{{$kitchen->color}}</td>
                        <td>{{$kitchen->username}}</td>
                        <td>
                            <i class="bi bi-lock"></i>
                            <i class="bi bi-lock"></i>
                            <i class="bi bi-lock"></i>
                        </td>
                        <td>{{$kitchen->status}}</td>
                        <td>
                            <a href="{{ route('kitchens.edit', $kitchen->id) }}">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </header>
</div>
@endsection