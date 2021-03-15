@extends('layouts.admin')
@section('css')
<style type="text/css">
    .tables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        grid-gap: 1rem; 
    }
    .tables-grid-item {      
        text-decoration: none;
        display: block;  
        height: 100px;
        width: 100px;
        position: relative;
        text-align: center;        
        position: relative;
        background-image: url('/assets/table-icon.png');
        background-size: 100px;
        background-blend-mode: overlay;
        background-repeat: no-repeat;
        cursor: pointer;
    }
    .tables-grid-item  span {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 900;
        /* position: absolute;
        top: 40%; */
        font-size: 4rem;
        color: black;        
        text-shadow: 4px 4px 5px rgb(228, 4, 4);
    }
</style>
@endsection
@section('content')
    <div class="container">
        <h2>
            <a href="{{route('admin.masterdatamanagement')}}">🔙</a>
            Tables</h2>
        <h4>Create New Table</h4>
        @if  (session('msg'))
        <p class="alert alert-success">
            {{ session('msg') }}
        </p>
        @endif
        <section>
            <form action="{{ route('tables.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="name">Table Name or Table No</label>
                    <input autfocus name="name" type="text" class="form-control" placeholder="Enter Table Name" required>
                    <p style="color:red">{{ $errors->first('name') }}</p>
                </div>
                <button class="btn btn-primary">Submit</button>
            </form>
        </section>

        <section>
            <h2>All Tables</h2>
            <div class="tables-grid">            
                @foreach($tables as $table)
                <a href="{{route('tables.edit', $table->id)}}" class="tables-grid-item">
                    <span>{{$table->name}}</span>
                </a>                
                @endforeach
            </div>
        </section>
    </div>
@endsection