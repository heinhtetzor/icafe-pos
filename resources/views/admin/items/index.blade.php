@extends('layouts.admin')
@section('content')
<div class="container">
    <h2>
        <a href="{{ route('admin.masterdatamanagement') }}">π</a>
        Item αα»α¬αΈ</h2>
    <h4>Create New Item</h4>
    
    @if (session('msg'))
        {{ session('msg') }}
    @endif
    @if (session('error'))
        {{ session('error') }}
    @endif
    <section>
        <form action="{{ route('items.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">α‘αα»α­α―αΈα‘αααΊ</label>
                        <input autofocus name="name" type="text" class="form-control" placeholder="α‘αααΊ αααΊα·αα½ααΊαΈαα«" required>
                        <p style="color:red">{{ $errors->first('name') }}</p>   
                    </div>            
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">αα»αααΊα·αα½α±</label>
                        <input autofocus name="cost" type="text" class="form-control" placeholder="αα»αααΊα·αα½α± αααΊα·αα½ααΊαΈαα«" required>
                        <p style="color:red">{{ $errors->first('cost') }}</p>   
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <br>
                        <input name="is_general_item" value="1" class="form-check-input" type="checkbox" id="isGeneralItemRadio">
                        <label class="form-check-label" for="isGeneralItemRadio">
                        α‘αα½α±αα½α±
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="menu_group_id">Menu Group αα½α±αΈαα«</label>
                        <select name="menu_group_id" required class="form-control" id="menuGroupItemSelect">
                            <option>=====</option>
                            @foreach ($menu_groups as $menu_group)
                            <option value="{{$menu_group->id}}">{{$menu_group->name}}</option>
                            @endforeach
                        </select>
                        <p style="color:red">{{ $errors->first('menu_group_id') }}</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-success" type="submit">Submit</button>
        </form>
    </section>
    <section>
        <h3>All Items</h3>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th>No</th>
                    <th>α‘αααΊ</th>
                    <th>αα»αααΊα·αα½α±</th>
                    <th>Menu Group</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $key => $item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->cost }}</td>
                    @if ($item->is_general_item == 1)
                    <td>α‘αα½α±αα½α±</td>
                    @endif
                    @if ($item->menu_group)
                    <td>{{ $item->menu_group->name }}</td>
                    @endif
                    <td>
                        <a href="{{ route('items.edit', $item->id) }}">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$items->links()}}
    </section>
</div>
@endsection
@section('js')
<script>
    (() => {
        const isGeneralItemRadio = document.querySelector('#isGeneralItemRadio');
        const menuGroupItemSelect = document.querySelector('#menuGroupItemSelect');
        
        isGeneralItemRadio.addEventListener('click', function () {
            if (isGeneralItemRadio.checked) {
                menuGroupItemSelect.disabled = true;
                // invoiceNo.disabled = false;
            }
            else {                
                menuGroupItemSelect.disabled = false;
                // invoiceNo.disabled = true;
            }
        })
    })();
</script>
@endsection