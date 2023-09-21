@extends('layouts.admin')
@section('css')
<style>
    
</style>
@endsection

@section('content')
<header class="header">
        <h3>
            <a href="{{ route('menus.index') }}">🔙</a>
            အရောင်းပစ္စည်းအသစ်</h3>
    </header>
        @if  (session('msg'))
        <p class="alert alert-success">
            {{ session('msg') }}
        </p>
        @endif
        <section>
            <form action="{{ route('menus.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input name="store_id" type="hidden" value="{{Auth::guard('admin_account')->user()->store_id}}"/>
                <div class="col-md-4">
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

                </div>
            </form>
        </section>
@endsection

@section('js')
@endsection