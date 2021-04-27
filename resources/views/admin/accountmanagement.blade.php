@extends('layouts.admin')
@section('css')
<style>
    .flex {
    display: flex;
    flex-wrap: wrap;    
    }
    .flex > * {
        width: 300px;
        margin-right: 1rem;
    }
    .card-icon {
        display: block;
        margin: 0 auto;
    }
    .card-footer {
        text-align: center;
    }
</style>
@endsection
@section('content')
<div class="container">
    <h3>
        <a href="{{route('admin.home')}}">🔙 </a>
        Account Management
    <h3>
    <div class="flex">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Admin Accounts</h4>
            </div>
            <div class="card-body">
                <img width="100" class="card-icon" src="/assets/admin-icon.png" alt="admin">
            </div>
            <div class="card-footer">
                <a href="{{route('admin_accounts.index')}}" class="btn btn-primary">Go</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Waiter Accounts</h4>
            </div>
            <div class="card-body">
                <img width="100" class="card-icon" src="/assets/waiter-icon.png" alt="waiter">

            </div>
            <div class="card-footer">
                <a href="{{route('waiters.index')}}" class="btn btn-primary">Go</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Kitchens</h4>
            </div>
            <div class="card-body">
                {{-- Kitchen Management --}}
                <img width="100" class="card-icon" src="/assets/kitchen.png" alt="kitchen">
            </div>
            <div class="card-footer">
                <a class="btn btn-warning" href="{{route('kitchens.index')}}">Go</a>
            </div>
        </div>
    </div>
    <hr>
    <div class="flex">
        <div class="card">
            <div class="card-header">
                <h4>Other Settings</h4>
            </div>
            <div class="card-body">
                <img src="/assets/lock.png" alt="lock" width="100" class="card-icon">
            </div>
            <div class="card-footer">
                <a class="btn btn-warning" href="{{ route('settings.index') }}">Go</a>
            </div>
        </div>
    </div>
</div>
@endsection 