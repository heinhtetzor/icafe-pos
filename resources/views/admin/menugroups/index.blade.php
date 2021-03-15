@extends('layouts.admin')
@section('css')
<style>
    
        
</style>
@endsection
@section('content')
    <div class="content">
        @include('admin.menugroups.sidebar')
        <div class="main">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <a class="btn btn-danger" href="{{route('admin.masterdatamanagement')}}">←</a>
                        All
                    </h4>
                </div>
                <div class="card-body">
                    <div class="grid">                    
                        @foreach($menus as $menu)
                        <a class="grid-item" href="{{route('menus.edit', $menu->id)}}">
                            <img src="/storage/menu_images/{{$menu->image ?? 'default.png'}}"/>
                            <span class="menu-text">
                                {{$menu->name}} <br> 
                                {{$menu->price}} Ks
                            </span>
                            
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
