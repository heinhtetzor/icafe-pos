@extends('layouts.admin')
@section('css')
<style>
    .menu-image {
        height: 4rem;
    }
</style>
@endsection
@section('content')
        <div class="container-fluid">
            <section>
                <!-- action buttons -->
                <div class="row">
                    <div class="col-md-3">
                        <h3>
                            <a href="{{ route('admin.masterdatamanagement') }}">🔙</a>
                            အရောင်းပစ္စည်းများ
                            <a type="button" class="btn btn-primary" href="/admin/menus/create">+ အသစ်</a>
                        </h3>
                    </div>
                    <div class="col-md-3">

                    </div>
                    <div class="col-md-3">

                    </div>
                    <div class="col-md-3">

                    </div>

                </div>
                <!-- search form -->
                <br>
                <form action="/admin/menus" method="GET" id="search_from">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-group">
                                <input value="{{ request()->search }}" type="text" name="search" class="form-control" placeholder="Item name or Item code" autofocus autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-success">ရှာပါ</button>
                                    <a type="submit" class="btn btn-danger" href="/admin/menus">Reset</a>
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-md-3">
                        </div>
                        
                        <div class="col-md-2">

                        </div>
                        <div class="col-md-1">
                        <select name="status" id="status" class="form-control">
                                <option {{ request()->get('status') == '1' ? 'selected' : ''}} value="1">Active</option>
                                <option {{ request()->get('status') == '0' ? 'selected' : ''}} value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            
                            <select name="menu_group_id" class="form-control" id="menu_group_id">
                                <option  value="">Group ဖြင့်ရှာပါ<option>
                                @foreach ($menu_groups as $menu_group)
                                <option {{ request()->get('menu_group_id') == $menu_group->id ? 'selected' : ''}} value="{{$menu_group->id}}">{{$menu_group->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                <!-- menu list -->
                <table class="table table-striped">
                    <thead class="table-success">
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
                                <img height="40" loading="lazy" src="/storage/menu_images/{{$menu->image}}"/>
                                @else 
                                <img height="40" loading="lazy" src="/images/default-menu.svg"/>
                                @endif                            
                            </td>
                            <td>{{$menu->price}}</td>
                            <td>{{$menu->menu_group->name}}</td>
                            <td>{{$menu->status == '1' ? "Active" : "Inactive"}}</td>
                            <td>
                                <a href="{{ route('menus.edit', $menu->id) }}">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{$menus->appends($_GET)->links()}}
            </section>
        </div>


@endsection

@section('js')
<script>
    const menuGroupIdEle = document.querySelector('#menu_group_id');
    const statusEle = document.querySelector('#status');

    const searchFormEle = document.querySelector('#search_from');
    
    menuGroupIdEle.addEventListener('change', () => {
        searchFormEle.submit();
    })

    statusEle.addEventListener('change', () => {
        searchFormEle.submit();
    })
</script>
@endsection