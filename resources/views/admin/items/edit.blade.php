@extends('layouts.admin')
@section('content')
<div class="container">
    <h2>
        <a href="{{ route('items.index') }}">π</a>        
        Editing {{ $item->name }}</h2>
    
    @if (session('msg'))
        <div class="alert alert-success">{{ session('msg') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <section>
        <form action="{{ route('items.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">α‘αα»α­α―αΈα‘αααΊ</label>
                <input value="{{$item->name}}" autofocus name="name" type="text" class="form-control" placeholder="α‘αααΊ αααΊα·αα½ααΊαΈαα«" required>
                <p style="color:red">{{ $errors->first('name') }}</p>   
            </div>            
            <div class="form-group">
                <label for="name">αα»αααΊα·αα½α±</label>
                <input value="{{$item->cost}}" autofocus name="cost" type="text" class="form-control" placeholder="αα»αααΊα·αα½α± αααΊα·αα½ααΊαΈαα«" required>
                <p style="color:red">{{ $errors->first('cost') }}</p>   
            </div>
            <div class="form-group">
                <input @if ($item->is_general_item == 1) checked  @endif name="is_general_item" value="{{ $item->is_general_item }}" class="form-check-input" type="checkbox" id="isGeneralItemRadio">
                <label class="form-check-label" for="isGeneralItemRadio">
                α‘αα½α±αα½α±
                </label>
            </div>
            <div class="form-group">
                <label for="menu_group_id">Menu Group αα½α±αΈαα«</label>
                <select @if ($item->is_general_item == 1)  disabled @endif name="menu_group_id" required class="form-control" id="menuGroupItemSelect">
                    <option>=====</option>
                    @foreach ($menu_groups as $menu_group)
                    <option
                    @if($menu_group->id == $item->menu_group_id) selected @endif
                    value="{{$menu_group->id}}">{{$menu_group->name}}</option>
                    @endforeach
                </select>
                <p style="color:red">{{ $errors->first('menu_group_id') }}</p>
            </div>
            <button class="btn btn-success" type="submit">Submit</button>
        </form>
        <hr>
        <button 
            onclick="if(!confirm('Are you sure?')) return; document.querySelector('#delete-form').submit();" 
            class="btn btn-danger">
                Delete	
            </button>
            {{-- hidden delete form --}}
            <form id="delete-form" class="hidden" action="{{ route('items.destroy', $item->id) }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="id" value="{{ $item->id }}">
            </form>
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