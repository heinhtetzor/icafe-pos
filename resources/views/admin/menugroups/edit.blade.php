@extends('layouts.admin')
@section('content')
    <h2>Editing {{ $menu_group->name}}</h2>  
    <section>
        <form action="{{ route('menugroups.update', $menu_group->id) }}" method="post">
            @csrf
            @method('put')
            <div class="form-group">
                <label for="name">Table Name or Table No</label>
                <input value="{{$menu_group->name}}" name="name" type="text" class="form-control" placeholder="Enter Table Name" required>
                <p style="color:red">{{ $errors->first('name') }}</p>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-info" href="{{url()->previous()}}">Back</a>
        </form>
        <hr>
        <button 
            onclick="document.querySelector('#delete-form').submit();" 
            class="btn btn-danger">
                Delete	
            </button>
            {{-- hidden delete form --}}
            <form id="delete-form" class="hidden" action="{{ route('menugroups.destroy', $menu_group->id) }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="id" value="{{ $menu_group->id }}">
            </form>
    </section>
@endsection