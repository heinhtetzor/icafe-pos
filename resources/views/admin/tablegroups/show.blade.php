{{-- actually table group show  --}}

@extends('layouts.admin')
@section('css')
    <link rel="stylesheet" href="/css/tables.css">
@endsection
@section('content')
<div class="container">
    <h4>        
        <a href="{{ route('tables.index') }}">🔙</a>
        {{ $table_group->name }} ရှိ Table များ
        <a href="{{ route('tablegroups.edit', $table_group->id) }}">[Edit]</a>
    </h4>
    
    <section>
        <form action="{{ route('tables.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="name">Table Name or Table No</label>
                <input type="hidden" name="table_group_id" value="{{ $table_group->id }}">
                <input autofocus name="name" type="text" class="form-control" placeholder="Enter Table Name" required>
                <p style="color:red">{{ $errors->first('name') }}</p>
            </div>
            <button class="btn btn-primary">Submit</button>
        </form>
    </section>

    <section>        
        <div class="tables-grid">
            @foreach($table_group->tables as $table)
            <a href="{{route('tables.edit', $table->id)}}" class="tables-grid-item">
                <span>{{$table->name}}</span>
            </a>                
            @endforeach
        </div>
    </section>
</div>
@endsection