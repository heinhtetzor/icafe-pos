@extends('layouts.admin')
@section('content')
<div class="container">
    <h2>
        <a href="{{route('tablegroups.show', $table_group->id)}}">🔙</a>
        Editing {{ $table_group->name}}</h2>  
        @if  (session('msg'))
        <p class="alert alert-danger">
            {{ session('msg') }}
        </p>
        @endif
    <section>
        <form action="{{ route('tablegroups.update', $table_group->id) }}" method="post">
            @csrf
            @method('put')
            <div class="form-group">
                <label for="name">Table Name or Table No</label>
                <input value="{{$table_group->name}}" name="name" type="text" class="form-control" placeholder="Enter Table Name" required>
                <p style="color:red">{{ $errors->first('name') }}</p>
            </div>

            <div class="form-group">
                <input type="hidden" name="status" value="0"/>
                <input {{$table_group->status == 1 ? "checked" : ""}} name="status" value="1" class="form-check-input" type="checkbox" id="active_menu_radio">
                <label class="form-check-label" for="active_menu_radio">
                    Active
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-info" href="{{route('tablegroups.show', $table_group->id)}}">Back</a>
        </form>
        <hr>
        <button 
            onclick="document.querySelector('#delete-form').submit();" 
            class="btn btn-danger">
                Delete	
            </button>
            {{-- hidden delete form --}}
            <form id="delete-form" class="hidden" action="{{ route('tablegroups.destroy', $table_group->id) }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="id" value="{{ $table_group->id }}">
            </form>
    </section>
</div>
@endsection