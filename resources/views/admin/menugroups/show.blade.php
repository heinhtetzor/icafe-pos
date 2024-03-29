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
                                <input name="store_id" type="hidden" value="{{Auth::guard('admin_account')->user()->store_id}}"/>
                                <input type="hidden" name="menu_group_id" value="{{$menu_group->id}}">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">အမျိုးအမည်</label>
                                        <input autofocus name="name" type="text" class="form-control" placeholder="Enter Menu Name" required>
                                        <p style="color:red">{{ $errors->first('name') }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="code">Code</label>
                                        <input type="text" name="code" type="text" class="form-control" placeholder="Enter Code" required> 
                                    </div>
                                </div>
                                <div class="col-md-4">
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
                                </div>

                                <div class="col-md-6">
                                    <input name="is_stock_menu" value="yes" class="form-check-input" type="checkbox" id="stock_menu_radio">
                                    <label class="form-check-label" for="stock_menu_radio">
                                        Stock Item
                                    </label>
                                </div>
                                <hr>
                                <div class="col-md-6 mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
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
                                <a class="grid-item {{ $menu->status == 0 ? 'grid-item-inactive' : '' }}" href="{{route('menus.edit', $menu->id)}}">
                                    @if ($menu->image)
                                    <img src="/storage/menu_images/{{$menu->image}}"/>
                                    @else 
                                    <img src="/images/default-menu.svg"/>
                                    @endif                                    
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