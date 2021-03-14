@extends('layouts.admin')
@section('content')
    <h2>Editing {{ $menu->name}}</h2>  
    <section>
        <form action="{{ route('menus.update', $menu->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="form-group">
                <label for="name">Menu Name</label>
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
                    @if($menu_group->id === $menu->menu_group_id) selected @endif
                    value="{{$menu_group->id}}">{{$menu_group->name}}</option>
                    @endforeach
                </select>
                <p style="color:red">{{ $errors->first('menu_group_id') }}</p>
            </div>
            {{-- menu price --}}
            <div class="form-group">
                <label for="name">Price</label>
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
            onclick="document.querySelector('#delete-form').submit();" 
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
@endsection