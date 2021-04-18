@extends('layouts.admin')
@section('content')
<div class="container">
    <h2>
    <a href="{{route('menugroups.index')}}">🔙</a>
    Editing {{ $menu->name}}</h2>  
    @if (session('error'))
    <div class="alert alert-danger">
        {{session('error')}}
    </div>
    @endif
    <section>
        <form action="{{ route('menus.update', $menu->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="form-group">
                <label for="name">အမျိုးအမည်</label>
                <input value="{{$menu->name}}" name="name" type="text" class="form-control" placeholder="Enter Table Name" required>
                <p style="color:red">{{ $errors->first('name') }}</p>
            </div>
            {{-- menu group select --}}
            <div class="form-group">
                <label for="name">Menu Group</label>
                <select name="menu_group_id" id="" required class="form-control">
                    <option>====================</option>
                    @foreach ($menu_groups as $menu_group)
                    <option
                    @if($menu_group->id == $menu->menu_group_id) selected @endif
                    value="{{$menu_group->id}}">{{$menu_group->name}}</option>
                    @endforeach
                </select>
                <p style="color:red">{{ $errors->first('menu_group_id') }}</p>
            </div>
            <div class="form-group">
                <label for="code">Code</label>
                <input type="text" value="{{$menu->code}}" type="number" class="form-control" name="code">
                <p style="color:red">{{ $errors->first('code') }}</p>
            </div>
            {{-- menu price --}}
            <div class="form-group">
                <label for="price">Price</label>
                <input value="{{$menu->price}}" name="price" type="number" class="form-control" placeholder="Enter Price" required>
                <p style="color:red">{{ $errors->first('price') }}</p>
            </div>
            {{-- meny image --}}
            <div class="form-group">
                <label for="name">Upload Image</label>
                <input name="image" type="file" class="form-control">
                <p style="color:red">{{ $errors->first('image') }}</p>
            </div>

            
            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-info" href="/admin/menugroups">Back</a>
        </form>
        <hr>
        <button 
            onclick="if(!confirm('Are you sure?')) return; document.querySelector('#delete-form').submit();" 
            class="btn btn-danger">
                Delete	
            </button>
            {{-- hidden delete form --}}
            <form id="delete-form" class="hidden" action="{{ route('menus.destroy', $menu->id) }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="id" value="{{ $menu->id }}">
            </form>
    </section>
</div>
@endsection