@extends('layouts.client')
@section('style')
    <style>
        .container {
            padding: 3rem;
        }
        .tables-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
        .tables-container-item {
            border: 1px solid #b3b3b3;
            min-height: 200px;
        }
    </style>
@endsection
@section('content')

    <div class="container">
        <h2>Tables</h2>
        <div class="tables-container">
            @foreach($tables as $table)
            <a class="tables-container-item" href="{{route('client.waiter.menus', $table->id)}}">{{$table->name}}</a>
            @endforeach
        </div>
    </div>

@section('script')
<script>
    (function() {
        redirectIfNoUser();


    })();
</script>
@endsection
@endsection