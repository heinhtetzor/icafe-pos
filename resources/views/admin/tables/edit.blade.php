@extends('layouts.admin')
@section('content')
    <h2>Editing {{ $table->name}}</h2>  
    <section>
        <form action="{{ route('tables.update', $table->id) }}" method="post">
            @csrf
            @method('put')
            <div class="form-group">
                <label for="name">Table Name or Table No</label>
                <input value="{{$table->name}}" name="name" type="text" class="form-control" placeholder="Enter Table Name" required>
                <p style="color:red">{{ $errors->first('name') }}</p>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-info" href="{{route('tables.index')}}">Back</a>
        </form>
        <hr>
        <button 
            onclick="document.querySelector('#delete-form').submit();" 
            class="btn btn-danger">
                Delete	
            </button>
            {{-- hidden delete form --}}
            <form id="delete-form" class="hidden" action="{{ route('tables.destroy', $table->id) }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="id" value="{{ $table->id }}">
            </form>
    </section>
@endsection