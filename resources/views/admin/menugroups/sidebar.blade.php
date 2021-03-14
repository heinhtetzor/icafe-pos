
<div class="sidebar">
    <ul class="list">
        <li>
            <form action="{{ route('menugroups.store') }}" method="post" class="list-item-add-new">
                @csrf
                <input id="name" autofocus name="name" type="text" class="form-control" placeholder="Enter Menu Group Name" required>
                {{-- <p style="color:red">{{ $errors->first('name') }}</p> --}}
                <button id="btn" class="btn btn-primary" type="submit">Add</button>
            </form>
        </li>
        <li class="list-item @if  ($selected_menu_group == 'ALL') selected-list-item @endif">
            <a class="list-item-link" href="{{ route('menugroups.index')}}">
                ALL
            </a>
        </li>
        @foreach($menu_groups as $menu_group)
        <li class="list-item @if  ($selected_menu_group == $menu_group->id) selected-list-item @endif">
            <a class="list-item-link" href="{{ route('menugroups.show', $menu_group->id) }}">
                {{$menu_group->name}}
            </a>
        </li>
        @endforeach

    </ul>
</div>