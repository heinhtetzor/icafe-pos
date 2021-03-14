@extends('layouts.admin')
@section('content')
    <header class="header">
        <h2>Tables</h2>
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
             <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tables as $table)
                    <tr>
                        <td>{{$table->name}}</td>
                        <td>
                            <a href="{{ route('tables.edit', $table->id) }}">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </header>
@endsection