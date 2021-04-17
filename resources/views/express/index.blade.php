@extends('layouts.client')
@section('style')
<style>
    .container {        
        margin-top: 4rem;
    }
    .menus-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
    .menus-grid-item {
        width: 100px;
        height: 100px;
        border: 1px solid #d3d2d2;
        cursor: pointer;
        position: relative;         
        overflow: hidden;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="menus-grid">
        @foreach ($menus as $menu)
        <div class="menus-grid-item"
        style="background-size:cover;background-image: url('/storage/menu_images/{{$menu->image ?? 'default.png'}}')">
         {{ $menu->name }}
        </div>        
        @endforeach
    </div>
</div>
@endsection
@section('js')

@endsection