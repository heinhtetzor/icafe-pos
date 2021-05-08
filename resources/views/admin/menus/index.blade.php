@extends('layouts.admin')
@section('css')
<style>
    .menu-image {
        height: 4rem;
    }
</style>
@endsection
@section('content')
    <header class="header">
        <h2>Menus</h2>
        <h4>Create New Menu</h4>
        @if  (session('msg'))
        <p class="alert alert-success">
            {{ session('msg') }}
        </p>
        @endif
        <section>
            <form action="{{ route('menus.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                {{-- menu name --}}
                <div class="form-group">
                    <label for="name">Menu Name</label>
                    <input autofocus name="name" type="text" class="form-control" placeholder="Enter Menu Name" required>
                    <p style="color:red">{{ $errors->first('name') }}</p>
                </div>
                {{-- menu group select --}}
                <div class="form-group">
                    <label for="name">Choose Menu Group</label>
                    <select name="menu_group_id" id="" required class="form-control">
                        <option>=====</option>
                        @foreach ($menu_groups as $menu_group)
                        <option value="{{$menu_group->id}}">{{$menu_group->name}}</option>
                        @endforeach
                    </select>
                    <p style="color:red">{{ $errors->first('menu_group_id') }}</p>
                </div>
                {{-- menu price --}}
                <div class="form-group">
                    <label for="name">Price</label>
                    <input name="price" type="number" class="form-control" placeholder="Enter Price" required>
                    <p style="color:red">{{ $errors->first('price') }}</p>
                </div>
                {{-- meny image --}}
                <div class="form-group">
                    <label for="name">Upload Image</label>
                    <input name="image" type="file" class="form-control">
                    <p style="color:red">{{ $errors->first('image') }}</p>
                </div>
                <button class="btn btn-primary">Submit</button>
            </form>
        </section>

        <section>
            <h2>All Menus</h2>
             <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Menu Group</th>
                        <th>Status</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menus as $menu)
                    <tr>
                        <td>{{$menu->name}}</td>
                        <td>
                            @if ($menu->image)
                            <img src="/storage/menu_images/{{$menu->image}}"/>
                            @else 
                            <img src="/images/default.png"/>
                            @endif                            
                        </td>
                        <td>{{$menu->price}}</td>
                        <td>{{$menu->menu_group->name}}</td>
                        <td>{{$menu->status}}</td>
                        <td>
                            <a href="{{ route('menus.edit', $menu->id) }}">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </header>
@endsection