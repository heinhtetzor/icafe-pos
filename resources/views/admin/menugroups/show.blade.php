@extends('layouts.admin')
@section('css')
<style>
    
</style>
@endsection
@section('content')
    <div class="container-fluid"> 
        <div class="row">
            @include('admin.menugroups.sidebar')
            <div class="main col-md-9">
                <h4>
                    <a class="btn btn-danger" href="{{route('admin.masterdatamanagement')}}">←</a>
                    Menus in {{$menu_group->name}} [<a href="{{route('menugroups.edit', $menu_group->id)}}">Edit</a>]</h4>
                <div class="card">
                    <div class="card-header">
                        <h5>Add New</h5>
                    </div>
                    <div class="card-body">
                        @if  (session('msg'))
                        <p class="alert alert-success">
                            {{ session('msg') }}
                        </p>
                        @endif
                        <section>
                            <form class="inline-form" action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="menu_group_id" value="{{$menu_group->id}}">
                                <div class="form-group">
                                    <label for="name">အမျိုးအမည်</label>
                                    <input autofocus name="name" type="text" class="form-control" placeholder="Enter Menu Name" required>
                                    <p style="color:red">{{ $errors->first('name') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="code">Code</label>
                                    <input type="text" name="code" type="text" class="form-control" placeholder="Enter Code" required> 
                                </div>
                                {{-- menu price --}}
                                <div class="form-group">
                                    <label for="name">စျေးနှုန်း</label>
                                    <input name="price" type="number" class="form-control" placeholder="Enter Price" required>
                                    <p style="color:red">{{ $errors->first('price') }}</p>
                                </div>
                                {{-- meny image --}}
                                <div class="form-group">
                                    <label for="name">Upload Image</label>
                                    <input name="image" type="file" class="form-control">
                                    <p style="color:red">{{ $errors->first('image') }}</p>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>        
                        </section>
        
                    </div>
                </div>
                <hr>
                <section>
                    <div class="card">
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
                </section>
            </div>
            
        </div>       
    </div>
@endsection