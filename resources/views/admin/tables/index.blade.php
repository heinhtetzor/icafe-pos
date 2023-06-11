@extends('layouts.admin')
@section('css')
<link rel="stylesheet" href="/css/tables.css">
@endsection
@section('content')
    <div class="container">
        <h2>
            <a href="{{ route('admin.masterdatamanagement') }}">🔙</a>
            Table အုပ်စုများ</h2>
        @if (session('msg'))
            {{ session('msg') }}
        @endif
        <h4>Create New Table Group</h4>
        <section>
            <form action="{{ route('tablegroups.store') }}" method="post">
                @csrf
                <input name="store_id" type="hidden" value="{{Auth::guard('admin_account')->user()->store_id}}"/>
                <div class="form-group">
                    <label for="name">အမည်</label>
                    <input type="text" autofocus name="name" class="form-control" placeholder="Enter Table Group Name" required>
                    <p style="color:red">{{ $errors->first('name') }}</p>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </section>
        <section>
            <h2>All Table Groups</h2>
            <div class="tables-grid">
                @foreach($table_groups as $table_group)
                <a href="{{ route('tablegroups.show', $table_group->id) }}" class="">
                    <span>{{ $table_group->name }}</span>
                </a>
                @endForeach
            </div>
        </section>
    </div>
@endsection