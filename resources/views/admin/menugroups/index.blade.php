@extends('layouts.admin')
@section('css')
<style>
    
        
</style>
@endsection
@section('content')
    <div class="container-fluid">
        <h3><a href="{{ route('admin.masterdatamanagement') }}">🔙</a>  
            အရောင်းပစ္စည်းအုပ်စုများ
            <a type="button" class="btn btn-primary" href="/admin/menugroups/create">+ အသစ်</a>
        </h3>
        <table class="table table-condensed">
            <thead class="table table-primary">
                <tr>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($menu_groups as $menu_group)
                <tr>
                    <td>
                        {{$menu_group->name}}
                    </td>
                    <td>
                        <a class="list-item-link" href="{{ route('menugroups.edit', $menu_group->id) }}">
                            Edit
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
@endsection

@section('js')
<script>
    const menuSearchInput=document.querySelector('#menuSearchInput');
    menuSearchInput.addEventListener('input', menuSearchInputHandler);
    const originalMenuItems=[...document.querySelector('.grid').children];

    function menuSearchInputHandler (e) {
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
            const textLower = text.toLowerCase();
            const menuCodeLower = x.dataset['menuCode'].toLowerCase();
            const menuNameLower = x.dataset['menuName'].toLowerCase();
            if (!menuNameLower.includes(textLower) && !menuCodeLower.includes(textLower)) {
                x.style.display = 'none';
            }
        })            
    }
</script>
@endsection
