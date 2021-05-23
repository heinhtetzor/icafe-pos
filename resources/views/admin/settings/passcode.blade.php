@extends('layouts.admin')
@section('content')
<div class="container">
    <h4><a href="{{route('settings.index')}}">🔙</a>
    Passcode</h4>
    <div class="row">
        @if(session('msg'))
        <p class="alert alert-success">{{ session('msg') }}</p>
        @endif
        @if(session('error'))
        <p class="alert alert-danger">{{ session('error') }}</p>
        @endif
        <form action="{{ route('settings.savePasscode') }}" method="post">
            @csrf
            <div class="col-md-3">
                <div class="form-group">
                    <label for="old_passcode">Old Passcode</label>
                    <input type="password" name="old_passcode_value" class="form-control" required autofocusx>
                </div>
                <div class="form-group">
                    <label for="passcode">Passcode</label>
                    <input name="passcode_value" type="password" class="form-control"  required>
                    <hr>
                    <button class="btn btn-success">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection