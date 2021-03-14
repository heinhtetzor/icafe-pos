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
</style>
@endsection
@section('content')
<div class="container">
    <div class="flex">
        <div class="card bg-success text-white">
            <div class="card-header">
                <h2 class="card-title">Orders</h2>
            </div>
            <div class="card-body">

            </div>
            <div class="card-footer">
                <a class="btn btn-warning" href="{{route('orders.index')}}">Go</a>
            </div>
        </div>
    </div>
    <br>
    <div class="flex">
        <div class="card bg-success text-white">
            <div class="card-header">
                <h2 class="card-title">Master Data</h2>
            </div>
            <div class="card-body">
                Manage Master Data such as Menus, MenuGroups and Tables.
            </div>
            <div class="card-footer">
                <a class="btn btn-warning" href="{{route('admin.masterdatamanagement')}}">Go</a>
            </div> 
        </div>
        <div class="card bg-primary text-white">
            <div class="card-header">
                <h2 class="card-title">Account Management</h2>
            </div>
            <div class="card-body">
                Waiter accounts and Admin Account Management
            </div>
            <div class="card-footer">
                <a class="btn btn-warning" href="{{route('admin.accountmanagement')}}">Go</a>
            </div>
        </div>
    </div>


    <hr>
</div>
@endsection