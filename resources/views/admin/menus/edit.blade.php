@extends('layouts.admin')
@section('content')
<div class="container">
    <h2>
    <a href="javascript:history.back()">🔙</a>
    Editing {{ $menu->name}}</h2>  
    @if (session('msg'))
        <div class="alert alert-success">{{ session('msg') }}</div>
    @endif    
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

            <div class="form-group">
                <input {{$menu->isStockMenu() ? "checked" : ""}} name="is_stock_menu" value="yes" class="form-check-input" type="checkbox" id="stock_menu_radio">
                <label class="form-check-label" for="stock_menu_radio">
                    Stock Item
                </label>
            </div>
            <br>

            <div class="form-group">
                <input type="hidden" name="status" value="0"/>
                <input {{$menu->status == 1 ? "checked" : ""}} name="status" value="1" class="form-check-input" type="checkbox" id="active_menu_radio">
                <label class="form-check-label" for="active_menu_radio">
                    Active
                </label>
            </div>
            <br>
            
            <button type="submit" class="btn btn-primary">Submit</button>
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