@extends('layouts.admin')
@section('content')
<div class="container">
    <h2>
        <a href="{{ route('admin.masterdatamanagement') }}">🔙</a>
        Item များ</h2>
    @if (session('msg'))
        {{ session('msg') }}
    @endif
    <h4>Create New Item</h4>
    
    <section>
        <form action="">
            
        </form>
    </section>
</div>
@endsection