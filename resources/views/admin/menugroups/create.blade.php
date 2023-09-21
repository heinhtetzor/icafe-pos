@extends('layouts.admin')
@section('css')
<style>
    
</style>
@endsection

@section('content')
<header class="header">
    <h3>
        <a href="{{ route('menugroups.index') }}">🔙</a>
        အရောင်းပစ္စည်းအုပ်စုအသစ်
    </h3>
</header>

<section>
    <form action="{{ route('menugroups.store') }}" method="post">
        @csrf
        @if (session('error'))
        <p class="alert alert-danger">{{ session('error') }}</p>
        @endif
        @if (session('msg'))
        <p class="alert alert-success">{{ session('msg') }}</p>
        @endif              
        <input name="store_id" type="hidden" value="{{Auth::guard('admin_account')->user()->store_id}}"/>

        <div class="col-md-4">
            <div class="form-group">
                <label for="name">Menu အုပ်စု အမည်</label>
                <input autofocus name="name" type="text" class="form-control" placeholder="Enter Menu Group Name" required>
                <p style="color:red">{{ $errors->first('name') }}</p>
            </div>

            <div class="form-group">
                <label for="name">Color</label>
                <input name="color" type="color" class="form-control" required>
                <p style="color:red">{{ $errors->first('color') }}</p>
            </div>

            <br>

            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>

</section>

@endsection
