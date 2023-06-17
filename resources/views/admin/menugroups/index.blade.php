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
                    <input type="text" id="menuSearchInput" class="form-control mb-4" placeholder="ရှာပါ" role="search">
                    <div class="grid">                    
                        @foreach($menus as $menu)
                        <a data-menu-code="{{ $menu->code }}" data-menu-name="{{ $menu->name }}" class="grid-item {{ $menu->status == 0 ? 'grid-item-inactive' : '' }}" href="{{route('menus.edit', $menu->id)}}">
                            @if ($menu->image)
                            <img src="/storage/menu_images/{{$menu->image}}"/>
                            @else 
                            <img src="/images/default.png"/>
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
        </div>
    </div>
@endsection

@section('js')
<script>
    const menuSearchInput=document.querySelector('#menuSearchInput');
    menuSearchInput.addEventListener('input', menuSearchInputHandler);
    const originalMenuItems=[...document.querySelector('.grid').children];

    function menuSearchInputHandler (e) {
        console.log(e.target.value);
        filterByTextSearch(originalMenuItems, e.target.value);
    }

    function filterByTextSearch(originalMenuItems, text) {
        originalMenuItems.forEach(x=>{
            x.style.display='block';
        }) 
        if (!text) {
            return;
        }
        originalMenuItems.forEach (x => {      
            console.log(x)          
            if (!x.dataset['menuName'].includes(text) && !x.dataset['menuCode'].includes(text)) {
                x.style.display = 'none';
            }
        })            
    }
</script>
@endsection
